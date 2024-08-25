<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password', 'email', 'phone', 'createdate', 'last_login', 'role_id'];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];


    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    public function getEmployeeList() {
        $list = $this->select('user.id, user.username, employee.name, employee.telegram_id')
            ->join('employee', 'user.id = employee.user_id', 'left')
            ->findAll();
        return $list;
    }

    public function insertEmployee($data) {
        $builder = $this->db->table('employee');
        return $builder->insert($data);
    }

    public function getEmployeeInfo($telegram_id) {
        $builder = $this->db->table('employee');
        $query = $builder->select('employee.name, employee.telegram_id')
            ->where('telegram_id', $telegram_id);
        $result = $query->get()->getRowArray();
        return $result['name'];
    }

    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    public function getRole($user_id) {
        $db = \Config\Database::connect();
        $builder = $db->table('user_role');
        $query = $builder->select('role_id')->where('user_id', $user_id);
        $result = $query->get()->getResultArray();
        $roles = [];
        foreach($result as $value) {
            $roles[] = $value['role_id'];
        }
        return $roles;
    }

    public function insertUser($data, $role=null) {
        $this->db->transStart();
    
        $this->insert($data);
    
        $count = $this->db->affectedRows();
    
        if ($count == 0) {
            $this->db->transRollback();
            return false;
        }
    
        $user_id = $this->select('id')->where('username', $data['username'])->get()->getRowArray();
    
        if (!$user_id) {
            $this->db->transRollback();
            return false;
        }
    
        $db = \Config\Database::connect();
        $builder = $db->table('user_role');
        if ($role != null) {
            foreach ($role as $r) {
                $builder->insert(['user_id' => $user_id['id'], 'role_id' => $r]);
                if ($r == '3'){
                    $data = [
                        'user_id' => $user_id
                    ];
                    $query = $this->db->table('reviewer_info')
                        ->insert($data);
                }
            }
        } else {
            $builder->insert(['user_id' => $user_id['id'], 'role_id' => 2]); //Mặc định để role tác giả
        }
            
        $count_roleid = $db->affectedRows();
    
        if ($count_roleid == 0) {
            $this->db->transRollback();
            return false;
        }
    
        $this->db->transComplete();
    
        if ($this->db->transStatus() === FALSE) {
            return false;
        }
    
        return true;
    }
    
    public function logUser($id) {
        $this->set('lastlogin', date("Y-m-d H:i:s"))
            ->where('id', $id)
            ->update();
    }

    public function getRoleList() {
        $db = \Config\Database::connect();
        $builder = $db->table('role');
        $builder->select('id, name');
        return $builder->get()->getResultArray();
    }

    public function getUserList($page, $perPage, $username = '', $displayname = '', $role_id = '') {
        // $db = \Config\Database::connect();
        $builder = $this->db->table('user');
        // $pager = service('pager');
        // $offset = ($page-1) * $perPage;

        $builder->select('user.id, username, displayname, email, role.name as role, user_role.role_id')
            ->join('user_role', 'user_role.user_id = user.id')
            ->join('role', 'user_role.role_id = role.id')
            ->orderBy('user_role.role_id', 'ASC')->orderBy('username', 'ASC')
            ->where('deleted !=', 1);
        if ($username != '') {
            $builder->where('username', $username);
        }
        if ($displayname != '') {
            $builder->where('displayname', $displayname);
        }
        if ($role_id != '') {
            $builder->where('user_role.role_id', $role_id);
        }
        $total = $builder->countAllResults(false);

        return [
            'data' => $builder->get()->getResultArray(),
            // 'links' => $pager->makeLinks($page, $perPage, $total, 'default_full'),
        ];
    }

    public function addUserRole($userId, $roleId)
    {
        $data = [
            'user_id' => $userId,
            'role_id' => $roleId,
        ];

        $this->db->table('user_role')->insert($data);
    }

    public function delUserRole($userId, $roleId)
    {
        $data = [
            'user_id' => $userId,
            'role_id' => $roleId,
        ];

        $this->db->table('user_role')->delete($data);
    }

    public function deleteUser($userId)
    {
        $this->update($userId, ['deleted' => 1]);
    }
}
