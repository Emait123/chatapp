<?php

namespace App\Controllers;
use App\Models\TimeoffModel;

use Exception;
use \OpenAI;

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
            'user-input' => $this->ask($post['question'], $post['context']),
            'confirm-timeoff' => $this->registerTimeoff($post),
            'clear-session' => $this->clearSession(),
        };

        return $this->response->setJSON($response);
    }

    private function registerTimeoff($params) {
        if (!isset($params['confirm'])) {
            return;
        }
        $user = $this->session->get('user');

        $timeoff = $this->session->get('timeoff');
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
                                        4.'Tuần sau' means the next week, starts at Monday, {$next_monday}.",
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

        //Gửi câu hỏi đến GPT
        try {
            $yourApiKey = getenv('OPENAI_API_KEY');
            $client = OpenAI::client($yourApiKey);

            //Tạo stream để nhận token ngay, tránh lỗi timeout
            $stream = $client->chat()->createStreamed([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => "You're a helpful assistant, able to process Vietnamese sentences. User's previous messages are: {$context}. Reply in Vietnamese." ],
                    ['role' => 'user', 'content' => $question],
                ],
                'tools' => $tools,
                'temperature' => 0.1,
                // 'max_tokens' => 400,
                
            ]);

            $params = '';
            $text = '';
            $function_list = [];
            foreach ($stream as $chunk) {
                $array = $chunk->choices[0]->toArray();
                $delta = $array['delta'];

                //Khi chạy đến token cuối thì delta sẽ rỗng
                if (empty($delta)) {
                    break;
                }

                if (!empty($delta['tool_calls'])) {
                    $function = $delta['tool_calls'][0]['function'];
                    if (isset($function['name'])) {
                        //Nếu có function khác thì thêm %% để sau có thể tách ra
                        $function_list[] = $function['name'];
                        $params .= "%%";
                    }
                    $params .= $function['arguments'];
                } else {
                    if (!isset($delta['role']) && isset($delta['content'])) {
                        $text .= $delta['content'];
                    }
                }
            }
            
            //Nếu GPT gọi function
            if ($params != '') {
                //Xử lý các params trước khi đưa vào function
                $function_args = explode('%%', $params);
                $json_array = [];
                foreach ($function_args as $arg) {
                    if ($arg != '') {
                        $json = json_decode($arg, true);
                        $json_array[] = $json;
                    }
                }
                $param = array_merge(...$json_array);

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
            } else { //Nếu GPT gửi câu trả lời bình thường
                $content = [
                    'type' => 'chat',
                    'content' => $text
                ];
                return $content;
            }
        } catch (Exception $e) {
            log_message('error', "Lỗi: {$e->getMessage()}, line: {$e->getLine()}");
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
}
