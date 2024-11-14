<?php

namespace App\Models;

use CodeIgniter\Model;

class TimeoffModel extends Model
{
    protected $table = 'timeoff';
    protected $primaryKey = 'id';
    protected $allowedFields = ['employee_id', 'request_date', 'start_date', 'end_date', 'duration', 'reason', 'type', 'type_id', 'deleted'];
    protected $deletedField  = 'deleted';

    public function getYearList() {
        return $this->distinct()->select('YEAR(request_date) as year')->orderBy('request_date', 'ASC')->get()->getResultArray();
    }

    public function getTimeOffList($filter) {
        if ($filter['month'] != 'all') {
            $this->where('MONTH(request_date)', $filter['month']);
        }
        if ($filter['year'] != 'all') {
            $this->where('YEAR(request_date)', $filter['year']);
        }

        return $this->select("timeoff.id, employee_id, DATE_FORMAT(request_date, '%d/%m/%Y %H:%i') as request_date, DATE_FORMAT(start_date, '%d/%m/%Y %H:%i') as start_date, DATE_FORMAT(end_date, '%d/%m/%Y %H:%i') as end_date, employee.name, reason, duration")
            ->join('employee', 'timeoff.employee_id = employee.id', 'left')
            ->where('deleted', 0)->findAll();
    }

    public function getEmployeeTimeoff($userID) {
        $this->select()
            ->join('employee', 'timeoff.employee_id = employee.id', 'left')
            ->where('employee.user_id', $userID)
            ->where('deleted', 0)
            ->orderBy('request_date', 'ASC');
        return $this->get()->getResultArray();
    }
}