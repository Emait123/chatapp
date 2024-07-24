<?php

namespace App\Controllers;

use Exception;
use \OpenAI;

class Home extends BaseController
{
    public function index() {
        $user = $this->session->get('user');
        if (!isset($user)) {
            return redirect()->route('Login::index');
        }

        return view('index');
    }

    public function fetch() {
        $action = $this->request->getPost('action');
        if (!isset($action)) {
            return $this->response->setJSON(['error' => lang('Error.error')]);
        }

        $post = $this->request->getPost();
        $response =  match ($action) {
            'user-input' => $this->ask($post['question'], $post['context']),
        };

        return $this->response->setJSON($response);
    }

    private function ask($question, $context) {
        $weekday = date('l');
        $today = date('h:i a d/m/Y');
        $tomorrow = date("d/m/Y", strtotime("+1 day"));
        $tomorrow_date = date("l", strtotime("+1 day"));
        $ngaykia = date("d/m/Y", strtotime("+2 day"));
        $ngaykia_date = date("l", strtotime("+2 day"));
        $next_monday = date("d/m/Y", strtotime('next monday'));
        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name'  => 'get_timeoff_detail',
                    'description' => "extract detail from a Vietnamese request for time off. Reference these dates: 
                                        1.Today: {$weekday}, {$today}; 
                                        2.Tomorrow: {$tomorrow_date}, {$tomorrow};
                                        3.'Ngày kia': {$ngaykia_date}, {$ngaykia};
                                        4.'Tuần sau' means the next week, starts at {$next_monday}.",
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'date' => [
                                'type' => 'string',
                                // 'description' => "The requested time off period, Absolute or relative date-time in a format parseable by the Python dateparser package."
                                'description' => "The requested timeoff date, in 'DD/MM/YYYY' format if possible. If it's a date range, return date start and date end in 'DD/MM/YYYY' format if possible. Return '' if not found."
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
                        'required' => ['date']
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name'  => 'get_intention',
                    'description' => "determine user's intention when requesting for timeoff. Context is taken from previous messages.",
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'intention' => [
                                'type' => 'string',
                                'description' => "The user's intention. Choose from: register, update, cancel, confirm, unknown."
                            ]
                        ],
                        'required' => ['context']
                    ]
                ]
            ]
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
            foreach ($stream as $s) {
                $array = $s->choices[0]->toArray();
                $delta = $array['delta'];

                //Khi chạy đến token cuối thì delta sẽ rỗng
                if (empty($delta)) {
                    break;
                }

                if (!empty($delta['tool_calls'])) {
                    $function = $delta['tool_calls'][0]['function'];
                    if (isset($function['name'])) {
                        $function_list[] = $function['name'];
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
                $param = json_decode($params, true);
                foreach ($function_list as $function) {
                    match($function) {
                        'get_timeoff_detail' => $this->get_timeoff_detail($param),
                        'get_intention' => $this->get_intention($param),
                        default => 'a'
                    };
                }
                // $response =  match ($function_list) {
                //     'get_timeoff_detail' => $this->get_timeoff_detail($param),
                //     default => [
                //         'type' => 'response',
                //         'content' => 'tính năng đang test...'
                //     ]
                // };

                $timeoff = $this->session->get('timeoff');
                $context = $this->session->get('context');
                if (isset($timeoff) && $timeoff['date'] != 'Chưa rõ') {
                    if ($context == 'confirm') {
                        $response = [
                            'type' => 'timeoff',
                            'content' => "Bạn đã xin nghỉ vào ngày: {$timeoff['date']} {$timeoff['time']} với lý do: {$timeoff['reason']}. Thông tin đã được ghi nhận."
                        ];
                    } else {                        
                        $response = [
                            'type' => 'timeoff',
                            'content' => "Bạn đã xin nghỉ vào ngày: {$timeoff['date']} {$timeoff['time']} với lý do: {$timeoff['reason']}. Thông tin này đã chính xác chưa?"
                        ];
                    }
                } else {
                    if ($context == 'cancel') {
                        $response = [
                            'type' => 'timeoff',
                            'content' => "Bạn đã hủy yêu cầu nghỉ thành công."
                        ];
                    } else {
                        $response = [
                            'type' => 'timeoff',
                            'content' => "Xin mời cung cấp thông tin để xin nghỉ."
                        ];
                    }
                } 
                return $response;
            } else { //Nếu GPT gửi câu trả lời bình thường
                $content = [
                    'type' => 'response',
                    'content' => $text
                ];
                return $content;
            }
        } catch (Exception $e) {
            log_message('error', "Lỗi: {$e->getMessage()}");
            $content = [
                'type' => 'error',
                'content' => "Có lỗi xảy ra trong khi xử lý yêu cầu của bạn. Vui lòng thử lại sau."
            ];
            return $content;
        }
    }

    private function get_timeoff_detail($params) {
        $timeoff = $this->session->get('timeoff');
        $context = $this->session->get('context');

        //Nếu là yêu cầu xin nghỉ mới
        if (!isset($timeoff)) {
            $params['date']     = (!isset($params['date']) || $params['date'] == '') ? 'Chưa rõ' : $params['date'];
            $params['time']     = (!isset($params['time'])) ? '' : $params['time'];
            $params['reason']   = (!isset($params['reason']) || $params['reason'] == '') ? 'Chưa rõ' : $params['reason'];
            $timeoff = [
                'date'  => $params['date'],
                'reason'  => $params['reason'],
                'time'  => $params['time'],
            ];
            $context = 'register';
            $this->session->set('timeoff', $timeoff);
            $this->session->set('context', $context);
        }
    }

    private function get_intention($params) {
        $timeoff = $this->session->get('timeoff');
        $context = $this->session->get('context');
        if (!isset($context)) {
            $context = 'register';
        }

        if (!isset($timeoff)) {
            $params['date']     = (!isset($params['date']) || $params['date'] == '') ? 'Chưa rõ' : $params['date'];
            $params['time']     = (!isset($params['time'])) ? '' : $params['time'];
            $params['reason']   = (!isset($params['reason']) || $params['reason'] == '') ? 'Chưa rõ' : $params['reason'];
            $timeoff = [
                'date'  => $params['date'],
                'reason'  => $params['reason'],
                'time'  => $params['time'],
            ];
            $context = 'register';
        }

        if ($params['intention'] == 'cancel') {
            $context = 'cancel';
            $this->session->remove('timeoff');
        } else if ($params['intention'] == 'register') {
            $context = 'register';
        } else if ($params['intention'] == 'update') {
            //Nếu có thông tin mới thì update, nếu không thì giữ nguyên
            $timeoff['date']    = (isset($params['date']) && $params['date'] != '') ? $params['date'] : $timeoff['date'];
            $timeoff['time']    = (isset($params['time']) && $params['time'] != '') ? $params['time'] : $timeoff['time'];
            $timeoff['reason']  = (isset($params['reason']) && $params['reason'] != '') ? $params['reason'] : $timeoff['reason'];
            $context = 'update';
        } else if ($params['intention'] == 'confirm') {
            $context = 'confirm';
        }

        $this->session->set('timeoff', $timeoff);
        $this->session->set('context', $context);
    }
}
