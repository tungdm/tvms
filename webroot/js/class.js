var testing = false;
var perData = {};
perData.selected = [];
perData.preAddCounter = 0;

var editor = new Simditor({
    textarea: $('.edittextarea'),
    toolbar: ['title', 'bold', 'italic', 'underline', 'color', '|',  'alignment', 'ol', 'ul', '|', 'table', 'link', 'image']
});

$(document).ready(function() {
    // init selected data
    $('.student-id').each(function() {
        perData.selected.push(parseInt($(this).find('input').val()));
    });

    $('#student-name').select2({
        ajax: {
            url: DOMAIN_NAME + '/jclasses/search-student',
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
                    return {id: obj.id, text: obj.fullname};
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
        placeholder: 'Tìm kiếm học viên',
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

    $('.submit-class-btn').click(function () {
        var validateResult = $('#add-class-form').parsley().validate();
        if (validateResult) {
            $('#add-class-form')[0].submit();
        }
    });

    $('#modal-class').change(function() {
        var classId = this.value;
        if (!this.value) {
            return;
        }
        if (ajaxing) {
            // still requesting
            return;
        }
        ajaxing = true;
        $('#change-class-modal-overlay').removeClass('hidden');
        $.ajax({
            type: 'GET',
            url: DOMAIN_NAME + '/jclasses/getClassTestInfo',
            data: {
                id: classId,
            },
            success: function(resp) {
                if (resp.info == "test") {
                    testing = true;
                } else {
                    testing = false;
                }
            },
            complete: function() {
                ajaxing = false;
                $('#change-class-modal-overlay').addClass('hidden');
            }
        });
    });
});

function showAddStudentModal() {
    // reset modal
    $('#student-name').val(null).trigger('change');
    $('#pre-add-student-container').empty();
    // reset selected counter
    perData.preAddCounter = 0;
    $('#add-student-modal').modal('toggle');
}

function preAddStudent() {
    var studentId = parseInt($('#student-name').val());
    if (!studentId) {
        return;
    }
    if (perData.selected.indexOf(studentId) >= 0) {
        alert('Bạn đã chọn học viên này. Vui lòng chọn một học viên khác!');
        return;
    }
    var elem = document.querySelector('#std-' + studentId);
    if (elem != null) {
        return;
    }
    // get student info
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#add-student-modal-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/jclasses/getStudent',
        data: {
            id: studentId
        },
        success: function(resp) {
            if (resp) {
                var source = $("#pre-add-student-template").html();
                var template = Handlebars.compile(source);
                var html = template({
                    'row': perData.preAddCounter + 1,
                    'counter': perData.preAddCounter,
                    'enrolledDate': resp.enrolled_date,
                    'id': resp.id,
                    'code': resp.code,
                    'fullname': resp.fullname,
                    'gender': resp.gender,
                    'phone': resp.phone,
                });
                $('#pre-add-student-container').append(html);
                var elem = document.querySelector('#std-' + resp.id);
                var init = new Switchery(elem, {
                    size: 'small'
                });

                perData.preAddCounter++;
            }
        },
        complete: function() {
            ajaxing = false;
            $('#add-student-modal-overlay').addClass('hidden');
        }
    });
}

function addStudent() {
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    var datas = [];
    elems.forEach(function (e) {
        if (e.checked) {
            var data = [];
            var row = $(e).closest('.row-pre');
            data['row'] = perData.selected.length;
            data['id'] = row.find('#studentid').val();
            data['code'] = row.find('#studentcode').val();
            data['fullname'] = row.find('#fullname').val();
            data['gender'] = row.find('#gender').val();
            data['phone'] = row.find('#phone').val();
            data['enrolledDate'] = row.find('#studentenrolleddate').val();
            datas.push(data);

            perData.selected.push(parseInt(data['id']));
        }
    });
    if (datas.length == 0) {
        return;
    }
    var source = $("#add-student-template").html();
    var template = Handlebars.compile(source);
    var html = template(datas);
    $('#student-container').append(html);

    // close modal
    $('#add-student-modal').modal('toggle');
    perData.preAddCounter = 0;
}

function showEditStudentModal(ele) {
    // reset form
    $('#edit-student-form')[0].reset();
    editor.setValue($(ele).closest('.row-std').find('.note').val());

    var rowIdArr = $(ele).closest('.row-std').attr('id').split('-');
    var rowId = rowIdArr[rowIdArr.length-1];

    $('#edit-student-btn').remove();
    $('<button type="button" class="btn btn-success" id="edit-student-btn" onclick="editStudent('+rowId+')">Hoàn tất</button>').insertBefore('#close-edit-modal-btn');

    $('#edit-student-modal').modal('toggle');
}

function editStudent(rowId) {
    $('#row-student-'+rowId).find('.note').val($('#modal-note').val());
    $('#edit-student-modal').modal('toggle');
}

function showChangeClassModal(ele) {
    if ($('input[name="have_test"]').val() === "true") {
        swal({
            title: 'Cảnh báo!',
            text: "Lớp học sắp có cuộc thi năng lực tiếng Nhật. Bạn không thể thực hiện việc chuyển lớp vào lúc này.",
            type: 'warning',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok'
        });
    } else {
        // reset form
        $('#modal-class').val(null).trigger('change');

        var rowIdArr = $(ele).closest('.row-std').attr('id').split('-');
        var rowId = rowIdArr[rowIdArr.length-1];
        $('#change-class-btn').remove();
        $('<button type="button" class="btn btn-success" id="change-class-btn" onclick="changeClass('+rowId+')">Hoàn tất</button>').insertBefore('#close-change-class-modal-btn');

        $('#change-class-modal').modal('toggle');
    }
}

function changeClass(rowId) {
    if ($('#modal-class').val()) {
        if (testing) {
            swal({
                title: 'Cảnh báo!',
                text: "Lớp " + $('#modal-class option:selected').html() + " sắp có cuộc thi năng lực tiếng Nhật. Bạn không thể thực hiện việc chuyển lớp vào lúc này.",
                type: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Ok'
            });
        } else {
            execChangeClass(rowId);
        }
    }
}

function execChangeClass(rowId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#change-class-modal-overlay').removeClass('hidden');

    $.ajax({
        type: 'POST',
        url: DOMAIN_NAME + '/jclasses/changeClass',
        data: {
            'id': $('#class-student-'+rowId+'-id').find('input').val(),
            'class': $('#modal-class').val()
        },
        success: function(resp){
            if (resp.status == 'success') {
                var delEl = $('#row-student-' + rowId);
                deleteRow(delEl, rowId);
                
                $('#change-class-modal').modal('toggle');
                
                swal({
                    title: resp.alert.title,
                    text: resp.alert.message,
                    type: resp.alert.type
                })
            }
        },
        complete: function() {
            ajaxing = false;
            $('#change-class-modal-overlay').addClass('hidden');
        }
    });
}

function deleteStudent(delEl, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Xóa học viên khỏi lớp',
            text: "Bạn không thể hồi phục được thông tin nếu đã xóa!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#ddd',
            cancelButtonText: 'Đóng',
            confirmButtonText: 'Vâng, tôi muốn xóa!'
        }).then((result) => {
            if (result.value) {
                var rowIdArr = $(delEl).closest('.row-std').attr('id').split('-');
                var rowId = rowIdArr[rowIdArr.length-1];

                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/jclasses/deleteStudent',
                    data: {
                        'id': $('#class-student-'+rowId+'-id').find('input').val(),
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
    $(delEl).closest('tr.row-std').remove();

    if (hiddenId != null) {
        // case: remove record exists in database
        var delId = parseInt($('#student-'+hiddenId+'-id').find('input').val());
        $('#student-'+hiddenId+'-id').remove();
        $('#class-student-'+hiddenId+'-id').remove();
    } else {
        var delId = parseInt($(delEl).closest('tr.row-std').find('.id').val());
    }

    // remove in selected array
    perData.selected.splice(perData.selected.indexOf(delId), 1);

    var trows = $('#student-container > tr');
    var idField = $('.student-id').find('input');
    var classStudentField = $('.class-std-id').find('input');
    var inputField = $('#student-container').find('input');
    var textField = $('#student-container').find('textarea');
    var sttField = $('#student-container').find('.stt-col');

    for (var i = 0; i < sttField.length; i++) {
        sttField[i].innerText = i + 1;
        trows[i].id = 'row-student-' + i;
        if (hiddenId != null) {
            $('.student-id')[i].id = 'student-' + i + '-id';
            $('.class-std-id')[i].id = 'class-student-' + i + '-id';
            idField[i].name = 'students[' + i + '][id]';
            classStudentField[i].name = 'students[' + i + '][_joinData][id]';
        } else {
            if (i < idField.length) {
                continue;
            }
            var classArr = inputField[i-idField.length].className.split(' ');
            inputField[i-idField.length].name = 'students[' + i + '][' + classArr[classArr.length-1] + ']';
        }

        classArr = textField[i].className.split(' ');
        textField[i].name = 'students[' + i + '][_joinData][' + classArr[classArr.length-1] + ']';
    }
}