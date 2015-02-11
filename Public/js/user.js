$(document).ready(function(){
	//选中的菜单高亮
	$("#user_page").addClass("active");

	//提交新增操作
	$("#user_add_form").submit(doAdd);
	
	//提交更新表单
	$("#user_update_form").submit(doUpdate);
	
	//提交修改个人资料表单
	$("#user_update_info_form").submit(doUpdateInfo);
	
	//提交修改密码表单
	$("#user_update_pwd_form").submit(doUpdatePwd);
	//初始化select控件
	$("#addedApps").select2({
	    placeholder: "Select App(s)",
	    allowClear: true
	});
	
	
	
	//更新操作勾选检查
	$("#user_update").click(function(){
		var checked_num = $(".selectCell:checked").length; 
		if (checked_num > 1) {
			showError("一次只能修改一条记录！");
			return false;
		};

		if (checked_num < 1) {
			showInfo("请选择需要修改的记录！");
			return false;
		}; 

		var id = $(".selectCell:checked").attr("value");
		redirect("/User/update?id=" + id);
	});


	//删除操作
	$("#user_delete").click(function(){
		var checked_num = $(".selectCell:checked").length; 
		if (checked_num < 1) {
			showInfo("请选择需要删除的记录！");
			return false;
		}; 

		showError("该操作不可逆，你确认要删除这些记录吗？");

		$("#error_conform").click(function(){
			$('#error').modal('hide');
			var ids = new Array();
			$(".selectCell:checked").each(function(){
				id = $(this).attr("value");
				ids.push(id);
			});
	        $.ajax({
	            type: "post",
	            url: URL + "/delete",
	            data: "ids=" + ids,
	            success:function (result) {
	            	status = result.status;
	            	if(status == 10001){
	            		redirect('/Login/login');
	            	}
	            	if (status == "success:true") {
	            		location.reload();
	            	}
	            	if(status == 'success:false'){
	            		showError(result.info);
	            		return false;
	            	}
	            },
	        });	
		});
	});
});

//新增user到数据库
var doAdd = function(){
	var apps = " ";
	var obj = $("#addedApps").find("option:selected");
	for(var i=0; i<obj.length-1; i++){
		apps += obj[i].value + " ";
	}
	$.ajax({
		type:"post",
		url:URL + "/doAdd",
		data:$('#user_add_form').serialize() + apps,
		success:function(result){
			status = result.status;
			if(status == 10001){
				redirect('/Login/login');
			}
			if(status == "success:true") {
				redirect('/User/ulist');
			} 
			if(status == "success:false"){
				console.info(status);
				showInfo(result.info);
			}
		}
	});
	return false;
}

//异步更新user
var doUpdate = function(){
	var apps = " ";
	var obj = $("#addedApps").find("option:selected");
	for(var i=0; i<obj.length-1; i++){
		apps += obj[i].value + " ";
	}
	$.ajax({
		type:"post",
		url:URL + "/doUpdate",
		data:$('#user_update_form').serialize() + apps,
		success:function(result){
			status = result.status;
			if(status == 10001){
				redirect('/Login/login');
			}
			if(status == "success:true") {
				redirect('/User/ulist');
			} 
			if(status == 'success:false'){
				showInfo(result.info);
			}
		}
	});
	return false;
}

//异步更新user个人信息
var doUpdateInfo = function(){
	$.ajax({
		type:"post",
		url:URL + "/doUpdateInfo",
		data:$('#user_update_info_form').serialize(),
		success:function(result){
			status = result.status;
			if(status == 10001){
				redirect('/Login/login');
			}
			if(status == "success:true") {
				redirect('/Index/index');
			} 
			if(status == 'success:false'){
				showInfo(result.info);
			}
		}
	});
	return false;
}

//异步修改user密码
var doUpdatePwd = function(){
	$.ajax({
		type:"post",
		url:URL + "/doUpdatePwd",
		data:$('#user_update_pwd_form').serialize(),
		success:function(result){
			status = result.status;
			if(status == 10001){
				redirect('/Login/login');
			}
			if(status == "success:true") {
				redirect('/Login/loginOut');
			} 
			if(status == 'success:false'){
				showInfo(result.info);
			}
		}
	});
	return false;
}



