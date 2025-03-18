<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class AuthController extends ResourceController
{
    private $secretKey = 'SECRET_KEY';

    public function login()
    {
        return view('login'); // Login sayfasını yükle
    }

    public function register()
    {
        return view('register'); // Kayıt sayfasını yükle
    }
    public function loginPost()
    {
        $userModel = new User();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $userModel->where('username', $username)->first();

        if (!$user || !password_verify($password, $user->password)) {
            return $this->response->setJSON(['error' => 'Geçersiz kullanıcı adı veya şifre.'])->setStatusCode(401);
        }

        $tokenData = [
            "id" => $user->id,
            "username" => $user->username,
            "role" => $user->role,
            "exp" => time() + 3600
        ];

        $token = JWT::encode($tokenData, $this->secretKey, 'HS256');

        return $this->response->setJSON([
            'message' => 'Giriş başarılı!',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'balance' => $user->balance
            ]
        ]);
    }

    public function registerPost()
    {
        $userModel = new User();
        $data = [
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role') ?? 'user',
            'balance' => 1000
        ];

        if ($userModel->insert($data)) {
            return $this->response->setJSON(['message' => 'Kullanıcı başarıyla oluşturuldu.'])->setStatusCode(201);
        }

        return $this->response->setJSON(['error' => 'Kayıt sırasında hata oluştu.'])->setStatusCode(500);
    }

    public function logout()
    {
        return $this->response->setJSON(['message' => 'Çıkış başarılı, client tarafında token temizlenmeli.']);
    }

    
}

