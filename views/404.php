<div class="row">
    <div class="main">
        <?php if ($status == 500): ?>
            <h2>Unexpected error.</h2>
        <?php else: ?>
            <h2>Page not found</h2>
            <p><a href="/">Go to home.</a></p>
        <?php endif; ?>
        <p>Please contact administrators if this problem persists.</p>
    </div>
</div>