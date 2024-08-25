<?php

namespace App\Controllers;
use App\Models\UserModel;

class Login extends BaseController
{
    public function index() {
        return view('login');
    }

    public function login() {
        $post = $this->request->getPost();
        $username = $post['username'];
        $password = $post['password'];
        // $data = [
        //     'username' => $username,
        //     'password' => $password,
        //     'email'    => 'tetra.dragon197@gmail.com',
        //     'phone'     => '0123456',
        //     'last_login' => date('Y-m-d H:i:s'),
        //     'role_id'   => '1',
        // ];
        $userModel = model('UserModel');
        // $userModel->insert($data);
        $user = $userModel->where('username', $username)->first();
        if ($user == null){
            return redirect()->route('login')->with('error', 'Not exist');
        }
        if ($user && password_verify($password, $user['password'])) {
            $userModel->where('id', $user['id'])->set('last_login', date('Y-m-d H:i:s'))->update();
            $userInfo = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email'    => $user['email'],
                'phone'     => $user['phone'],
                'role_id'   => $user['role_id'],
            ];
            $this->session->set('user', $userInfo);
            return redirect()->route('Home::index');
        }
         else {
            return redirect()->back();
        }
    }

    public function logout() {
        $this->session->destroy();
        return redirect()->route('login');
    }
}