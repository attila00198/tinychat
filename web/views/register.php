<!-- views/register.php -->
<div id="div-form">
    <h1 class="text-center">Sign Up</h1>
    <form action="/login" method="post">
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
                type="email"
                class="form-control"
                name="eamil"
                id="field-eamil"
                placeholder="eamil"
                required />
            <label for="field-eamil">Email <em>*</em></label>
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
        <div class="form-control mb-5">
            <input type="checkbox" name="iaggre" id="check-iagre" required>
            <label for="check-iagre">Terms&Services</label>
        </div>
        <button class="btn btn-primary float-end" type="submit" name="login" id="btn-login">Login</button>
    </form>
</div>

<?php
