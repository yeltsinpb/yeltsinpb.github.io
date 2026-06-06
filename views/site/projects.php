<section class="page-head">
    <span class="page-head__index">— Index 02</span>
    <h1 class="page-head__title">All <em>work</em></h1>
    <p class="page-head__sub"><?= count($projects) ?> projects, ordered by recency and personal favoritism.</p>
</section>

<section class="projects projects--full">
    <?php foreach ($projects as $i => $project): ?>
        <a href="<?= url('/projects/' . (e($project['slug']))) ?>" class="project-card" style="--i: <?= $i ?>">
            <div class="project-card__num"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></div>
            <div class="project-card__body">
                <h3 class="project-card__title">
                    <?= e($project['title']) ?>
                    <?php if ($project['featured']): ?>
                        <span class="badge">★ Featured</span>
                    <?php endif; ?>
                </h3>
                <p class="project-card__summary"><?= e($project['summary']) ?></p>
                <div class="project-card__tags">
                    <?php foreach (array_slice(explode(',', $project['tech_stack']), 0, 5) as $tag): ?>
                        <span><?= e(trim($tag)) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="project-card__arrow">→</div>
        </a>
    <?php endforeach; ?>
</section>
