function viewSetting(settingId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-setting-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/notification-settings/view/' + settingId,
        success: function (resp) {
            if (resp.status == 'success') {
                $('#view-title').html(resp.data.title);
                if (resp.data.template) {
                    $('#view-template').html(resp.data.template.replace(/\r?\n/g, '<br/>'));
                } else {
                    $('#view-template').html('N/A');
                }
                $('#view-send-before').html(resp.data.send_before);
                $('#view-receiver-groups').html(resp.receivers);
                if (resp.data.modified_by_user) {
                    $('.modified').removeClass('hidden');
                    $('#view-modified-by').html(resp.data.modified_by_user.fullname);
                    $('#view-modified').html(resp.modified);
                } else {
                    $('.modified').addClass('hidden');
                }
                // toggle modal
                $('#view-setting-modal').modal('toggle');
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
            $('#list-setting-overlay').addClass('hidden');
        }
    });
}

function showEditSettingModal(settingId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-setting-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/notification-settings/view/' + settingId,
        success: function (resp) {
            if (resp.status == 'success') {
                // reset modal
                $('#edit-setting-form')[0].reset();
                $('#edit-setting-form').parsley().reset();
                $('#edit-setting-id').val(resp.data.id);
                $('#title').val(resp.data.title);
                if (resp.data.template) {
                    $('#template').html(resp.data.template);
                }
                $('#send-before').val(resp.data.send_before);
                $('#groups').val(resp.receiversArr).trigger('change');
                // show modal
                $('#edit-setting-modal').modal('toggle');
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
            $('#list-setting-overlay').addClass('hidden');
        }
    });

}