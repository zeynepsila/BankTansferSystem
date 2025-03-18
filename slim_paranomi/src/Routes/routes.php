<?php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log'); // Hataları log dosyasına yaz

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\AuthController;
use App\Controllers\TransferController;
use App\Middlewares\AuthMiddleware;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;
use Exception;

$app->group('/api', function (RouteCollectorProxy $group) {
    // Kullanıcı Kaydı
    $group->post('/register', function ($request, $response) {
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);

        // Gelen JSON'u loga yaz (HAM JSON ve Slim'in işlediği JSON)
        error_log("========== Yeni İstek ==========");
        error_log("API'ye Gelen HAM JSON: " . $body);
        error_log("API'ye Slim'in işlediği JSON:");
        error_log(print_r($data, true));

        // Eğer JSON eksik veya hatalıysa, hata döndür
        if (!$data || !isset($data['username']) || !isset($data['password']) || !isset($data['role'])) {
            error_log("❌ Eksik veya hatalı veri geldi!");
            return $response->withJson([
                "error" => "Eksik veya hatalı veri geldi",
                "gelen_veri" => $data
            ], 400);
        }

        // AuthController çağrılıyor
        error_log("✅ Kullanıcı AuthController->register() fonksiyonuna yönlendiriliyor.");
        return (new AuthController())->register($request, $response);
    });

    // Kullanıcı Girişi
    $group->post('/login', [AuthController::class, 'login']);

    // **✅ Kullanıcı Bilgilerini Döndüren Yeni API Endpoint**
    $group->get('/user', function ($request, $response) {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $response->withJson(['error' => 'Yetkilendirme başarısız'], 401);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key('GİZLİ_ANAHTARIN', 'HS256'));

            // Veritabanı bağlantısı
            $db = new PDO("mysql:host=localhost;dbname=ci4_blog;charset=utf8", "root", "");

            $stmt = $db->prepare("SELECT id, username, role FROM users WHERE id = ?");
            $stmt->execute([$decoded->id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                return $response->withJson($user);
            } else {
                return $response->withJson(['error' => 'Kullanıcı bulunamadı'], 404);
            }
        } catch (Exception $e) {
            return $response->withJson(['error' => 'Geçersiz token: ' . $e->getMessage()], 401);
        }
    });

    // Transfer İşlemleri (Auth Middleware ile)
    $group->group('/transfer', function (RouteCollectorProxy $group) {
        $group->post('', [TransferController::class, 'createTransfer']);
        $group->post('/approve', [TransferController::class, 'approveTransfer']);
    })->add(new AuthMiddleware());
});
