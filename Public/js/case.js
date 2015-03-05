$(document).ready(function(){
	getModules();
	updatePageBindEvent();
	$("#case_page").addClass("active");
	//更新操作
	$("#case_update").click(function(){
		var checked_num = $(".selectCell:checked").length; 
		if (checked_num > 1) {
			showInfo("一次只能修改一条记录！");
			return false;
		};
		if (checked_num < 1) {
			showInfo("请选择需要修改的记录！");
			return false;
		}; 
		var id = $(".selectCell:checked").attr("value");
		location.href = URL + "/update?id=" + id;
	});

	//重置查询条件
	$("#reset-btn").click(resetSearchKey);
	
	//删除操作
	$("#case_delete").click(deleteCase);
	
	//给异步返回的复层中查找按钮添加点击事件
	$("#api_search").click(getApiTable);
	
	//添加断言操作
	$(".add-assert").click(function(){
		getTmpl('assert','');
	});
	
	//添加参数提取器
	$(".add-regex").click(function(){
		getTmpl('regex','');
	});	
	//添加脚本执行器
	$(".add-bsf").click(function(){
		getTmpl('bsf','');
	});	
	
	//新增case
	$("#add_save_btn").click(function(){
		getCaseDetails("doAdd");
	});
	//更新case
	$("#update_save_btn").click(function(){
		getCaseDetails("doUpdate");
	});
	
	//执行case
	$('#case_run').click(executeCase);
	
	//下载case
	$("#case_download").click(caseDownLoad);
	
	//查看结果
	$("#case_report").click(viewReport);
	
	//添加case浮层
	$("#case_search").click(findCase);
	
	//复制用例
	$("#case_copy").click(caseCopy);
	
	//查询条件
	$("input[name=radio_type]").click(function(){
		$("input[name=case_type]").val($(this).val());
	});
	
	//设置dlist页类型查询条件的值
	$("select[name=case_type]").find("option[value="+$("select[name=case_type]").attr("value")+"]").attr("selected",true);
	
	//设置case更新概要信息tab中用例类型的值
	$("input[name=radio_type]").each(function(){
		if($(this).val() == $("input[name=case_type]").val()){
			$(this).attr("checked",true);
			return;
		}
	});
});


var findCase = function(){
	var id = $("#case_id").val();
	var desc = $("#case_desc").val();
	if(id == '' && desc == ''){
		showInfo("请输入条件进行查询");
		return false;
	}
	$.ajax({
		type : "get",
		url : URL + "/getCase",
		data : "id=" + id + "&desc=" + desc,
		success:function(result){
			status = result.status;
        	if(status == 10001){
        		redirect('/Login/login');
        	}
        	if(status == 'success:false'){
        		showInfo(result.info);
        		return false;
        	}
        	ret = eval(result.data);
        	html = '';
        	for(i=0;i<ret.length;i++){
        		html += "<tr><td>" + ret[i].id + "</td><td>" + ret[i].desc + "</td><td><i class='icon-plus add-case' id="+ ret[i].id +"></i>"; 
        	}
			$("#case_body").html(html);
			//给增加按钮绑定点击事件
			$(".add-case").click(function(){
				getTmpl('testcase','id='+$(this).attr('id'));
			});
		}
	});
}

//获取api-table
var getApiTable = function(){
	var path = $("#api_path").val();
	if(path == ''){
		showInfo("请填写查询条件");
		return false;
	}
	$.ajax({
		type : "get",
		url : URL + "/allApis",
		data : "path=" + path,
		success:function(result){
			status = result.status;
        	if(status == 10001){
        		redirect('/Login/login');
        	}
			$("#api_table").html(result.data);
			//给增加按钮绑定点击事件
			$(".add-api").click(function(){
				id = $(this).attr("id");
				getTmpl('http','id='+id);
			});
		}
	});
}

//case步骤向上移动
var moveToUp = function(){
	var move = $(this).closest(".accordion-group");
	var prev = move.prev(".accordion-group");
	prev.before(move);
}
//case步骤向下移动
var moveToDown = function(){
	var move = $(this).closest(".accordion-group");
	var next = $(this).closest(".accordion-group").next(".accordion-group");
	next.after(move);
}

//删除case操作
var deleteCase = function(){
	var checked_num = $(".selectCell:checked").length; 
	if (checked_num < 1) {
		showInfo("请选择需要删除的记录！");
		return false;
	}; 
	showError("该操作不可逆，你确认要删除这些记录吗？");
	$("#error_conform").click(function(){
		$("#error_conform").unbind("click");
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
            		showInfo(result.info);
            	}
            }
        });	
	});
}

//获取模版的通用方法
var getTmpl = function(action,params){
	$.ajax({
		type : "get",
		url : APP + "/Component/" + action,
		data : params,
		success:function(result){
			status = result.status;
        	if(status == 10001){
        		redirect('/Login/login');
        	}
			if(status == "success:false"){
				showError(result.info);
				return false;
			}
			$("#accordion").append(result.data);
			$('.modal').modal('hide');
			//绑定删除按钮事件
			$(".icon-remove").click(function(){
				$(this).closest(".accordion-group").remove();
			});
			$("#accordion .icon-circle-arrow-up").last().click(moveToUp);
			$("#accordion .icon-circle-arrow-down").last().click(moveToDown);
			$(".accordion-heading").mouseover(function(){
				$(this).css('background-color','#f5f5f5');
			});
			$(".accordion-heading").mouseout(function(){
				$(this).css('background-color','');
			});
			
			$(".http input").keyup(function(){
				keys($(this));
			});
			
			$(".stype").click(function(){
				select = $(this).val();
				$(this).prev("input[name=script_type]").val(select);
			});
		}
	});
}

var getCaseDetails = function(action){
	if(false == checkApiNextToAssert()){
		return false;
	}
	step = {};data = {};
	summary = formToJson($("#case_summary"));
	//TODO稍后请添加前端对测试用例数据的验证
	$(".accordion-group").each(function(i){
		step[i] = formToJson($(this));
	});
	data['summary'] = summary;
	data['steps'] = step;
	if(action == 'doAdd'){
		param = {caseData:JSON.stringify(data)};
	}
	if(action == 'doUpdate'){
		id = $("#update_case_id").val();
		param = {caseData:JSON.stringify(data),id:id};
	}
	$.post(URL + "/" + action,param)
	.success(function(result){
		status = result.status;
    	if(status == 10001){
    		redirect('/Login/login');
    	}
		if(status == 'success:true'){
			redirect('/Case/clist');
		}
		if(status == 'success:false'){
			showInfo(result.info);
		}
		
	});
}

var formToJson = function(obj){
	var data = {};
	obj.find("input,textarea,select").each(function(){
		var key = $(this).attr('name');
		var value = $(this).val();
		data[key] = value;
	});
	return data;
}

var updatePageBindEvent = function(){
	$(".icon-remove").click(function(){
		$(this).closest(".accordion-group").remove();
	});
	$("#accordion .icon-circle-arrow-up").click(moveToUp);
	$("#accordion .icon-circle-arrow-down").click(moveToDown);
	
	//给步骤添加鼠标over时的背景
	$(".accordion-heading").mouseover(function(){
		$(this).css('background-color','#f5f5f5');
	});
	
	//mouseout之后，去除背景色
	$(".accordion-heading").mouseout(function(){
		$(this).css('background-color','');
	});
	
	//给类型为HTTP的步骤添加输入提示功能
	$(".http input").keyup(function(){
		keys($(this));
	});
	
	//给select绑定点击事件
	$(".stype").click(function(){
		select = $(this).val();
		$(this).prev("input[name=script_type]").val(select);
	});
	
	//为select设置值
	$("input[name=script_type]").each(function(){
		selectItem = $(this).val();
		$(this).next("select").find("option[value="+selectItem+"]").attr("selected",true);
	});
}

var checkApiNextToAssert = function(){
	result = true;
	$('.http').each(function(){
		className = $(this).next().attr('class');
		if(className != 'accordion-group assert'){
			showError('每个API后面必须紧跟断言，请调整用例步骤，谢谢！');
			result = false;
			return false;
		}
	});
	return result;
}

var executeCase = function(){
	var checked_num = $(".selectCell:checked").length; 
	if (checked_num > 1) {
		showError("调试模式一次只能执行一条记录！");
		return false;
	};
	if (checked_num < 1) {
		showError("请选择需要执行的测试用例！");
		return false;
	}; 
	var id = $(".selectCell:checked").attr("value");
	var type = $(".selectCell:checked").attr("ctype");
	if(type==3){
		showError("类型为方法的用例不能够单独执行！");
		return false;
	}
	$.ajax({
		type : "post",
		url : URL + "/execute",
		data : "id="+id + "&type="+type,
		success:function(result){
			console.info(result);
			status = result.status;
        	if(status == 10001){
        		redirect('/Login/login');
        	}
			if(status == "success:false"){
				showError(result.info);
				return false;
			}
			if(status == "success:true"){
				location.href = APP + '/Report/crlist?caseId=' + id;
			}
		}
	});
}

var caseDownLoad = function(){
	var checked_num = $(".selectCell:checked").length; 
	if (checked_num > 1) {
		showInfo("一次只能下载一条记录！");
		return false;
	};
	if (checked_num < 1) {
		showInfo("请选择需要下载的记录！");
		return false;
	}; 
	var id = $(".selectCell:checked").attr("value");
	location.href = APP + "/Case/downloadCase?id="+id;
}

//查看结果
var viewReport = function(){
	var checked_num = $(".selectCell:checked").length; 
	if (checked_num > 1) {
		showInfo("一次只能操作一条记录！");
		return false;
	};
	if (checked_num < 1) {
		showInfo("请选择需要操作的记录！");
		return false;
	}; 
	var id = $(".selectCell:checked").attr("value");
	location.href = APP + '/Report/crlist?caseId=' + id;
}

//复制用例
function caseCopy(){
	var checked_num = $(".selectCell:checked").length; 
	if (checked_num > 1) {
		showInfo("一次只能复制一条记录！");
		return false;
	};
	if (checked_num < 1) {
		showInfo("请选择需要复制的记录！");
		return false;
	}; 
	var id = $(".selectCell:checked").attr("value");
	$.ajax({
		type : "post",
		url : APP + "/Case/copy",
		data : "id="+id,
		success:function(result){
			status = result.status;
        	if(status == 10001){
        		redirect('/Login/login');
        	}
			if(status == "success:false"){
				showError(result.info);
				return false;
			}
			if(status == 'success:true'){
				location.reload();
			}
		}
	});
}

//参数化模糊查询，输入提示
var keys = function(obj){
	obj.typeahead({
	    source: function (query, process) {
	         $.get(APP + '/Data/keys', { key: query }, function (result) {
	         	var temp = [];
	         	$keys = $.parseJSON(result.data);
	        	for(i in $keys){
	        		temp.push($keys[i]);
	        	}
	            process($keys);
	        });
	    },
	    items:6,
	    updater:function(item){
	    	return "${" + item + "}";
	    }
	});
}