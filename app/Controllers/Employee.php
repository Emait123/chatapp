<?php

namespace App\Controllers;
use App\Models\UserModel;

class Employee extends BaseController
{
    public function index() {
        $user = $this->session->get('user');
        if (!isset($user)) {
            return redirect()->route('Login::index');
        }
        $user['role_name'] = 'Admin';
        $userModel = new UserModel();
        $data = [
            'user'  => $user,
            'active' => 'employee',
            'employeeList' => $userModel->getEmployeeList()
        ];
        return view('employee_list', $data);
    }

    public function process() {
        $post = $this->request->getPost();
        $userModel = new UserModel();
        $action = $post['action'];
        switch ($action) {
            case 'save':
                $userData = [
                    'username' => $post['username'],
                    'password' => $post['password'],
                    'createdate' => date('Y-m-d H:i:s'),
                    'role_id' => 2,
                ];
                $userID = $userModel->insert($userData, true);
                $employeeData = [
                    'user_id' => $userID,
                    'name' => $post['name'],
                    'telegram_id' => $post['telegram_id'],
                ];
                $userModel->insertEmployee($employeeData);
                return redirect()->route('Employee::index');
                break;
        }
    }
}