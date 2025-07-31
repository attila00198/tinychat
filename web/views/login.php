<!-- views/login.php -->
<div id="div-form">
    <h1 class="text-center">Login</h1>
    <span><?= $_SESSION["message"]["error"] ?? "" ?></span>
    <form action="/login" method="POST">
        <div class="form-floating mb-3">
            <input
                type="text"
                class="form-control"
                name="username"
                id="field-username"
                placeholder="Username"
                required />
            <label for="field-username">Username <em>*</em></label>
        </div>
        <div class="form-floating mb-3">
            <input
                type="password"
                class="form-control"
                name="password"
                id="field-password"
                placeholder="Password"
                required />
            <label for="field-password">Password <em>*</em></label>
        </div>
        <button class="btn btn-primary float-end" type="submit" name="login" id="btn-login">Login</button>
    </form>
</div>