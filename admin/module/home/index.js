var Actions = {
    New: "NEW",
	Create:"CREATE",
    Resize: "RESIZE",
    Move: "MOVE",
	Edit: "EDIT",
	Idle: "IDLE"
};
var current_action;

var $edit_dialog = null;
var $edit_dialog_validator = null;

var $actvice_column = null;
var $actvice_box = null;

var $container = null;

var mousedown_point_y = null;


function getAjaxUrl(fn, m){
    return "ajax.php?fn=" + fn + "&m=" + (m || "home");
}

function setAction(a) {
	current_action = a;
	if (Actions.Idle == current_action) {
		$actvice_box = null;
	}
}
function getAction() {
	return current_action;
}

$(function(){

    //scroll to 8:00am
    $container = $("#gridcontainer").scrollTop(480);
    
    //create event box dialog and validator
    $edit_dialog = new Boxy($("#timesheet_detailDialog"), {
        title: "Edit Information",
        modal: true,
        afterHide: function(){
			if (Actions.Create == getAction()) {
				$actvice_box.remove();
			}
			setAction(Actions.Idle);
        }
    });
    $edit_dialog_validator = $("#timesheet_detailDialog form:first").validate();
    
    //handle timesheet actions
    $(".timesheet_daywrapper").mousedown(function(e){
		setAction(Actions.New);
		
        $actvice_column = $(this);

        var top = mousedown_point_y = (e.pageY - $actvice_column.position().top);
        top = top - (top % 15);

		//create temp event box
        $actvice_box = new EventBox();
        $actvice_box.setPosition(top);
        $actvice_box.setParent($actvice_column);
        if ($actvice_box.boundary.upper - $actvice_box.position.top >= 60) {
            $actvice_box.setSize(60);
        } else {
            $actvice_box.setSize($actvice_box.boundary.upper - $actvice_box.position.top);
        }
    });
    
    $().mousemove(function(e){
		switch(getAction()) {
			case Actions.New:
	            $actvice_box.updateSize(mousedown_point_y, e.pageY - $actvice_column.position().top);
	            $actvice_box.updateTime();
				break;
			case Actions.Resize:
	            $actvice_box.updateSize(mousedown_point_y, e.pageY - $actvice_column.position().top);
	            $actvice_box.updateTime();
				break;
			default:
				return;
		}

        //handle scrolling
		var top = $container.position().top;
        var y = e.pageY - top;
        if (y > ($container.height() - 30)) {
            $container.scrollTop($container.scrollTop() + 15);
        } else if (y < 30) {
            $container.scrollTop($container.scrollTop() - 15);
        }
		
    }).mouseup(function(e){
		switch(getAction()) {
			case Actions.New:
				setAction(Actions.Create);
				clearEditForm();
				$edit_dialog.show();
				$("#job_no").focus();
				break;
			case Actions.Move:
				save();
				break;
			case Actions.Resize:
				save();
				break;
			default:
				return;
		}
    });

	var selected_date = new Date($('#selected_date').val() * 1000);
	//var selected_date = $('#selected_day').val() + '/' + $('#selected_mon').val() + '/' + $('#selected_year').val();
    $('#weekCalendar').datePicker({
        inline: true,
        selectWeek: true,
        startDate: '01/12/2008'
    }).trigger('change').bind('dateSelected', onDateChanged)
	.dpSetSelected(selected_date.format('d/m/Y'));
});


function onDateChanged(e, selectedDate, $td) {
	var day_ts,week_start_ts = selectedDate.getTime()/1000;	//get selected date(timestamp)
	var objDate = new Date();
	
	$('.timesheet_columnTop .year').html(" " + selectedDate.format('Y'));
	
	day_ts = week_start_ts;
	$('.timesheet_columnTop td').each(function(){
		objDate.setTime(day_ts*1000);
		$(this).html(objDate.format('D, j M'));
		
		day_ts += 86400;
	});	
	
	day_ts = week_start_ts;	
	$('#gridcontainer .timesheet_daywrapper').each(function(){
		$(this).attr('id',"column_" + day_ts);	//update column id
		$(this).find('.timesheet_columnParam').html("{date:" + day_ts + "}");	//update column params
		day_ts += 86400;
	})
	
	load(week_start_ts);	//load event from database
}

if($.browser.msie){
	document.onselectstart = document.onselect = document.ondblclick = document.onmousedown = function captureEvents(){
	    return false;
	};
}

function del() {
	if(!confirm("delete?")){return;}
	$.post(getAjaxUrl("del"),{id:$actvice_box.getId()},function(data){
		cs_handleResponse(data,function(param){				
			$actvice_box.remove();
			$actvice_box = null;
			$edit_dialog.hide();
		});
	});
}

function edit(obj) {
	clearEditForm();
	
	$('#job_no').val($actvice_box.getTitle());
	$('#job_description').val($actvice_box.getDescription());
	
	setAction(Actions.Edit);
	$edit_dialog.show();
}

function load(timestamp){
	removeAll();

    $.getJSON(getAjaxUrl("filter"), {action:'DATE_CHANGED',date:timestamp}, function(data){
        cs_handleResponse(data, function(json){
            for (var i = 0; i < json.length; i++) {
				createEventBoxByJSON(json[i]);
            }
        });
    });
}

function removeAll() {
	$('#gridcontainer .timesheet_eventbox').remove();
}

function save(){
	
	var action = '';
	var $obj = $actvice_box;
	
	switch(getAction()) {
		case Actions.Create:
			action = 'CREATE';
					
			if(!$edit_dialog_validator.form()) {
				return;
			}	
			
			$actvice_box.setTitle($('#job_no').val());
			$actvice_box.setDescription($('#job_description').val());
			break;
		case Actions.Edit:
			action = 'EDIT';
			
			if(!$edit_dialog_validator.form()) {
				return;
			}	
			$actvice_box.setTitle($('#job_no').val());
			$actvice_box.setDescription($('#job_description').val());
			break;
		case Actions.Resize:
			action = 'EDIT';
			break;
		case Actions.Move:
			action = 'EDIT';
			break;
		default:
			return;
	}
	
	setAction(Actions.Idle);
	
    $.post(getAjaxUrl("save"), "action=" + action + "&" + $obj.getParams(), function(data){
        cs_handleResponse(data, function(json){
			if(action == 'CREATE') {
				$obj.remove();
				createEventBoxByJSON(json);
			} else if(action == 'EDIT') {
				updateEventBoxByJSON($obj,json);
			}
			$edit_dialog.hide();
        });
    });
}



function updateEventBoxByJSON($box,json) {
    $box.setTitle(json.name);
    $box.setDescription(json.description);
    $box.setTime(json.start, json.end);
    $box.setType(json.type);
}

function clearEditForm() {
	$("#timesheet_detailDialog_form")[0].reset();
	if ($edit_dialog_validator) {
		$edit_dialog_validator.resetForm();
	}
}

function createEventBoxByJSON(json){
	$actvice_column = $("#column_" + json.date);
    return createEventBox(json.id, json.name, json.description, json.start, json.end, json.type);
}
function createEventBox(event_id, title,description, start, end,type){
    var $box = new EventBox($actvice_column, {
        beforeResize: function(e,$obj){
            $actvice_column = $obj.$parent;
            $actvice_box = $obj;
			setAction(Actions.Resize);
            mousedown_point_y = $obj.position.top;
        },
		beforeDrag: function(e,$obj){
            $actvice_column = $obj.$parent;
            $actvice_box = $obj;
			setAction(Actions.Move);
            mousedown_point_y = $obj.position.top;		
		},
		editClicked:function(e,$obj){
            $actvice_column = $obj.$parent;
            $actvice_box = $obj;
			setAction(Actions.Edit);
		}
    });
	
    $box.setTitle(title);
    $box.setDescription(description);
    $box.setTime(start, end);
	$box.setId(event_id);
	$box.setType(type);
}