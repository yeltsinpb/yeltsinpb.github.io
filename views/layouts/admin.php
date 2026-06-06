<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title) ?> · Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=JetBrains+Mono:wght@300;400;500&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= url('/assets/style.css') ?>">
<link rel="stylesheet" href="<?= url('/assets/admin.css') ?>">
</head>
<body class="admin-body">
<div class="grain grain--subtle"></div>

<aside class="admin-sidebar">
    <a href="<?= url('/') ?>" class="admin-brand">
        <span class="admin-brand__mark">✦</span>
        <div>
            <div class="admin-brand__name">Studio</div>
            <div class="admin-brand__sub">Admin Console</div>
        </div>
    </a>

    <nav class="admin-nav">
        <a href="<?= url('/admin') ?>"          class="admin-nav__link <?= is_active('/admin') ?>"><span>◇</span> Dashboard</a>
        <a href="<?= url('/admin/profile') ?>"  class="admin-nav__link <?= is_active('/admin/profile') ?>"><span>◇</span> Profile</a>
        <a href="<?= url('/admin/skills') ?>"   class="admin-nav__link <?= is_active('/admin/skills') ?>"><span>◇</span> Skills</a>
        <a href="<?= url('/admin/projects') ?>" class="admin-nav__link <?= is_active('/admin/projects') ?>"><span>◇</span> Projects</a>
        <a href="<?= url('/admin/messages') ?>" class="admin-nav__link <?= is_active('/admin/messages') ?>"><span>◇</span> Messages</a>
    </nav>

    <form method="post" action="<?= url('/logout') ?>" class="admin-logout">
        <?= csrf_field() ?>
        <button type="submit">Sign out ↩</button>
    </form>
    <div class="admin-user">
        Signed in as <strong><?= e(auth()['email']) ?></strong>
    </div>
</aside>

<main class="admin-main">
    <header class="admin-topbar">
        <h1><?= e($title) ?></h1>
        <a href="<?= url('/') ?>" target="_blank" rel="noopener" class="admin-view-site">View site ↗</a>
    </header>

    <?php if ($msg = flash('success')): ?>
        <div class="alert alert--ok"><?= e($msg) ?></div>
    <?php endif; ?>
    <?php if ($msg = flash('error')): ?>
        <div class="alert alert--err"><?= e($msg) ?></div>
    <?php endif; ?>

    <?= $content ?>
</main>
</body>
</html>
