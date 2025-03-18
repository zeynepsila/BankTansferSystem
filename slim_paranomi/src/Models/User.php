<?php
namespace App\Models;

use PDO;

class User
{
    private $db;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config.php';
        $this->db = new PDO(
            'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['dbname'], 
            $config['db']['user'], 
            $config['db']['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] 
        );
    }

    public function createUser($username, $password, $role)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $result = $stmt->execute([$username, $hashedPassword, $role]);

            if (!$result) {
                error_log("❌ Kullanıcı kaydı başarısız! Hata: " . implode(" | ", $stmt->errorInfo()));
            } else {
                error_log("✅ Kullanıcı başarıyla kaydedildi!");
            }

            return $result;
        } catch (\PDOException $e) {
            error_log("❌ Veritabanı Hatası: " . $e->getMessage());
            return false;
        }
    }

    public function authenticate($username, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }
    public function getBalanceById($userId)
{
    $query = $this->db->prepare("SELECT balance FROM users WHERE id = ?");
    $query->execute([$userId]);
    $result = $query->fetch();

    return $result ? $result['balance'] : 0; 
}


    public function getDbError()
    {
        return $this->db->errorInfo();
    }
}
