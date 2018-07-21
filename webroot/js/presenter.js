$(document).ready(function() {
    $('#edit-presenter-submit-btn').click(function() {
        // validate form
        var validateResult = $('#edit-presenter-form').parsley().validate();
        if (validateResult) {
            if (ajaxing) {
                // still requesting
                return;
            }
            ajaxing = true;

            $.ajax({
                type: "POST",
                url: $('#edit-presenter-form').attr('action'),
                data: $('#edit-presenter-form').serialize(),
                success: function(resp){
                    if (resp.status == 'success') {
                        window.location = resp.redirect; 
                    } else {
                        var notice = new PNotify({
                            title: '<strong>' + resp.flash.title + '</strong>',
                            text: resp.flash.message,
                            type: resp.flash.type,
                            
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

function viewPresenter(presenterId) {
    var overlayId = '#list-presenter-overlay';
    globalViewPresenter(presenterId, overlayId);
}

function showAddPresenterModal() {
    $('#add-presenter-form')[0].reset();
    $('#add-presenter-form').parsley().reset();

    $('#add-presenter-modal').modal('toggle');
}

function editPresenter(presenterId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-presenter-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/presenters/edit',
        data: {id: presenterId},
        success: function(resp) {
            // fill data to edit form
            $('#edit-id').val(resp['id']);
            $('#edit-name').val(resp['name']);
            $('#edit-address').val(resp['address']);
            $('#edit-phone').val(resp['phone']);
            $('#edit-type').val(resp['type']).trigger('change');

            // toggle modal
            $('#edit-presenter-modal').modal('toggle');
        },
        complete: function() {
            ajaxing = false;
            $('#list-presenter-overlay').addClass('hidden');
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

