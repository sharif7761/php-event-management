<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="alert alert-danger">
        <h4 class="alert-heading">Error</h4>
        <p><?= htmlspecialchars($e->getMessage()) ?></p>
        <hr>
        <p class="mb-0">Please try again or contact support if the problem persists.</p>
    </div>
</div>
</body>
</html>