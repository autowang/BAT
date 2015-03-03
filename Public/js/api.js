$(document).ready(function(){
	getModules();
	//选中的菜单高亮
	$("#api_page").addClass("active");

	//提交新增操作
	$("#api_add_form").submit(doAdd);
	
	//提交更新表单
	$("#api_update_form").submit(doUpdate);
	
	$("select").find("option[value="+$("select").attr("value")+"]").attr("selected",true);
	//更新操作勾选检查
	$("#api_update").click(function(){
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
		redirect("/Api/update?id=" + id);
	});
	
	$('#case_add').click(function(){
		var checked_num = $(".selectCell:checked").length; 
		if (checked_num > 1) {
			showError("一次只能操作一条记录！");
			return false;
		};

		if (checked_num < 1) {
			showError("请选择需要操作的记录！");
			return false;
		}; 

		var id = $(".selectCell:checked").attr("value");
		redirect("/case/add?apiId=" + id);
	});
	

	//删除操作
	$("#api_delete").click(function(){
		var checked_num = $(".selectCell:checked").length; 
		if (checked_num < 1) {
			showInfo("请选择需要删除的记录！");
			return false;
		}; 

		showError("该操作不可逆，你确认要删除这些记录吗？");

		$("#error_conform").click(function(){
			$('#error').modal('toggle');
			var ids = new Array();
			$(".selectCell:checked").each(function(){
				id = $(this).val();
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
	            		showInfo(result.info);
	            		$("#error_conform").unbind("click");
	            		return false;
	            	}
	            }
	        });	
		});
	});

});


//新增api到数据库
var doAdd = function(){
	$.ajax({
		type:"post",
		url:URL + "/doAdd",
		data:$('#api_add_form').serialize(),
		success:function(result){
			status = result.status;
			if(status == 10001){
				redirect('/Login/login');
			}
			if(status == "success:true") {
				redirect('/Api/alist');
			} 
			if(status == "success:false"){
				showInfo(result.info);
			}
		}
	});
	return false;
}

//异步更新api
var doUpdate = function(){
	$.ajax({
		type:"post",
		url:URL + "/doUpdate",
		data:$('#api_update_form').serialize(),
		success:function(result){
			status = result.status;
			if(status == 10001){
				redirect('/Login/login');
			}
			if(status == "success:true") {
				redirect('/Api/alist');
			} 
			if(status == 'success:false'){
				showInfo(result.info);
			}
		}
	});
	return false;
}




