<?php
namespace App\Models;

use PDO;

class Transfer
{
    private $db;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config.php';
        $this->db = new PDO('mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['dbname'], $config['db']['user'], $config['db']['password']);
    }

    public function initiateTransfer($sender_id, $receiver_id, $amount)
    {
        $stmt = $this->db->prepare("INSERT INTO transfers (sender_id, receiver_id, amount, status) VALUES (?, ?, ?, 'pending')");
        return $stmt->execute([$sender_id, $receiver_id, $amount]);
    }

    public function approveTransfer($transfer_id, $approver_id)
    {
        $stmt = $this->db->prepare("UPDATE transfers SET status = 'approved', approved_by = ? WHERE id = ? AND status = 'pending'");
        return $stmt->execute([$approver_id, $transfer_id]);
    }
}
