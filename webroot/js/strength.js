function showAddStrengthModal() {
    // reset modal
    $('#add-strength-form')[0].reset();
    $('#add-strength-form').parsley().reset();
    // show modal
    $('#add-strength-modal').modal('toggle');
}

function showEditStrengthModal(strengthId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-strengths-overlay').removeClass('hidden');


    // get char info
    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/strengths/view/' + strengthId,
        success: function (resp) {
            if (resp.status == 'success') {
                // reset modal
                $('#edit-strength-form')[0].reset();
                $('#edit-strength-form').parsley().reset();
                // fill data to edit form
                $('#edit-strength-id').val(resp.data['id']);
                $('#edit-strength-name').val(resp.data['name']);
                $('#edit-strength-name-jp').val(resp.data['name_jp']);
                // show modal
                $('#edit-strength-modal').modal('toggle');
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
            $('#list-strengths-overlay').addClass('hidden');
        }
    });

}

function viewStrength(strengthId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-chars-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/strengths/view/' + strengthId,
        success: function (resp) {
            if (resp.status == 'success') {
                $('#view-strength-name').html(resp.data.name);
                if (resp.data.name_jp) {
                    $('#view-strength-name-jp').html(resp.data.name_jp);
                } else {
                    $('#view-strength-name-jp').html('N/A');
                }

                if (resp.data.created) {
                    $('#view-strength-created').html(resp.created);
                } else {
                    $('#view-strength-created').html('N/A');
                }
                if (resp.data.created_by_user) {
                    $('#view-strength-created-by').html(resp.data.created_by_user.fullname);
                } else {
                    $('#view-strength-created-by').html('N/A');
                }
                if (resp.data.modified_by_user) {
                    $('.modified').removeClass('hidden');
                    $('#view-strength-modified-by').html(resp.data.modified_by_user.fullname);
                    $('#view-strength-modified').html(resp.modified);
                } else {
                    $('.modified').addClass('hidden');
                }

                // toggle modal
                $('#view-strength-modal').modal('toggle');
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
            $('#list-chars-overlay').addClass('hidden');
        }
    });
}