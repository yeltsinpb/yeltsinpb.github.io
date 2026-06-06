<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Database;

final class AdminController
{
    public function __construct()
    {
        require_auth();
    }

    public function dashboard(): void
    {
        $stats = [
            'projects' => (int)Database::fetchOne('SELECT COUNT(*) c FROM projects')['c'],
            'skills'   => (int)Database::fetchOne('SELECT COUNT(*) c FROM skills')['c'],
            'messages' => (int)Database::fetchOne('SELECT COUNT(*) c FROM messages')['c'],
            'unread'   => (int)Database::fetchOne('SELECT COUNT(*) c FROM messages WHERE read_at IS NULL')['c'],
        ];
        $recent = Database::fetchAll('SELECT * FROM messages ORDER BY created_at DESC LIMIT 5');

        render('admin/dashboard', [
            'title' => 'Dashboard',
            'stats' => $stats,
            'recent' => $recent,
        ], 'layouts/admin');
    }

    // ─── Profile ─────────────────────────────────────────────────────
    public function editProfile(): void
    {
        $profile = Database::fetchOne('SELECT * FROM profile WHERE id = 1');
        $user    = Database::fetchOne('SELECT id, email FROM users WHERE id = ?', [$_SESSION['user_id']]);
        render('admin/profile', [
            'title'   => 'Edit Profile',
            'profile' => $profile,
            'user'    => $user,
        ], 'layouts/admin');
    }

    public function updateProfile(): void
    {
        verify_csrf();

        // ── Account email ────────────────────────────────────────────
        $newEmail  = trim($_POST['account_email'] ?? '');
        $curUser   = Database::fetchOne('SELECT id, email FROM users WHERE id = ?', [$_SESSION['user_id']]);

        if ($newEmail !== $curUser['email']) {
            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                flash('error', 'Please enter a valid email address.');
                $_SESSION['_old']['account_email'] = $newEmail;
                redirect('/admin/profile');
            }
            $taken = Database::fetchOne('SELECT id FROM users WHERE email = ? AND id != ?',
                [$newEmail, $curUser['id']]);
            if ($taken) {
                flash('error', 'That email address is already in use.');
                $_SESSION['_old']['account_email'] = $newEmail;
                redirect('/admin/profile');
            }
            Database::update('users', ['email' => $newEmail], 'id = ?', [$curUser['id']]);
        }
        unset($_SESSION['_old']['account_email']);

        // ── Profile table ────────────────────────────────────────────
        $fields = ['name', 'tagline', 'bio', 'email', 'location',
                   'github_url', 'linkedin_url', 'twitter_url', 'dribbble_url'];
        $data = [];
        foreach ($fields as $f) $data[$f] = trim($_POST[$f] ?? '');
        $data['updated_at'] = date('Y-m-d H:i:s');

        if (!empty($_FILES['avatar']['tmp_name'])) {
            $filename = $this->saveUpload($_FILES['avatar']);
            if ($filename) $data['avatar'] = $filename;
        }

        Database::update('profile', $data, 'id = ?', [1]);
        flash('success', 'Profile updated.');
        redirect('/admin/profile');
    }

    public function updatePassword(): void
    {
        verify_csrf();

        $user    = Database::fetchOne('SELECT id, password FROM users WHERE id = ?', [$_SESSION['user_id']]);
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!password_verify($current, $user['password'])) {
            flash('error', 'The password you entered is incorrect.');
            redirect('/admin/profile');
        }

        if (strlen($new) < 8) {
            flash('error', 'New password must be at least 8 characters.');
            redirect('/admin/profile');
        }

        if ($new !== $confirm) {
            flash('error', 'New passwords do not match.');
            redirect('/admin/profile');
        }

        Database::update('users', ['password' => password_hash($new, PASSWORD_BCRYPT)], 'id = ?', [$user['id']]);
        session_regenerate_id(true);
        flash('success', 'Password updated successfully.');
        redirect('/admin/profile');
    }

    // ─── Skills ──────────────────────────────────────────────────────
    public function skills(): void
    {
        $skills = Database::fetchAll('SELECT * FROM skills ORDER BY sort_order, id');
        render('admin/skills', [
            'title' => 'Manage Skills',
            'skills' => $skills,
        ], 'layouts/admin');
    }

    public function createSkill(): void
    {
        verify_csrf();
        Database::insert('skills', [
            'name' => trim($_POST['name'] ?? ''),
            'category' => trim($_POST['category'] ?? 'Other'),
            'proficiency' => (int)($_POST['proficiency'] ?? 80),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ]);
        flash('success', 'Skill added.');
        redirect('/admin/skills');
    }

    public function updateSkill(string $id): void
    {
        verify_csrf();
        Database::update('skills', [
            'name' => trim($_POST['name'] ?? ''),
            'category' => trim($_POST['category'] ?? 'Other'),
            'proficiency' => (int)($_POST['proficiency'] ?? 80),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
        ], 'id = ?', [(int)$id]);
        flash('success', 'Skill updated.');
        redirect('/admin/skills');
    }

    public function deleteSkill(string $id): void
    {
        verify_csrf();
        Database::delete('skills', 'id = ?', [(int)$id]);
        flash('success', 'Skill deleted.');
        redirect('/admin/skills');
    }

    // ─── Projects ────────────────────────────────────────────────────
    public function projects(): void
    {
        $projects = Database::fetchAll('SELECT * FROM projects ORDER BY sort_order, id');
        render('admin/projects', [
            'title' => 'Manage Projects',
            'projects' => $projects,
        ], 'layouts/admin');
    }

    public function newProject(): void
    {
        render('admin/project_form', [
            'title' => 'New Project',
            'project' => null,
        ], 'layouts/admin');
    }

    public function createProject(): void
    {
        verify_csrf();
        $data = $this->projectFormData();
        $data['slug'] = $this->uniqueSlug($data['title']);

        if (!empty($_FILES['cover_image']['tmp_name'])) {
            $f = $this->saveUpload($_FILES['cover_image']);
            if ($f) $data['cover_image'] = $f;
        }

        Database::insert('projects', $data);
        flash('success', 'Project created.');
        redirect('/admin/projects');
    }

    public function editProject(string $id): void
    {
        $project = Database::fetchOne('SELECT * FROM projects WHERE id = ?', [(int)$id]);
        if (!$project) { http_response_code(404); return; }

        $images = Database::fetchAll(
            'SELECT * FROM project_images WHERE project_id = ? ORDER BY sort_order, id',
            [(int)$id]
        );

        render('admin/project_form', [
            'title' => 'Edit: ' . $project['title'],
            'project' => $project,
            'images' => $images,
        ], 'layouts/admin');
    }

    public function updateProject(string $id): void
    {
        verify_csrf();
        $data = $this->projectFormData();

        $existing = Database::fetchOne('SELECT slug FROM projects WHERE id = ?', [(int)$id]);
        if ($existing && slugify($data['title']) !== $existing['slug']) {
            $data['slug'] = $this->uniqueSlug($data['title'], (int)$id);
        }

        if (!empty($_FILES['cover_image']['tmp_name'])) {
            $f = $this->saveUpload($_FILES['cover_image']);
            if ($f) $data['cover_image'] = $f;
        }

        Database::update('projects', $data, 'id = ?', [(int)$id]);
        flash('success', 'Project updated.');
        redirect('/admin/projects/' . (int)$id . '/edit');
    }

    public function deleteProject(string $id): void
    {
        verify_csrf();
        // Clean up gallery images on disk too
        $images = Database::fetchAll('SELECT filename FROM project_images WHERE project_id = ?', [(int)$id]);
        foreach ($images as $img) {
            $path = __DIR__ . '/../../storage/uploads/' . $img['filename'];
            if (is_file($path)) @unlink($path);
        }
        Database::delete('project_images', 'project_id = ?', [(int)$id]);
        Database::delete('projects', 'id = ?', [(int)$id]);
        flash('success', 'Project deleted.');
        redirect('/admin/projects');
    }

    public function uploadProjectImages(string $id): void
    {
        verify_csrf();
        $projectId = (int)$id;
        $project = Database::fetchOne('SELECT id FROM projects WHERE id = ?', [$projectId]);
        if (!$project) { http_response_code(404); return; }

        if (empty($_FILES['images']['tmp_name']) || !is_array($_FILES['images']['tmp_name'])) {
            flash('error', 'No images uploaded.');
            redirect('/admin/projects/' . $projectId . '/edit');
        }

        $maxOrder = Database::fetchOne(
            'SELECT COALESCE(MAX(sort_order), 0) AS m FROM project_images WHERE project_id = ?',
            [$projectId]
        );
        $order = (int)($maxOrder['m'] ?? 0);

        $count = count($_FILES['images']['tmp_name']);
        $added = 0;
        for ($i = 0; $i < $count; $i++) {
            if (empty($_FILES['images']['tmp_name'][$i])) continue;
            $file = [
                'name'     => $_FILES['images']['name'][$i],
                'type'     => $_FILES['images']['type'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error'    => $_FILES['images']['error'][$i],
                'size'     => $_FILES['images']['size'][$i],
            ];
            $saved = $this->saveUpload($file);
            if ($saved) {
                $order++;
                Database::insert('project_images', [
                    'project_id' => $projectId,
                    'filename'   => $saved,
                    'sort_order' => $order,
                ]);
                $added++;
            }
        }

        flash('success', $added . ' image' . ($added === 1 ? '' : 's') . ' added to gallery.');
        redirect('/admin/projects/' . $projectId . '/edit');
    }

    public function deleteProjectImage(string $id, string $imageId): void
    {
        verify_csrf();
        $img = Database::fetchOne(
            'SELECT * FROM project_images WHERE id = ? AND project_id = ?',
            [(int)$imageId, (int)$id]
        );
        if ($img) {
            $path = __DIR__ . '/../../storage/uploads/' . $img['filename'];
            if (is_file($path)) @unlink($path);
            Database::delete('project_images', 'id = ?', [(int)$imageId]);
            flash('success', 'Image removed.');
        }
        redirect('/admin/projects/' . (int)$id . '/edit');
    }

    // ─── Messages ────────────────────────────────────────────────────
    public function messages(): void
    {
        $messages = Database::fetchAll('SELECT * FROM messages ORDER BY created_at DESC');
        // Mark all as read
        Database::query('UPDATE messages SET read_at = ? WHERE read_at IS NULL',
            [date('Y-m-d H:i:s')]);

        render('admin/messages', [
            'title' => 'Messages',
            'messages' => $messages,
        ], 'layouts/admin');
    }

    public function deleteMessage(string $id): void
    {
        verify_csrf();
        Database::delete('messages', 'id = ?', [(int)$id]);
        flash('success', 'Message deleted.');
        redirect('/admin/messages');
    }

    // ─── Helpers ─────────────────────────────────────────────────────
    private function projectFormData(): array
    {
        return [
            'title' => trim($_POST['title'] ?? ''),
            'summary' => trim($_POST['summary'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'tech_stack' => trim($_POST['tech_stack'] ?? ''),
            'live_url' => trim($_POST['live_url'] ?? '') ?: null,
            'repo_url' => trim($_POST['repo_url'] ?? '') ?: null,
            'featured' => isset($_POST['featured']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'completed_on' => trim($_POST['completed_on'] ?? '') ?: null,
        ];
    }

    private function uniqueSlug(string $title, ?int $exceptId = null): string
    {
        $base = slugify($title);
        $slug = $base;
        $i = 2;
        while (true) {
            $sql = 'SELECT id FROM projects WHERE slug = ?';
            $params = [$slug];
            if ($exceptId) { $sql .= ' AND id != ?'; $params[] = $exceptId; }
            if (!Database::fetchOne($sql, $params)) return $slug;
            $slug = $base . '-' . $i++;
        }
    }

    private function saveUpload(array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) return null;
        if ($file['size'] > 5 * 1024 * 1024) return null;

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $allowed, true)) return null;

        $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png',
                'image/webp' => 'webp', 'image/gif' => 'gif'][$mime];
        $name = bin2hex(random_bytes(8)) . '.' . $ext;
        $dest = __DIR__ . '/../../storage/uploads/' . $name;

        if (!is_dir(dirname($dest))) mkdir(dirname($dest), 0775, true);
        if (!move_uploaded_file($file['tmp_name'], $dest)) return null;

        return $name;
    }
}
