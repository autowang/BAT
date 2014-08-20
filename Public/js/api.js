$(document).ready(function(){
	$("#api_page").addClass("active");

	//全选控制
	$("#selectAll").click(function(){
		var checked = $("#selectAll").is(':checked');
		$(".selectCell").prop("checked",checked);
	});
});