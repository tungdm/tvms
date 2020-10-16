var data = {};
data.counter = 0;
data.adminCompanyCounter = 0;

$(document).ready(function () {
    data.counter = $('#add-company-container > tr').length;
    data.adminCompanyCounter = $('#admin-company-container > tr').length;

    if ($('#guilds-tabs')[0]) {
        $('#guilds-tabs').tabCollapse();
        var focusTab = window.location.hash;
        if (focusTab) {
            $('#guilds-tabs a[href="' + focusTab + '"]').tab('show');
        }
    }

    $('.submit-guild-btn').click(function () {
        var validateResult = $('#guild-form').parsley().validate();
        if (validateResult) {
            $('#guild-form')[0].submit();
        }
    });

    $('.submit-installment-btn').click(function () {
        var validateResult = $('#add-installment-form').parsley().validate();
        if (validateResult) {
            $('#add-installment-form')[0].submit();
        }
    });

    // custom validator for select2
    window.Parsley.addValidator('notDuplicateCompany', {
        validateString: function (value, requirement, parsleyField) {
            var currentClass = parsleyField.$element[0].className.split(' ')[0];
            var elems = Array.prototype.slice.call($('.company-container').find('.' + currentClass));
            var selectedValues = [];
            elems.forEach(function (ele) {
                if ($(ele).hasClass('div-container')) {
                    var selected = $(ele).html();
                } else {
                    if (ele.name !== parsleyField.$element[0].name) {
                        var selected = $('select[name="' + ele.name + '"] :selected').val();
                    }
                }
                selectedValues.push(selected);

            });
            return selectedValues.indexOf(value) < 0;
        },
        messages: {
            vn: 'Thông tin bị trùng',
        }
    });

    window.Parsley.addValidator('notDuplicateAdminCompany', {
        validateString: function (value, requirement, parsleyField) {
            var selectedValues = [];
            $('.admin-company').each(function (index) {
                if ($(this).val() != $('#modal-flag').val()) {
                    selectedValues.push($(this).val());
                }
            });
            return selectedValues.indexOf(value) < 0;
        },
        messages: {
            vn: 'Thông tin bị trùng',
        }
    });

    // init switchery
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function (html) {
        size = 'small';
        if (html.classList.contains('medium-size')) {
            size = 'medium';
        }
        var switchery = new Switchery(html, {
            size: size
        });
    });
});

function viewGuild(guildId) {
    var overlayId = '#list-guild-overlay';
    globalViewGuild(guildId, overlayId);
}

function showAddGuildModal() {
    // reset form
    $('#add-guild-form')[0].reset();
    $('#add-guild-form').parsley().reset();
    // clear container
    $('.company-container').empty();
    // reset counter
    data.counter = 0;
    // show modal
    $('#add-guild-modal').modal('toggle');
}

function showEditGuildModal(guildId) {
    $('.company-container').empty();
    $('#edit-guild-form')[0].reset();

    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    // $('#list-guild-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/guilds/edit',
        data: { id: guildId },
        success: function (resp) {
            // reset form
            $('#edit-guild-form').parsley().reset();

            // fill data to edit form
            $('#edit-id').val(resp['id']);
            $('#edit-name-romaji').val(resp['name_romaji']);
            $('#edit-name-kanji').val(resp['name_kanji']);
            $('#edit-address-romaji').val(resp['address_romaji']);
            $('#edit-address-kanji').val(resp['address_kanji']);
            $('#edit-phone-vn').val(resp['phone_vn']);
            $('#edit-phone-jp').val(resp['phone_jp']);

            $('#edit-deputy-name-romaji').val(resp['deputy_name_romaji']);
            $('#edit-deputy-name-kanji').val(resp['deputy_name_kanji']);
            $('#edit-license-number').val(resp['license_number']);
            if (resp['signing_date']) {
                $('#edit-signing-date').val(moment(resp['signing_date']).format('DD-MM-YYYY'));
            }
            $('#edit-subsidy').val(resp['subsidy']);

            data.counter = resp.companies.length;
            var source = $("#edit-company-template").html();
            var template = Handlebars.compile(source);
            var html = template(resp.companies);
            $('#edit-company-container').html(html);

            if (resp.companies.length != 0) {
                resp.companies.forEach(function (company, index) {
                    $('select[name="companies[' + index + '][id]"').val(company.id);
                });
            }

            // toggle modal
            $('#edit-guild-modal').modal('toggle');
        },
        complete: function () {
            ajaxing = false;
            // $('#list-guild-overlay').addClass('hidden');
        }
    });
};

function addCompany(eleId) {
    var source = $("#company-template").html();
    var template = Handlebars.compile(source);
    var html = template({
        "counter": data.counter
    });
    data.counter++;
    $('#' + eleId).prepend(html);
}

function deleteCompany(delEl, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Xóa công ty tiếp nhận',
            text: "Bạn không thể hồi phục được thông tin nếu đã xóa!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#222d32',
            cancelButtonText: 'Đóng',
            confirmButtonText: 'Vâng, tôi muốn xóa!'
        }).then((result) => {
            if (result.value) {
                var companySelect = $(delEl).closest('.row-company').find('.companyId');
                var deleteCompanyName = companySelect[0][companySelect[0].selectedIndex].text;
                var deleteCompanyId = companySelect.val();
                var recordId = $(delEl).closest('.row-company').find('.recordId').val();
                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/guilds/deleteGuildCompany',
                    data: {
                        'recordId': recordId
                    },
                    success: function (resp) {
                        swal({
                            title: resp.alert.title,
                            text: resp.alert.message,
                            type: resp.alert.type
                        })
                        if (resp.status == 'success') {
                            deleteRow(delEl);
                        }
                    }
                });
            }
        });
    } else {
        deleteRow(delEl);
    }
}

function deleteRow(delEl, hiddenId) {
    // remove DOM
    $(delEl).closest('.row-company').remove();
    data.counter--;
    $('.company-container > tr').each(function (index) {
        $(this).find('.stt-col').html(index + 1);
        $(this).find('.companyId').attr('name', `companies[${index}][id]`);
        $(this).find('.recordId').attr('name', `companies[${index}][_joinData][id]`);
    });
}

function recoverCompany(ele, recordId, deletedCompanyId) {
    swal({
        title: 'Phục hồi công ty tiếp nhận',
        text: "Dữ liệu giữa công ty tiếp nhận với nghiệp đoàn sẽ được phục hồi",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#222d32',
        cancelButtonText: 'Đóng',
        confirmButtonText: 'Vâng, tiếp tục!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: 'POST',
                url: DOMAIN_NAME + '/guilds/recoverCompany',
                data: {
                    'recordId': recordId
                },
                success: function (resp) {
                    swal({
                        title: resp.alert.title,
                        text: resp.alert.message,
                        type: resp.alert.type
                    })
                    if (resp.status == 'success') {
                        // remove highlight record
                        $(ele).closest('.row-company').find('.stt-col').removeClass('deletedRecord');

                        var source = $("#recover-company-template").html();
                        var template = Handlebars.compile(source);
                        var counter = parseInt($(ele).closest('.row-company').find('.stt-col').html()) - 1;
                        var html = template({
                            "counter": counter,
                            "recordId": recordId,
                        });
                        $(ele).closest('.row-company').find('.companyName').html(html);
                        $('select[name="companies[' + counter + '][id]"').val(deletedCompanyId);
                        // add delete button
                        $(ele).closest('.row-company').find('.actions').html('<a href="javascript:;" onclick="deleteCompany(this, true)"><i class="fa fa-2x fa-remove" style="font-size: 2.3em;"></i></a>');
                    }
                }
            });
        }
    });
}


function showAddAdminCompany() {
    resetAdminCompanyForm();
    // replace submit btn
    $('#submit-admin-company-btn').remove();
    $('<button type="button" class="btn btn-success" id="submit-admin-company-btn" onclick="addAdminCompany()">Hoàn tất</button>').insertBefore('#close-modal-btn');
    // show modal
    toggleModal('admin-company-modal');
}


function resetAdminCompanyForm() {
    $('#admin-company-form')[0].reset();
    $('#modal-flag').val(null);
    $('#modal-admin-company').val(null).trigger('change');
    $('#admin-company-form').parsley().reset();
}

function addAdminCompany() {
    var validateResult = $('#admin-company-form').parsley().validate();
    if (validateResult) {
        var adminCompanyHtml = createAdminCompanyTemplate(data.adminCompanyCounter);
        $('#admin-company-container').prepend(adminCompanyHtml);
        // close modal
        toggleModal('admin-company-modal');
        resetAdminCompanyForm();
        data.adminCompanyCounter++;
    }
}

function createAdminCompanyTemplate(counter) {
    var template = Handlebars.compile($('#admin-company-template').html());
    var subsidyTxt = $('#modal-subsidy-txt').val();
    var firstThreeTxt = $('#modal-first-three-years-fee-txt').val();
    var twoLaterTxt = $('#modal-two-years-later-fee-txt').val();
    var preTrainingTxt = $('#modal-pre-training-fee-txt').val();
    return template({
        'counter': counter,
        'adminCompanyAlias': $('#modal-admin-company option:selected').html(),
        'adminCompany': `admin_companies[${counter}][id]`,
        'adminCompanyId': $('#modal-admin-company').val(),

        'subsidyTxt': subsidyTxt ? `${subsidyTxt}¥/tháng` : '-',
        'subsidy': `admin_companies[${counter}][_joinData][subsidy]`,
        'subsidyVal': $('#modal-subsidy').val(),

        'firstThreeTxt': firstThreeTxt ? `${firstThreeTxt}¥` : '-',
        'firstThree': `admin_companies[${counter}][_joinData][first_three_years_fee]`,
        'firstThreeVal': $('#modal-first-three-years-fee').val(),

        'twoLaterTxt': twoLaterTxt ? `${twoLaterTxt}¥` : '-',
        'twoLater': `admin_companies[${counter}][_joinData][two_years_later_fee]`,
        'twoLaterVal': $('#modal-two-years-later-fee').val(),

        'preTrainingTxt': preTrainingTxt ? `${preTrainingTxt}¥` : '-',
        'preTraining': `admin_companies[${counter}][_joinData][pre_training_fee]`,
        'preTrainingVal': $('#modal-pre-training-fee').val(),
    });
}

function showEditAdminCompanyModal(ele) {
    resetAdminCompanyForm();
    var $row = $(ele).closest('.row-admin-company');
    var rowIdArr = $row.attr('id').split('-');
    var adminCompanyId = $row.find('.admin-company').val();

    $('#modal-flag').val(adminCompanyId);

    $('#modal-admin-company').val(adminCompanyId).trigger('change');

    $('#modal-subsidy-txt').val(numberWithCommas($row.find('.subsidy').val()));
    $('#modal-subsidy').val($row.find('.subsidy').val());

    $('#modal-first-three-years-fee-txt').val(numberWithCommas($row.find('.first-three').val()));
    $('#modal-first-three-years-fee').val($row.find('.first-three').val());

    $('#modal-two-years-later-fee-txt').val(numberWithCommas($row.find('.two-later').val()));
    $('#modal-two-years-later-fee').val($row.find('.two-later').val());

    $('#modal-pre-training-fee-txt').val(numberWithCommas($row.find('.pre-training').val()));
    $('#modal-pre-training-fee').val($row.find('.pre-training').val());

    // replace submit btn
    $('#submit-admin-company-btn').remove();
    $(`<button type="button" class="btn btn-success" id="submit-admin-company-btn" onclick="editAdminCompany(${rowIdArr[rowIdArr.length - 1]})">Hoàn tất</button>`).insertBefore('#close-modal-btn');

    // show modal
    toggleModal('admin-company-modal');
}

function editAdminCompany(rowNum) {
    var validateResult = $('#admin-company-form').parsley().validate();
    if (validateResult) {
        var adminCompanyHtml = createAdminCompanyTemplate(rowNum);
        $(`#row-admin-company-${rowNum}`).replaceWith(adminCompanyHtml);
        // close modal
        toggleModal('admin-company-modal');
        resetAdminCompanyForm();
    }
}

function removeAdminCompany(ele, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Xóa công ty quản lý',
            text: "Bạn không thể hồi phục được thông tin nếu đã xóa!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#222d32',
            cancelButtonText: 'Đóng',
            confirmButtonText: 'Vâng, tôi muốn xóa!'
        }).then((result) => {
            if (result.value) {
                // send ajax delete request to server
                var rowIdArr = $(ele).closest('.row-admin-company').attr('id').split('-');
                var rowId = rowIdArr[rowIdArr.length - 1];
                var id = $(`#guild-admin-company-id-${rowId}`).find('input').val();
                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + `/guilds/deleteGuildAdminCompany/${id}`,
                    success: function (resp) {
                        swal({
                            title: resp.alert.title,
                            text: resp.alert.message,
                            type: resp.alert.type
                        })
                        if (resp.status == 'success') {
                            deleteAdminCompanyRow(ele, rowId);
                        }
                    }
                });
            }
        })
    } else {
        deleteAdminCompanyRow(ele);
    }
}

function deleteAdminCompanyRow(delEl, hiddenId) {
    // remove DOM
    $(delEl).closest('tr.row-admin-company').remove();
    if (hiddenId) {
        // case: remove hidden id field of record exists in database
        $(`#guild-admin-company-id-${hiddenId}`).remove();
    }
    data.adminCompanyCounter--;
    var idFields = $('.guild-admin-company-id').find('input');
    var $container = $('#admin-company-container');
    var rows = $('#admin-company-container > tr');
    var inputFields = $container.find('.form-control');

    if (rows.length >= 1) {
        for (var i = 0; i < rows.length; i++) {
            rows[i].id = `row-admin-company-${i}`;
            if (hiddenId) {
                $('.guild-admin-company-id')[i].id = `guild-admin-company-id-${i}`;
                idFields[i].name = `admin_companies[${i}][_joinData][id]`;
            }
        }
    }

    for (var i = 0; i < inputFields.length; i++) {
        inputFields[i].name = inputFields[i].name.replace(/(?<=\[)\d+(?=\])/g, Math.floor(i / 5));
    }
}

function toggleModal(id) {
    $(`#${id}`).modal('toggle');
}