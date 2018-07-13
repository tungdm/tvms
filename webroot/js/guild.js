$(document).ready(function() {
    $('#edit-guild-submit-btn').click(function() {
        // validate form
        var validateResult = $('#edit-guild-form').parsley().validate();
        if (validateResult) {
            $.ajax({
                type: "POST",
                url: $('#edit-guild-form').attr('action'),
                data: $('#edit-guild-form').serialize(),
                success: function(resp){
                    if (resp.status == 'success') {
                        window.location = resp.redirect; 
                    } else {
                        PNotify.desktop.permission();
                        (new PNotify({
                            title: resp.flash.title,
                            text: resp.flash.message,
                            type: resp.flash.type,
                            desktop: {
                                desktop: true
                            }
                        }))
                    }
                }
            });
        }
    });
});

$(document).ready(function () {
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
                        PNotify.desktop.permission();
                        (new PNotify({
                            title: resp.flash.title,
                            text: resp.flash.message,
                            type: resp.flash.type,
                            desktop: {
                                desktop: true
                            }
                        }))
                    }
                }
            });
        }
    });
});
function editGuild(guildId) {
    $.ajax({
        type: 'GET',
        url: '/tvms/guilds/edit',
        data: {id: guildId},
        success: function(resp) {
            // fill data to edit form
            $('#edit-id').val(resp['id']);
            $('#edit-name-romaji').val(resp['name_romaji']);
            $('#edit-name-kanji').val(resp['name_kanji']);
            $('#edit-address-romaji').val(resp['address_romaji']);
            $('#edit-address-kanji').val(resp['address_kanji']);
            $('#edit-phone-vn').val(resp['phone_vn']);
            $('#edit-phone-jp').val(resp['phone_jp']);
            // toggle modal
            $('#edit-guild-modal').modal('toggle');
        }
    });
};

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

$('#setting-guild-close-btn').click(function() {
    // reset form before close
    $('#setting-guild-form')[0].reset();
});
