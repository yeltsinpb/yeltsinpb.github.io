<section class="page-head">
    <span class="page-head__index">— Index 03</span>
    <h1 class="page-head__title">Let's <em>build</em> something</h1>
    <p class="page-head__sub">Drop a note. I read every message and reply within a day or two.</p>
</section>

<div class="contact-grid">
    <div class="contact-info">
        <div class="contact-info__block">
            <h3>Email</h3>
            <a href="mailto:<?= e($profile['email']) ?>"><?= e($profile['email']) ?></a>
        </div>
        <div class="contact-info__block">
            <h3>Based in</h3>
            <p><?= e($profile['location']) ?></p>
        </div>
        <div class="contact-info__block">
            <h3>Elsewhere</h3>
            <ul>
                <?php foreach ([
                    'github_url' => 'GitHub',
                    'linkedin_url' => 'LinkedIn',
                    'dribbble_url' => 'Portfolio',
                ] as $k => $label): ?>
                    <?php if (!empty($profile[$k])): ?>
                        <li><a href="<?= e($profile[$k]) ?>" target="_blank" rel="noopener"><?= $label ?> ↗</a></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <form method="post" action="<?= url('/contact') ?>" class="contact-form">
        <?= csrf_field() ?>

        <?php if ($msg = flash('success')): ?>
            <div class="alert alert--ok"><?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
            <div class="alert alert--err"><?= e($msg) ?></div>
        <?php endif; ?>

        <div class="field">
            <label for="name">Your name</label>
            <input id="name" name="name" type="text" value="<?= e(old('name')) ?>" required>
        </div>
        <div class="field">
            <label for="email">Your email</label>
            <input id="email" name="email" type="email" value="<?= e(old('email')) ?>" required>
        </div>
        <div class="field">
            <label for="subject">Subject</label>
            <input id="subject" name="subject" type="text" value="<?= e(old('subject')) ?>" required>
        </div>
        <div class="field">
            <label for="body">Message</label>
            <textarea id="body" name="body" rows="6" required><?= e(old('body')) ?></textarea>
        </div>
        <button type="submit" class="cta">
            <span>Send message</span>
            <span class="cta__arrow">→</span>
        </button>
    </form>
</div>
<?php unset($_SESSION['_old']); ?>
