$(document).ready(function(){

	//登录验证逻辑
	$("#doLogin").click(function(){
		username = $("input[name=username]").val();
		password = $("input[name=password]").val();
		imgCode = $("input[name=imgCode]").val();
		if (username == '') {
    		showInfo("请填写正确的用户名!");
    		return false;
		};

		if (password == '') {
    		showInfo("请填写正确的密码!");
    		return false;
		};

		if (imgCode == '') {
    		showInfo("请填写正确的验证码!");
    		return false;
		};
		$.ajax({
			url:URL + "/doLogin",
			data:$('#formLogin').serialize(),
			type:"POST",
			success:function(result){
				if (result.status == 'success:true') {
					window.location = APP + "/Index/index";
				}else{
            		showInfo(result.info);
            		return false;
				}
			}
		})
		return false;
	});
});

var showInfo = function(msg){
	$("#errmsg span").text(msg);
	$("#errmsg").fadeIn(1000);
	$("#errmsg").fadeOut(2000);	
}