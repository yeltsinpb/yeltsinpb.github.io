<div class="auth-wrap">
    <div class="auth-card">
        <a href="<?= url('/') ?>" class="auth-back">← Back to site</a>
        <div class="auth-mark">✦</div>
        <h1 class="auth-title">Admin <em>access</em></h1>
        <p class="auth-sub">Sign in to manage your portfolio.</p>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert--err"><?= e($msg) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= url('/login') ?>">
            <?= csrf_field() ?>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="<?= e(old('email')) ?>" autofocus required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
            </div>
            <button type="submit" class="cta cta--full">
                <span>Sign in</span>
                <span class="cta__arrow">→</span>
            </button>
        </form>
    </div>
</div>
<?php unset($_SESSION['_old']); ?>
