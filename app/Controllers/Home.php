<?php

namespace App\Controllers;

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

        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => "You're a helpful assistant. The user's previous messages are: {$context}" ],
                ['role' => 'user', 'content' => $question],
            ],
        ]);

        return $result->choices[0]->message;
    }
}
