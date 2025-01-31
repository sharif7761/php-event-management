<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-body">
            <h1>Register Event</h1>
            <h3 class="card-title"><?= $event['name'] ?></h3>
            <div class="row mt-3">
                <div class="col-md-8">
                    <p class="text-muted">
                        <strong>Date & Time:</strong>
                        <?= date('F j, Y - g:i A', strtotime($event['event_datetime'])) ?>
                    </p>
                    <p class="card-text"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                </div>
            </div>
        </div>
    </div>
    <div id="message-container"></div>

    <form id="attendeeRegistrationForm" class="needs-validation" novalidate>
        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">

        <div class="mb-3">
            <label for="attendee_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="attendee_name" name="attendee_name" required>
            <div class="invalid-feedback">
                Please enter your name
            </div>
        </div>

        <div class="mb-3">
            <label for="attendee_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="attendee_email" name="attendee_email" required>
            <div class="invalid-feedback">
                Please enter a valid email address
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Register for Event</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="/js/event-registration.js"></script>