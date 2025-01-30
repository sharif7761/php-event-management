<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><?= htmlspecialchars($event['name']) ?></h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="/events" class="btn btn-secondary">Back to Events</a>
        </div>
    </div>

    <div class="row">
        <!-- Event Details Card -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Event Details</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">
                        <strong>Description:</strong><br>
                        <?= nl2br(htmlspecialchars($event['description'])) ?>
                    </p>
                    <p class="card-text">
                        <strong>Date & Time:</strong><br>
                        <?= date('F j, Y g:i A', strtotime($event['event_datetime'])) ?>
                    </p>
                    <p class="card-text">
                        <strong>Maximum Capacity:</strong><br>
                        <?= htmlspecialchars($event['max_capacity']) ?> people
                    </p>
                    <p class="card-text">
                        <strong>Created By:</strong><br>
                        <?= htmlspecialchars($event['creator_name']) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Attendees List Card -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Attendees</h5>
                        <a href="/events/<?= $event['id'] ?>/download-attendees" class="btn btn-success btn-sm">
                            Download CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form action="/events/show/<?= $event['id'] ?>" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search by name or email"
                                   name="search" value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-outline-secondary" type="submit">Search</button>
                            <?php if (!empty($search)): ?>
                                <a href="/events/show/<?= $event['id'] ?>" class="btn btn-outline-secondary">Clear</a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <!-- Attendees Table -->
                    <?php if (empty($attendees)): ?>
                        <div class="alert alert-info">
                            <?= empty($search) ? 'No attendees yet.' : 'No attendees found matching your search.' ?>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Registration Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($attendees as $attendee): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($attendee['attendee_name']) ?></td>
                                        <td><?= htmlspecialchars($attendee['attendee_email']) ?></td>
                                        <td><?= date('M j, Y g:i A', strtotime($attendee['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted">
                            Showing <?= count($attendees) ?> attendee(s)
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>