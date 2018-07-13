$(document).ready(function() {
    $('#edit-company-submit-btn').click(function() {
        // validate form
        var validateResult = $('#edit-company-form').parsley().validate();
        if (validateResult) {
            $.ajax({
                type: "POST",
                url: $('#edit-company-form').attr('action'),
                data: $('#edit-company-form').serialize(),
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


function editCompany(companyId) {
    $.ajax({
        type: 'GET',
        url: '/tvms/companies/edit',
        data: {id: companyId},
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
            $('#edit-company-modal').modal('toggle');
        }
    });
};



$('#setting-company-submit-btn').click(function() {
    var elems = Array.prototype.slice.call($('#setting-company-form').find('input[type="checkbox"]'));
    elems.forEach(function (ele) {
        if (ele.checked) {
            $('.' + ele.name).removeClass('hidden');
        } else {
            $('.' + ele.name).addClass('hidden');
        }
    });
    $('#setting-company-modal').modal('hide');
});

$('#setting-company-close-btn').click(function() {
    // reset form before close
    $('#setting-company-form')[0].reset();
});
