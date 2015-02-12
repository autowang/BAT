//切换空间
$(document).ready(function(){
	getAppInfo();
	controlSelect();
	//重置查询条件
    $("#reset-btn").click(resetSearchKey);
    
    $(".bs-docs-sidenav li").click(function(){
		$(".bs-docs-sidenav li").removeClass("active");
		$(this).addClass("active");
	});
	
	$("#api_path,input[name=api_path]").keyup(function(){
		$(this).typeahead({
		    source: function (query, process) {
		         $.get(APP + '/Api/apis', { path: query }, function (result) {
		         	var temp = [];
		         	apis = $.parseJSON(result.data);
		        	for(i in apis){
		        		temp.push(apis[i]);
		        	}
		            process(apis);
		        });
		    },
		    items:5
		});
	});
	
});



//全选和取消全选的控制
var controlSelect = function(){
	$("#selectAll").click(function(){
		var checked = $("#selectAll").is(':checked');
		$(".selectCell").prop("checked",checked);
	});
}
//重置搜索条件
var resetSearchKey = function(){
	$(this).prevAll('input:visible').each(function(){$(this).val('')});
	$(this).prevAll("select").each(function(){
		$(this).find("option").first().attr("selected",true);
	});
}
//显示错误浮层
var showError = function(msg){
	$("#error_body").text(msg);
	$('#error').modal('show');
}

var showInfo = function(msg){
	$("#errmsg span").text(msg);
	$("#errmsg").slideToggle();
	setTimeout("$('#errmsg').slideToggle(1000)",4000);
}

var getAppInfo = function(){
	$.ajax({
		url:APP + "/app/appInfo",
		data:null,
		method:"post",
		success:function(result){
			status = result.status;
        	if(status == 10001){
        		redirect('/Login/login');
        	}
			if(status == 'success:false'){
				showError(result.info);
				return false;
			}
			$("#space_menu").append(result.data);
			//给item绑定click事件
			$(".menu_item").click(function(){
				changeApp($(this));  
			});
		}
	});
}

var changeApp = function(obj){
	var appId = obj.attr("id");
	var appName = obj.text();
	$.ajax({
		url:APP + "/app/switchApp",
		data:"appId=" + appId + "&appName=" + appName,
		method:"post",
		success:function(result){
			status = result.status;
        	if(status == 10001){
        		redirect('/Login/login');
        	}
			if (status == 'success:true') {
				location.href = APP;
			}
			if(status == 'success:false'){
				showInfo(result.info);
				return false;	
			}
		}
	});
}

function redirect(target){
	location.href = APP + target;
}

//查看执行日志
function getLog(obj){
	reportId = obj.attr("id");
	$.ajax({
		type : "get",
		url : APP + "/Report/getLog",
		data : "reportId=" + reportId,
		success:function(result){
			status = result.status;
        	if(status == 10001){
        		redirect('/Login/login');
        	}
        	if(status == 'success:true'){
        		obj.popover({
					content:"<pre style='margin:0px;'>" + result.data + "</pre>",
					html:true
        		}).popover('show');
        	}
        	//鼠标移开后，销毁log浮层
        	$(".log-detail").mouseleave(function(){
				obj.popover('destroy');
			});
		}
	});
}

function getModules(){
	$.ajax({
		type : "get",
		url : APP + "/Module/modules",
		success:function(result){
			status = result.status;
        	if(status == 10001){
        		redirect('/Login/login');
        	}
        	if(status == 'success:true'){
        		$("select[name=mid]").html(result.data);
        		s = $("select[name=mid]").attr("value");
				$("select[name=mid] option").each(function(){
					if($(this).val() == s){
						$(this).attr("selected",true);
					}
				});
        	}
		}
	});
}
