var ajaxing = false;
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

    $('#candidate-name').select2({
        ajax: {
            url: '/tvms/orders/search-candidate',
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
        placeholder: 'Search for a candidate',
        minimumInputLength: 1,
        allowClear: true,
        theme: "bootstrap",
        language: {
            noResults: function() {
                return "Không tìm thấy kết quả";
            }
        }
    });

    $('.submit-order-btn').click(function () {
        var validateResult = $('#add-order-form').parsley().validate();
        if (validateResult) {
            $('#add-order-form')[0].submit();
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
        url: '/tvms/orders/recommendCandidate',
        data: {
            ageFrom: $('#age-from').val(),
            ageTo: $('#age-to').val(),
            height: $('#height').val() ? $('#height').val() : 0,
            weight: $('#weight').val() ? $('#weight').val() : 0,
            job: $('#job-id').val()
        },
        success: function(resp) {
            $('#recommend-container').empty();
            
            var candidates = resp.candidates;

            $.each(candidates, function(index, value) {
                if (perData.selected.indexOf(value.id) >= 0) {
                    candidates.splice(index, 1);
                }
            });
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

function viewCandidate(candidateId, permission) {
    if (!candidateId) {
        candidateId = $('#candidate-name').val();
    }
    if (permission == 1) {
        // read-only
        window.open('/tvms/students/view/' + candidateId, '_blank');
    } else {
        window.open('/tvms/students/info/' + candidateId, '_blank');
    }
}

function addCandidate() {
    var candidateId = parseInt($('#candidate-name').val());
    if (perData.selected.indexOf(candidateId) >= 0) {
        alert('You have already selected this student. Please choose another candidate!');
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
        url: '/tvms/orders/getCandidate',
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
                    'age': resp.age
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
    $('<button type="button" class="btn btn-success" id="set-interview-result-btn" onclick="setInterviewResult('+rowIdArr[rowIdArr.length-1]+')">Submit</button>').insertBefore('#close-result-modal-btn');
    
    // show modal
    $('#set-pass-modal').modal('toggle');
}

function setInterviewResult(rowId) {
    // validate form
    var validateResult = $('#set-pass-form').parsley().validate();
    if (validateResult) {
        $('#row-candidate-'+rowId).find('.result-text').html($('#result option:selected').html());
        $('#row-candidate-'+rowId).find('.interviewResult').val($('#result').val()).trigger('change');

        $('#row-candidate-'+rowId).find('.interviewDesc').val($('#description').val());

        // show edit doc button when pass interview
        if ($('#result').val() == '1') {
            $('#row-candidate-'+rowId).find('.edit-doc').removeClass('hidden');
        } else {
            $('#row-candidate-'+rowId).find('.edit-doc').addClass('hidden');
        }
        $('#set-pass-modal').modal('toggle');
    }
}

function deleteCandidate(delEl, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Remove candidate',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.value) {
                var rowIdArr = $(delEl).closest('.row-rec').attr('id').split('-');
                var rowId = rowIdArr[rowIdArr.length-1];

                $.ajax({
                    type: 'POST',
                    url: '/tvms/orders/deleteCandidate',
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
            var classArr = inputField[i-idField.length].className.split(' ');
            inputField[i-idField.length].name = 'students[' + i + '][' + classArr[classArr.length-1] + ']';
        }

        classArr = selectField[i].className.split(' ');
        selectField[i].name = 'students[' + i + '][_joinData][' + classArr[classArr.length-1] + ']';

        classArr = textField[i].className.split(' ');
        textField[i].name = 'students[' + i + '][_joinData][' + classArr[classArr.length-1] + ']';
    }
}

function editDoc(candidateId, permission) {
    if (permission == 1) {
        // read-only
        window.open('/tvms/students/view/' + candidateId + '#tab_content4', '_blank');
    } else {
        window.open('/tvms/students/info/' + candidateId + '#tab_content4', '_blank');
    }
}
