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

function search() {
    var filter = $('#studentname').val().toUpperCase().normalize('NFD').replace(/[\u0300-\u036f]/g, "");
    $('#pre-add-student-container').find('.row-pre').each(function() {
        var fullname = $(this).find('#fullname').val().toUpperCase().normalize('NFD').replace(/[\u0300-\u036f]/g, "");
        if (fullname.indexOf(filter) > -1) {
            $(this).removeClass('hidden');
        } else {
            $(this).addClass('hidden');
        }
    })
}

function showAddStudentModal() {
    // reset modal
    $('#student-name').val(null).trigger('change');
    // get list student
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-student-class-overlay').removeClass('hidden');

    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/jclasses/recommendStudent',
        success: function(resp) {
            $('#pre-add-student-container').empty();
            
            var students = resp.students;
            var removeIndexes = [];

            $.each(students, function(index, value) {
                if (perData.selected.indexOf(value.id) >= 0) {
                    removeIndexes.push(index);
                }
            });
            // remove duplicate candidate
            for (let index = removeIndexes.length-1; index >= 0; index--) {
                students.splice(removeIndexes[index], 1);
            }

            perData.preAddCounter = students.length;
            if (students.length > 0) {
                var source = $("#recommend-student-template").html();
                var template = Handlebars.compile(source);
                var html = template(students);
                $('#pre-add-student-container').html(html);
    
                // init switchery
                var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                elems.forEach(function (html) {
                    var switchery = new Switchery(html, {
                        size: 'small'
                    });
                });
            }
            // show modal
            $('#add-student-modal').modal('toggle');
        }, 
        complete: function() {
            ajaxing = false;
            $('#list-student-class-overlay').addClass('hidden');
        }
    });
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
            data['city'] = row.find('#city').html();
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

function showHistoryModal(studentId) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;
    $('#list-student-class-overlay').removeClass('hidden');
    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/jclasses/getAllHistories/' + classId,
        data: {
            id: studentId,
            type: 'education'
        },
        success: function(resp) {
            // reset form
            if (resp.status == 'success') {
                // update counter
                historyCounter = resp.histories.length;
                // re-render view
                var source = $("#all-histories-template").html();
                var template = Handlebars.compile(source);
                var html = template(resp.histories);
                $('.history-detail').remove();
                $(html).insertAfter('#now-tl');
                $('#student-created').html(resp.student_created);

                $('#add-history').remove();
                $('#refresh-history').remove();
                $('<a href="javascript:;" class="btn btn-box-tool" onclick="showAddHistoryModal('+studentId+', \'education\', \'jclasses\', '+classId+')" id="add-history"><i class="fa fa-plus"></i></a>').insertBefore('#close-history');
                $('<a href="javascript:;" class="btn btn-box-tool" onclick="getAllHistories('+studentId+', \'education\', \'list-history-overlay\', \'jclasses\')" id="refresh-history"><i class="fa fa-refresh"></i></a>').insertBefore('#close-history');

                // show modal
                $('#all-histories-modal').modal('toggle');
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
                notice.get().click(function() {
                    notice.remove();
                });
            }
        },
        complete: function() {
            ajaxing = false;
            $('#list-student-class-overlay').addClass('hidden');
        }
    });
}

function editStudent(rowId) {
    $('#row-student-'+rowId).find('.note').val($('#modal-note').val());
    $('#edit-student-modal').modal('toggle');
}

function showChangeClassModal(ele) {
    if (ajaxing) {
        // still requesting
        return;
    }
    ajaxing = true;

    // check if current class have test or not
    $.ajax({
        type: 'GET',
        url: DOMAIN_NAME + '/jclasses/checkTest/' + classId,
        success: function(resp) {
            if (resp) {
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
        },
        complete: function() {
            ajaxing = false;
        }
    });
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
            cancelButtonColor: '#222d32',
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