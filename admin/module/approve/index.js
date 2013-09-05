var filter_param = null;
var sort_param = null;
var $edit_dialog = null;
var $edit_dialog_validator = null;

var $main_container;

$(function() {
	$main_container = $('#div_view_container');
	
	view();
});

function onDateChanged(e, selectedDate, $td) {
	var day_ts = selectedDate.getTime()/1000;	//get selected date(timestamp)
	
}
function onWeekChanged(e, selectedDate, $td) {
	var day_ts = selectedDate.getTime()/1000;	//get selected date(timestamp)
	
	view(day_ts);
}

function view(day, department, user, view){
	day = (day||$('#selected_date').val());
	department = (department||$('#selected_department').val());
	user = (user||$('#selected_user').val());
	view = (view||$('#selected_view').val());
    $main_container.load(getAjaxUrl('month'), {
        d: (day||0),
        g: (department||0),
        u: (user||0),
        v: (view||'')
    });
}

function getAjaxUrl(fn, m){
    return "ajax.php?fn=" + fn + "&m=" + (m || "approve");
}

function approved(obj,user_id,ts) {
	$.post()
}

function filter(refresh,param) {
	refresh = (refresh || false);
	if (!refresh) {
		filter_param = $("#filter_form").serialize();
	}
	sort_param = (param || sort_param);

	$.ajax({
		type: "POST",
	    url: getAjaxUrl("filter"),
		data:filter_param + "&" + sort_param,
		dataType:"json",
	    success: function(data){
			if(data){
				$("#main_list tbody").html(data.html);
				$("#main_list").trigger("update",data.pager).trigger("applyWidgets");
			}
	    }
	});
}

function edit(id){
	$.getJSON(getAjaxUrl("edit"),{id:id},function(data){
		cs_handleResponse(data,function(param){				
			$.updateWithJSON(param);
			$("#action").val("EDIT");
			$edit_dialog.show();
		});
	});
}
function del(id) {
	if(!confirm("delete?")){return;}
	$.post(getAjaxUrl("del"),{id:id},function(data){
		cs_handleResponse(data,function(param){				
			filter(true);
		});
	});
}
function create() {
	$("#edit_form")[0].reset();
	if ($edit_dialog_validator) {
		$edit_dialog_validator.resetForm();
	}
	$("#id").val("0");
	$("#action").val("CREATE");
	$edit_dialog.show();
}

function save() {
	if($edit_dialog_validator.form()){
		$.post(getAjaxUrl("save"),$("#edit_form").serialize(),function(data){
			cs_handleResponse(data,function(param){				
				$edit_dialog.hide();
				filter(true);
			});
		});
	}
}