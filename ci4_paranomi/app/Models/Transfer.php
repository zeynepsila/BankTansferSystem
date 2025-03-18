<?php

namespace App\Models;

use CodeIgniter\Model;

class Transfer extends Model
{
    protected $table = 'transfers';
    protected $primaryKey = 'id';
    protected $allowedFields = ['sender_id', 'receiver_id', 'amount', 'status', 'approved_by', 'created_at'];
}
