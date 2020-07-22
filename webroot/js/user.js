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
        var counter = Math.floor(i / 2);
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
            title: 'Xóa quyền của nhân viên',
            text: "Bạn không thể hồi phục được thông tin nếu đã xóa!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#ddd',
            cancelButtonText: 'Đóng',
            confirmButtonText: 'Vâng, tôi muốn xóa!'
        }).then((result) => {
            if (result.value) {
                // send ajax delete request to server
                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/users/deletePermission',
                    data: {
                        'id': $(delEl).closest('tr.row-permission').find('input').val()
                    },
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', getCsrfToken());
                    },
                    success: function (resp) {
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

$(document).ready(function () {
    // init switchery
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function (html) {
        var switchery = new Switchery(html, {
            size: 'medium'
        });
    });
    // init datetime picker
    var birthday = $('#birthday').val();
    $('#user-birthday').datetimepicker({
        useCurrent: false,
        date: moment(birthday, ['DD-MM-YYYY']),
        format: 'DD-MM-YYYY',
        locale: 'vi'
    });
    $('#user-birthday').on('dp.change', function (e) {
        $('#user-birthday input').parsley().validate();
    });

    if ($('#permission-template')[0]) {
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

        $('#permission-container').prepend(permission_html);
        perData.counter++;
    });

    $('body').on('change', '.select-group', function (e) {
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

    $('#create-user-btn').click(function () {
        var validateResult = $('#create-user-form').parsley().validate();
        if (validateResult) {
            if (ajaxing) {
                // still requesting
                return;
            }
            ajaxing = true;

            $.ajax({
                type: "POST",
                url: $('#create-user-form').attr('action'),
                data: $('#create-user-form').serialize(),

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
                        notice.get().click(function () {
                            notice.remove();
                        });
                    }
                },
                complete: function () {
                    ajaxing = false;
                }
            });
        }
    });

    $('#update-profile-btn').click(function () {
        var validateResult = $('#update-profile-form').parsley().validate();
        if (validateResult) {
            $('#update-profile-form')[0].submit();
        }
    });
    // select display fields
    $('#settings-submit-btn').click(function () {
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

    $('#setting-close-btn').click(function () {
        // reset form before close
        $('#setting-form')[0].reset();
    });

    $('#filter-refresh-btn').click(function () {
        $('#filter-form')[0].reset();
    });

    $('#change-password-btn').click(function () {
        var validateResult = $('#change-password-form').parsley().validate();
        if (validateResult) {
            if (ajaxing) {
                // still requesting
                return;
            }
            ajaxing = true;
            $('#change-password-overlay').removeClass('hidden');

            $.ajax({
                type: "POST",
                url: $('#change-password-form').attr('action'),
                data: $('#change-password-form').serialize(),

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
                        notice.get().click(function () {
                            notice.remove();
                        });
                    }
                },
                complete: function () {
                    ajaxing = false;
                    $('#change-password-overlay').addClass('hidden');
                }
            });
        }
    });

    $('#edit-permission-btn').click(function () {
        var validateResult = $('#edit-permission-form').parsley().validate();
        if (validateResult) {
            $.ajax({
                type: "POST",
                url: $('#edit-permission-form').attr('action'),
                data: $('#edit-permission-form').serialize(),
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
                        notice.get().click(function () {
                            notice.remove();
                        });
                    }
                }
            });
        }
    });

    // custom validator for select2
    window.Parsley.addValidator('notDuplicateScope', {
        validateString: function (value, requirement, parsleyField) {
            var currentClass = parsleyField.$element[0].className.split(' ')[0];
            var elems = Array.prototype.slice.call($('#permission-container').find('.' + currentClass));
            var selectedValues = [];
            elems.forEach(function (ele) {
                if (ele.name !== parsleyField.$element[0].name) {
                    var selected = $('select[name="' + ele.name + '"] :selected').val();
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
            success: function (resp) {
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
                    $('select[name="permissions[' + index + '][scope]"]').val(obj.scope);
                    $('select[name="permissions[' + index + '][action]"]').val(obj.action);
                }

                perData.counter = allPermissions.length;

                $('#edit-permission-modal').modal('toggle');
            },
            complete: function () {
                ajaxing = false;
                $('.overlay').addClass('hidden');
            }
        });
    }
}



