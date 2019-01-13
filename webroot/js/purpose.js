function showAddPurposeModal() {
    // reset modal
    $('#add-purpose-form')[0].reset();
    $('#add-purpose-form').parsley().reset();
    // show modal
    $('#add-purpose-modal').modal('toggle');
}

function showEditPurposeModal(purposeId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-purposes-overlay').removeClass('hidden');


    // get purpose info
    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/purposes/view/' + purposeId,
        success: function (resp) {
            if (resp.status == 'success') {
                // reset modal
                $('#edit-purpose-form')[0].reset();
                $('#edit-purpose-form').parsley().reset();
                // fill data to edit form
                $('#edit-purpose-id').val(resp.data['id']);
                $('#edit-purpose-name').val(resp.data['name']);
                $('#edit-purpose-name-jp').val(resp.data['name_jp']);
                // show modal
                $('#edit-purpose-modal').modal('toggle');
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
            $('#list-purposes-overlay').addClass('hidden');
        }
    });

}

function viewPurpose(purposeId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-purposes-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/purposes/view/' + purposeId,
        success: function (resp) {
            if (resp.status == 'success') {
                $('#view-purpose-name').html(resp.data.name);
                if (resp.data.name_jp) {
                    $('#view-purpose-name-jp').html(resp.data.name_jp);
                } else {
                    $('#view-purpose-name-jp').html('N/A');
                }

                if (resp.data.created) {
                    $('#view-purpose-created').html(resp.created);
                } else {
                    $('#view-purpose-created').html('N/A');
                }
                if (resp.data.created_by_user) {
                    $('#view-purpose-created-by').html(resp.data.created_by_user.fullname);
                } else {
                    $('#view-purpose-created-by').html('N/A');
                }
                if (resp.data.modified_by_user) {
                    $('.modified').removeClass('hidden');
                    $('#view-purpose-modified-by').html(resp.data.modified_by_user.fullname);
                    $('#view-purpose-modified').html(resp.modified);
                } else {
                    $('.modified').addClass('hidden');
                }

                // toggle modal
                $('#view-purpose-modal').modal('toggle');
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
            $('#list-purposes-overlay').addClass('hidden');
        }
    });
}