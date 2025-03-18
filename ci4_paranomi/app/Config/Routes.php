<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setAutoRoute(true);

# -----------------------------------------------
# 📌 Ana Sayfa → Giriş Sayfasına Yönlendirme
# -----------------------------------------------
$routes->get('/', 'AuthController::login');

# -----------------------------------------------
# 📌 Kullanıcı Kimlik Doğrulama İşlemleri
# -----------------------------------------------
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::loginPost');
$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::registerPost');
$routes->get('/logout', 'AuthController::logout');

# -----------------------------------------------
# 📌 Kullanıcı Dashboard ve Transfer İşlemleri
# -----------------------------------------------
$routes->get('/dashboard', 'DashboardController::index', ['filter' => 'jwtAuth']);
$routes->post('/transfer', 'TransferController::transferMoney', ['filter' => 'jwtAuth']);

# -----------------------------------------------
# 📌 Admin Paneli (Havale Onay İşlemleri)
# -----------------------------------------------
$routes->get('/admin/transfers', 'AdminController::transfers', ['filter' => 'adminAuth']);  
$routes->post('/admin/transfer/approve/(:num)', 'AdminController::approveTransfer/$1', ['filter' => 'adminAuth']);  
$routes->post('/admin/approveTransfer/(:num)', 'AdminController::approveTransfer/$1', ['filter' => 'adminAuth']); 

# -----------------------------------------------
# 📌 Slim API ile Entegrasyon İçin Ekstra Rotalar
# -----------------------------------------------
$routes->post('/api/auth/login', 'AuthController::apiLogin');
$routes->get('/api/user/balance', 'UserController::getBalance', ['filter' => 'jwtAuth']); 

