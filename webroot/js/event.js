var curColor = '#3a87ad'; //default
var calendar;
var ajaxing = false;

$(document).ready(function() {
    init_calendar();

    // init color
    $('input[name="color"]').val(curColor);
    
    $('#title-color').css({'background-color': curColor, 'border-color': curColor});

    $('#color-chooser > li > a').click(function (e) {
        e.preventDefault();
        curColor = $(this).css('color');
        $('#title-color').css({'background-color': curColor, 'border-color': curColor});
        // set value for input
        $('input[name="color"]').val(curColor);
    });

    $('.select2-theme').change(function(e) {
        $('#' + e.target.id).parsley().validate();

        if ($(this).val() === "2") {
            // global event
            curColor = 'red';
            // hide color chooser
            $('.color-chooser-group').addClass('hidden');
        } else {
            // only me event
            curColor = '#3a87ad'; // default color
            // show color chooser
            $('.color-chooser-group').removeClass('hidden');
        }
        $('#title-color').css({'background-color': curColor, 'border-color': curColor});
        // set value for input
        $('input[name="color"]').val(curColor);
    });

});

function init_calendar() {
    var date = new Date(),
		d = date.getDate(),
		m = date.getMonth(),
        y = date.getFullYear(),
		started,
		categoryClass;
    var now = moment();

	calendar = $('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
        },
        locale: 'vi',
        nowIndicator: true,
        now: now,
		selectable: true,
        selectHelper: true,
		editable: true,
        events: '/tvms/events/getEvents',
        eventLimit: true,
        height: "auto",
		select: function (start, end, jsEvent) {
            // reset form
            resetModal();

            // init form data
            var allDayEvent = 'false'; // false
            if (end - start == 86400000) {
                allDayEvent = 'true'; // true
            }
            // remove id
            $('input[name="id"]').remove();
            $('input[name="all_day"]').val(allDayEvent);
            $('input[name="start"]').val(start);
            $('input[name="end"]').val(end);

            // renew add-btn
            $('#submit-event-btn').remove();
            $('#delete-event-btn').remove();
            $('<button type="button" class="btn btn-success" id="submit-event-btn" onclick="addEvent()">Submit</button>').insertBefore('#close-event-modal-btn');
            
            // show modal
            $('#event-modal').modal('toggle');

			started = start;
			ended = end;
		},
		eventClick: function (calEvent, jsEvent, view) {
            // reset form
            resetModal();

            // get event detail
            var mode = 'view';
            if (calEvent.editable) {
                // edit information
                mode = 'edit';
            }
            getEvent(calEvent.id, mode);

            // update event
            $(document).on('click', '#submit-event-btn', function() {
                if (ajaxing) {
                    // still requesting
                    return;
                }
                var validateResult = $('#event-form').parsley().validate();
                if (validateResult) {
                    ajaxing = true;
                    $.ajax({
                        type: 'POST',
                        url: '/tvms/events/edit/' + calEvent.id,
                        data: $('#event-form').serialize(),
                        success: function(resp) {
                            if (resp.status === "success") {
                                calEvent.title = resp.title;
                                calEvent.start = resp.start;
                                calEvent.end = resp.end;
                                calEvent.allDay = resp.allDay;
                                calEvent.backgroundColor = resp.backgroundColor,
                                calEvent.borderColor = resp.borderColor
                            }
                            calendar.fullCalendar('updateEvent', calEvent);
                            // hide modal
                            $('#event-modal').modal('hide');
                        }, complete: function() {
                            ajaxing = false;
                        }
                    });
                }
            });

            // delte event
            $(document).on('click', '#delete-event-btn', function() {
                if (confirm("Are you sure to delete this event?")) {
                    $.ajax({
                        type: 'POST',
                        url: '/tvms/events/delete/' + calEvent.id,
                        success: function(resp) {
                            if (resp.status == "success") {
                                calendar.fullCalendar('removeEvents', calEvent.id);

                                // hide modal
                                $('#event-modal').modal('hide');
                            }
                        }
                    });
                }
            });
            calendar.fullCalendar('unselect');

            
        },
        eventDrop: function (event, delta, revertFunc) {
            if (!confirm("Are you sure about this change?")) {
                revertFunc();
            } else {
                $.ajax({
                    type: 'POST',
                    url: '/tvms/events/editDuration/' + event.id,
                    data: {
                        start: event.start.format(),
                        end: event.end.format()
                    },
                    success: function(resp) {
                        console.log('changed');
                    }
                });
            }
        },
        eventResize: function (event, delta, revertFunc) {
            if (!confirm("Are you sure about this change?")) {
                revertFunc();
            } else {
                $.ajax({
                    type: 'POST',
                    url: '/tvms/events/editDuration/' + event.id,
                    data: {
                        start: event.start.format(),
                        end: event.end.format()
                    },
                    success: function(resp) {
                        console.log('changed');
                    }
                });
            }
        }
	});
}

function addEvent() {
    if (ajaxing) {
        // still requesting
        return;
    }
    //validate form
    var validateResult = $('#event-form').parsley().validate();
    if (validateResult) {
        ajaxing = true;
        $.ajax({
            type: 'POST',
            url: $('#event-form').attr('action'),
            data: $('#event-form').serialize(),
            success: function(resp){
                if (resp.status === "success") {
                    calendar.fullCalendar('renderEvent', {
                        id: resp.id,
                        title: resp.title,
                        start: resp.start,
                        end: resp.end,
                        allDay: resp.allDay,
                        backgroundColor: resp.backgroundColor,
                        borderColor: resp.borderColor
                    },
                        true // make the event "stick"
                    );
                }

                calendar.fullCalendar('unselect');
                
                // hide modal
                $('#event-modal').modal('hide');
            }, complete: function() {
                ajaxing = false;
            }
        });
    }
}

function getEvent(id, mode) {
    if (id) {
        if (ajaxing) {
            // still requesting
            return;
        }

        ajaxing = true;
        $.ajax({
            type: 'GET',
            url: '/tvms/events/getEvent',
            data: {
                id: id
            },
            success: function(resp) {
                fillData(resp, mode);
            }, complete: function() {
                ajaxing = false;
            }
        });
    }
}

function fillData(resp, mode) {
    if (mode === "edit") {
        // fill data to form
        $('<input>').attr({
            type: 'hidden',
            id: 'event-id',
            name: 'id'
        }).appendTo('#event-form');
        $('input[name="id"]').val(resp.id);

        $('input[name="all_day"]').val(resp.allDay);
        $('input[name="start"]').val(resp.start);
        $('input[name="end"]').val(resp.end);
        $('input[name="title"]').val(resp.title);
        $('textarea[name="description"]').val(resp.description);

        curColor = resp.backgroundColor;
        $('#title-color').css({'background-color': curColor, 'border-color': curColor});

        $('input[name="color"]').val(curColor);

        $('select[name="scope"]').val(resp.scope).trigger('change');

        $('#submit-event-btn').remove();
        $('#delete-event-btn').remove();
        // create delete button
        $('<button type="button" class="btn btn-danger" id="delete-event-btn">Delete</button>').insertBefore('#close-event-modal-btn');
        // renew add-btn
        $('<button type="button" class="btn btn-success" id="submit-event-btn">Save Change</button>').insertBefore('#close-event-modal-btn');
        //show modal
        $('#event-modal').modal('toggle');                                                          
    } else {
        $('#event-title').html(resp.title);
        $('#event-description').html((resp.description).replace(/\r?\n/g,'<br/>'));
        $('#event-owner').html(resp.owner);
        
        // show modal
        $('#event-info-modal').modal('toggle');
    }
    
}

function resetModal() {
    $('#event-form')[0].reset();
    $('#scope').val(null).trigger('change');
    $('#event-form').parsley().reset();
}