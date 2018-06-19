var curColor = '#3a87ad'; //default
var calendat;

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
        buttonText: {
            today: 'Hôm nay',
            month: 'Tháng',
            week : 'Tuần',
            day  : 'Ngày'
        },
        nowIndicator: true,
        now: now,
		selectable: true,
        selectHelper: true,
		editable: true,
        events: '/tvms/events/getEvents',        
		select: function (start, end, jsEvent) {
            // reset form
            resetModal();

            // init form data
            var allDayEvent = '2'; // false
            if (end - start == 86400000) {
                allDayEvent = '1'; // true
            }
            $('input[name="all_day"]').val(allDayEvent);
            $('input[name="start"]').val(start);
            $('input[name="end"]').val(end);

            // renew add-btn
            $('#submit-event-btn').remove();
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
            getEvent(calEvent.id);

			$('#fc_edit').click();
			$('#title2').val(calEvent.title);

			$(".antosubmit2").on("click", function () {
				calEvent.title = $("#title2").val();

				calendar.fullCalendar('updateEvent', calEvent);
				$('.antoclose2').click();
			});

			calendar.fullCalendar('unselect');
		},
	});
}

function addEvent() {
    //validate form
    var validateResult = $('#event-form').parsley().validate();
    if (validateResult) {
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
                $('#event-modal').modal('toggle');
            }
        });
    }
}

function getEvent(id) {
    if (id) {
        $.ajax({
            type: 'GET',
            url: '/tvms/events/getEvent',
            data: {
                id: id
            },
            success: function(resp) {
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

                $('#scope').val(resp.scope).trigger('change');

                //show modal
                $('#event-modal').modal('toggle');
            }
        });
    }
}

function resetModal() {
    $('#event-form')[0].reset();
    $('#scope').val(null).trigger('change');
    $('#event-form').parsley().reset();
    
}