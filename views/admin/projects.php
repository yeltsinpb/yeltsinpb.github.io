<section class="card">
    <header class="card__head">
        <h2>Your projects (<?= count($projects) ?>)</h2>
        <a href="<?= url('/admin/projects/new') ?>" class="btn btn--accent">+ New project</a>
    </header>

    <?php if (empty($projects)): ?>
        <p class="empty">No projects yet. <a href="<?= url('/admin/projects/new') ?>">Add the first one →</a></p>
    <?php else: ?>
        <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Stack</th>
                    <th>Featured</th>
                    <th>Order</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $p): ?>
                    <tr>
                        <td>
                            <strong><?= e($p['title']) ?></strong>
                            <div class="muted"><?= e($p['summary']) ?></div>
                        </td>
                        <td class="muted"><?= e($p['tech_stack']) ?></td>
                        <td><?= $p['featured'] ? '★' : '—' ?></td>
                        <td><?= (int)$p['sort_order'] ?></td>
                        <td class="muted"><?= fmt_date($p['created_at']) ?></td>
                        <td class="row-actions">
                            <a href="<?= url('/projects/' . (e($p['slug']))) ?>" target="_blank" rel="noopener" class="btn btn--small btn--ghost">View</a>
                            <a href="<?= url('/admin/projects/' . ((int)$p['id']) . '/edit') ?>" class="btn btn--small">Edit</a>
                            <form method="post" action="<?= url('/admin/projects/' . ((int)$p['id']) . '/delete') ?>" onsubmit="return confirm('Delete this project? This cannot be undone.')" style="display:inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn--small btn--danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</section>
