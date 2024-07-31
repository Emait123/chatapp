<?php

namespace App\Models;

use CodeIgniter\Model;

class TimeoffModel extends Model
{
    protected $table = 'timeoff';
    protected $primaryKey = 'id';
    protected $allowedFields = ['employee_id', 'request_date', 'start_date', 'end_date', 'duration', 'reason', 'type_id'];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
}