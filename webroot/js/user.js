var ajaxing = false;
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
        var counter = Math.floor(i/2);
        if (i % 2 == 0) {
            selectFields[i].name = 'permissions[' + counter + '][scope]';
            selectFields[i].id = 'scope-' + counter;
        } else {
            selectFields[i].name = 'permissions[' + counter + '][action]';
            selectFields[i].id = 'permissions-' + counter;
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
    // init datetime picker
    var birthday = $('#birthday').val();
    $('#user-birthday').datetimepicker({
        useCurrent: false,
        date: birthday,
        format: 'YYYY-MM-DD',
        locale: 'vi'
    });
    $('#user-birthday').on('dp.change', function(e) {
        $('#user-birthday input').parsley().validate();
    });
    
    if ($('#permission-template')[0]){
        var permission_template = Handlebars.compile($('#permission-template').html());
    }
    perData.counter = $('#permission-container > tr').length;

    // add new row
    $('body').on('click', '#add-permission-top', function (e) {
        var permission_html = permission_template({
            'scope': 'permissions[' + perData.counter + '][scope]',
            'permission': 'permissions[' + perData.counter + '][action]',
            'counter': perData.counter
        });

        $('#permission-container').append(permission_html);
        perData.counter++;
    });

    $('body').on('change', '.select-group', function(e) {
        $(this).parsley().validate();
    });
    
    $('select[name=role_id]').change(function () {
        var role_id = $(this).val();
        if (role_id == 1 || role_id == '') {
            // user with admin role, hide permission group
            $('.permission-group').hide();

            if ($(this).hasClass('add-role')) {
                // add mode, remove all permission data
                $('#permission-container').empty();
            }
            
        } else {
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

    $('#change-password-btn').click(function() {
        var validateResult = $('#change-password-form').parsley().validate();
        if (validateResult) {
            $.ajax({
                type: "POST",
                url: $('#change-password-form').attr('action'),
                data: $('#change-password-form').serialize(),
        
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

    $('#edit-permission-btn').click(function() {
        var validateResult = $('#edit-permission-form').parsley().validate();
        if (validateResult) {
            $.ajax({
                type: "POST",
                url: $('#edit-permission-form').attr('action'),
                data: $('#edit-permission-form').serialize(),
                success: function (resp){
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

    // custom validator for select2
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
            en: 'Tài khoản đã có quyền này',
        }
    });
});

function showEditPerModal(userId, userRole) {
    if (userId) {
        if (ajaxing) {
            // still requesting
            return;
        }
        ajaxing = true;
        $('.overlay').removeClass('hidden');
        
        $.ajax({
            type: "GET",
            url: $('#edit-permission-form').attr('action'),
            data: {
                id: userId
            },
            success: function(resp) {
                var allPermissions = resp.permissions;

                $('input[name="id"]').val(userId);
                $('select[name="role_id"]').val(userRole).trigger('change');

                var source = $("#edit-permissions-template").html();
                var template = Handlebars.compile(source);
                var html = template(allPermissions);
                $('#permission-container').html(html);
                
                // set value for selectbox
                for (var index = 0; index < allPermissions.length; index++) {
                    var obj = allPermissions[index];
                    $('select[name="permissions['+index+'][scope]"]').val(obj.scope);
                    $('select[name="permissions['+index+'][action]"]').val(obj.action);
                }

                perData.counter = allPermissions.length;

                $('#edit-permission-modal').modal('toggle');
            },
            complete: function() {
                ajaxing = false;
                $('.overlay').addClass('hidden');
            }
        });
    }
}



