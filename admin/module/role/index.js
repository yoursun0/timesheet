var filter_param = null;
var sort_param = null;
var $edit_dialog = null;
var $edit_dialog_validator = null;

$(function() {
	$edit_dialog = new Boxy($("#edit_dialog"),{title:"Add / Edit Form"});
	$edit_dialog_validator = $("#edit_form").validate();
	$("#main_list").tablesorter({
		ajaxPager:"#main_pager",
		ajaxSorter:function(page,sort){
			filter(true,$.param({pager:$.param(page),"sorter[]":sort}));
		}
	});	
	filter();
});

function getAjaxUrl(fn, m){
    return "ajax.php?fn=" + fn + "&m=" + (m || "role");
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