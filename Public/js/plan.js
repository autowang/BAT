$(document).ready(function(){
	$("#plan_page").addClass("active");
	
	//打开添加case的浮层
	$("#add-case").click(function(){
		id=$("#plan-id").val();
		$("#case_body").html();
		$.get(APP + "/Plan/allCase?id=" + id,function(result){
			$("#case_body").html(result.data);
		});
	});
	
	//添加case
	$("#case-do-add").click(function(){
		$('#add_case_modal').modal('toggle');
		$(".selectCell:checked").each(function(){
			tr = $(this).closest('tr');
			tr.find('td').first().remove();
			tr.append("<td><i class='icon-remove'></i></td>");
			$("#need-to-add").append(tr);
		});
		
		$(".icon-remove").click(function(){
			$(this).closest('tr').remove();
		});
	});
	
	//删除添加到测试计划的case
	$(".icon-remove").click(function(){
		$(this).closest('tr').remove();
	});
    //查找case
    $("#case_search").click(findCase);
	
	//添加测试计划表单提交
	$("#plan_add_form").submit(function(){
		$.ajax({
			type:"post",
			url:URL + "/doAdd",
			data:$('#plan_add_form').serialize(),
			success:function(result){
				status = result.status;
				if(status == 10001){
					redirect('/Login/login');
				}
				if(status == "success:true") {
					redirect('/Plan/plist');
				} 
				if(status == "success:false"){
					showError(result.info);
				}
			}
		});
		return false;
	});
	//打开更新计划页面
	$("#plan_update").click(updatePlan);
	
	//更新计划
	$("#plan_update_form").submit(doUpdate);
	
	//删除操作
	$("#plan_delete").click(deletePlan);
	//执行计划
	$("#plan_run").click(execute);
	
	$(".log-detail").click(function(){
		getLog($(this));
	});
});

//打开计划修改页面
function updatePlan(){
	var checked_num = $(".selectCell:checked").length; 
	if (checked_num > 1) {
		showError("一次只能修改一条记录！");
		return false;
	};

	if (checked_num < 1) {
		showError("请选择需要修改的记录！");
		return false;
	}; 
	var id = $(".selectCell:checked").attr("value");
	redirect("/Plan/update?id=" + id);
}

//执行更新测试计划
function doUpdate(){
	$.ajax({
		type:"post",
		url:URL + "/doUpdate",
		data:$('#plan_update_form').serialize(),
		success:function(result){
			status = result.status;
			if(status == 10001){
				redirect('/Login/login');
			}
			if(status == "success:true") {
				redirect('/Plan/plist');
			} 
			if(status == "success:false"){
				showError(result.info);
			}
		}
	});
	return false;
}
//删除测试计划
function deletePlan(){
	var checked_num = $(".selectCell:checked").length; 
	if (checked_num < 1) {
		showError("请选择需要删除的记录！");
		return false;
	}; 
	showError("该操作不可逆，你确认要删除这些记录吗？");
	$("#error_conform").click(function(){
		$('#error').modal('hide');
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
            		showError(result.info);
//            		$("#error_conform").unbind("click");
            		return false;
            	}
            },
        });	
	});
}
var findCase = function(){
    var id = $("#plan-id").val();
    var case_id = $("#case_id").val();
    var case_desc = $("#case_desc").val();
    if(case_id == '' && case_desc == ''){
        showInfo("请输入条件进行查询");
        return false;
    }
    $.ajax({
        type : "get",
        url : APP + "/Plan/allCase",
        data : "id=" + id + "&case_id=" + case_id + "&case_desc=" + case_desc,
        success:function(result){
            status = result.status;
            if(status == 10001){
                redirect('/Login/login');
            }
            if(status == 'success:false'){
                showInfo(result.info);
                return false;
            }
            $("#case_body").html(result.data);
        }
    });
}
//执行测试计划
function execute(){
	var checked_num = $(".selectCell:checked").length; 
	if (checked_num > 1) {
		showError("一次只能执行一个测试计划！");
		return false;
	};

	if (checked_num < 1) {
		showError("请选择需要执行的测试计划！");
		return false;
	}; 
	var id = $(".selectCell:checked").attr("value");
	$.ajax({
		type:"get",
		url:URL + "/execute",
		data:'planId=' + id,
		success:function(result){
			status = result.status;
			if(status == 10001){
				redirect('/Login/login');
			}
			if(status == "success:true") {
				redirect('/Plan/plist');
			} 
			if(status == "success:false"){
				showError(result.info);
			}
		}
	});
	showError("执行任务发起成功，点击计划描述，进入报告查看！");
}