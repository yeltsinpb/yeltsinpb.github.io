<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Database;

final class AuthController
{
    public function showLogin(): void
    {
        if (auth()) redirect('/admin');
        render('site/login', ['title' => 'Admin Login'], 'layouts/auth');
    }

    public function login(): void
    {
        verify_csrf();

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = Database::fetchOne('SELECT * FROM users WHERE email = ?', [$email]);

        if (!$user || !password_verify($password, $user['password'])) {
            flash('error', 'Invalid credentials.');
            $_SESSION['_old'] = ['email' => $email];
            redirect('/login');
        }

        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];

        redirect('/admin');
    }

    public function logout(): void
    {
        verify_csrf();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        redirect('/');
    }
}
