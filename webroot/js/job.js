function viewJob(jobId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-jobs-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/jobs/view/' + jobId,
        success: function (resp) {
            if (resp.status == 'success') {
                $('#view-job-name-romaji').html(resp.data.job_name);
                if (resp.data.job_name_jp) {
                    $('#view-job-name-kanji').html(resp.data.job_name_jp);
                } else {
                    $('#view-job-name-kanji').html('N/A');
                }

                if (resp.data.description) {
                    $('#job-description').html((resp.data.description).replace(/\r?\n/g, '<br/>'));
                } else {
                    $('#job-description').html('N/A');
                }

                if (resp.data.created) {
                    $('#view-job-created').html(resp.created);
                } else {
                    $('#view-job-created').html('N/A');
                }
                if (resp.data.created_by_user) {
                    $('#view-job-created-by').html(resp.data.created_by_user.fullname);
                } else {
                    $('#view-job-created-by').html('N/A');
                }

                if (resp.data.modified_by_user) {
                    $('.modified').removeClass('hidden');
                    $('#view-job-modified-by').html(resp.data.modified_by_user.fullname);
                    $('#view-job-modified').html(resp.modified);
                } else {
                    $('.modified').addClass('hidden');
                }

                // toggle modal
                $('#view-job-modal').modal('toggle');
            }
        },
        complete: function () {
            ajaxing = false;
            $('#list-jobs-overlay').addClass('hidden');
        }
    });
}

function showAddJobModal() {
    // reset modal
    $('#add-job-form')[0].reset();
    $('#add-job-form').parsley().reset();
    // show modal
    $('#add-job-modal').modal('toggle');
}

function addJob() {
    // validate form
    var validateResult = $('#add-job-form').parsley().validate();
    if (validateResult) {
        if (ajaxing) {
            // still requesting
            return;
        }
        ajaxing = true;
        $('#add-modal-overlay').removeClass('hidden');

        $.ajax({
            type: "POST",
            url: $('#add-job-form').attr('action'),
            data: $('#add-job-form').serialize(),
            success: function (resp) {
                if (resp.status == 'success') {
                    window.location = resp.redirect;
                } else {
                    var notice = new PNotify({
                        title: '<strong>' + resp.flash.title + '</strong>',
                        text: resp.flash.message,
                        type: resp.flash.type,
                        styling: 'bootstrap3',
                        icon: resp.flash.icon,
                        cornerclass: 'ui-pnotify-sharp',
                        buttons: {
                            closer: false,
                            sticker: false
                        }
                    });
                    notice.get().click(function () {
                        notice.remove();
                    });
                }
            },
            complete: function () {
                ajaxing = false;
                $('#add-modal-overlay').addClass('hidden');
            }
        });
    }
}

function showEditJobModal(jobId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-job-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/jobs/view/' + jobId,
        success: function (resp) {
            if (resp.status == 'success') {
                // reset form
                $('#edit-job-form').parsley().reset();

                // fill data to edit form
                $('#edit_job_id').val(resp.data['id']);
                $('#edit_job_name').val(resp.data['job_name']);
                $('#edit_job_name_jp').val(resp.data['job_name_jp']);
                $('#edit_description').val(resp.data['description']);

                // toggle modal
                $('#edit-job-modal').modal('toggle');
            }
        },
        complete: function () {
            ajaxing = false;
            $('#list-job-overlay').addClass('hidden');
        }
    });
}
