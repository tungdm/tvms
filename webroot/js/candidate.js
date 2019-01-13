var data = {};
data.consultantCounter = 0;
$(document).ready(function () {
    data.consultantCounter = $('#consultant-container > tr').length;
    $('#source').change(function (e) {
        if ($(this).val() == '1') {
            $('.facebook-group').removeClass('hidden');
            $('#fb-name').val('');
            $('#fb-link').val('');
        } else {
            $('.facebook-group').addClass('hidden');
        }
    });

    $('.submit-candidate-btn').click(function () {
        var validateResult = $('#add-candidate-form').parsley().validate();
        if (validateResult) {
            // submit form
            $('#add-candidate-form').submit();
        }
    });

    // init switchery
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function (html) {
        var switchery = new Switchery(html, {
            size: 'medium'
        });
    });
})

function showAddConsultantModal() {
    // reset modal
    $('#consultant-form')[0].reset();
    $('#modal-consultant-user').val('').trigger('change');
    $('#consultant-form').parsley().reset();

    $('#submit-consultant-btn').remove();
    $('<button type="button" class="btn btn-success" id="submit-consultant-btn" onclick="addConsultant()">Hoàn tất</button>').insertBefore('#close-consultant-modal-btn');
    // show modal
    $('#consultant-modal').modal('toggle');
}

function addConsultant() {
    var validateResult = $('#consultant-form').parsley().validate();
    if (validateResult) {
        var source = $("#consultant-template").html();
        var template = Handlebars.compile(source);
        var html = template({
            'counter': data.consultantCounter,
            'consultantDate': $('#modal-consultant-date').val(),
            'consultantUser': $('#modal-consultant-user option:selected').html(),
            'consultantUserId': $('#modal-consultant-user').val(),
            'noteRaw': $('#modal-note').val(),
            'note': $('#modal-note').val().replace(/\r?\n/g, '<br />'),
        });
        data.consultantCounter++;
        $('#consultant-container').append(html);
        $('#consultant-modal').modal('toggle');
    }
}

function showEditConsultantModal(el) {
    var row = $(el).closest('.cons-rec');
    var rowNum = $(row).attr('id').split('-')[1];
    $('#modal-consultant-date').val($(row).find('.consultant_date').val());
    $('#modal-consultant-user').val($(row).find('.user_id').val()).trigger('change');
    $('#modal-note').val($(row).find('.note').val());

    // show modal
    $('#consultant-modal').modal('toggle');

    $('#submit-consultant-btn').remove();
    $('<button type="button" class="btn btn-success" id="submit-consultant-btn" onclick="editConsultant(' + rowNum + ')">Hoàn tất</button>').insertBefore('#close-consultant-modal-btn');
}

function editConsultant(rowNum) {
    var validateResult = $('#consultant-form').parsley().validate();
    if (validateResult) {
        $('#row-' + rowNum).find('.consultantDate').html($('#modal-consultant-date').val());
        $('#row-' + rowNum).find('.consultant_date').val($('#modal-consultant-date').val());
        $('#row-' + rowNum).find('.user_id').val($('#modal-consultant-user').val());
        $('#row-' + rowNum).find('.consultantUser').html($('#modal-consultant-user option:selected').html());
        $('#row-' + rowNum).find('.note').val($('#modal-note').val());
        $('#row-' + rowNum).find('.notes').html($('#modal-note').val().replace(/\r?\n/g, '<br />'));
        $('#consultant-modal').modal('toggle');
    }
}

function deleteConsultant(delEl, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Xóa lịch tư vấn',
            text: "Bạn không thể hồi phục được thông tin nếu đã xóa!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#222d32',
            cancelButtonText: 'Đóng',
            confirmButtonText: 'Vâng, tôi muốn xóa!'
        }).then((result) => {
            if (result.value) {
                var rowNum = $(delEl).closest('.cons-rec').attr('id').split('-')[1];

                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/candidates/deleteConsultantNote',
                    data: {
                        'consultantId': $(delEl).closest('.cons-rec').find('.consultant_id').val()
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

function deleteRow(delEl) {
    $(delEl).closest('.cons-rec').remove();
    $('#consultant-container > tr').each(function (index) {
        $(this).find('.stt-col').html(index + 1);
        $(this).find('.consultant_id').attr('name', 'consultant_notes[' + index + '][id]');
        $(this).find('.consultant_date').attr('name', 'consultant_notes[' + index + '][consultant_date]');
        $(this).find('.user_id').attr('name', 'consultant_notes[' + index + '][user_id]');
        $(this).find('.note').attr('name', 'consultant_notes[' + index + '][note]');
    });
    data.consultantCounter--;
}