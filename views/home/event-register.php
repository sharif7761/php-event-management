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
<script>
    $(document).ready(function() {
        $('#attendeeRegistrationForm').on('submit', function(e) {
            e.preventDefault();
            $('#message-container').empty();
            $(this).find('.is-invalid').removeClass('is-invalid');
            const formData = $(this).serialize();

            $.ajax({
                url: '/events-register',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#message-container').html(`
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${response.success}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `);

                        $('#attendeeRegistrationForm')[0].reset();
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;

                    if (response.error) {
                        if (typeof response.error === 'object') {
                            Object.keys(response.error).forEach(field => {
                                $(`#${field}`).addClass('is-invalid');
                                $(`#${field}`).siblings('.invalid-feedback').text(response.error[field]);
                            });
                        } else {
                            $('#message-container').html(`
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ${response.error}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);
                        }
                    }
                }
            });
        });
    });
</script>