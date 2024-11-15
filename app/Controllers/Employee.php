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

        $get = $this->request->getGet();
        $filter = [
            'month' => (isset($get['month'])) ? $get['month'] : date("m"),
            'year' => (isset($get['year'])) ? $get['year'] : 'all',
        ];

        $user['role_name'] = 'Admin';
        $userModel = new UserModel();
        $timeoffModel = model('TimeoffModel');
        $employeeList = $userModel->getEmployeeList();
        foreach ($employeeList as $k => $v) {
            $timeOffDetail = $userModel->getEmployeeTimeOff_ByTime($v['employee_id'], $filter);
            $v['timeoffDetail'] = $timeOffDetail;
            $employeeList[$k] = $v;
        }
        $data = [
            'user'  => $user,
            'active' => 'employee',
            'years' => $timeoffModel->getYearList(),
            'employeeList' => $employeeList,
            'curMonth' => date("m"),
            'curYear' => date("Y"),
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
            case 'edit':
                $userID = $post['user-id'];
                $employeeID = $post['employee-id'];

                $userData['username'] = $post['username'];
                if (isset($post['password']) && $post['password'] != '') {
                    $userData['password'] = $post['password'];
                }
                $employeeData = [
                    'telegram_id' => $post['telegram_id'],
                    'name' => $post['name'],
                ];
                $userModel->update($userID, $userData);
                $userModel->updateEmployee($employeeID, $employeeData);
                return redirect()->route('Employee::index');
                break;
            case 'delete':
                $userID = $post['id'];
                $userModel->set('deleted', 1)->where('id', $userID)->update();
                return $this->response->setJSON(['result' => true ]);
                break;
            case 'getInfo':
                $userID = $post['userID'];
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