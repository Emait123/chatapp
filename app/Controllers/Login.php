<?php

namespace App\Controllers;

class Login extends BaseController
{
    public function index() {
        return view('login');
    }

    public function login() {
        $post = $this->request->getPost();
        $username = $post['username'];
        $password = $post['password'];
        if ($username == 'user' && $password == 'mhn') {
            $this->session->set('user', $username);
            return redirect()->route('Home::index');
        } else {
            return redirect()->back();
        }
    }
}