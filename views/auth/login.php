<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="alert alert-info">
            <h4 class="alert-heading">Test Credentials:</h4>
            <p>
                * After seeding or importing the database you can use the credentials. Or you can create a user from registration page.<br/>
                Admin Account (admin can manage all events): <br/>
                Email: admin@example.com <br/>
                Password: admin123 <br/>
                User Account (user can manage events created by him): <br/>
                Email: user@example.com <br/>
                Password: user123
            </p>
        </div>
        <div class="card">
            <div class="card-header">Login</div>
            <div class="card-body">

                <?php include __DIR__ . '/../errors/validation-error.php'; ?>

                <form method="POST" action="/login">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" value="<?= htmlspecialchars($_SESSION['old']['password'] ?? '') ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>