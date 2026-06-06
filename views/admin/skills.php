<section class="card">
    <header class="card__head">
        <h2>Add new skill</h2>
    </header>
    <form method="post" action="<?= url('/admin/skills') ?>" class="inline-form">
        <?= csrf_field() ?>
        <div class="field">
            <label>Name</label>
            <input name="name" type="text" placeholder="e.g. Svelte" required>
        </div>
        <div class="field">
            <label>Category</label>
            <input name="category" type="text" placeholder="e.g. Frameworks" required list="categories">
            <datalist id="categories">
                <?php
                $cats = array_unique(array_column($skills, 'category'));
                foreach ($cats as $c): ?>
                    <option value="<?= e($c) ?>">
                <?php endforeach; ?>
            </datalist>
        </div>
        <div class="field field--small">
            <label>Level</label>
            <input name="proficiency" type="number" min="0" max="100" value="80" required>
        </div>
        <div class="field field--small">
            <label>Order</label>
            <input name="sort_order" type="number" value="<?= count($skills) + 1 ?>">
        </div>
        <button type="submit" class="btn">Add</button>
    </form>
</section>

<section class="card">
    <header class="card__head">
        <h2>Your skills (<?= count($skills) ?>)</h2>
    </header>

    <?php if (empty($skills)): ?>
        <p class="empty">No skills yet — add one above.</p>
    <?php else: ?>
        <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Level</th>
                    <th>Order</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($skills as $s): ?>
                    <tr>
                        <form method="post" action="<?= url('/admin/skills/' . ((int)$s['id']) . '/update') ?>">
                            <?= csrf_field() ?>
                            <td><input name="name" value="<?= e($s['name']) ?>" required></td>
                            <td><input name="category" value="<?= e($s['category']) ?>" required></td>
                            <td><input name="proficiency" type="number" min="0" max="100" value="<?= (int)$s['proficiency'] ?>"></td>
                            <td><input name="sort_order" type="number" value="<?= (int)$s['sort_order'] ?>"></td>
                            <td class="row-actions">
                                <button type="submit" class="btn btn--small">Save</button>
                        </form>
                        <form method="post" action="<?= url('/admin/skills/' . ((int)$s['id']) . '/delete') ?>" onsubmit="return confirm('Delete this skill?')" style="display:inline">
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
