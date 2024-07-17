<?php

namespace App\Controllers;

use \OpenAI;

class Home extends BaseController
{
    public function index()
    {
        // $yourApiKey = getenv('OPENAI_API_KEY');
        // $client = OpenAI::client($yourApiKey);

        // $result = $client->chat()->create([
        //     'model' => 'gpt-3.5-turbo',
        //     'messages' => [
        //         ['role' => 'user', 'content' => 'Hello!'],
        //     ],
        // ]);

        // echo $result->choices[0]->message->content; // Hello! How can I assist you today?
        
        return view('index');
    }

    public function fetch() {
        $action = $this->request->getPost('action');
        if (!isset($action)) {
            return $this->response->setJSON(['error' => lang('Error.error')]);
        }

        $post = $this->request->getPost();
        return match ($action) {
            'user-input' => $this->ask($post['question']),
        };
    }

    private function ask($question) {
        $yourApiKey = getenv('OPENAI_API_KEY');
        $client = OpenAI::client($yourApiKey);

        $result = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $question],
            ],
        ]);

        return $result->choices[0]->message->content;
    }
}
