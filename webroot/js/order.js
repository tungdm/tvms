var perData = {};
perData.recommendCounter = 0;
perData.selectedCounter = 0;
perData.selected = [];

$(document).ready(function() {
    // init selected data
    $('.candidate-id').each(function() {
        perData.selected.push(parseInt($(this).find('input').val()));
        perData.selectedCounter++;
    });

    $('.limit-min').change(function() {
        var targetId = $(this).attr('less-than');
        $(targetId).attr('min', $(this).val());
    });

    $('.limit-max').change(function() {
        var targetId = $(this).attr('greater-than');
        $(targetId).attr('max', $(this).val());
    });

    $('#work-time').change(function() {
        var returnDate = calReturnDate($('#work-time').val(), $('#departure-date').val());
        setReturnDate(returnDate);
    });

    $('#departure-date-div').on('dp.change', function() {
        var returnDate = calReturnDate($('#work-time').val(), $('#departure-date').val());
        setReturnDate(returnDate);
    });

    $('#candidate-name').select2({
        ajax: {
            url: DOMAIN_NAME + '/orders/search-candidate',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                var processedOptions = $.map(data.items, function(obj, index) {
                    return {id: index, text: obj};
                });
                return {
                    results: processedOptions,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        placeholder: 'Tìm kiếm lao động phù hợp',
        minimumInputLength: 1,
        allowClear: true,
        theme: "bootstrap",
        language: {
            noResults: function() {
                return "Không tìm thấy kết quả";
            },
            searching: function() {
                return "Đang tìm kiếm...";
            },
            inputTooShort: function (args) {
                var remainingChars = args.minimum - args.input.length;
                var message = 'Vui lòng nhập ít nhất ' + remainingChars + ' kí tự';
                return message;
            },
        }
    });

    $('.submit-order-btn').click(function () {
        var validateResult = $('#add-order-form').parsley().validate();
        if (validateResult) {
            if (perData.selected.length == 0) {
                swal({
                    title: 'Danh sách ứng viên hiện đang bỏ trống',
                    text: "Xin vui lòng kiểm tra lại!",
                    type: 'error',
                });
                return;
            }
            $('#add-order-form')[0].submit();
        }
    });

    // init switchery
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function (html) {
        var switchery = new Switchery(html, {
            size: 'small'
        });
    });

    $('.js-switch').click(function(e) {
        if ($(this)[0].checked) {
            $(this).closest('tr').find('select').prop('disabled', false);
        } else {
            // clear select value
            $(this).closest('tr').find('select').prop('disabled', true);
        }
    });
});

function showAddCandidateModal() {
    // reset form in modal
    $('#candidate-name').val(null).trigger('change');

    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-candidate-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/orders/recommendCandidate',
        data: {
            ageFrom: $('#age-from').val(),
            ageTo: $('#age-to').val(),
            height: $('#height').val(),
            weight: $('#weight').val(),
            job: $('#job-id').val()
        },
        success: function(resp) {
            $('#recommend-container').empty();
            
            var candidates = resp.candidates;
            var removeIndexes = [];

            $.each(candidates, function(index, value) {
                if (perData.selected.indexOf(value.id) >= 0) {
                    removeIndexes.push(index);
                }
            });
            // remove duplicate candidate
            for (let index = removeIndexes.length-1; index >= 0; index--) {
                candidates.splice(removeIndexes[index], 1);
            }

            perData.recommendCounter = candidates.length;
            if (candidates.length > 0) {
                var source = $("#recommend-candidate-template").html();
                var template = Handlebars.compile(source);
                var html = template(candidates);
                $('#recommend-container').html(html);
    
                // init switchery
                var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                elems.forEach(function (html) {
                    var switchery = new Switchery(html, {
                        size: 'small'
                    });
                });
            }
            // show modal
            $('#add-candidate-modal').modal('toggle');
        }, 
        complete: function() {
            ajaxing = false;
            $('#list-candidate-overlay').addClass('hidden');
        }
    });
}

function viewCandidate(candidateId) {
    if (!candidateId) {
        candidateId = $('#candidate-name').val();
    }
    window.open(DOMAIN_NAME + '/students/view/' + candidateId, '_blank');
}

function addCandidate() {
    var candidateId = parseInt($('#candidate-name').val());
    if (!candidateId) {
        return;
    }
    if (perData.selected.indexOf(candidateId) >= 0) {
        alert('Bạn đã chọn lao động này. Vui lòng chọn một lao động khác.');
        return;
    }
    var elem = document.querySelector('#cdd-' + candidateId);
    if (elem != null) {
        return;
    }
    // get candidate info
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#add-candidate-modal-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/orders/getCandidate',
        data: {
            id: candidateId
        },
        success: function(resp) {
            if (resp) {
                perData.recommendCounter++;
                var source = $("#add-recommend-candidate-template").html();
                var template = Handlebars.compile(source);
                var html = template({
                    'row': perData.recommendCounter,
                    'counter': perData.recommendCounter - 1,
                    'id': resp.id,
                    'fullname': resp.fullname,
                    'gender': resp.gender,
                    'phone': resp.phone,
                    'age': resp.age,
                    'status': resp.status,
                });
                $('#recommend-container').append(html);
                var elem = document.querySelector('#cdd-' + resp.id);
                var init = new Switchery(elem, {
                    size: 'small'
                });
            }
        },
        complete: function() {
            ajaxing = false;
            $('#add-candidate-modal-overlay').addClass('hidden');
        }
    });
}

function selectCandidate() {
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    var datas = [];
    elems.forEach(function (e) {
        if (e.checked) {
            var data = [];
            var row = $(e).closest('.row-rec');
            data['row'] = perData.selectedCounter;
            data['id'] = row.find('#candidateid').val();
            data['fullname'] = row.find('#fullname').val();
            data['age'] = row.find('#age').val();
            data['gender'] = row.find('#gender').val();
            data['phone'] = row.find('#phone').val();
            data['status'] = row.find('#status').val();
            datas.push(data);

            perData.selectedCounter++;
            perData.selected.push(parseInt(data['id']));
        }
    });
    if (datas.length == 0) {
        return;
    }
    var source = $("#selected-candidate-template").html();
    var template = Handlebars.compile(source);
    var html = template(datas);
    $('#candidate-container').append(html);

    // close modal
    $('#add-candidate-modal').modal('toggle');
}

function setPassed(ele) {
    // reset modal
    $('#set-pass-form')[0].reset();
    // set value for select box
    $('#result').val($(ele).closest('.row-rec').find('.interviewResult').val()).trigger('change');
    $('#description').val($(ele).closest('.row-rec').find('.interviewDesc').val());

    var rowIdArr = $(ele).closest('.row-rec').attr('id').split('-');
    $('#set-interview-result-btn').remove();
    $('<button type="button" class="btn btn-success" id="set-interview-result-btn" onclick="setInterviewResult('+rowIdArr[rowIdArr.length-1]+')">Hoàn tất</button>').insertBefore('#close-result-modal-btn');
    
    // show modal
    $('#set-pass-modal').modal('toggle');
}

function calReturnDate(workTime, departureDate) {
    var duration = moment.duration(parseInt(workTime), 'years');
    return moment(departureDate).add(duration).format('YYYY-MM'); 
}

function setReturnDate(returnDate) {
    $('.return_date').each(function() {
        var interviewResult = $(this).closest('.row-rec').find('.result').val();
        if (interviewResult == '1') { // passed
            $(this).val(returnDate);
        }
    })
}

function setInterviewResult(rowId) {
    // validate form
    var validateResult = $('#set-pass-form').parsley().validate();
    if (validateResult) {
        $('#row-candidate-'+rowId).find('.result-text').html($('#result option:selected').html());
        $('#row-candidate-'+rowId).find('.interviewResult').val($('#result').val()).trigger('change');

        $('#row-candidate-'+rowId).find('.interviewDesc').val($('#description').val());

        // update result counter
        resultCounter = updateResultCounter();
        if (resultCounter == perData.selected.length) {
            $('input[name="status"]').val('4');
        } else {
            $('input[name="status"]').val('');
        }

        // show edit doc button when pass interview
        if ($('#result').val() === '1') {
            $('#row-candidate-'+rowId).find('.result-text').addClass('bold-text');
            // set return date
            var returnDate = calReturnDate($('select[name="work_time"]').val(), $('input[name="departure_date"]').val());
            $('#row-candidate-'+rowId).find('.return_date').val(returnDate);
            if ($('#row-candidate-'+rowId).find('.status').val() != '3') {
                $('#row-candidate-'+rowId).find('.status').val('3');
            }
        } else {
            $('#row-candidate-'+rowId).find('.result-text').removeClass('bold-text');

            // set return date
            $('#row-candidate-'+rowId).find('.return_date').val('');
            if ($('#row-candidate-'+rowId).find('.status').val() == '3') {
                $('#row-candidate-'+rowId).find('.status').val('2');
            }
        }
        
        $('#set-pass-modal').modal('toggle');
    }
}

function deleteCandidate(delEl, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Xóa ứng viên',
            text: "Bạn không thể hồi phục được thông tin nếu đã xóa!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#222d32',
            cancelButtonText: 'Đóng',
            confirmButtonText: 'Vâng, tôi muốn xóa!'
        }).then((result) => {
            if (result.value) {
                var rowIdArr = $(delEl).closest('.row-rec').attr('id').split('-');
                var rowId = rowIdArr[rowIdArr.length-1];

                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/orders/deleteCandidate',
                    data: {
                        'id': $('#interview-'+rowId+'-id').find('input').val(),
                    },
                    success: function(resp){
                        swal({
                            title: resp.alert.title,
                            text: resp.alert.message,
                            type: resp.alert.type
                        })
                        if (resp.status == 'success') {
                            deleteRow(delEl, rowId);
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
    $(delEl).closest('tr.row-rec').remove();
    if (hiddenId) {
        // case: remove record exists in database
        var delId = parseInt($('#candidate-'+hiddenId+'-id').find('input').val());
        $('#candidate-'+hiddenId+'-id').remove();
        $('#interview-'+hiddenId+'-id').remove();
    } else {
        var delId = parseInt($(delEl).closest('tr.row-rec').find('.id').val());
    }
    perData.selectedCounter--;

    // delete in selected array
    perData.selected.splice(perData.selected.indexOf(delId), 1);
    
    var trows = $('#candidate-container > tr');
    var idField = $('.candidate-id').find('input');
    var interviewIdField = $('.interview-id').find('input');
    var inputField = $('#candidate-container').find('input');
    var textField = $('#candidate-container').find('textarea');
    var selectField = $('#candidate-container').find('select');
    var sttField = $('#candidate-container').find('.stt-col');

    for (var i = 0; i < sttField.length; i++) {
        sttField[i].innerText = i + 1;
        trows[i].id = 'row-candidate-' + i;
        if (hiddenId) {
            $('.candidate-id')[i].id = 'candidate-' + i + '-id';
            $('.interview-id')[i].id = 'interview-' + i + '-id';
            idField[i].name = 'students[' + i + '][id]';
            interviewIdField[i].name = 'students[' + i + '][_joinData][id]';
        } else {
            if (i < idField.length) {
                continue;
            }
            $('#row-candidate-'+i).find('.id').attr('name', 'students[' + i + '][id]');
            $('#row-candidate-'+i).find('.status').attr('name', 'students[' + i + '][_joinData][status]');
            $('#row-candidate-'+i).find('.return_date').attr('name', 'students[' + i + '][return_date]');
        }

        classArr = selectField[i].className.split(' ');
        selectField[i].name = 'students[' + i + '][_joinData][' + classArr[classArr.length-1] + ']';

        classArr = textField[i].className.split(' ');
        textField[i].name = 'students[' + i + '][_joinData][' + classArr[classArr.length-1] + ']';
    }
}

function editDoc(candidateId) {
    window.open(DOMAIN_NAME + '/students/view/' + candidateId + '#tab_content4', '_blank');
}

function viewGuild(guildId) {
    var overlayId = '#list-order-overlay';
    globalViewGuild(guildId, overlayId);
}

function viewCompany(companyId) {
    var overlayId = '#list-order-overlay';
    globalViewCompany(companyId, overlayId);
}

function updateResultCounter() {
    result = 0;
    $('.interviewResult').each(function() {
        if ($(this).val() !== "0") {
            result++;
        }
    });
    return result;
}

function showExportModal(orderId) {
    var source = $("#export-template").html();
    var template = Handlebars.compile(source);
    var html = template({
        'orderId': orderId
    });
    $('#export-container').html(html);

    // show modal
    $('#export-order-modal').modal('toggle');
}

function settings() {

    // show modal
    $('#setting-modal').modal('toggle');
}