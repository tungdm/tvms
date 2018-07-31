var perData = {};
perData.skillSelected = [];

// init handlebars
if ($('#skill-template')[0]){
    var skillTemplate = Handlebars.compile($("#skill-template").html());
}

$(document).ready(function() {
    // init selected skill
    $('.skill-id').each(function() {
        var rowIdArr = $(this).attr('id').split('-');
        var rowId = rowIdArr[rowIdArr.length-1];
        perData.skillSelected.push(parseInt($('#row-skill-'+rowId).find('.skill').val()));
    });

    $('.submit-test-btn').click(function () {
        var validateResult = $('#add-test-form').parsley().validate();
        if (validateResult) {
            $('#add-test-form')[0].submit();
        }
    });

    $('.set-score-btn').click(function () {
        var validateResult = $('#set-score-form').parsley().validate();
        if (validateResult) {
            $('#set-score-form')[0].submit();
        }
    });

    $('.select-lesson-from').change(function() {
        var lessonTo = parseInt($('.select-lesson-to').val());
        var lessonFrom = parseInt($(this).val());
        if (lessonTo !=0 || lessonFrom < lessonTo) {
        $('.select-lesson-to').attr('min', $(this).val());
            var optionTxt = $('.select-lesson-to option').map(function(e) { 
                if ($(this).val() == lessonFrom) {
                    return $(this).html(); 
                } 
            });
            $('.select-lesson-to').attr('data-parsley-min-message', 'Please choose option after ' + optionTxt[0]);
        }
    });

    $('.select-lesson-to').change(function() {
        var lessonFrom = parseInt($('.select-lesson-from').val());
        var fromMax = parseInt($('.select-lesson-from').attr('max'));
        var lessonTo = parseInt($(this).val());
        if (lessonFrom < lessonTo && lessonTo < fromMax ) {
            $('.select-lesson-from').attr('max', lessonTo);
            var optionTxt = $('.select-lesson-from option').map(function(e) { 
                if ($(this).val() == lessonTo) {
                    return $(this).html(); 
                } 
    });
            $('.select-lesson-from').attr('data-parsley-max-message', 'Please choose option before ' + optionTxt[0]);
        }
    });

    $('#jclass-id').change(function() {
        var classId = this.value;
        if (ajaxing) {
            // still requesting
            return;
        }
        ajaxing = true;
        $('#add-test-overlay').removeClass('hidden');
        
        $.ajax({
            type: 'GET',
            url: DOMAIN_NAME + '/jtests/getStudents',
            data: {
                id: classId,
                testId: $('input[name="id"]').val()
            },
            success: function(resp) {
                if (resp === undefined || resp.length == 0) {
                    return;
                }

                // clear id field
                $('#student-test-container').html('');

                if (resp.status === "unchanged") {
                    // init id when the attendeces unchanged
                    var source = $("#student-test-template").html();
                    var template = Handlebars.compile(source);
                    var html = template(resp.ids);
                    $('#student-test-container').html(html);
                    $('input[name="changed"]').val('false');
                } else {
                    $('input[name="changed"]').val('true');
                }
                var source = $("#student-template").html();
                var template = Handlebars.compile(source);
                var html = template(resp.data);
                $('#student-container').html(html);

                // remove all attribute
                $('.select-lesson-to').removeAttr('max');
                $('.select-lesson-to').removeAttr('min');
                $('.select-lesson-from').removeAttr('max');
                $('.select-lesson-from').removeAttr('min');

                // set max lesson for testing
                $('.select-lesson-to').attr('max', resp.currentLesson);

                var max = $('.select-lesson-to').val();
                if ($('.select-lesson-to').val() == "" || $('.select-lesson-to').val() > resp.currentLesson) {
                    $('.select-lesson-from').attr('max', resp.currentLesson);
                    max = resp.currentLesson;
                } else {
                    $('.select-lesson-from').attr('max', $('.select-lesson-to').val());
                }

                var optionTxt = $('.select-lesson-to option').map(function(e) { 
                    if (parseInt($(this).val()) == max) {
                        return $(this).html(); 
                    } 
                });
                $('.select-lesson-to').attr('data-parsley-max-message', 'Please choose option before ' + optionTxt[0]);
                $('.select-lesson-from').attr('data-parsley-max-message', 'Please choose option before ' + optionTxt[0]);

                if ($('.select-lesson-from').val()) {
                    $('.select-lesson-from').parsley().validate();
                }

                if ($('.select-lesson-to').val()) {
                    $('.select-lesson-to').parsley().validate();
                }
            },
            complete: function() {
                ajaxing = false;
                $('#add-test-overlay').addClass('hidden');
            }
        });
    });
});

function showAddSkillModal() {
    // reset form
    $('#modal-skill').val(null).trigger('change');
    $('#modal-teacher').val(null).trigger('change');
    $('#add-skill-form').parsley().reset();

    $('#add-skill-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-skill-btn" onclick="addSkill()">Hoàn tất</button>').insertBefore('#close-add-skill-modal-btn');

    $('#add-skill-modal').modal('toggle');
}

function showEditSkillModal(ele) {
    $('#modal-skill').val($(ele).closest('.row-skill').find('.skill').val()).trigger('change');
    $('#modal-teacher').val($(ele).closest('.row-skill').find('.teacher').val()).trigger('change');

    var rowIdArr = $(ele).closest('.row-skill').attr('id').split('-');
    var rowId = rowIdArr[rowIdArr.length-1];
    var initSkill = $('#modal-skill').val();

    $('#add-skill-btn').remove();
    $('<button type="button" class="btn btn-success" id="add-skill-btn" onclick="editSkill('+rowId+', '+initSkill+')">Hoàn tất</button>').insertBefore('#close-add-skill-modal-btn');

    $('#add-skill-modal').modal('toggle');
}

function createSkillTemplate(counter) {
    var html = skillTemplate({
        'row': counter + 1,
        'counter': counter,

        'skillText': $('#modal-skill option:selected').html(),
        'teacherText': $('#modal-teacher option:selected').html(),
    });
    return html;
}

function addSkill() {
    //validate form
    var skillId = parseInt($('#modal-skill').val());
    if (perData.skillSelected.indexOf(skillId) >= 0) {
        alert('Bạn đã chọn thi kỹ năng này. Vui lòng chọn một kỹ năng khác.');
        return;
    }

    var validateResult = $('#add-skill-form').parsley().validate();
    if (validateResult) {
        var skill_html = createSkillTemplate(perData.skillSelected.length);
        $('#skill-container').append(skill_html);

        // set value for select box
        $('select[name="jtest_contents['+perData.skillSelected.length+'][skill]"]').val($('#modal-skill').val());
        $('select[name="jtest_contents['+perData.skillSelected.length+'][user_id]"]').val($('#modal-teacher').val());

        $('#add-skill-modal').modal('toggle');
        perData.skillSelected.push(parseInt($('#modal-skill').val()));

        // edit flag
        $('input[name="flag"]').val(perData.skillSelected.length);
    }
}

function editSkill(rowId, initSkill) {
    var skillId = parseInt($('#modal-skill').val());
    if (perData.skillSelected.indexOf(skillId) >= 0) {
        alert('You have already selected this skill. Please choose another skill!');
        return;
    }

    //validate form
    var validateResult = $('#add-skill-form').parsley().validate();
    if (validateResult) {
        // update selected data
        var orgIndex = perData.skillSelected.indexOf(initSkill);
        perData.skillSelected[orgIndex] = skillId;

        // change text
        $('#row-skill-'+rowId).find('.skill-name').html($('#modal-skill option:selected').html());
        $('#row-skill-'+rowId).find('.teacher-name').html($('#modal-teacher option:selected').html());
        // set value for select box
        $('select[name="jtest_contents['+rowId+'][skill]"]').val($('#modal-skill').val());
        $('select[name="jtest_contents['+rowId+'][user_id]"]').val($('#modal-teacher').val());

        // close modal
        $('#add-skill-modal').modal('toggle');
    }
}

function deleteSkill(delEl, sendAjax) {
    if (sendAjax) {
        swal({
            title: 'Xóa kỹ năng thi',
            text: "Bạn không thể hồi phục được thông tin nếu đã xóa!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#222d32',
            cancelButtonText: 'Đóng',
            confirmButtonText: 'Vâng, tôi muốn xóa!'
        }).then((result) => {
            if (result.value) {
                var rowIdArr = $(delEl).closest('.row-skill').attr('id').split('-');
                var rowId = rowIdArr[rowIdArr.length-1];

                $.ajax({
                    type: 'POST',
                    url: DOMAIN_NAME + '/jtests/deleteSkill',
                    data: {
                        'id': $('#skill-id-'+rowId).find('input').val(),
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
    $(delEl).closest('.row-skill').remove();

    if (hiddenId != null) {
        // case: remove record exists in database
        $('#skill-id-'+hiddenId).remove();
    } 
    var skillId = parseInt($(delEl).closest('.row-skill').find('.skill').val());
    // remove in selected array
    perData.skillSelected.splice(perData.skillSelected.indexOf(skillId), 1);
    // update flag
    $('input[name="flag"]').val(perData.skillSelected.length);

    var trows = $('#skill-container > tr');
    var idField = $('.skill-id').find('input');
    var selectField = $('#skill-container').find('select');
    var sttField = $('#skill-container').find('.stt-col');

    for (var i = 0; i < sttField.length; i++) {
        sttField[i].innerText = i + 1;
        trows[i].id = 'row-skill-' + i;

        if (hiddenId != null) {
            $('.skill-id')[i].id = 'skill-id-' + i;
            idField[i].name = 'jtest_contents[' + i + '][id]';
        }

        classArr = selectField[i].className.split(' ');
        selectField[i].name = 'jtest_contents[' + Math.floor(i/2) + '][' + classArr[classArr.length-1] + ']';
    }

    for (var i = 0; i < selectField.length; i++) {
        classArr = selectField[i].className.split(' ');
        selectField[i].name = 'jtest_contents[' + Math.floor(i/2) + '][' + classArr[classArr.length-1] + ']';
    }
}