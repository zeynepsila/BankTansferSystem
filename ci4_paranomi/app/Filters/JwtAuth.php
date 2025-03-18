<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtAuth implements FilterInterface
{
    private $secretKey = 'SECRET_KEY'; // Aynı secret key

    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');

        if (!$header || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return service('response')->setJSON(['error' => 'Yetkilendirme başarısız! Token gerekli.'])->setStatusCode(401);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            $request->user = $decoded; // Kullanıcı bilgilerini isteğe ekle
        } catch (Exception $e) {
            return service('response')->setJSON(['error' => 'Geçersiz token!'])->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Burada bir işlem yapmaya gerek yok.
    }
}
