var perData = {};
perData.dayOff = [];

$(document).ready(function () {
    $('.holidayId').each(function () {
        perData.dayOff.push($(this).closest('.row-rec').find('.dayOffDate').val());
    });


    $('.submit-schedule-btn').click(function () {
        var validateResult = $('#schedule-form').parsley().validate();
        if (validateResult) {
            $('#schedule-form')[0].submit();
        }
    });
})

function showAddDayOffModal() {
    $('#day-off-form')[0].reset();
    $('#modal-type').val(null).trigger('change');
    $('#day-off-form').parsley().reset();
    // replace edit-btn with add-btn 
    $('#add-day-off-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-day-off-btn" onclick="addDayOff()">Hoàn tất</button>').insertBefore('#close-modal-btn');
    // show modal
    $('#day-off-modal').modal('toggle');
}

function addDayOff() {
    var startDateTxt = $('#start-date-div').html();
    var startDateVal = moment(startDateTxt, 'DD-MM-YYYY').format('YYYY-MM-DD');
    var endDateTxt = $('#end-date-div').html();
    var endDateVal = moment(endDateTxt, 'DD-MM-YYYY').format('YYYY-MM-DD');

    var dayOffTxt = $('#modal-dayoff').val();
    if (!dayOffTxt) {
        return;
    }
    if (perData.dayOff.indexOf(dayOffTxt) >= 0) {
        alert('Bạn đã chọn ngày này. Vui lòng chọn ngày nghỉ khác.');
        return;
    }
    dayOffVal = moment(dayOffTxt, 'DD-MM-YYYY').format('YYYY-MM-DD');
    if (!moment(dayOffVal).isBetween(startDateVal, endDateVal, 'days', '[]')) {
        alert('Vui lòng chọn ngày nghỉ trong thời gian từ ' + startDateTxt + ' đến ' + endDateTxt + '.');
        return;
    }
    var validateResult = $('#day-off-form').parsley().validate();
    if (validateResult) {
        var source = $("#day-off-template").html();
        var template = Handlebars.compile(source);
        var html = template({
            'counter': perData.dayOff.length,
            'dayOffTxt': dayOffTxt,
            'dayOffVal': moment($('#modal-dayoff').val(), 'DD-MM-YYYY').format('YYYY-MM-DD'),
            'dayOffTypeTxt': $('#modal-type option:selected').html(),
            'dayOffTypeVal': $('#modal-type').val(),
        });
        perData.dayOff.push(dayOffTxt);
        $('#day-off-container').append(html);
        // Update end date
        if (isWeekday(dayOffVal)) {
            var newEndDate = moment(endDateVal).add(1, 'days').format('YYYY-MM-DD');
            while (isWeekend(newEndDate)) {
                newEndDate = moment(newEndDate).add(1, 'days').format('YYYY-MM-DD');
            }
            newEndDate = moment(newEndDate).format('DD-MM-YYYY');
            $('#end-date-div').html(newEndDate);
            $('#end-date').val(newEndDate);
        }
        // hide modal
        $('#day-off-modal').modal('toggle');
    }
}

function showEditDayOffModal(ele) {
    var dayOff = $(ele).closest('.row-rec').find('.dayOffDate').val();
    $('#modal-type').val($(ele).closest('.row-rec').find('.dayOffType').val()).trigger('change');
    $('#modal-dayoff').val(dayOff);
    var rowIdArr = $(ele).closest('.row-rec').attr('id').split('-');
    // replace add-btn with edit-btn
    $('#add-day-off-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-day-off-btn" onclick="editDayOff(' + rowIdArr[rowIdArr.length - 1] + ', \'' + dayOff + '\')">Hoàn tất</button>').insertBefore('#close-modal-btn');

    // show modal
    $('#day-off-modal').modal('toggle');
}

function editDayOff(rowId, selectedDay) {
    var startDateTxt = $('#start-date-div').html();
    var startDateVal = moment(startDateTxt, 'DD-MM-YYYY').format('YYYY-MM-DD');
    var endDateTxt = $('#end-date-div').html();
    var endDateVal = moment(endDateTxt, 'DD-MM-YYYY').format('YYYY-MM-DD');
    var selectedDayVal = moment(selectedDay, 'DD-MM-YYYY').format('YYYY-MM-DD');

    var dayOffTxt = $('#modal-dayoff').val();
    if (!dayOffTxt) {
        return;
    }
    if (dayOffTxt != selectedDay && perData.dayOff.indexOf(dayOffTxt) >= 0) {
        alert('Bạn đã chọn ngày này. Vui lòng chọn ngày nghỉ khác.');
        return;
    }
    dayOffVal = moment(dayOffTxt, 'DD-MM-YYYY').format('YYYY-MM-DD');
    if (!moment(dayOffVal).isBetween(startDateVal, endDateVal, 'days', '[]')) {
        alert('Vui lòng chọn ngày nghỉ trong thời gian từ ' + startDateTxt + ' đến ' + endDateTxt + '.');
        return;
    }
    var validateResult = $('#day-off-form').parsley().validate();
    if (validateResult) {
        if (dayOffTxt != selectedDay) {
            perData.dayOff.splice(perData.dayOff.indexOf(selectedDay), 1);
            perData.dayOff.push(dayOffTxt);
            // Update end date
            if (isWeekday(selectedDayVal) && isWeekend(dayOffVal)) {
                var newEndDate = moment(endDateVal).subtract(1, 'days').format('YYYY-MM-DD');
                while (isWeekend(newEndDate)) {
                    newEndDate = moment(newEndDate).subtract(1, 'days').format('YYYY-MM-DD');
                }
                newEndDate = moment(newEndDate).format('DD-MM-YYYY');
                $('#end-date-div').html(newEndDate);
                $('#end-date').val(newEndDate);
            } else if (isWeekend(selectedDayVal) && isWeekday(dayOffVal)) {
                var newEndDate = moment(endDateVal).add(1, 'days').format('YYYY-MM-DD');
                while (isWeekend(newEndDate)) {
                    newEndDate = moment(newEndDate).add(1, 'days').format('YYYY-MM-DD');
                }
                newEndDate = moment(newEndDate).format('DD-MM-YYYY');
                $('#end-date-div').html(newEndDate);
                $('#end-date').val(newEndDate);
            }

        }
        $('#row-day-off-' + rowId).find('.holidayTxtDiv').html(dayOffTxt);
        $('#row-day-off-' + rowId).find('.dayOffDate').val(dayOffTxt);
        $('#row-day-off-' + rowId).find('.holidayType').html($('#modal-type option:selected').html());
        $('#row-day-off-' + rowId).find('.dayOffType').val($('#modal-type').val());

        // hide modal
        $('#day-off-modal').modal('toggle');
    }
}

function deleteDayOff(delEl, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Xóa ngày nghỉ',
            text: "Bạn không thể hồi phục được thông tin nếu đã xóa!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#222d32',
            cancelButtonText: 'Đóng',
            confirmButtonText: 'Vâng, tôi muốn xóa!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/orders/deleteHoliday',
                    data: {
                        'holidayId': $(delEl).closest('.row-rec').find('.holidayId').val()
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

function refreshEndDate(scheduleId) {
    swal({
        title: 'Cập nhật thời gian dự kiến',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#222d32',
        cancelButtonText: 'Đóng',
        confirmButtonText: 'Vâng, tôi muốn cập nhật!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: 'POST',
                url: DOMAIN_NAME + '/orders/refreshScheduleEndDate/' + scheduleId,
                success: function (resp) {
                    swal({
                        title: resp.alert.title,
                        text: resp.alert.message,
                        type: resp.alert.type
                    })
                    if (resp.status == 'success') {
                        $('#end-date-div').html(resp.newEndDate);
                    }
                }
            });
        }
    })
}

function deleteRow(delEl) {
    var endDateTxt = $('#end-date-div').html();
    var endDateVal = moment(endDateTxt, 'DD-MM-YYYY').format('YYYY-MM-DD');

    var dayOffTxt = $(delEl).closest('.row-rec').find('.dayOffDate').val();
    var dayOffVal = moment(dayOffTxt, 'DD-MM-YYYY').format('YYYY-MM-DD');
    // update end date
    if (isWeekday(dayOffVal)) {
        var newEndDate = moment(endDateVal).subtract(1, 'days').format('YYYY-MM-DD');
        while (isWeekend(newEndDate)) {
            newEndDate = moment(newEndDate).subtract(1, 'days').format('YYYY-MM-DD');
        }
        newEndDate = moment(newEndDate).format('DD-MM-YYYY');
        $('#end-date-div').html(newEndDate);
        $('#end-date').val(newEndDate);
    }
    // delete in selected array
    perData.dayOff.splice(perData.dayOff.indexOf(dayOffTxt), 1);
    // remove DOM
    $(delEl).closest('.row-rec').remove();
    $('#day-off-container > tr').each(function (index) {
        $(this).find('.stt-col').html(index + 1);
        $(this).find('.dayOffDate').attr('name', 'holidays[' + index + '][date]');
        $(this).find('.dayOffType').attr('name', 'holidays[' + index + '][type]');
    });
}

function addBussinessDay(startDate, numOfDay) {
    date = moment(startDate);
    while (numOfDay > 0) {
        if (date.isoWeekday() !== 6 && date.isoWeekday() !== 7) {
            numOfDay -= 1;
        }
        date = date.add(1, 'days');
    }
    return date.format('DD-MM-YYYY');
}

function isWeekend(date) {
    return moment(date).isoWeekday() == 6 || moment(date).isoWeekday() == 7;
}

function isWeekday(date) {
    return moment(date).isoWeekday() !== 6 && moment(date).isoWeekday() !== 7;
}