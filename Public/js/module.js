function OnRightClick(event, treeId, treeNode) {
	if (!treeNode && event.target.tagName.toLowerCase() != "button" && $(event.target).parents("a").length == 0) {
		zTree.cancelSelectedNode();
		showRMenu("root", event.clientX, event.clientY);
	} else if (treeNode && !treeNode.noR) {
		zTree.selectNode(treeNode);
		showRMenu("node", event.clientX, event.clientY);
	}
}

function showRMenu(type, x, y) {
	$("#rMenu ul").show();
	if (type=="root") {
		$("#m_del").hide();
		$("#m_check").hide();
		$("#m_unCheck").hide();
	} else {
		$("#m_del").show();
		$("#m_check").show();
		$("#m_unCheck").show();
	}
	rMenu.css({"top":y+"px", "left":x+"px", "visibility":"visible"});

	$("body").bind("mousedown", onBodyMouseDown);
}
function hideRMenu() {
	if (rMenu) rMenu.css({"visibility": "hidden"});
	$("body").unbind("mousedown", onBodyMouseDown);
}
function onBodyMouseDown(event){
	if (!(event.target.id == "rMenu" || $(event.target).parents("#rMenu").length>0)) {
		rMenu.css({"visibility" : "hidden"});
	}
}
var addCount = 1;
function addTreeNode() {
	hideRMenu();
	var newNode = { name:"增加" + (addCount++)};
	if (zTree.getSelectedNodes()[0]) {
		newNode.checked = zTree.getSelectedNodes()[0].checked;
		zTree.addNodes(zTree.getSelectedNodes()[0], newNode);
	} else {
		zTree.addNodes(null, newNode);
	}
}
function removeTreeNode() {
	hideRMenu();
	var nodes = zTree.getSelectedNodes();
	if (nodes && nodes.length>0) {
		if (nodes[0].children && nodes[0].children.length > 0) {
			var msg = "要删除的节点是父节点，如果删除将连同子节点一起删掉。\n\n请确认！";
			if (confirm(msg)==true){
				zTree.removeNode(nodes[0]);
			}
		} else {
			zTree.removeNode(nodes[0]);
		}
	}
}

function editTreeNode(){
	console.info("edit node");
}

var zTree, rMenu;
$(document).ready(function(){
    //初始化select控件
    $("#addedApps").select2({
        placeholder: "Select App(s)",
        allowClear: true
    });
    //提交新增操作
    $("#module_add_form").submit(doAdd);
	var setting = {
		view: {
			dblClickExpand: false
		},
		check: {
			enable: true
		},
		callback: {
			onRightClick: OnRightClick
		}
	};
	$.ajax({
		 type: "get",
	     url: APP + "/Module/mlist",
	     data: "pid=0",
	     success:function (result) {
	     	status = result.status;
	     	if(status == 10001){
	     		redirect('/Login/login');
	     	}
	     	if (status == "success:true") {
	     		zNodes = $.parseJSON(result.data);
				$.fn.zTree.init($("#treeDemo"), setting, zNodes);
				zTree = $.fn.zTree.getZTreeObj("treeDemo");
				rMenu = $("#rMenu");
	     	}
	     }
	});
});
//新增module到数据库
var doAdd = function(){
    var apps = " ";
    var obj = $("#addedApps").find("option:selected");
    for(var i=0; i<obj.length-1; i++){
        apps += obj[i].value + " ";
    }
    $.ajax({
        type:"post",
        url:URL + "/doAdd",
        data:$('#module_add_form').serialize() + apps,
        success:function(result){
            status = result.status;
            if(status == 10001){
                redirect('/Login/login');
            }
            if(status == "success:true") {
                redirect('/Module/mlist');
            }
            if(status == "success:false"){
                console.info(status);
                showInfo(result.info);
            }
        }
    });
    return false;
}

