<section class="hero">
    <div class="hero__layout">
        <div class="hero__main">
            <div class="hero__meta">
                <span class="pill">★ Available for freelance work</span>
            </div>

            <h1 class="hero__title">
                <span class="hero__line hero__typeline" style="--d:0ms"
                      data-type-em="Hello,"
                      data-type-rest=" I'm <?= e(explode(' ', $profile['name'])[0]) ?>">
                    <span class="hero__type-em"><em>Hello,</em></span><span class="hero__type-rest"> I'm <?= e(explode(' ', $profile['name'])[0]) ?></span><span class="hero__caret" aria-hidden="true"></span>
                </span>
                <span class="hero__line hero__line--accent" style="--d:120ms"><?= e($profile['tagline']) ?>.</span>
            </h1>

            <div class="hero__cluster">
                <div class="hero__bio" style="--d:240ms">
                    <?= nl2br(e($profile['bio'])) ?>
                </div>
            </div>

            <a href="<?= url('/projects') ?>" class="cta" style="--d:480ms">
                <span>See selected work</span>
                <span class="cta__arrow">→</span>
            </a>
        </div>

        <div class="hero__terminal" aria-hidden="true">
            <div class="terminal" id="heroTerminal">
                <div class="terminal__bar">
                    <span class="terminal__dots">
                        <span class="terminal__dot terminal__dot--red"></span>
                        <span class="terminal__dot terminal__dot--yellow"></span>
                        <span class="terminal__dot terminal__dot--green"></span>
                    </span>
                    <span class="terminal__title">~/yeltsin</span>
                </div>
                <div class="terminal__body" id="heroTerminalBody"></div>
            </div>

            <div class="hero__sigil" style="--d:360ms">
                <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" class="sigil">
                    <defs>
                        <linearGradient id="sigil-grad" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#ffb3d9"/>
                            <stop offset="50%" stop-color="#c084fc"/>
                            <stop offset="100%" stop-color="#67e8f9"/>
                        </linearGradient>
                    </defs>
                    <circle cx="100" cy="100" r="88" stroke="url(#sigil-grad)" stroke-width="0.5" opacity="0.4"/>
                    <circle cx="100" cy="100" r="70" stroke="url(#sigil-grad)" stroke-width="0.5" opacity="0.6"/>
                    <circle cx="100" cy="100" r="50" stroke="url(#sigil-grad)" stroke-width="1"/>
                    <path d="M100 30 L100 170 M30 100 L170 100 M50 50 L150 150 M150 50 L50 150"
                          stroke="url(#sigil-grad)" stroke-width="0.5" opacity="0.5"/>
                    <circle cx="100" cy="100" r="4" fill="url(#sigil-grad)"/>
                    <text x="100" y="20" text-anchor="middle" fill="currentColor" font-family="JetBrains Mono" font-size="6" opacity="0.6">N · 14.07°</text>
                    <text x="100" y="190" text-anchor="middle" fill="currentColor" font-family="JetBrains Mono" font-size="6" opacity="0.6">S · 121.85°</text>
                </svg>
            </div>

            <span class="hero__orb hero__orb--1">
                <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="hero__orb-svg">
                    <circle cx="50" cy="50" r="46" stroke="url(#sigil-grad)" stroke-width="1" opacity="0.6"/>
                    <circle cx="50" cy="50" r="30" stroke="url(#sigil-grad)" stroke-width="0.75" opacity="0.4"/>
                    <path d="M50 4 L50 96 M4 50 L96 50" stroke="url(#sigil-grad)" stroke-width="0.75" opacity="0.45"/>
                    <circle cx="50" cy="50" r="2.5" fill="url(#sigil-grad)"/>
                </svg>
            </span>
            <span class="hero__orb hero__orb--2">
                <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="hero__orb-svg">
                    <circle cx="50" cy="50" r="46" stroke="url(#sigil-grad)" stroke-width="1.5" opacity="0.6"/>
                    <path d="M50 4 L50 96 M4 50 L96 50 M18 18 L82 82 M82 18 L18 82" stroke="url(#sigil-grad)" stroke-width="1" opacity="0.4"/>
                    <circle cx="50" cy="50" r="3.5" fill="url(#sigil-grad)"/>
                </svg>
            </span>
            <span class="hero__orb hero__orb--3"></span>
        </div>
    </div>
</section>

<section class="section">
    <div class="section__head">
        <span class="section__index">— 02</span>
        <h2 class="section__title">Tools of the <em>trade</em></h2>
        <p class="section__sub">A non-exhaustive list of what I reach for when the brief lands on my desk.</p>
    </div>

    <div class="skills-grid">
        <?php foreach ($skillsByCategory as $category => $skills): ?>
            <div class="skills-card">
                <h3 class="skills-card__cat"><?= e($category) ?></h3>
                <ul class="skills-list">
                    <?php foreach ($skills as $skill): ?>
                        <li class="skill" style="--p: <?= (int)$skill['proficiency'] ?>%">
                            <div class="skill__head">
                                <span class="skill__name"><?= e($skill['name']) ?></span>
                                <span class="skill__pct"><?= (int)$skill['proficiency'] ?></span>
                            </div>
                            <div class="skill__bar"><div class="skill__fill"></div></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="section__head">
        <span class="section__index">— 03</span>
        <h2 class="section__title">Selected <em>work</em></h2>
        <p class="section__sub">Recent projects, picked because I'm proud of them.</p>
    </div>

    <div class="carousel" id="projectCarousel" data-count="<?= count($projects) ?>">
        <div class="carousel__viewport">
            <div class="carousel__track" id="carouselTrack">
                <?php foreach ($projects as $i => $project): ?>
                    <?php $tags = array_slice(explode(',', $project['tech_stack']), 0, 4); ?>
                    <article class="pcard" data-index="<?= $i ?>" style="--i: <?= $i ?>">
                        <a class="pcard__link" href="<?= url('/projects/' . e($project['slug'])) ?>">
                            <div class="pcard__frame">
                                <div class="pcard__chrome">
                                    <span class="pcard__dot"></span>
                                    <span class="pcard__dot"></span>
                                    <span class="pcard__dot"></span>
                                    <span class="pcard__url"><?= e($project['slug']) ?>.dev</span>
                                </div>
                                <div class="pcard__shot">
                                    <?php if (!empty($project['cover_image'])): ?>
                                        <img src="<?= e(url('/uploads/' . $project['cover_image'])) ?>"
                                             alt="<?= e($project['title']) ?>" loading="lazy">
                                    <?php else: ?>
                                        <div class="pcard__placeholder" aria-hidden="true">
                                            <span class="pcard__placeholder-glyph"><?= e(strtoupper(substr($project['title'], 0, 1))) ?></span>
                                            <span class="pcard__placeholder-label"><?= e($project['title']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="pcard__meta">
                                <h3 class="pcard__title"><?= e($project['title']) ?></h3>
                                <p class="pcard__summary"><?= e($project['summary']) ?></p>
                                <div class="pcard__tags">
                                    <?php foreach ($tags as $tag): ?>
                                        <span><?= e(trim($tag)) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <span class="pcard__cta">View project <span class="pcard__arrow">→</span></span>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <button class="carousel__nav carousel__nav--prev" id="carouselPrev" type="button" aria-label="Previous project">←</button>
        <button class="carousel__nav carousel__nav--next" id="carouselNext" type="button" aria-label="Next project">→</button>

        <div class="carousel__dots" id="carouselDots" role="tablist" aria-label="Project navigation">
            <?php foreach ($projects as $i => $project): ?>
                <button class="carousel__dot" type="button" data-index="<?= $i ?>" aria-label="Go to <?= e($project['title']) ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="see-all">
        <a href="<?= url('/projects') ?>" class="cta cta--ghost">
            <span>All projects</span>
            <span class="cta__arrow">→</span>
        </a>
    </div>
</section>

<section class="section section--cta">
    <h2 class="cta-block">
        <em>Have a project</em> that needs a developer who ships?
    </h2>
    <a href="<?= url('/contact') ?>" class="cta cta--large">
        <span>Let's talk</span>
        <span class="cta__arrow">→</span>
    </a>
</section>
