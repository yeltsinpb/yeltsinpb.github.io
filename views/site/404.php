<section class="errpage">
    <div class="errpage__terminal">
        <div class="errpage__bar">
            <span class="errpage__dots">
                <span class="errpage__dot errpage__dot--red"></span>
                <span class="errpage__dot errpage__dot--yellow"></span>
                <span class="errpage__dot errpage__dot--green"></span>
            </span>
            <span class="errpage__path">~/404</span>
        </div>
        <div class="errpage__body">
            <p class="errpage__row"><span class="errpage__prompt">$</span> <span class="errpage__cmd">curl -I <?= e(strtok($_SERVER['REQUEST_URI'] ?? '/', '?')) ?></span></p>
            <p class="errpage__row errpage__out">&gt; HTTP/1.1 <span class="errpage__code">404</span> Not Found</p>
            <p class="errpage__row errpage__out">&gt; the route you requested doesn't exist on this server</p>
            <p class="errpage__row errpage__out errpage__muted">&gt; check the URL, or head back to a known page</p>
            <p class="errpage__row"><span class="errpage__prompt">$</span> <span class="errpage__cmd">cd</span> <span class="errpage__cursor">&#9612;</span></p>
        </div>
    </div>

    <h1 class="errpage__title">This page is in <em>another</em> dimension.</h1>
    <p class="errpage__lead">The URL you followed doesn't lead anywhere here.</p>

    <div class="errpage__actions">
        <a href="<?= url('/') ?>" class="cta"><span>Take me home</span><span class="cta__arrow">→</span></a>
        <a href="<?= url('/projects') ?>" class="cta cta--ghost"><span>See the work</span><span class="cta__arrow">→</span></a>
    </div>
</section>
