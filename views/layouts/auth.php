<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title ?? 'Login') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=JetBrains+Mono:wght@300;400;500&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= url('/assets/style.css') ?>">
</head>
<body class="auth-body">
<div class="grain"></div>
<div class="aurora">
    <div class="aurora__blob aurora__blob--1"></div>
    <div class="aurora__blob aurora__blob--2"></div>
    <div class="aurora__blob aurora__blob--3"></div>
</div>
<?= $content ?>
</body>
</html>
