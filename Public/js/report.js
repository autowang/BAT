$(document).ready(function(){
	$(".step_detail").first().addClass("active");
	
	$(".log-detail").click(function(){
		getLog($(this));
	});
	
	$(".running").each(function(){
		if($.trim($(this).text()) != "执行完成"){
			setTimeout("location.reload()",2000);
		}
	});
});



