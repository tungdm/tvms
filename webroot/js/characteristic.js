function showAddCharModal() {
    // reset modal
    $('#add-char-form')[0].reset();
    $('#add-char-form').parsley().reset();
    // show modal
    $('#add-char-modal').modal('toggle');
}

function showEditCharModal(charId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-chars-overlay').removeClass('hidden');


    // get char info
    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/characteristics/view/' + charId,
        success: function (resp) {
            if (resp.status == 'success') {
                // reset modal
                $('#edit-char-form')[0].reset();
                $('#edit-char-form').parsley().reset();
                // fill data to edit form
                $('#edit-char-id').val(resp.data['id']);
                $('#edit-char-name').val(resp.data['name']);
                $('#edit-char-name-jp').val(resp.data['name_jp']);
                // show modal
                $('#edit-char-modal').modal('toggle');
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

function viewChar(charId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-chars-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/characteristics/view/' + charId,
        success: function (resp) {
            if (resp.status == 'success') {
                $('#view-char-name').html(resp.data.name);
                if (resp.data.name_jp) {
                    $('#view-char-name-jp').html(resp.data.name_jp);
                } else {
                    $('#view-char-name-jp').html('N/A');
                }

                if (resp.data.created) {
                    $('#view-char-created').html(resp.created);
                } else {
                    $('#view-char-created').html('N/A');
                }
                if (resp.data.created_by_user) {
                    $('#view-char-created-by').html(resp.data.created_by_user.fullname);
                } else {
                    $('#view-char-created-by').html('N/A');
                }
                if (resp.data.modified_by_user) {
                    $('.modified').removeClass('hidden');
                    $('#view-char-modified-by').html(resp.data.modified_by_user.fullname);
                    $('#view-char-modified').html(resp.modified);
                } else {
                    $('.modified').addClass('hidden');
                }

                // toggle modal
                $('#view-char-modal').modal('toggle');
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