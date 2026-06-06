<section class="errpage">
    <div class="errpage__terminal">
        <div class="errpage__bar">
            <span class="errpage__dots">
                <span class="errpage__dot errpage__dot--red"></span>
                <span class="errpage__dot errpage__dot--yellow"></span>
                <span class="errpage__dot errpage__dot--green"></span>
            </span>
            <span class="errpage__path">~/500</span>
        </div>
        <div class="errpage__body">
            <p class="errpage__row"><span class="errpage__prompt">$</span> <span class="errpage__cmd">./serve</span></p>
            <p class="errpage__row errpage__out">&gt; HTTP/1.1 <span class="errpage__code">500</span> Internal Server Error</p>
            <p class="errpage__row errpage__out">&gt; something broke on my end while building this page</p>
            <p class="errpage__row errpage__out errpage__muted">&gt; the error has been logged — try again in a moment</p>
            <p class="errpage__row"><span class="errpage__prompt">$</span> <span class="errpage__cmd">retry</span> <span class="errpage__cursor">&#9612;</span></p>
        </div>
    </div>

    <h1 class="errpage__title">Something went <em>sideways</em>.</h1>
    <p class="errpage__lead">An unexpected error occurred. It's not you — it's the server.</p>

    <div class="errpage__actions">
        <a href="<?= url('/') ?>" class="cta"><span>Back to home</span><span class="cta__arrow">→</span></a>
    </div>
</section>
