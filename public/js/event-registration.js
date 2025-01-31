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