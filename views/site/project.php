<article class="project-detail">
    <a href="<?= url('/projects') ?>" class="back-link">← Back to all work</a>

    <header class="project-detail__head">
        <h1 class="project-detail__title"><?= e($project['title']) ?></h1>
        <p class="project-detail__summary"><?= e($project['summary']) ?></p>
    </header>

    <?php if (!empty($project['cover_image'])): ?>
        <div class="project-detail__cover">
            <img src="<?= e(url('/uploads/' . $project['cover_image'])) ?>" alt="<?= e($project['title']) ?>">
        </div>
    <?php endif; ?>

    <div class="project-detail__grid">
        <div class="project-detail__body">
            <h2>About</h2>
            <?php foreach (explode("\n\n", $project['description']) as $para): ?>
                <p><?= nl2br(e($para)) ?></p>
            <?php endforeach; ?>
        </div>

        <aside class="project-detail__meta">
            <div class="meta-block">
                <h3>Stack</h3>
                <div class="meta-tags">
                    <?php foreach (explode(',', $project['tech_stack']) as $tag): ?>
                        <span><?= e(trim($tag)) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (!empty($project['live_url'])): ?>
                <div class="meta-block">
                    <h3>Live</h3>
                    <a href="<?= e($project['live_url']) ?>" target="_blank" rel="noopener">Visit project ↗</a>
                </div>
            <?php endif; ?>

            <?php if (!empty($project['repo_url'])): ?>
                <div class="meta-block">
                    <h3>Source</h3>
                    <a href="<?= e($project['repo_url']) ?>" target="_blank" rel="noopener">View repository ↗</a>
                </div>
            <?php endif; ?>

            <div class="meta-block">
                <h3>Completed</h3>
                <p><?= !empty($project['completed_on']) ? fmt_date($project['completed_on']) : fmt_date($project['created_at']) ?></p>
            </div>
        </aside>
    </div>

    <?php if (!empty($images)): ?>
    <section class="gallery-section">
        <div class="gallery-section__head">
            <span class="gallery-section__label">// gallery</span>
            <h2 class="gallery-section__title">Visuals from <em><?= e($project['title']) ?></em></h2>
            <p class="gallery-section__count"><?= count($images) ?> image<?= count($images) === 1 ? '' : 's' ?> · click to expand</p>
        </div>

        <div class="gallery-grid" id="projectGallery">
            <?php foreach ($images as $i => $img): ?>
                <button type="button"
                        class="gallery-grid__item"
                        data-index="<?= $i ?>"
                        data-src="<?= e(url('/uploads/' . $img['filename'])) ?>"
                        data-caption="<?= e($img['caption'] ?? '') ?>"
                        style="--i: <?= $i ?>">
                    <img src="<?= e(url('/uploads/' . $img['filename'])) ?>" alt="" loading="lazy">
                    <span class="gallery-grid__hover">
                        <span class="gallery-grid__icon">⤢</span>
                    </span>
                </button>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Futuristic lightbox viewer -->
    <div class="lightbox" id="lightbox" aria-hidden="true" role="dialog" aria-label="Image viewer">
        <div class="lightbox__backdrop"></div>
        <div class="lightbox__frame">
            <div class="lightbox__chrome lightbox__chrome--top">
                <span class="lightbox__pos" id="lbPos">01 / <?= count($images) ?></span>
                <button type="button" class="lightbox__btn lightbox__btn--close" id="lbClose" aria-label="Close">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 6l12 12M6 18L18 6"/></svg>
                </button>
            </div>

            <div class="lightbox__stage">
                <button type="button" class="lightbox__nav lightbox__nav--prev" id="lbPrev" aria-label="Previous image">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M15 18l-6-6 6-6"/></svg>
                </button>

                <div class="lightbox__imgwrap">
                    <div class="lightbox__corner lightbox__corner--tl"></div>
                    <div class="lightbox__corner lightbox__corner--tr"></div>
                    <div class="lightbox__corner lightbox__corner--bl"></div>
                    <div class="lightbox__corner lightbox__corner--br"></div>
                    <img id="lbImg" src="" alt="">
                </div>

                <button type="button" class="lightbox__nav lightbox__nav--next" id="lbNext" aria-label="Next image">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 6l6 6-6 6"/></svg>
                </button>
            </div>

            <div class="lightbox__chrome lightbox__chrome--bottom">
                <div class="lightbox__thumbs" id="lbThumbs">
                    <?php foreach ($images as $i => $img): ?>
                        <button type="button" class="lightbox__thumb" data-index="<?= $i ?>">
                            <img src="<?= e(url('/uploads/' . $img['filename'])) ?>" alt="">
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</article>
