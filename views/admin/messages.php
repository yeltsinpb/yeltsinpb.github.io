<section class="card">
    <header class="card__head">
        <h2>Inbox (<?= count($messages) ?>)</h2>
    </header>

    <?php if (empty($messages)): ?>
        <p class="empty">No messages yet.</p>
    <?php else: ?>
        <div class="messages">
            <?php foreach ($messages as $m): ?>
                <details class="message">
                    <summary>
                        <div class="message__from">
                            <strong><?= e($m['name']) ?></strong>
                            <span class="muted"><?= e($m['email']) ?></span>
                        </div>
                        <div class="message__subj"><?= e($m['subject']) ?></div>
                        <div class="message__date muted"><?= fmt_date($m['created_at']) ?></div>
                    </summary>
                    <div class="message__body">
                        <p><?= nl2br(e($m['body'])) ?></p>
                        <div class="message__actions">
                            <a href="mailto:<?= e($m['email']) ?>?subject=Re: <?= e($m['subject']) ?>" class="btn btn--small">Reply ↗</a>
                            <form method="post" action="<?= url('/admin/messages/' . ((int)$m['id']) . '/delete') ?>" onsubmit="return confirm('Delete this message?')" style="display:inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn--small btn--danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
