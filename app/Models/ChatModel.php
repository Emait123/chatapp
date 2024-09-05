<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatModel extends Model
{
    protected $table = 'chat_history';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tel_user_id', 'add_date', 'message', 'type', 'deleted'];
    protected $returnType     = 'array';
    protected $deletedField  = 'deleted';

    public function getChatContext($tel_userID, $limit) {
        $today = date("Y-m-d H:i:s", strtotime("today midnight"));

        $chatList = $this->where('tel_user_id', $tel_userID)
            ->where("add_date >=", $today)
            ->where('deleted', 0)
            ->orderBy('add_date', 'ASC')->findAll(limit:$limit);
        $context = '';
        foreach ($chatList as $v) {
            $context .= $v['type'].':'.$v['message'].';';
        }
        return $context;
    }
}