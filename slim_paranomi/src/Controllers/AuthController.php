<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use App\Models\Transfer;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{
    private $config;
    private $secretKey = 'SECRET_KEY';

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config.php';
    }

    public function register(Request $request, Response $response)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        if (!isset($data['username']) || !isset($data['password']) || !isset($data['role'])) {
            return $this->jsonErrorResponse($response, 'Eksik alanlar var!', 400);
        }

        $user = new User();
        $result = $user->createUser($data['username'], $data['password'], $data['role']);

        if ($result) {
            return $this->jsonSuccessResponse($response, 'Kullanıcı başarıyla oluşturuldu.', 201);
        }

        return $this->jsonErrorResponse($response, 'Kayıt başarısız', 500);
    }

    public function login(Request $request, Response $response)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        if (!isset($data['username']) || !isset($data['password'])) {
            return $this->jsonErrorResponse($response, 'Eksik alanlar var!', 400);
        }

        $user = new User();
        $result = $user->authenticate($data['username'], $data['password']);

        if ($result) {
            $balance = $user->getBalanceById($result['id']);
            $token = JWT::encode([
                'id' => $result['id'],
                'role' => $result['role'],
                'exp' => time() + 3600
            ], $this->secretKey, 'HS256');

            return $this->jsonSuccessResponse($response, [
                'token' => $token,
                'user' => [
                    'id' => $result['id'],
                    'username' => $result['username'],
                    'role' => $result['role'],
                    'balance' => $balance
                ]
            ]);
        }

        return $this->jsonErrorResponse($response, 'Geçersiz kullanıcı bilgileri', 401);
    }

    public function transfer(Request $request, Response $response)
    {
        $headers = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $headers);

        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            $data = json_decode($request->getBody()->getContents(), true);

            if (!isset($data['receiver_id']) || !isset($data['amount'])) {
                return $this->jsonErrorResponse($response, 'Eksik alanlar var!', 400);
            }

            $transfer = new Transfer();
            $result = $transfer->initiateTransfer($decoded->id, $data['receiver_id'], $data['amount']);

            if ($result) {
                return $this->jsonSuccessResponse($response, 'Transfer başarıyla başlatıldı.', 201);
            }
            return $this->jsonErrorResponse($response, 'Transfer başarısız!', 500);
        } catch (\Exception $e) {
            return $this->jsonErrorResponse($response, 'Yetkilendirme hatası!', 401);
        }
    }

    private function jsonSuccessResponse(Response $response, $data, int $status = 200)
    {
        $response->getBody()->write(json_encode(['success' => true, 'data' => $data]));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    }

    private function jsonErrorResponse(Response $response, string $error, int $status = 400)
    {
        $response->getBody()->write(json_encode(['success' => false, 'error' => $error]));
        return $response->withStatus($status)->withHeader('Content-Type', 'application/json');
    }
}
