<?php

namespace App\Controllers;

use DateTime;
use DateInterval;
use DatePeriod;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\TimeoffModel;
use App\Models\UserModel;
use Exception;

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

    public function telegram_receiveMessage() {
        $request = $this->request->getJSON();
        $chatModel = model('ChatModel');
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
                                        Timeoff time ranges from 08:00 to 17:00.",
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
            log_message('notice', json_encode($request));
            $chatId = $request->message->chat->id;
            $userID = $request->message->from->id;
            $text = $request->message->text;
            
            if (!isset($text)) {
                return $this->response->setStatusCode(ResponseInterface::HTTP_OK)->setJSON(['status' => 'ok']);
            }
            
            $userModel = new UserModel();
            $userName = $userModel->getEmployeeInfo($userID);
            $userName = isset($userName) ? $userName['name'] : '';
            // Process the message or perform any action here
            if ($text == '/start') {
                $this->sendMessage($chatId, "Xin chào {$userName}! Tôi có thể giúp gì bạn?");
            } elseif ($text == '/info') {
                $info = $userModel->getEmployeeTimeOff($userID);
                $cur_month = date("m");
                $this->sendMessage($chatId, "Xin chào {$userName}! \nTrong năm nay bạn đã xin nghỉ tổng cộng {$info['total']} ngày công. \nTháng {$cur_month} này bạn đã xin nghỉ {$info['month_count']} lần với tổng số {$info['month_duration']} ngày công, trong đó: \nNghỉ chế độ: {$info['month_chedo']}\nNghỉ có lương: {$info['month_coluong']}\nNghỉ không lương: {$info['month_koluong']} \nBạn còn {$info['remain']} ngày phép nữa.");
            } elseif ($text == '/deletehistory') {
                $chatModel->where('tel_user_id', $userID)->set('deleted', 1)->update();
                $this->sendMessage($chatId, "Lịch sử chat trong CSDL đã xóa xong.");
            }
            else {
                $data = [
                    'tel_user_id' => $userID,
                    'add_date' => date("Y-m-d H:i:s", $request->message->date),
                    'message' => $text,
                    'type' => 'user',
                ];
                $chatModel->insert($data);
                $context = $chatModel->getChatContext($userID, 10);
                // log_message('notice', "CONTEXT: {$context}");
                if ($userName != '') {
                    $context = "Tên tôi là:{$userName}" . $context;
                }

                $result = $this->gpt_api_call(question:$text, context:$context, tools:$tools);

                if ($result['result'] == 'chat') {
                    $this->sendMessage($chatId, $result['text']);
                } elseif ($result['result'] == 'tool') {
                    $this->processToolCall($userID, $chatId, $result['param'], $result['func_list']);
                    // $this->sendMessage($chatId, $reply);
                } elseif ($result['result'] == 'fail') {
                    $this->sendMessage($chatId, 'Có lỗi xảy ra trong khi xử lý yêu cầu của bạn.');
                }
            }
        } elseif (isset($request->callback_query)) {
            $chatId = $request->callback_query->message->chat->id;
            $userID = $request->callback_query->from->id;
            $messageID = $request->callback_query->message->message_id;
            $text   = $request->callback_query->message->text;
            $data   = $request->callback_query->data;

            //Edit lại tin nhắn, bỏ inline button vì telegram không tự động bỏ
            $this->editMessage($chatId, $messageID, $text);

            if ($data == 'confirm_false') {
                $data = [
                    'tel_user_id' => $userID,
                    'add_date' => date("Y-m-d H:i:s"),
                    'message' => 'Bạn muốn thay đổi thông tin gì?',
                    'type' => 'assistant',
                ];
                $chatModel->insert($data);
                $this->sendMessage($chatId, $data['message']);
            } elseif ($data == 'confirm_true') {
                // log_message('notice', "Confirm mess:{$text}");
                $insertResult = $this->saveTimeOff($text, $userID);
                if ($insertResult) {
                    $this->sendMessage($chatId, 'Cảm ơn bạn đã xác nhận! Thông tin nghỉ phép của bạn đã được ghi nhận.');
                } else {
                    $this->sendMessage($chatId, 'Có lỗi xảy ra trong quá trình lưu.');
                }
            }

        }

        return $this->response->setStatusCode(ResponseInterface::HTTP_OK)->setJSON(['status' => 'ok']);
    }

    private function processToolCall($userID, $chatID, $params, $function_list) {
        $chatModel = model('ChatModel');
        foreach ($function_list as $func) {
            if ($func == 'get_timeoff_detail') {
                $result = $this->get_timeoff_detail($params);
            } else {
                $result = [
                    'result' => 'fail',
                    'text' => 'Có lỗi xảy ra'
                ];
            }
        }

        // log_message('notice', json_encode($result));
        
        if ($result['result'] == 'fail' || $result['result'] == 'noinfo') {
            $this->sendMessage($chatID, $result['text']);
        } elseif ($result['result'] == 'check') {
            $data = [
                'tel_user_id' => $userID,
                'add_date' => date("Y-m-d H:i:s"),
                'message' => $result['text'],
                'type' => 'assistant',
            ];
            $chatModel->insert($data);

            $this->sendMessage($chatID, $result['text'], $result['inline_keyboard']);
        }

        return $result;
    }

    private function get_timeoff_detail($params) {
        $params['date'] = isset($params['date']) ? $this->parseDate($params['date']) : '';
        $params['enddate'] = isset($params['enddate']) ? $this->parseDate($params['enddate']) : '';
        // $params['time']     = (!isset($params['time'])) ? '' : $params['time'];
        $params['reason']   = (isset($params['reason'])) ? $params['reason'] : '';

        $inline_button = [
            'inline_keyboard' => [
                [
                    ['text' => 'Đồng ý', 'callback_data' => 'confirm_true'],
                    ['text' => 'Không đồng ý', 'callback_data' => 'confirm_false'],
                ]
            ]
        ];

        // Lấy thời gian hiện tại
        $currentDateTime = new DateTime();
        $currentHour = (int) $currentDateTime->format('H');

        // Kiểm tra nếu thời gian hiện tại đã vượt qua 17:00 (5:00 PM)
        if ($currentHour >= 17) {
            // Nếu vượt qua 17:00, lấy 08:00 sáng ngày hôm sau
            $currentDateTime->modify('+1 day')->setTime(8, 0);
        }
        // Trả về thời gian theo định dạng
        $today = $currentDateTime->format('d/m/Y H:i');
        $today_end = $currentDateTime->setTime(17, 0)->format('d/m/Y H:i');

        // $today = date('Y-m-d 08:00:00');

        //Tạo 2 biến: startDate là ngày bắt đầu, endDate là 17h cùng ngày
        if ($params['date'] != '') {
            $startDate_string = date('d/m/Y H:i',strtotime($params['date']));
            $startDate = DateTime::createFromFormat('d/m/Y H:i', $startDate_string);
            $endDate = $startDate->setTime(17, 0)->format('d/m/Y H:i');
        }

        if ($params['date'] != '' && $params['enddate'] == '') {
            // Nếu không có end_date, lấy end_date là cuối ngày
            $params['enddate'] = $endDate;
        } elseif ($params['date'] == '' && $params['enddate'] != '') {
            // Nếu không có start_date, lấy start_date bắt đầu từ bây giờ
            $params['enddate'] = $today;
        }

        if ($params['reason'] == '') {
            if ($params['date'] == '' && $params['enddate'] == '') {
                return [
                    'result' => 'noinfo',
                    'text' => "Các thông tin xin nghỉ phép vẫn chưa đầy đủ. Xin mời cung cấp ngày bắt đầu, ngày kết thúc, lý do xin nghỉ phép.",
                ];
            } else {
                $result = [
                    'result' => 'check',
                    'text' => "Thông tin nghỉ phép;\nBắt đầu: {$params['date']};\nKết thúc: {$params['enddate']};\nLý do: {$params['reason']};\nVì chưa có lý do nghỉ nên yêu cầu sẽ bị tính là nghỉ không lương. Các thông tin này đã đúng chưa?",
                    'inline_keyboard' => json_encode($inline_button)
                ];
            }
        } else {
            if ($params['date'] == '' && $params['enddate'] == '') {
                $params['date'] = $today;
                $params['enddate'] = $today_end;
                $result = [
                    'result' => 'check',
                    'text' => "Bạn chưa cung cấp ngày nghỉ nên sẽ lấy ngày gần nhất. Thông tin nghỉ phép;\nBắt đầu: {$params['date']};\nKết thúc: {$params['enddate']};\nLý do: {$params['reason']};\nCác thông tin này đã đúng chưa?",
                    'inline_keyboard' => json_encode($inline_button)
                ];
            } else {
                $result = [
                    'result' => 'check',
                    'text' => "Thông tin nghỉ phép;\nBắt đầu: {$params['date']};\nKết thúc: {$params['enddate']};\nLý do: {$params['reason']};\nCác thông tin này đã đúng chưa?",
                    'inline_keyboard' => json_encode($inline_button)
                ];
            }
        }

        return $result;
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

        //Tính số ngày công xin nghỉ
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

        //Thông tin để lưu vào CSDL
        $employee = $userModel->getEmployeeInfo($telegram_user_id);
        $data = [
            'employee_id' => $employee['id'],
            'request_date' => date('Y-m-d H:i:s'),
            'start_date' => $startDate->format('Y-m-d H:i:s'),
            'end_date' => $endDate->format('Y-m-d H:i:s'),
            'reason' => $details['reason'],
            'duration' => $workdays,
        ];

        //Phân loại yêu cầu nghỉ dựa theo lý do nghỉ
        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name'  => 'get_timeoff_type',
                    'description' => "Categorize a Vietnamese request for timeoff according to reason. The categories are:
                                        1.denmuon: if the user is late for work, maximum half a day;
                                        2.nghiphep: if the timeoff request is for 1 day or more;
                                        3.chedo: if the timeoff request is due to sickness or pregnancy;
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

        if ($details['reason'] != '') {
            $api_response = $this->gpt_api_call(
                tools:$tools, 
                question:$details['reason'], 
                context:'', //Chỉ truyền vào đoạn text được chỉ định trước, tránh gây nhầm lẫn cho AI
                tool_choice:'required' //Bắt buôc phải gọi tool get_timeoff_type
            );
            if ($api_response['result'] == 'tool') {
                $type = $api_response['param']['timeoff_type'];
                if ($type == 'chedo') {
                    //Nghỉ chế độ, không tính vào số ngày phép
                    $data['type'] = 'chedo';
                } elseif (in_array($type, ['denmuon', 'nghiphep', 'congtac', 'nghibu'])) {
                    //Nghỉ có lương nếu còn ngày phép, không lương nếu hết ngày
                    $timeOffInfo = $userModel->getEmployeeTimeOff($telegram_user_id);
                    if ($timeOffInfo['remain'] > $workdays) {
                        $data['type'] = 'luong';
                    } else {
                        $data['type'] = 'koluong';
                    }
                } else {
                    //Các trường hợp còn lại đều không lương
                    $data['type'] = 'koluong';
                }
            } else {
                $data['type'] = 'koluong';
            }
        }

        return $timeOffModel->insert($data);
    }

    private function parseDate($inputDate) {
        try {
            $outputDate = strtotime($inputDate);
            $formatedDate = date('d/m/Y H:i',$outputDate);
            // $formatedDate = date('Y-m-d H:i:s',$outputDate);
            $startHour = date('H:i:s', strtotime('08:00:00'));
            $endHour = date('H:i:s', strtotime('17:00:00'));
            return $formatedDate;
        } catch (Exception $e) {
            return '';
        }
    }

    // Send a message using Telegram API
    public function sendMessage($chatId, $message, $inline = null)
    {
        $token = getenv('TELEGRAM_BOT_TOKEN');
        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        if (!isset($inline)) {
            $data = [
                'chat_id' => $chatId,
                'text' => $message
            ];
        } else {
            $data = [
                'chat_id' => $chatId,
                'text' => $message,
                'reply_markup' => $inline
            ];
        }

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

    // Chỉnh sửa tin nhắn
    public function editMessage($chatID, $messageID, $text)
    {
        $token = getenv('TELEGRAM_BOT_TOKEN');
        $url = "https://api.telegram.org/bot{$token}/editMessageText";

        $data = [
            'chat_id' => $chatID,
            'message_id' => $messageID,
            'text' => $text
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
