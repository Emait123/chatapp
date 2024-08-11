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
            'timeoffList' => $timeoffModel->where('employee_id', $user['id'])->where('deleted', 0)->findAll()
        ];
        return view('timeoff', $data);
    }

    public function fetch() {
        $timeoffModel = model('TimeoffModel');
        $post = $this->request->getPost();
        switch ($post['action']) {
            case 'delTimeOff':
                $id = $post['id'];
                $data = [
                    'deleted' => '1',
                ];
                if ($timeoffModel->update($id, $data)) {
                    return $this->response->setJSON(['result' => true]);
                } else {
                    return $this->response->setJSON(['result' => false]);
                }
        }
    }

}