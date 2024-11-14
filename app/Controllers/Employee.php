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
            case 'delete':
                $userID = $post['id'];
                $userModel->set('deleted', 1)->where('id', $userID)->update();
                return $this->response->setJSON(['result' => true ]);
                break;
            case 'getInfo':
                $userID = $post['id'];
                $employee = $userModel->getEmployee_userID($userID);
                return $this->response->setJSON([ 'result' => true, 'info' => $employee ]);
                break;
        }
    }

    public function timeoffList() {
        $user = $this->session->get('user');
        if (!isset($user)) {
            return redirect()->route('Login::index');
        }
        $user['role_name'] = 'Admin';

        $get = $this->request->getGet();
        $filter = [
            'month' => (isset($get['month'])) ? $get['month'] : 'all',
            'year' => (isset($get['year'])) ? $get['year'] : 'all',
        ];

        $timeoffModel = model('TimeoffModel');
        $data = [
            'user'  => $user,
            'active' => 'list',
            'years' => $timeoffModel->getYearList(),
            'timeoffList' => $timeoffModel->getTimeOffList($filter),
        ];
        return view('employee_timeoff_list', $data);
    }
}