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
        $today = date('d/m/Y');
        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name'  => 'get_timeoff_detail',
                    'description' => "extract detail from a Vietnamese request for time off. Today is {$weekday}, {$today}. Ngày kia means two days from today, Tuần sau means the next week.",
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'time' => [
                                'type' => 'string',
                                'description' => "The requested time off period, in 'DD/MM/YYYY' format if possible. If it's a date range, return date start and date end in 'DD/MM/YYYY' format if possible."
                            ],
                            'reason' => [
                                'type' => 'string',
                                'description' => "The reason for time off, e.g. bị ốm."
                            ]
                        ],
                        'required' => ['time']
                    ]
                ]
            ],
            [
                'type' => 'function',
                'function' => [
                    'name'  => 'confirmation_context',
                    'description' => "check if user has confirmed their intention and get the confirmation context. Context is taken from previous messages",
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'confirmation' => [
                                'type' => 'boolean',
                                'description' => "Return true if user agree, false if user disagree or can't determine"
                            ],
                            'context' => [
                                'type' => 'string',
                                'description' => "The context the user agree or disagree on, regarding timeoff request. Choose from: register, update, cancel, unknown"
                            ]
                        ],
                        'required' => ['confirmation', 'context']
                    ]
                ]
            ]
        ];

        //Gửi câu hỏi đến GPT
        try {
            $yourApiKey = getenv('OPENAI_API_KEY');
            $client = OpenAI::client($yourApiKey);

            //Tạo stream để nhận token ngay, tránh lỗi timeout
            $res = '';
            $stream = $client->chat()->createStreamed([
                'model' => 'gpt-3.5-turbo',
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
            $function_name = '';
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
                        $function_name = $function['name'];
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
                $response =  match ($function_name) {
                    'get_timeoff_detail' => $this->get_timeoff_detail($param),
                    default => [
                        'type' => 'response',
                        'content' => 'abc'
                    ]
                };
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
            $params['time']     = (!isset($params['time'])) ? 'Chưa rõ' : $params['time'];
            $params['reason']   = (!isset($params['reason'])) ? 'Chưa rõ' : $params['reason'];
            $timeoff = [
                'time'  => $params['time'],
                'reason'  => $params['reason'],
            ];
            $context = 'register';
        } else { //Nếu trong session đã có yêu cầu xin nghỉ
            //Nếu có thông tin mới thì update, nếu không thì giữ nguyên
            $timeoff['time'] = (isset($params['time'])) ? $params['time'] : $timeoff['time'];
            $timeoff['reason'] = (isset($params['reason'])) ? $params['reason'] : $timeoff['reason'];
            $context = 'update';
        }

        $this->session->set('timeoff', $timeoff);
        $this->session->set('context', $context);

        $content = [
            'type' => 'timeoff',
            'content' => "Bạn đã xin nghỉ vào ngày: {$timeoff['time']} với lý do: {$timeoff['reason']}. Thông tin này đã chính xác chưa?"
        ];
        return $content;
    }

    private function confirmation_context() {
        
    }
}
