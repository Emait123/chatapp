<?php

namespace App\Models;

use CodeIgniter\Model;

class ToolModel extends Model
{
    protected $table = 'tool';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'img_name', 'msv'];

    public function getDS() {
        $ds = $this->findAll();
        $result = [];
        foreach($ds as $value) {
            $result[$value['img_name']] = $value['msv'];
        }
        return $result;
    }
}