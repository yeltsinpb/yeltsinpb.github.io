<?php $isEdit = $project !== null; ?>
<form method="post"
      action="<?= $isEdit ? url('/admin/projects/' . (int)$project['id'] . '/update') : url('/admin/projects') ?>"
      enctype="multipart/form-data"
      class="form-card">
    <?= csrf_field() ?>

    <h2 class="form-section">Basics</h2>
    <div class="field">
        <label for="title">Project title</label>
        <input id="title" name="title" type="text" value="<?= e($project['title'] ?? '') ?>" required>
    </div>
    <div class="field">
        <label for="summary">Summary <small>— one sentence shown on cards</small></label>
        <input id="summary" name="summary" type="text" value="<?= e($project['summary'] ?? '') ?>" required>
    </div>
    <div class="field">
        <label for="description">Description <small>— full write-up, blank line for new paragraph</small></label>
        <textarea id="description" name="description" rows="10" required><?= e($project['description'] ?? '') ?></textarea>
    </div>
    <div class="field">
        <label for="tech_stack">Tech stack <small>— comma-separated</small></label>
        <input id="tech_stack" name="tech_stack" type="text" value="<?= e($project['tech_stack'] ?? '') ?>" placeholder="Laravel, Vue, MySQL" required>
    </div>

    <h2 class="form-section">Links</h2>
    <div class="field-row">
        <div class="field">
            <label for="live_url">Live URL</label>
            <input id="live_url" name="live_url" type="url" value="<?= e($project['live_url'] ?? '') ?>" placeholder="https://...">
        </div>
        <div class="field">
            <label for="repo_url">Repository URL</label>
            <input id="repo_url" name="repo_url" type="url" value="<?= e($project['repo_url'] ?? '') ?>" placeholder="https://github.com/...">
        </div>
    </div>

    <h2 class="form-section">Cover image</h2>
    <?php if ($isEdit && !empty($project['cover_image'])): ?>
        <div class="avatar-preview">
            <img src="<?= e(url('/uploads/' . $project['cover_image'])) ?>" alt="">
        </div>
    <?php endif; ?>
    <div class="field">
        <input name="cover_image" type="file" accept="image/*">
        <small>Optional — jpg, png, webp, max 5MB. This is the thumbnail shown on project cards.</small>
    </div>

    <h2 class="form-section">Display</h2>
    <div class="field-row">
        <div class="field field--checkbox">
            <label>
                <input type="checkbox" name="featured" value="1" <?= !empty($project['featured']) ? 'checked' : '' ?>>
                Feature on homepage
            </label>
        </div>
        <div class="field field--small">
            <label for="sort_order">Sort order</label>
            <input id="sort_order" name="sort_order" type="number" value="<?= (int)($project['sort_order'] ?? 0) ?>">
        </div>
        <div class="field">
            <label for="completed_on">Completed on <small>— YYYY-MM or YYYY-MM-DD</small></label>
            <input id="completed_on" name="completed_on" type="text" value="<?= e($project['completed_on'] ?? '') ?>" placeholder="2025-09">
        </div>
    </div>

    <div class="form-actions">
        <a href="<?= url('/admin/projects') ?>" class="btn btn--ghost">Cancel</a>
        <button type="submit" class="cta">
            <span><?= $isEdit ? 'Save changes' : 'Create project' ?></span>
            <span class="cta__arrow">→</span>
        </button>
    </div>
</form>

<?php if ($isEdit): ?>
<div class="form-card form-card--gallery">
    <h2 class="form-section" style="margin-top:0">Project gallery</h2>
    <p class="form-hint">Add screenshots and visuals shown in a futuristic viewer on the public project page. Drag-and-drop or click to upload.</p>

    <?php $images = $images ?? []; ?>
    <?php if (!empty($images)): ?>
        <div class="gallery-admin">
            <?php foreach ($images as $img): ?>
                <div class="gallery-admin__item">
                    <img src="<?= e(url('/uploads/' . $img['filename'])) ?>" alt="">
                    <form method="post" action="<?= url('/admin/projects/' . (int)$project['id'] . '/images/' . (int)$img['id'] . '/delete') ?>" onsubmit="return confirm('Remove this image from the gallery?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="gallery-admin__remove" aria-label="Remove">×</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="form-hint" style="opacity:0.5">No gallery images yet.</p>
    <?php endif; ?>

    <form method="post"
          action="<?= url('/admin/projects/' . (int)$project['id'] . '/images') ?>"
          enctype="multipart/form-data"
          class="gallery-upload">
        <?= csrf_field() ?>
        <label class="gallery-upload__drop">
            <input type="file" name="images[]" accept="image/*" multiple required>
            <span class="gallery-upload__icon">⊕</span>
            <span class="gallery-upload__text">Click to add images (multiple allowed)</span>
            <small>jpg, png, webp · max 5MB each</small>
        </label>
        <button type="submit" class="cta">
            <span>Upload</span>
            <span class="cta__arrow">↑</span>
        </button>
    </form>
</div>
<?php endif; ?>
