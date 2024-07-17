<?php

namespace App\Controllers;

use Exception;
use \OpenAI;

class Home extends BaseController
{
    public function index() {     
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
        $yourApiKey = getenv('OPENAI_API_KEY');
        $client = OpenAI::client($yourApiKey);

        $today = date('d/m/Y');
        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name'  => 'get_timeoff_detail',
                    'description' => "extract detail from a Vietnamese request for time off.",
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
            $result = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => "You're a helpful assistant,able to extract relevant information from a user's prompt. Today is {$today}. User's previous messages are: {$context}" ],
                    ['role' => 'user', 'content' => $question],
                ],
                'tools' => $tools
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
