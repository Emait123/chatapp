<?php

namespace App\Controllers;
use App\Models\TimeoffModel;

class TimeoffList extends BaseController
{
    public function index() {
        $user = $this->session->get('user');
        if (!isset($user)) {
            return redirect()->route('Login::index');
        }
        $user['role_name'] = 'Nhân viên';

        $timeoffModel = model('TimeoffModel');
        $data = [
            'user'  => $user,
            'active' => 'list',
            'timeoffList' => $timeoffModel->where('employee_id', $user['id'])->findAll()
        ];
        return view('timeoff', $data);
    }

}