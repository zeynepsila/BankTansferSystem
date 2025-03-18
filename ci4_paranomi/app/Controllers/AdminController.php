<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Transfer;
use App\Models\User;

class AdminController extends ResourceController
{
    private $secretKey = 'SECRET_KEY';

    public function transfers()
    {
        $transferModel = new Transfer();
        $transfers = $transferModel->where('status', 'pending')->findAll();

        return $this->response->setJSON($transfers);
    }

    public function approveTransfer($id)
    {
        $transferModel = new Transfer();
        $userModel = new User();

        $transfer = $transferModel->find($id);

        if (!$transfer || $transfer->status !== 'pending') {
            return $this->response->setJSON(['error' => 'Geçersiz transfer işlemi.'])->setStatusCode(400);
        }

        $sender = $userModel->find($transfer->sender_id);
        $receiver = $userModel->find($transfer->receiver_id);

        if (!$sender || !$receiver) {
            return $this->response->setJSON(['error' => 'Gönderen veya alıcı bulunamadı.'])->setStatusCode(400);
        }

        if ($sender->balance < $transfer->amount) {
            return $this->response->setJSON(['error' => 'Gönderenin bakiyesi yetersiz.'])->setStatusCode(400);
        }

        $userModel->update($sender->id, ['balance' => $sender->balance - $transfer->amount]);
        $userModel->update($receiver->id, ['balance' => $receiver->balance + $transfer->amount]);

        $transferModel->update($id, ['status' => 'approved']);

        return $this->response->setJSON(['message' => 'Transfer başarıyla onaylandı.']);
    }
}
