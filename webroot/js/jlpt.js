var perData = {};
perData.selectedStudent = [];


$(document).ready(function () {
    // init data
    $('.jlptStudentId').each(function () {
        perData.selectedStudent.push(parseInt($(this).val()));
    });

    // add new jlpt test
    $('.submit-test-btn').click(function () {
        var validateResult = $('#add-test-form').parsley().validate();
        if (validateResult) {
            if (perData.selectedStudent.length == 0) {
                swal({
                    title: 'Danh sách thi hiện đang bỏ trống',
                    text: "Xin vui lòng kiểm tra lại!",
                    type: 'error',
                });
                return;
            }
            $('#add-test-form')[0].submit();
        }
    });

    // submit set score form
    $('.set-score-btn').click(function () {
        var validateResult = $('#set-score-form').parsley().validate();
        if (validateResult) {
            $('#set-score-form')[0].submit();
        }
    });

    $('.partScore').on("change keyup paste", function () {
        // calculate total score
        var $row = $(this).closest('.row-score');
        var totalScore = 0;
        $row.find('.partScore').each(function () {
            totalScore += parseInt($(this).val());
        })
        $row.find('.totalScore').val(totalScore);
    });

    // change jclass test
    $('#modal-jclass').change(function () {
        if ($(this).val()) {

            // get all students of this class
            $.ajax({
                type: 'GET',
                url: DOMAIN_NAME + '/jlpt-tests/getStudents',
                data: {
                    'classId': $(this).val(),
                },
                success: function (resp) {
                    var candidates = resp.students;
                    var removeIndexes = [];

                    $.each(candidates, function (index, value) {
                        if (perData.selectedStudent.indexOf(value.id) >= 0) {
                            removeIndexes.push(index);
                        }
                    });
                    // remove duplicate candidate
                    for (let index = removeIndexes.length - 1; index >= 0; index--) {
                        candidates.splice(removeIndexes[index], 1);
                    }

                    if (candidates.length != 0) {
                        var source = $("#jclass-student-template").html();
                        var template = Handlebars.compile(source);
                        var html = template(resp.students);

                        $('#jclass-container').html(html);

                        // init switchery
                        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                        elems.forEach(function (html) {
                            var switchery = new Switchery(html, {
                                size: 'small'
                            });
                        });
                    } else {
                        $('#jclass-container').empty();
                    }
                }
            });
        } else {
            $('#jclass-container').empty();
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
})


function showAddStudentModal() {
    // reset modal
    $('#modal-jclass').val(null).trigger('change');
    $('#add-student-form').parsley().reset();
    $('#jclass-container').empty();
    // show modal
    $('#add-student-modal').modal('toggle');
}

function viewStudent(studentid) {
    window.open(DOMAIN_NAME + '/students/view/' + studentid, '_blank');
}

function addStudent() {
    var validateResult = $('#add-student-form').parsley().validate();
    if (validateResult) {
        if ($('#jclass-container > tr').length == 0) {
            return;
        }
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        var datas = [];
        elems.forEach(function (e) {
            if (e.checked) {
                var data = [];
                var row = $(e).closest('.row-rec');
                data['row'] = perData.selectedStudent.length;
                data['id'] = row.find('.studentId').val();
                data['fullname'] = row.find('.student-name-contain').html();
                data['jclass'] = $('#modal-jclass option:selected').html();

                datas.push(data);

                perData.selectedStudent.push(parseInt(data['id']));
            }
        });

        var source = $("#jlpt-student-template").html();
        var template = Handlebars.compile(source);
        var html = template(datas);
        $('#jlpt-students-container').append(html);

        // close modal
        $('#add-student-modal').modal('toggle');
    }
}

function deleteStudent(delEl, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Xóa thí sinh',
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
                    url: DOMAIN_NAME + '/jlpt-tests/deleteStudent',
                    data: {
                        'studentId': $(delEl).closest('.row-student').find('.jlptStudentId').val(),
                        'jlptId': jlptId
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
    // delete data in selected array
    var delStudentId = parseInt($(delEl).closest('.row-student').find('.jlptStudentId').val());
    perData.selectedStudent.splice(perData.selectedStudent.indexOf(delStudentId), 1);

    $(delEl).closest('.row-student').remove();

    $('#jlpt-students-container > tr').each(function (index) {
        $(this).attr('id', 'row-student-' + index);
        $(this).find('.stt-col').html(index + 1);
        $(this).find('.jlptStudentId').attr('name', 'students[' + index + '][id]');
    });
}

function reportJplt() {
    // reset modal

    // show modal
    $('#jlpt-report-modal').modal('toggle');
}