<div class="stat-grid">
    <div class="stat">
        <div class="stat__label">Projects</div>
        <div class="stat__value"><?= $stats['projects'] ?></div>
        <a href="<?= url('/admin/projects') ?>" class="stat__link">Manage →</a>
    </div>
    <div class="stat">
        <div class="stat__label">Skills</div>
        <div class="stat__value"><?= $stats['skills'] ?></div>
        <a href="<?= url('/admin/skills') ?>" class="stat__link">Manage →</a>
    </div>
    <div class="stat">
        <div class="stat__label">Messages</div>
        <div class="stat__value"><?= $stats['messages'] ?></div>
        <a href="<?= url('/admin/messages') ?>" class="stat__link">View →</a>
    </div>
    <div class="stat <?= $stats['unread'] > 0 ? 'stat--alert' : '' ?>">
        <div class="stat__label">Unread</div>
        <div class="stat__value"><?= $stats['unread'] ?></div>
        <a href="<?= url('/admin/messages') ?>" class="stat__link"><?= $stats['unread'] > 0 ? 'Read now →' : 'All caught up' ?></a>
    </div>
</div>

<section class="card">
    <header class="card__head">
        <h2>Recent messages</h2>
        <a href="<?= url('/admin/messages') ?>" class="link">View all →</a>
    </header>
    <?php if (empty($recent)): ?>
        <p class="empty">No messages yet.</p>
    <?php else: ?>
        <ul class="message-list">
            <?php foreach ($recent as $m): ?>
                <li>
                    <div class="message-list__head">
                        <strong><?= e($m['name']) ?></strong>
                        <span class="muted"><?= e($m['email']) ?></span>
                        <span class="muted muted--right"><?= fmt_date($m['created_at']) ?></span>
                    </div>
                    <div class="message-list__subj"><?= e($m['subject']) ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<section class="card">
    <header class="card__head">
        <h2>Quick actions</h2>
    </header>
    <div class="quick-actions">
        <a href="<?= url('/admin/projects/new') ?>" class="quick-action">
            <span>+</span>
            <div>New project</div>
        </a>
        <a href="<?= url('/admin/skills') ?>" class="quick-action">
            <span>✎</span>
            <div>Edit skills</div>
        </a>
        <a href="<?= url('/admin/profile') ?>" class="quick-action">
            <span>◉</span>
            <div>Update profile</div>
        </a>
        <a href="<?= url('/') ?>" target="_blank" rel="noopener" class="quick-action">
            <span>↗</span>
            <div>View live site</div>
        </a>
    </div>
</section>
