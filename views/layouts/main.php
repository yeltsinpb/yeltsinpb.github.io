<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title ?? $profile['name']) ?></title>
<meta name="description" content="<?= e($profile['tagline']) ?>">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=JetBrains+Mono:wght@300;400;500;600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="<?= url('/assets/style.css') ?>">
</head>
<body>
<div class="grain"></div>
<div class="aurora">
    <div class="aurora__blob aurora__blob--1"></div>
    <div class="aurora__blob aurora__blob--2"></div>
    <div class="aurora__blob aurora__blob--3"></div>
</div>

<header class="site-header">
    <a href="<?= url('/') ?>" class="logo">
        <span class="logo__mark">✦</span>
        <span class="logo__text"><?= e($profile['name']) ?></span>
    </a>
    <nav class="nav">
        <a href="<?= url('/') ?>" class="nav__link <?= is_active('/') ?>"><span>01</span> Home</a>
        <a href="<?= url('/projects') ?>" class="nav__link <?= is_active('/projects') ?>"><span>02</span> Work</a>
        <a href="<?= url('/contact') ?>" class="nav__link <?= is_active('/contact') ?>"><span>03</span> Contact</a>
    </nav>
    <?php if (auth()): ?>
        <a href="<?= url('/admin') ?>" class="admin-pill">Admin →</a>
    <?php else: ?>
        <!-- <a href="<?= url('/login') ?>" class="admin-pill admin-pill--ghost">↗</a> -->
    <?php endif; ?>
</header>

<main>
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="footer__line">
        <span>© <?= date('Y') ?> <?= e($profile['name']) ?></span>
        <span class="footer__dot">·</span>
        <span><?= e($profile['location']) ?></span>
    </div>
    <div class="footer__socials">
        <?php foreach ([
            'github_url' => 'GitHub',
            'linkedin_url' => 'LinkedIn',
            'dribbble_url' => 'Dribbble',
        ] as $key => $label): ?>
            <?php if (!empty($profile[$key])): ?>
                <a href="<?= e($profile[$key]) ?>" target="_blank" rel="noopener"><?= $label ?> ↗</a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</footer>

<script src="<?= url('/assets/site.js') ?>" defer></script>
</body>
</html>
