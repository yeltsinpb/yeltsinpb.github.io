<?php
declare(strict_types=1);

use App\Database;

/** Escape output for HTML */
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Render a view file with extracted data */
function view(string $name, array $data = []): string
{
    extract($data, EXTR_SKIP);
    ob_start();
    require __DIR__ . '/../views/' . $name . '.php';
    return ob_get_clean();
}

/** Render a view inside the layout */
function render(string $name, array $data = [], string $layout = 'layouts/main'): void
{
    $content = view($name, $data);
    $data['content'] = $content;
    echo view($layout, $data);
}

/**
 * Render a styled error page (404 / 403 / 500) inside the main layout.
 * Loads $profile so the header/footer render; falls back to safe defaults
 * if the database itself is unavailable (e.g. during a 500).
 */
function error_page(int $code, string $view): void
{
    if (!headers_sent()) {
        http_response_code($code);
    }

    $profile = null;
    try {
        $profile = App\Database::fetchOne('SELECT * FROM profile WHERE id = 1');
    } catch (\Throwable $e) {
        // DB may be the cause of the error — degrade gracefully.
    }
    if (!is_array($profile)) {
        $profile = [
            'name'     => 'Portfolio',
            'tagline'  => '',
            'location' => '',
        ];
    }

    $titles = [403 => 'Forbidden', 404 => 'Not Found', 500 => 'Server Error'];
    $label  = $titles[$code] ?? 'Error';

    render($view, [
        'title'   => $code . ' ' . $label . ' — ' . $profile['name'],
        'profile' => $profile,
        'code'    => $code,
    ]);
}

/** Base path of the app (e.g. "/portfolio/public" when in XAMPP subfolder, "" at web root) */
function base_path(): string
{
    static $base = null;
    if ($base === null) {
        $b = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        $base = ($b === '' || $b === '/' || $b === '.') ? '' : $b;
    }
    return $base;
}

/** Build an absolute (path-only) URL with the app base path prefixed */
function url(string $path = '/'): string
{
    if ($path === '' || $path[0] !== '/') $path = '/' . $path;
    return base_path() . $path;
}

/** Redirect helper — accepts app-relative paths like "/admin" and prefixes base */
function redirect(string $url): never
{
    if ($url !== '' && $url[0] === '/' && strpos($url, '//') !== 0) {
        $url = base_path() . $url;
    }
    header('Location: ' . $url);
    exit;
}

/** Old form value */
function old(string $key, $default = ''): string
{
    $val = $_SESSION['_old'][$key] ?? $default;
    return (string)$val;
}

/** Flash message */
function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }
    $msg = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $msg;
}

/** CSRF token */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['_csrf'] ?? '';
    $expected = $_SESSION['_csrf'] ?? '';
    if ($expected === '' || $token === '' || !hash_equals($expected, $token)) {
        http_response_code(419);
        die('CSRF token mismatch.');
    }
}

/** Auth */
function auth(): ?array
{
    if (empty($_SESSION['user_id'])) return null;
    static $cache = null;
    if ($cache === null) {
        $cache = Database::fetchOne('SELECT id, email, name FROM users WHERE id = ?', [$_SESSION['user_id']]);
    }
    return $cache;
}

function require_auth(): void
{
    if (!auth()) redirect('/login');
}

/** Slugify */
function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text) ?: 'item';
}

/** Format date nicely */
function fmt_date(?string $date): string
{
    if (!$date) return '';
    // If only year-month (no day), show "Month YYYY"
    if (preg_match('/^\d{4}-\d{2}$/', trim($date))) {
        return date('F Y', strtotime($date . '-01'));
    }
    return date('M j, Y', strtotime($date));
}

/** Active link helper */
function is_active(string $path): string
{
    $current = strtok($_SERVER['REQUEST_URI'], '?');
    $base = base_path();
    if ($base !== '' && strpos($current, $base) === 0) {
        $current = substr($current, strlen($base)) ?: '/';
    }
    return $current === $path ? 'active' : '';
}
