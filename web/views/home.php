<!-- views/home.php -->
<div class="text-center">
    <div class="row justify-content-centeer align-items-center mb-5">
        <h1>TinyChat</h1>
        <?php if (isset($_SESSION["user"])): ?>
            <div class="mt-4">
                <a href="/chat" class="btn btn-outline-primary">Enter Chat</a>
            </div>
        <?php endif; ?>
    </div>

</div>
