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
        $today = date('d/m/Y');
        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name'  => 'get_timeoff_detail',
                    'description' => "extract detail from a Vietnamese request for time off. Also take information from previous context",
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'time' => [
                                'type' => 'string',
                                'description' => "The requested time off period, in 'DD/MM/YYYY' format if possible"
                            ],
                            'reason' => [
                                'type' => 'string',
                                'description' => "The reason for time off, e.g. bị ốm. Return 'không rõ' is not found."
                            ]
                        ],
                        'required' => ['time']
                    ]
                ]
            ]
        ];

        //Gửi câu hỏi đến GPT
        try {
            $yourApiKey = getenv('OPENAI_API_KEY');
            $client = OpenAI::client($yourApiKey);
            $result = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => "You're a helpful assistant, if the user want to request for timeoff, call get_timeoff_detail function, take in user's previous messages as well. Today is {$today}. User's previous messages are: {$context}. Reply in Vietnamese." ],
                    ['role' => 'user', 'content' => $question],
                ],
                'tools' => $tools,
                'temperature' => 0.1,
                'max_tokens' => 400,
                
            ]);
        } catch (Exception $e) {
            $content = [
                'type' => 'error',
                'content' => "Có lỗi xảy ra trong khi xử lý yêu cầu của bạn. Vui lòng thử lại sau."
            ];
            return $content;
        }


        $response = $result->choices[0]->message;
        if (!empty($response->toolCalls)) {
            $args = $response->toolCalls[0]->function->arguments;
            $result = json_decode($args, true);
            if (!isset($result['reason'])) {
                $result['reason'] = 'Chưa rõ';
            }
            $content = [
                'type' => 'timeoff',
                'content' => "Bạn đã xin nghỉ vào ngày: {$result['time']} với lý do: {$result['reason']}"
            ];
            return $content;
        } else {
            return $response;
        }
    }
}
