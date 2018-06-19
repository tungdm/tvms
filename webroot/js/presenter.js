$(document).ready(function() {
    $('#edit-presenter-submit-btn').click(function() {
        // validate form
        var validateResult = $('#edit-presenter-form').parsley().validate();
        if (validateResult) {
            $.ajax({
                type: "POST",
                url: $('#edit-presenter-form').attr('action'),
                data: $('#edit-presenter-form').serialize(),
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


function editPresenter(presenterId) {
    $.ajax({
        type: 'GET',
        url: '/tvms/presenters/edit',
        data: {id: presenterId},
        success: function(resp) {
            // fill data to edit form
            $('#edit-id').val(resp['id']);
            $('#edit-name').val(resp['name']);
            $('#edit-address').val(resp['address']);
            $('#edit-phone').val(resp['phone']);
            $('#edit-type').val(resp['type']);
            // toggle modal
            $('#edit-presenter-modal').modal('toggle');
        }
    });
};



$('#setting-presenter-submit-btn').click(function() {
    var elems = Array.prototype.slice.call($('#setting-presenter-form').find('input[type="checkbox"]'));
    elems.forEach(function (ele) {
        if (ele.checked) {
            $('.' + ele.name).removeClass('hidden');
        } else {
            $('.' + ele.name).addClass('hidden');
        }
    });
    $('#setting-presenter-modal').modal('hide');
});

$('#setting-presenter-close-btn').click(function() {
    // reset form before close
    $('#setting-presenter-form')[0].reset();
});
