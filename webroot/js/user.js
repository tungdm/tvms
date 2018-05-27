var perData = {};
perData.counter = 0;

function getCsrfToken() {
    var token = $('input[name="_csrfToken"]').val();
    return token;
}

function removePermissionRow(delEl) {
    // remove DOM
    $(delEl).closest('tr.row-permission').remove();
    perData.counter--;

    selectFields = $('#permission-container').find('select');
    for (var i = 0; i < selectFields.length; i++) {
        if (i % 2 == 0) {
            selectFields[i].name = 'permissions[' + Math.floor(i/2) + '][scope]';
        } else {
            selectFields[i].name = 'permissions[' + Math.floor(i/2) + '][action]';
        }
    }
}
function removePermission(delEl, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Remove user\'s permission',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.value) {
                // send ajax delete request to server
                $.ajax({
                    type: 'POST',
                    url: '/tvms/users/deletePermission',
                    data: {
                        'id': $(delEl).closest('tr.row-permission').find('input').val()
                    },
                    beforeSend: function(xhr){
                        xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());
                    },
                    success: function(resp){
                        swal({
                            title: resp.alert.title,
                            text: resp.alert.message,
                            type: resp.alert.type
                        })
                        if (resp.status == 'success') {
                            removePermissionRow(delEl);
                        }
                    }
                });
            }
        })
    } else {
        removePermissionRow(delEl);
    }
}

$(document).ready(function() {
    // add.html
    var birthday = $('#profile-birthday').val();
    if (birthday) {
        $('#user-birthday').datetimepicker({
            useCurrent: false,
            date: birthday,
            format: 'YYYY-MM-DD',
            locale: 'vi'
        });
    }
    
    if ($('#permission-template')[0]){
        var permission_template = Handlebars.compile($('#permission-template').html());
    }
    perData.counter = $('#permission-container > tr').length;

    // add new row
    $('body').on('click', '#add-permission-top', function (e) {
        $('#add-permission-top').remove();
        var permission_html = permission_template({
            'scope': 'permissions[' + perData.counter + '][scope]',
            'permission': 'permissions[' + perData.counter + '][action]',
        });

        $('#permission-container').append(permission_html);
        $('<button type="button" class="btn btn-primary btn-permission" id="add-variants-bottom">Add new permission</button>').insertAfter('.permission-table');
        perData.counter++;
    });
    
    $('body').on('click', '#add-variants-bottom', function() {
        var permission_html = permission_template({
            'scope': 'permissions[' + perData.counter + '][scope]',
            'permission': 'permissions[' + perData.counter + '][action]',
        });
        $('#permission-container').find('#add-variants-bottom').before(permission_html);
        $(permission_html).insertAfter('.permission-table tbody>tr:last');
        perData.counter++;
    });

    $('select[name=role_id]').change(function () {
        var role_id = $(this).val();
        if (role_id == 1 || role_id == '') {
            // admin user, hide permission group
            $('.permission-group').hide();
        } else  if (role_id == 2) {
            $('.permission-group').show();
        }
    });

    $('#create-user-btn').click(function() {
        var validateResult = $('#create-user-form').parsley().validate();
        if (validateResult) {
            $.ajax({
                type: "POST",
                url: $('#create-user-form').attr('action'),
                data: $('#create-user-form').serialize(),
        
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
    
    // select display fields
    $('#settings-submit-btn').click(function() {
        var elems = Array.prototype.slice.call($('#setting-form').find('input[type="checkbox"]'));
        elems.forEach(function (ele) {
            if (ele.checked) {
                $('.' + ele.name).removeClass('hidden');
            } else {
                $('.' + ele.name).addClass('hidden');
            }
        });
        $('#setting-modal').modal('hide');
    });

    $('#setting-close-btn').click(function() {
        // reset form before close
        $('#setting-form')[0].reset();
    });

    $('#filter-refresh-btn').click(function() {
        $('#filter-form')[0].reset();
    });

    $('body').on('change', '.select-scope', function() {
        var elems = Array.prototype.slice.call($('#permission-container').find('.select-scope'));
        elems.forEach(function (ele) {
            if (ele.name !== $(this).name && ele.className.indexOf('parsley') > 0) {
                $('select[name="'+ ele.name +'"]').parsley().validate();
            }
        });
    });
});

window.Parsley.addValidator('notDuplicateScope', {
    validateString: function(value, requirement , parsleyField) {
        var currentClass = parsleyField.$element[0].className.split(' ')[0];
        var elems = Array.prototype.slice.call($('#permission-container').find('.' + currentClass));
        var selectedValues = [];
        elems.forEach(function (ele) {
            if (ele.name !== parsleyField.$element[0].name) {
                var selected = $('select[name="'+ ele.name +'"] :selected').val();
                selectedValues.push(selected);
            }
        });
        return selectedValues.indexOf(value) < 0;
    },
    messages: {
        en: 'Duplicate scope',
    }
});

