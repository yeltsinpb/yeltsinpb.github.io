<?php
/**
 * Front Controller — all requests enter here.
 * Run with: php -S localhost:8000 -t public
 */

declare(strict_types=1);

session_start();

require __DIR__ . '/src/bootstrap.php';

use App\Router;
use App\Controllers\SiteController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;

// ─── Don't leak errors to visitors; show a styled 500 instead ──────────
ini_set('display_errors', '0');
error_reporting(E_ALL);

set_exception_handler(function (\Throwable $e): void {
    error_log('[portfolio] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    if (ob_get_level() > 0) {
        ob_end_clean(); // discard any half-rendered output
    }
    error_page(500, 'site/500');
});

// Fatal errors (parse/type errors) aren't caught by set_exception_handler;
// convert the shutdown into the 500 page too.
register_shutdown_function(function (): void {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        error_log('[portfolio] fatal: ' . $err['message'] . ' in ' . $err['file'] . ':' . $err['line']);
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        error_page(500, 'site/500');
    }
});

$router = new Router();

// Public site
$router->get('/',                [SiteController::class, 'home']);
$router->get('/projects',        [SiteController::class, 'projects']);
$router->get('/projects/{slug}', [SiteController::class, 'project']);
$router->get('/contact',         [SiteController::class, 'contact']);
$router->post('/contact',        [SiteController::class, 'submitContact']);

// Auth
$router->get('/login',           [AuthController::class, 'showLogin']);
$router->post('/login',          [AuthController::class, 'login']);
$router->post('/logout',         [AuthController::class, 'logout']);

// Admin (protected)
$router->get('/admin',                       [AdminController::class, 'dashboard']);
$router->get('/admin/profile',               [AdminController::class, 'editProfile']);
$router->post('/admin/profile',              [AdminController::class, 'updateProfile']);
$router->post('/admin/password',             [AdminController::class, 'updatePassword']);

$router->get('/admin/skills',                [AdminController::class, 'skills']);
$router->post('/admin/skills',               [AdminController::class, 'createSkill']);
$router->post('/admin/skills/{id}/update',   [AdminController::class, 'updateSkill']);
$router->post('/admin/skills/{id}/delete',   [AdminController::class, 'deleteSkill']);

$router->get('/admin/projects',              [AdminController::class, 'projects']);
$router->get('/admin/projects/new',          [AdminController::class, 'newProject']);
$router->post('/admin/projects',             [AdminController::class, 'createProject']);
$router->get('/admin/projects/{id}/edit',    [AdminController::class, 'editProject']);
$router->post('/admin/projects/{id}/update', [AdminController::class, 'updateProject']);
$router->post('/admin/projects/{id}/delete', [AdminController::class, 'deleteProject']);
$router->post('/admin/projects/{id}/images', [AdminController::class, 'uploadProjectImages']);
$router->post('/admin/projects/{id}/images/{imageId}/delete', [AdminController::class, 'deleteProjectImage']);

$router->get('/admin/messages',              [AdminController::class, 'messages']);
$router->post('/admin/messages/{id}/delete', [AdminController::class, 'deleteMessage']);

// Serve uploaded images
$router->get('/uploads/{file}', [SiteController::class, 'upload']);

// Branded error pages (targets for Apache ErrorDocument)
$router->get('/error/{code}', [SiteController::class, 'error']);

$router->dispatch();
