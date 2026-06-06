<section class="errpage">
    <div class="errpage__terminal">
        <div class="errpage__bar">
            <span class="errpage__dots">
                <span class="errpage__dot errpage__dot--red"></span>
                <span class="errpage__dot errpage__dot--yellow"></span>
                <span class="errpage__dot errpage__dot--green"></span>
            </span>
            <span class="errpage__path">~/403</span>
        </div>
        <div class="errpage__body">
            <p class="errpage__row"><span class="errpage__prompt">$</span> <span class="errpage__cmd">cat <?= e(strtok($_SERVER['REQUEST_URI'] ?? '/', '?')) ?></span></p>
            <p class="errpage__row errpage__out">&gt; HTTP/1.1 <span class="errpage__code">403</span> Forbidden</p>
            <p class="errpage__row errpage__out">&gt; permission denied — this resource is off-limits</p>
            <p class="errpage__row errpage__out errpage__muted">&gt; nice try, though</p>
            <p class="errpage__row"><span class="errpage__prompt">$</span> <span class="errpage__cmd">exit</span> <span class="errpage__cursor">&#9612;</span></p>
        </div>
    </div>

    <h1 class="errpage__title">That door is <em>locked</em>.</h1>
    <p class="errpage__lead">You don't have access to this resource.</p>

    <div class="errpage__actions">
        <a href="<?= url('/') ?>" class="cta"><span>Back to home</span><span class="cta__arrow">→</span></a>
    </div>
</section>
