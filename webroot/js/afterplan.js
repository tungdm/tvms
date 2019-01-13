function showAddPlanModal() {
    // reset modal
    $('#add-plan-form')[0].reset();
    $('#add-plan-form').parsley().reset();
    // show modal
    $('#add-plan-modal').modal('toggle');
}

function showEditPlanModal(planId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-plans-overlay').removeClass('hidden');


    // get plan info
    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/after-plans/view/' + planId,
        success: function (resp) {
            if (resp.status == 'success') {
                // reset modal
                $('#edit-plan-form')[0].reset();
                $('#edit-plan-form').parsley().reset();
                // fill data to edit form
                $('#edit-plan-id').val(resp.data['id']);
                $('#edit-plan-name').val(resp.data['name']);
                $('#edit-plan-name-jp').val(resp.data['name_jp']);
                // show modal
                $('#edit-plan-modal').modal('toggle');
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
            $('#list-plans-overlay').addClass('hidden');
        }
    });

}

function viewPlan(planId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-plans-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/after-plans/view/' + planId,
        success: function (resp) {
            if (resp.status == 'success') {
                $('#view-plan-name').html(resp.data.name);
                if (resp.data.name_jp) {
                    $('#view-plan-name-jp').html(resp.data.name_jp);
                } else {
                    $('#view-plan-name-jp').html('N/A');
                }

                if (resp.data.created) {
                    $('#view-plan-created').html(resp.created);
                } else {
                    $('#view-plan-created').html('N/A');
                }
                if (resp.data.created_by_user) {
                    $('#view-plan-created-by').html(resp.data.created_by_user.fullname);
                } else {
                    $('#view-plan-created-by').html('N/A');
                }
                if (resp.data.modified_by_user) {
                    $('.modified').removeClass('hidden');
                    $('#view-plan-modified-by').html(resp.data.modified_by_user.fullname);
                    $('#view-plan-modified').html(resp.modified);
                } else {
                    $('.modified').addClass('hidden');
                }

                // toggle modal
                $('#view-plan-modal').modal('toggle');
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
            $('#list-plans-overlay').addClass('hidden');
        }
    });
}