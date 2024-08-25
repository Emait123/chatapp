<?php

namespace App\Controllers;

use Exception;
use \OpenAI;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\TimeoffModel;
use App\Models\UserModel;

class WebHook extends BaseController
{
    public function index() {
        $test = $this->request->getGet();
        $mode = $this->request->getGet('hub_mode');
        $token = $this->request->getGet('hub_verify_token');
        $challenge = $this->request->getGet('hub_challenge');

        if (isset($mode) && isset($token)) {
            $myToken = getenv('FB_VERIFY_TOKEN');
            if ($mode == 'subscribe' && $token == $myToken) {
                return $this->response->setStatusCode(200)->setBody($challenge);
            } else {
                return $this->response->setStatusCode(403, '403 Forbidden.');
            }
        } else {
            return $this->response->setStatusCode(200)->setBody('not found');
        }
    }

    public function postRequest() {
        $post = $this->request->getPost();
        // log_message('notice', json_encode($post));
        if (isset($post['message'])) {
            $sender = $post['sender']['id'];
            $pageID = $post['recipient']['id'];
            $message = $post['message']['text'];

            $result = $this->gpt_api_call(question:$message, context:'');

            $postData = [
                'recipient' => ['id' => $sender],
                'messaging_type' => 'RESPONSE',
                'message' => ['text' => 'Hello, world'],
            ];

            $client = \Config\Services::curlrequest();
            $apiURL = getenv('FB_API_URL');
            $response = $client->post($apiURL,['debug' => true,'json' => $postData]);
            $log = json_encode($response);
            log_message('notice', "Thanh cong: {$log}");
        }

        return $this->response->setJSON(['result' => 'ok']);
    }

    private function gpt_api_call($tools = null, $question, $context, $tool_choice = 'auto') {
        try {
            $yourApiKey = getenv('OPENAI_API_KEY');
            $client = OpenAI::client($yourApiKey);

            //Tạo stream để nhận token ngay, tránh lỗi timeout
            $stream = $client->chat()->createStreamed([
                'model' => 'gpt-4o-mini', //Tên model sử dung
                'messages' => [
                    ['role' => 'system', 'content' => "You're a helpful assistant, able to process Vietnamese sentences. User's previous messages are: {$context}. Reply in Vietnamese." ],
                    ['role' => 'user', 'content' => $question],
                ],
                'temperature' => 0.1, //Số càng thấp thì câu trả lời của GPT càng đồng nhất, không thay đổi nhiều
                // 'max_tokens' => 400, //Số lượng input, output token tối đa
                // 'tools' => $tools, //Khai báo các hàm muốn ChatGPT dùng
                // 'tool_choice' => $tool_choice,
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
                //Xử lý các params trước khi return
                $function_args = explode('%%', $params);
                $json_array = [];
                foreach ($function_args as $arg) {
                    if ($arg != '') {
                        $json = json_decode($arg, true);
                        $json_array[] = $json;
                    }
                }
                $param = array_merge(...$json_array);

                return [
                    'result' => 'tool',
                    'param' => $param,
                    'func_list' => $function_list
                ];
            }
            //Nếu GPT gửi câu trả lời bình thường 
            else {
                return [
                    'result' => 'chat',
                    'text' => $text,
                ];
            }

        } catch (Exception $e) {
            log_message('error', "Lỗi: {$e->getMessage()}, line: {$e->getLine()}");
            return [
                'result' => 'fail'
            ];
        }
    }

    public function telegram_receiveMessage() {
        $request = $this->request->getJSON();
        // log_message('notice', json_encode($request));

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

        if (isset($request->message)) {
            $chatId = $request->message->chat->id;
            $text = $request->message->text;
            $userID = $request->message->from->id;
            
            $userModel = new UserModel();
            $userName = $userModel->getEmployeeInfo($userID);
            $userName = isset($userName) ? $userName : '';
            // Process the message or perform any action here
            if ($text == '/start') {
                $this->sendMessage($chatId, "Xin chào {$userName}! Tôi có thể giúp gì bạn?");
            } elseif ($text == '/info') {
                $this->sendMessage($chatId, "Xin chào {$userName}! Câu lệnh này vẫn chưa setup xong");
            }
            else {
                $chatModel = model('ChatModel');
                $data = [
                    'tel_user_id' => $userID,
                    'add_date' => date("Y-m-d H:i:s", $request->message->date),
                    'message' => $text
                ];
                $chatModel->insert($data);
                $context = $chatModel->getChatContext($userID, 5);
                // $this->sendMessage($chatId, 'Bot chưa setup xong GPT.');
                $result = $this->gpt_api_call(tools:$tools, question:$text, context:$context);
                return $result;
                if ($result['result'] == 'chat') {
                    $this->sendMessage($chatId, $result['text']);
                } elseif ($result['result'] == 'tool') {
                    $reply = $this->processToolCall($result['param'], $result['func_list']);
                    $this->sendMessage($chatId, $reply);
                } elseif ($result['result'] == 'fail') {
                    $this->sendMessage($chatId, 'Có lỗi xảy ra trong khi xử lý yêu cầu của bạn.');
                }
            }
        }

        return $this->response->setStatusCode(ResponseInterface::HTTP_OK)->setJSON(['status' => 'ok']);
    }

    private function processToolCall($params, $function_list) {
        foreach ($function_list as $func) {
            if ($func == 'get_timeoff_detail') {
                return $this->get_timeoff_detail($params);
            } else {
                return 'Có lỗi xảy ra';
            }
        }
    }

    private function get_timeoff_detail($params) {
        //Trong hàm parseDate có try catch rồi nên k kiểm tra isset
        $params['date'] = isset($params['date']) ? $this->parseDate($params['date']) : '';
        $params['enddate'] = isset($params['enddate']) ? $this->parseDate($params['enddate']) : '';
        $params['time']     = (!isset($params['time'])) ? '' : $params['time'];
        $params['reason']   = (!isset($params['reason'])) ? '' : $params['reason'];

        return "Bạn đã xin nghỉ vào ngày {$params['date']} với lý do {$params['reason']}";
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

    // Send a message using Telegram API
    public function sendMessage($chatId, $message)
    {
        $token = getenv('TELEGRAM_BOT_TOKEN');
        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => $message
        ];

        $options = [
            'http' => [
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context  = stream_context_create($options);
        file_get_contents($url, false, $context);
    }
}
