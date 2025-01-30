<div class="container">
    <h1>Edit Event</h1>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="/events/<?= $event['id'] ?>" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Event Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="<?= htmlspecialchars($_SESSION['old']['name'] ?? $event['name']) ?>" required>
                            <?php if (isset($_SESSION['errors']['name'])): ?>
                                <div class="text-danger"><?= htmlspecialchars($_SESSION['errors']['name'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($_SESSION['old']['description'] ?? $event['description']) ?></textarea>
                            <?php if (isset($_SESSION['errors']['description'])): ?>
                                <div class="text-danger"><?= htmlspecialchars($_SESSION['errors']['description'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="max_capacity" class="form-label">Maximum Capacity</label>
                            <input type="number" min="0" class="form-control" id="max_capacity" name="max_capacity"
                                   value="<?= htmlspecialchars($_SESSION['old']['max_capacity'] ?? $event['max_capacity']) ?>" required>
                            <?php if (isset($_SESSION['errors']['max_capacity'])): ?>
                                <div class="text-danger"><?= htmlspecialchars($_SESSION['errors']['max_capacity'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="event_datetime" class="form-label">Event Date & Time</label>
                            <input type="datetime-local" class="form-control" id="event_datetime" name="event_datetime"
                                   value="<?= htmlspecialchars($_SESSION['old']['event_datetime'] ?? date('Y-m-d\TH:i', strtotime($event['event_datetime']))) ?>" required>
                            <?php if (isset($_SESSION['errors']['event_datetime'])): ?>
                                <div class="text-danger"><?= htmlspecialchars($_SESSION['errors']['event_datetime'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Event</button>
                            <a href="/events" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>