$(document).ready(function(){
	
	$("#data_page").addClass("active");

	//打开更新浮层
	$("#data_update").click(update);
	
	//重置查询条件
	$("#reset-btn").click(resetSearchKey);

	//删除
	$("#data_delete").click(deleteData);
	
	$("input[name=key]").keyup(function(){
		keys($(this));
	});
	
	$("input[name=upload_file]").change(function(){
		var file = $(this).get(0).files[0];
		$("input[name=value]").val(file.name);
	});
	
});

function update(){
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
	location.href = URL + "/update?id=" + id;
}

function deleteData(){
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
            		$("#error_conform").unbind("click");
            		return false;
            	}
            }
        });	
	});
}

function keys(obj){
	obj.typeahead({
	    source: function (query, process) {
	         $.get(URL + '/keys', { key: query }, function (result) {
	         	var temp = [];
	         	$keys = $.parseJSON(result.data);
	        	for(i in $keys){
	        		temp.push($keys[i]);
	        	}
	            process($keys);
	        });
	    },
	    items:6
	});
}
