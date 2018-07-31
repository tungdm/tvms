$(document).ready(function() {
    $('#add-guild-submit-btn').click(function () {
        var validateResult = $('#add-guild-form').parsley().validate();
        if (validateResult) {
            $.ajax({
                type: "POST",
                url: $('#add-guild-form').attr('action'),
                data: $('#add-guild-form').serialize(),
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
                        notice.get().click(function() {
                            notice.remove();
                        });
                    }
                }
            });
        }
    });

    $('#edit-guild-submit-btn').click(function() {
        // validate form
        var validateResult = $('#edit-guild-form').parsley().validate();
        if (validateResult) {
            if (ajaxing) {
                // still requesting
                return;
            }
            ajaxing = true;

            $.ajax({
                type: "POST",
                url: $('#edit-guild-form').attr('action'),
                data: $('#edit-guild-form').serialize(),
                success: function(resp){                    
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
                        notice.get().click(function() {
                            notice.remove();
                        });
                    }
                },
                complete: function() {
                    ajaxing = false;
                }
            });
        }
    });

    $('#setting-guild-submit-btn').click(function() {
        var elems = Array.prototype.slice.call($('#setting-guild-form').find('input[type="checkbox"]'));
        elems.forEach(function (ele) {
            if (ele.checked) {
                $('.' + ele.name).removeClass('hidden');
            } else {
                $('.' + ele.name).addClass('hidden');
            }
        });
        $('#setting-guild-modal').modal('hide');
    });
});

function viewGuild(guildId) {
    var overlayId = '#list-guild-overlay';
    globalViewGuild(guildId, overlayId);
}

function showAddGuildModal() {
    $('#add-guild-form')[0].reset();
    $('#add-guild-form').parsley().reset();

    $('#add-guild-modal').modal('toggle');
}

function editGuild(guildId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-guild-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/guilds/edit',
        data: {id: guildId},
        success: function(resp) {
            // reset form
            $('#edit-guild-form').parsley().reset();

            // fill data to edit form
            $('#edit-id').val(resp['id']);
            $('#edit-name-romaji').val(resp['name_romaji']);
            $('#edit-name-kanji').val(resp['name_kanji']);
            $('#edit-address-romaji').val(resp['address_romaji']);
            $('#edit-address-kanji').val(resp['address_kanji']);
            $('#edit-phone-vn').val(resp['phone_vn']);
            $('#edit-phone-jp').val(resp['phone_jp']);

            $('#edit-deputy-name-romaji').val(resp['deputy_name_romaji']);
            $('#edit-deputy-name-kanji').val(resp['deputy_name_kanji']);
            $('#edit-license-number').val(resp['license_number']);
            // toggle modal
            $('#edit-guild-modal').modal('toggle');
        },
        complete: function() {
            ajaxing = false;
            $('#list-guild-overlay').addClass('hidden');
        }
    });
};


