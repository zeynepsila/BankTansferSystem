<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;
use App\Filters\JwtAuth;

class Filters extends BaseConfig
{
    /**
     * Filtreler için takma adlar (alias'lar).
     * 
     * Kullanımı kolaylaştırmak için filtreleri burada tanımlıyoruz.
     *
     * @var array<string, class-string|list<class-string>>
     */
    public array $aliases = [
        'csrf'          => CSRF::class,             // CSRF koruması
        'toolbar'       => DebugToolbar::class,     // CodeIgniter Debug Toolbar
        'honeypot'      => Honeypot::class,         // Bot saldırılarını önleme
        'invalidchars'  => InvalidChars::class,     // Geçersiz karakter filtresi
        'secureheaders' => SecureHeaders::class,    // Güvenli başlıklar filtresi
        'cors'          => Cors::class,             // CORS yönetimi
        'forcehttps'    => ForceHTTPS::class,       // HTTPS zorunluluğu
        'pagecache'     => PageCache::class,        // Sayfa önbellekleme
        'performance'   => PerformanceMetrics::class, // Performans ölçümü
        'jwtAuth'       => JwtAuth::class,          // JWT Authentication Filtresi
    ];

    /**
     * Özel olarak **her istekten önce ve sonra** uygulanması gereken filtreler.
     *
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [
            'forcehttps', // HTTP yerine HTTPS kullanımını zorunlu tutar.
        ],
        'after' => [
            'performance', // Performans ölçümünü etkinleştirir.
            'toolbar',     // CI Debug Toolbar'ı devreye alır.
        ],
    ];

    /**
     * Tüm isteklerden önce ve sonra uygulanacak küresel filtreler.
     * 
     * Bu filtreler her istekte uygulanır, ancak özel yollar için hariç tutulabilir.
     *
     * @var array<string, list<string>>
     */
    public array $globals = [
        'before' => [
            // 'honeypot', // Opsiyonel: Form güvenliği için kullanılabilir.
            // 'csrf', // CSRF filtresi varsayılan olarak devre dışı.
            // 'invalidchars',
        ],
        'after' => [
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * **Belirli HTTP metodlarına özel filtreler.**
     *
     * Örneğin, `POST`, `PUT`, `DELETE` istekleri için JWT doğrulaması zorunlu.
     *
     * @var array<string, list<string>>
     */
    public array $methods = [
        'post'    => ['jwtAuth'], // POST isteklerinde JWT zorunlu
        'put'     => ['jwtAuth'], // PUT isteklerinde JWT zorunlu
        'delete'  => ['jwtAuth'], // DELETE isteklerinde JWT zorunlu
    ];

    /**
     * Belirli URI desenleri için **öncesinde veya sonrasında** çalışacak filtreler.
     * 
     * `transfer` işlemleri JWT doğrulaması gerektiriyor.
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [
        'jwtAuth' => [
            'before' => [
                'transfer/*',  // Tüm transfer işlemlerini JWT ile koru.
                'admin/*',     // Yönetici işlemlerini JWT ile koru.
            ],
        ],
    ];
}
