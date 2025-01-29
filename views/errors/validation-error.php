<?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($_SESSION['errors'] as $field => $error): ?>
                <?php if (is_array($error)): ?>
                    <?php foreach ($error as $errorMessage): ?>
                        <li><?php echo htmlspecialchars($errorMessage); ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['errors']); ?>
<?php endif; ?>