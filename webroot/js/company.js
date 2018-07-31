$(document).ready(function() {
    $('#edit-company-submit-btn').click(function() {
        // validate form
        var validateResult = $('#edit-company-form').parsley().validate();
        if (validateResult) {
            if (ajaxing) {
                // still requesting
                return;
            }
            ajaxing = true;

            $.ajax({
                type: "POST",
                url: $('#edit-company-form').attr('action'),
                data: $('#edit-company-form').serialize(),
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
});

function viewGuild(guildId) {
    var overlayId = '#list-company-overlay';
    globalViewGuild(guildId, overlayId);
}

function viewCompany(companyId) {
    var overlayId = '#list-company-overlay';
    globalViewCompany(companyId, overlayId);
}

function editCompany(companyId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-company-overlay').removeClass('hidden');
    
    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/companies/edit',
        data: {id: companyId},
        success: function(resp) {
            // reset form
            $('#edit-company-form').parsley().reset();

            // fill data to edit form
            $('#edit-id').val(resp['id']);
            $('#edit-guild').val(resp['guild_id']).trigger('change');
            $('#edit-name-romaji').val(resp['name_romaji']);
            $('#edit-name-kanji').val(resp['name_kanji']);
            $('#edit-address-romaji').val(resp['address_romaji']);
            $('#edit-address-kanji').val(resp['address_kanji']);
            $('#edit-phone-vn').val(resp['phone_vn']);
            $('#edit-phone-jp').val(resp['phone_jp']);
            $('#edit-deputy-name-romaji').val(resp['deputy_name_romaji']);
            $('#edit-deputy-name-kanji').val(resp['deputy_name_kanji']);
            // toggle modal
            $('#edit-company-modal').modal('toggle');
        },
        complete: function() {
            ajaxing = false;
            $('#list-company-overlay').addClass('hidden');
        }
    });
};

function showAddCompanyModal() {
    $('#add-company-form')[0].reset();
    $('#guild-id').val(null).trigger('change');

    $('#add-company-form').parsley().reset();

    $('#add-company-modal').modal('toggle');
}

