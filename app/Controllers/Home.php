<?php

namespace App\Controllers;
use App\Models\TimeoffModel;

use Exception;
use \OpenAI;
use DateTime;
use DateInterval;
use DatePeriod;

class Home extends BaseController
{
    public function index() {
        $this->session->remove('timeoff');
        $user = $this->session->get('user');
        if (!isset($user)) {
            return redirect()->route('Login::index');
        }

        $data = [
            'user' => $user,
            'active' => 'chat',
        ];

        return view('chat', $data);
    }

    public function fetch() {
        $action = $this->request->getPost('action');
        if (!isset($action)) {
            return $this->response->setJSON(['error' => lang('Error.error')]);
        }

        $post = $this->request->getPost();
        $response =  match ($action) {
            'user-input' => $this->saveTimeOff($post['question'], 7050621296),
            // 'user-input' => $this->ask($post['question'], $post['context']),
            'confirm-timeoff' => $this->registerTimeoff($post),
            'clear-session' => $this->clearSession(),
        };

        return $this->response->setJSON($response);
    }

    /**
     * Chức năng khi người dùng bấm xác nhận thông tin nghỉ phép
     */
    private function registerTimeoff($params) {
        if (!isset($params['confirm'])) {
            return;
        }
        $user = $this->session->get('user');

        $timeoff = $this->session->get('timeoff');

        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name'  => 'get_timeoff_type',
                    'description' => "Categorize a Vietnamese request for timeoff according to reason. The categories are:
                                        1.denmuon: if the user is late for work, maximum half a day;
                                        2.nghiphep: if the timeoff request is for 1 day or more;
                                        3.chedo: if the timeoff request is due to sickness for pregnancy;
                                        4.congtac: if the timeoff request is due to a bussiness trip or to study;
                                        5.nghibu: if the timeoff request is to makeup for working overtime;
                                        6.khac: can't determine the timeoff categories.",
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'timeoff_type' => [
                                'type' => 'string',
                                'description' => "The type of time off. Choose from this list: denmuon; nghiphep; chedo; congtac; nghibu; khac."
                            ]
                        ],
                        'required' => ['timeoff_type']
                    ]
                ]
            ],
        ];

        $question = "Xin phép nghỉ ngày {$timeoff['date']} với lý do: {$timeoff['reason']}.";

        $api_response = $this->gpt_api_call(
            tools:$tools, 
            question:$question, 
            context:$question, //Chỉ truyền vào đoạn text được chỉ định trước, tránh gây nhầm lẫn cho AI
            tool_choice:'required' //Bắt buôc phải gọi tool get_timeoff_type
        );
    
        $data = [
            'employee_id' => $user['id'],
            'start_date' => $timeoff['date'],
            'reason'    => $timeoff['reason'],
        ];
        if ($timeoff['enddate'] != '') {
            $data['end_date'] = $timeoff['enddate'];
        }
        $timeoffModel = model('TimeoffModel');
        $timeoffModel->save($data);
        $this->session->remove('timeoff');

        return ['result' => true];
    }

    private function clearSession() {
        $this->session->remove('timeoff');
        return ['result' => true];
    }

    private function ask($question, $context) {
        $today_date = date('l');
        $today = date('h:i a d/m/Y');
        $tomorrow_date = date("l", strtotime("+1 day"));
        $tomorrow = date("d/m/Y", strtotime("+1 day"));
        $ngaykia_date = date("l", strtotime("+2 day"));
        $ngaykia = date("d/m/Y", strtotime("+2 day"));
        $next_monday = date("d/m/Y", strtotime('next monday'));
        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name'  => 'get_timeoff_detail',
                    'description' => "extract detail from a Vietnamese request for time off. Reference these dates: 
                                        1.Today: {$today_date}, {$today}; 
                                        2.Tomorrow: {$tomorrow_date}, {$tomorrow};
                                        3.'Ngày kia': {$ngaykia_date}, {$ngaykia};
                                        4.'Tuần sau' means the next week, starts at Monday, {$next_monday}.
                                        Timeoff time ranges from 08:00 to 17:00",
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'date' => [
                                'type' => 'string',
                                'description' => "The start date for timeoff request, in 'Y-m-d H:i:s' format. Return '' if not found."
                            ],
                            'enddate' => [
                                'type' => 'string',
                                'description' => "The end date for timeoff request, in 'Y-m-d H:i:s' format."
                            ],
                            'time' => [
                                'type' => 'string',
                                'description' => "The time of day requested. Return '' if not found, else return 'AM' or 'PM'."
                            ],
                            'reason' => [
                                'type' => 'string',
                                'description' => "The reason for time off, e.g. bị ốm. Return '' if not found."
                            ]
                        ],
                        'required' => []
                    ]
                ]
            ],
        ];

        //Gọi ChatGPT API
        $api_response = $this->gpt_api_call($tools, $question, $context);
        if ($api_response['result'] == 'tool') { //Nếu GPT gọi function
            $function_list = $api_response['func_list'];
            $param = $api_response['param'];

            //Chạy function
            foreach ($function_list as $function) {
                match($function) {
                    'get_timeoff_detail' => $this->get_timeoff_detail($param),
                    // 'get_intention' => $this->get_intention($param),
                    default => 'a'
                };
            }

            //Lấy kết quả và trả lại response
            $timeoff = $this->session->get('timeoff');
            if (isset($timeoff)) {
                if ($timeoff['date'] == '' || $timeoff['reason'] == '') {
                    $response = [
                        'type' => 'incomplete',
                        'date' => $timeoff['date'],
                        'enddate' => $timeoff['enddate'],
                        'time' => $timeoff['time'],
                        'reason' => $timeoff['reason'],
                    ];
                } else {
                    $response = [
                        'type' => 'check',
                        'date' => $timeoff['date'],
                        'enddate' => $timeoff['enddate'],
                        'time' => $timeoff['time'],
                        'reason' => $timeoff['reason'],
                    ];
                }
            } else {
                $response = [
                    'type' => 'chat',
                    'content' => "Xin mời cung cấp thông tin để xin nghỉ."
                ];
            }
            return $response;
        }
        elseif ($api_response['result'] == 'chat') { //Nếu GPT gửi câu trả lời bình thường
            $content = [
                'type' => 'chat',
                'content' => $api_response['text']
            ];
            return $content;
        }
        elseif ($api_response['result'] == 'fail') { //Nếu lỗi
            $content = [
                'type' => 'error',
                'content' => "Có lỗi xảy ra trong khi xử lý yêu cầu của bạn. Vui lòng thử lại sau."
            ];
            return $content;
        }
    }

    private function get_timeoff_detail($params) {
        $timeoff = $this->session->get('timeoff');
        // $context = $this->session->get('context');

        //Trong hàm parseDate có try catch rồi nên k kiểm tra isset
        $params['date'] = $this->parseDate($params['date']);
        $params['enddate'] = isset($params['enddate']) ? $this->parseDate($params['enddate']) : '';

        //Nếu là yêu cầu xin nghỉ mới.
        if (!isset($timeoff)) {
            $params['date']     = (!isset($params['date'])) ? '' : $params['date'];
            $params['enddate']     = (!isset($params['enddate'])) ? '' : $params['enddate'];
            $params['time']     = (!isset($params['time'])) ? '' : $params['time'];
            $params['reason']   = (!isset($params['reason'])) ? '' : $params['reason'];
            // $params['date']     = (!isset($params['date']) || $params['date'] == '') ? 'Chưa rõ' : $params['date'];
            // $params['time']     = (!isset($params['time'])) ? '' : $params['time'];
            // $params['reason']   = (!isset($params['reason']) || $params['reason'] == '') ? 'Chưa rõ' : $params['reason'];
            $timeoff = [
                'date'  => $params['date'],
                'reason'  => $params['reason'],
                'time'  => $params['time'],
                'enddate' => $params['enddate']
            ];
        } else {
            //Nếu có thông tin mới thì update, nếu không thì giữ nguyên
            $timeoff['date']    = (isset($params['date']) && $params['date'] != '') ? $params['date'] : $timeoff['date'];
            $timeoff['enddate']    = (isset($params['enddate']) && $params['enddate'] != '') ? $params['enddate'] : $timeoff['enddate'];
            $timeoff['time']    = (isset($params['time']) && $params['time'] != '') ? $params['time'] : $timeoff['time'];
            $timeoff['reason']  = (isset($params['reason']) && $params['reason'] != '') ? $params['reason'] : $timeoff['reason'];
        }

        $this->session->set('timeoff', $timeoff);
    }

    private function get_intention($params) {
        if (isset($params['agree']) && $params['agree'] == true) {
            $this->session->set('confirm', true);
        } else {
            $this->session->set('confirm', false);
        }
    }

    private function parseDate($inputDate) {
        try {
            $outputDate = strtotime($inputDate);
            $formatedDate = date('Y-m-d H:i:s',$outputDate);
            $startHour = date('H:i:s', strtotime('08:00:00'));
            $endHour = date('H:i:s', strtotime('17:00:00'));
            return $formatedDate;
        } catch (Exception $e) {
            return '';
        }
    }

    private function saveTimeOff($text, $telegram_user_id) {
        $userModel = model('UserModel');
        $timeOffModel = model('TimeoffModel');

        $array = explode(";", $text);
        // log_message('notice', json_encode($array));
        $details = [
            'start' => trim(str_replace("Bắt đầu:", "", $array[1])),
            'end' => trim(str_replace("Kết thúc:", "", $array[2])),
            'reason' => trim(str_replace("Lý do:", "", $array[3])),
        ];

        $startDate = DateTime::createFromFormat('d/m/Y H:i', $details['start']);
        $endDate = DateTime::createFromFormat('d/m/Y H:i', $details['end']);

        // Đặt khoảng thời gian là 1 ngày
        $dateRange = new DatePeriod(
            $startDate, 
            new DateInterval('P1D'), 
            $endDate,
            DatePeriod::INCLUDE_END_DATE
        );

        $workdays = 0;
        foreach ($dateRange as $date) {
            // Kiểm tra nếu ngày hiện tại không phải là thứ 7 (6) hoặc Chủ nhật (0)
            if ($date->format('N') < 6 && $date->format('N') > 0) {
                if ($date == $dateRange->start || $date == $dateRange->end) {
                    // $endHour = $date;
                    // $endHour->setTime(17, 0);
                    // $diff = $endHour->diff($date);
                    // $hour = ($diff->h < 8) ? 0.5 : 1;
                    // $workdays += $hour;
                    $workdays++;
                } else {
                    $workdays++;
                }
            }
        }

        $employee = $userModel->getEmployeeInfo($telegram_user_id);
        $data = [
            'employee_id' => $employee['id'],
            'request_date' => date('Y-m-d H:i:s'),
            'start_date' => $startDate->format('Y-m-d H:i:s'),
            'end_date' => $endDate->format('Y-m-d H:i:s'),
            'reason' => $details['reason'],
            'duration' => $workdays,
        ];
        $timeOffModel->insert($data);
    }
}
