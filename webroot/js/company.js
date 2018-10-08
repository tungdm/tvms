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
    $('#edit-dis-company-submit-btn').click(function() {
        // validate form
        var validateResult = $('#edit-dispatching-company-form').parsley().validate();
        if (validateResult) {
            if (ajaxing) {
                // still requesting
                return;
            }
            ajaxing = true;

            $.ajax({
                type: "POST",
                url: $('#edit-dispatching-company-form').attr('action'),
                data: $('#edit-dispatching-company-form').serialize(),
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

function viewDispatchingCompany(companyId) {
    var overlayId = '#list-company-overlay';
    globalViewDispatchingCompany(companyId, overlayId);
}

function editCompany(companyId, type) {
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
            if (type == 1) {
                // cty phai cu
                $('#edit-dispatching-company-form').parsley().reset();
                // fill data to edit form
                $('#edit-dis-id').val(resp['id']);
                $('#edit-dis-name-romaji').val(resp['name_romaji']);
                $('#edit-dis-name-kanji').val(resp['name_kanji']);
                $('#edit-dis-address-romaji').val(resp['address_romaji']);
                $('#edit-dis-deputy-name-romaji').val(resp['deputy_name_romaji']);
                // toggle modal
                $('#edit-dispatching-company-modal').modal('toggle');
            } else {
                // cty tiep nhan
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
            }
        },
        complete: function() {
            ajaxing = false;
            $('#list-company-overlay').addClass('hidden');
        }
    });
};

function showAddCompanyModal(type) {
    if (type == '1') {
        // cty phai cu
        $('#add-dispatching-company-form')[0].reset();
        $('#add-dispatching-company-form').parsley().reset();
        $('#add-dispatching-company-modal').modal('toggle');
    } else {
        // cty tiep nhan
        $('#add-company-form')[0].reset();
        $('#guild-id').val(null).trigger('change');
        $('#add-company-form').parsley().reset();
        $('#add-company-modal').modal('toggle');
    }
}

function showListWorkersModal(companyId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-company-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/companies/viewWorkers/' + companyId,
        success: function(resp) {
            $('.total-count').html(resp.data.length);
            var source = $("#workers-template").html();
            var template = Handlebars.compile(source);
            var html = template(resp.data);
            $('#workers-container').html(html);
            // toggle modal
            $('#view-workers-modal').modal('toggle');
        },
        complete: function() {
            ajaxing = false;
            $('#list-company-overlay').addClass('hidden');
        }
    });
}


function viewWorkers(id) {
    if (!id) {
        return;
    }
    window.open(DOMAIN_NAME + '/students/view/' + id, '_blank');
}

function viewOrder(id) {
    if (!id) {
        return;
    }
    window.open(DOMAIN_NAME + '/orders/view/' + id, '_blank');
}