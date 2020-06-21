var data = {};
data.counter = 0;

$(document).ready(function () {
    data.counter = $('#add-company-container > tr').length;

    $('#guilds-tabs').tabCollapse();
    var focusTab = window.location.hash;
    if (focusTab) {
        $('#guilds-tabs a[href="' + focusTab + '"]').tab('show');
    }

    $('.submit-guild-btn').click(function () {
        var validateResult = $('#guild-form').parsley().validate();
        if (validateResult) {
            $('#guild-form')[0].submit();
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