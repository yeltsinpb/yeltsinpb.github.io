<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Database;

final class SiteController
{
    public function home(): void
    {
        $profile = Database::fetchOne('SELECT * FROM profile WHERE id = 1');
        $skills = Database::fetchAll('SELECT * FROM skills ORDER BY sort_order, id');
        $projects = Database::fetchAll(
            'SELECT * FROM projects WHERE featured = 1 ORDER BY sort_order, id'
        );

        // Group skills by category
        $skillsByCategory = [];
        foreach ($skills as $skill) {
            $skillsByCategory[$skill['category']][] = $skill;
        }

        render('site/home', [
            'title' => $profile['name'] . ' — ' . $profile['tagline'],
            'profile' => $profile,
            'skillsByCategory' => $skillsByCategory,
            'projects' => $projects,
        ]);
    }

    public function projects(): void
    {
        $profile = Database::fetchOne('SELECT * FROM profile WHERE id = 1');
        $projects = Database::fetchAll('SELECT * FROM projects ORDER BY sort_order, id');

        render('site/projects', [
            'title' => 'Projects — ' . $profile['name'],
            'profile' => $profile,
            'projects' => $projects,
        ]);
    }

    public function project(string $slug): void
    {
        $project = Database::fetchOne('SELECT * FROM projects WHERE slug = ?', [$slug]);
        if (!$project) {
            error_page(404, 'site/404');
            return;
        }
        $profile = Database::fetchOne('SELECT * FROM profile WHERE id = 1');
        $images = Database::fetchAll(
            'SELECT * FROM project_images WHERE project_id = ? ORDER BY sort_order, id',
            [(int)$project['id']]
        );

        render('site/project', [
            'title' => $project['title'] . ' — ' . $profile['name'],
            'profile' => $profile,
            'project' => $project,
            'images' => $images,
        ]);
    }

    public function contact(): void
    {
        $profile = Database::fetchOne('SELECT * FROM profile WHERE id = 1');
        render('site/contact', [
            'title' => 'Contact — ' . $profile['name'],
            'profile' => $profile,
        ]);
    }

    public function submitContact(): void
    {
        verify_csrf();

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'subject' => trim($_POST['subject'] ?? ''),
            'body' => trim($_POST['body'] ?? ''),
        ];

        $errors = [];
        if ($data['name'] === '') $errors[] = 'Name is required.';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
        if ($data['subject'] === '') $errors[] = 'Subject is required.';
        if (strlen($data['body']) < 10) $errors[] = 'Message must be at least 10 characters.';

        if ($errors) {
            $_SESSION['_old'] = $data;
            flash('error', implode(' ', $errors));
            redirect('/contact');
        }

        Database::insert('messages', $data);
        flash('success', "Message sent. I'll get back to you soon.");
        redirect('/contact');
    }

    /** Branded error pages for Apache ErrorDocument (403/404/500). */
    public function error(string $code): void
    {
        $code = (int)$code;
        $views = [403 => 'site/403', 404 => 'site/404', 500 => 'site/500'];
        $view = $views[$code] ?? 'site/404';
        error_page(isset($views[$code]) ? $code : 404, $view);
    }

    public function upload(string $file): void
    {
        // Basic path-traversal protection
        $file = basename($file);
        $path = __DIR__ . '/../../storage/uploads/' . $file;

        if (!is_file($path)) {
            http_response_code(404);
            return;
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Cache-Control: public, max-age=86400');
        readfile($path);
    }
}
