<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class DashboardController extends ResourceController
{
    private $secretKey = 'SECRET_KEY';

    public function index()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $this->failUnauthorized('Yetkilendirme hatası! JWT eksik.');
        }

        try {
            $decoded = JWT::decode($matches[1], new Key($this->secretKey, 'HS256'));
        } catch (\Exception $e) {
            return $this->failUnauthorized('Geçersiz token!');
        }

        $userModel = new User();
        $user = $userModel->find($decoded->id);

        if (!$user) {
            return $this->failNotFound('Kullanıcı bulunamadı.');
        }

        return $this->respond([
            'id' => $user->id,
            'username' => $user->username,
            'balance' => $user->balance
        ]);
    }
}
