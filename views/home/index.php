<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <input type="text"
                           name="search"
                           value="<?= htmlspecialchars($search) ?>"
                           placeholder="Search events..."
                           class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="" <?= empty($sort) ? 'selected' : '' ?>>Sort by...</option>
                        <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name</option>
                        <option value="max_capacity" <?= $sort === 'max_capacity' ? 'selected' : '' ?>>Available Capacity</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="order" class="form-select">
                        <option value="" <?= empty($order) ? 'selected' : '' ?>>Order by...</option>
                        <option value="asc" <?= $order === 'ASC' ? 'selected' : '' ?>>Date (Earliest first)</option>
                        <option value="desc" <?= $order === 'DESC' ? 'selected' : '' ?>>Date (Latest first)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        Filter
                    </button>
                </div>
                <div class="col-md-1">
                    <a href="/" class="btn btn-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

   <?php if(count($events)): ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($events as $event): ?>
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($event['name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>

                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2 text-muted">
                                <i class="bi bi-calendar-event me-2"></i>
                                <?= date('F j, Y g:i A', strtotime($event['event_datetime'])) ?>
                            </div>
                            <div class="d-flex align-items-center text-muted">
                                <i class="bi bi-people-fill me-2"></i>
                                <?= $event['attendees_count'] ?> / <?= $event['max_capacity'] ?> Attendees
                            </div>
                        </div>

                        <?php if ($event['attendees_count'] >= $event['max_capacity']): ?>
                            <div class="alert alert-danger mb-0">
                                Maximum capacity reached
                            </div>
                        <?php else: ?>
                            <a href="events-register/<?= $event['id'] ?>"
                               class="btn btn-success w-100">
                                Register Now
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                    <a class="page-link"
                       href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&order=<?= $order ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php else: ?>
        <h2 class="text-center">No available event!!!</h2>
    <?php endif ?>
</div>