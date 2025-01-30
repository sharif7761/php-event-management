<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $title ?></h1>
        <a href="/events/create" class="btn btn-primary">Create Event</a>
    </div>

    <!-- Search Form -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form action="/events" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search events by name"
                           name="search" value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-outline-secondary" type="submit">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="/events" class="btn btn-outline-secondary">Clear</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($events)): ?>
        <div class="alert alert-info">
            <?= empty($search) ? "You haven't created any events yet." : "No events found matching your search." ?>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($events as $event): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($event['name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars(substr($event['description'], 0, 150)) ?>...</p>
                            <p class="card-text">
                                <small class="text-muted">
                                    <strong>Date:</strong> <?= date('F j, Y g:i A', strtotime($event['event_datetime'])) ?><br>
                                    <strong>Capacity:</strong> <?= htmlspecialchars($event['max_capacity']) ?> people<br>
                                    <strong>Attendees:</strong> <?= htmlspecialchars($event['attendees_count']) ?>
                                </small>
                            </p>
                            <div class="d-flex justify-content-between">
                                <a href="/events/show/<?= $event['id'] ?>" class="btn btn-sm btn-info text-white">Show Details</a>
                                <div>
                                    <a href="/events/edit/<?= $event['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <form action="/events/delete/<?= $event['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>