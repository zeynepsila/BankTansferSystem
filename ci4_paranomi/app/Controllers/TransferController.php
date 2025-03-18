<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use App\Models\Transfer;

class TransferController extends ResourceController
{
    private $secretKey = 'SECRET_KEY';

    public function transferMoney()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $this->response->setJSON(['error' => 'Yetkilendirme hatası! JWT eksik.'])->setStatusCode(401);
        }

        try {
            $decoded = JWT::decode($matches[1], new Key($this->secretKey, 'HS256'));
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Geçersiz token!'])->setStatusCode(401);
        }

        $transferModel = new Transfer();
        $userModel = new User();

        $sender = $userModel->find($decoded->id);
        $receiverId = $this->request->getPost('receiver_id');
        $amount = (float) $this->request->getPost('amount');

        if (!$sender || !$receiverId || $amount <= 0) {
            return $this->response->setJSON(['error' => 'Eksik veya hatalı bilgiler!'])->setStatusCode(400);
        }

        if ($sender->balance < $amount) {
            return $this->response->setJSON(['error' => 'Yetersiz bakiye!'])->setStatusCode(400);
        }

        $userModel->update($sender->id, ['balance' => $sender->balance - $amount]);
        $userModel->update($receiverId, ['balance' => $userModel->find($receiverId)->balance + $amount]);

        $transferModel->insert([
            'sender_id' => $sender->id,
            'receiver_id' => $receiverId,
            'amount' => $amount,
            'status' => 'approved'
        ]);

        return $this->response->setJSON(['message' => 'Transfer başarılı!'])->setStatusCode(201);
    }
}
