$(document).ready(function() {
//添加KEYVALUE断言操作
    $('.add-rule-assert').unbind('click').click(function () {
        getKeyValueTmpl('keyvalue', '', $(this));
    });

    $("input[name^=assert_type]").unbind('click').click(function () {
        var i = $(this).attr("name").split("_")[2];
        switch ($("input[name=assert_type_"+i+"]:checked").attr("class")) {
            case "assert_full_"+i:
                $("div[name=assert_full_" + i + "]").show();
                $("div[name=assert_rule_" + i + "]").hide();
                break;
            case "assert_rule_"+i:
                $("div[name=assert_full_" + i + "]").hide();
                $("div[name=assert_rule_" + i + "]").show();
                break;
        }
    });
});

var trmoveToUp = function(){
    var move = $(this).closest(".assert_tr");
    var prev = move.prev(".assert_tr");
    prev.before(move);
}
//assert_tr步骤向下移动
var trmoveToDown = function(){
    var move = $(this).closest(".assert_tr");
    var next = $(this).closest(".assert_tr").next(".assert_tr");
    next.after(move);
}
//获取key-value模版的通用方法
var getKeyValueTmpl = function(action,params,dom){
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
            dom.parent().parent().parent().parent().append(result.data);
            //绑定删除按钮事件
            $(".icon-remove").click(function(){
                $(this).closest(".assert_tr").remove();
            });
            $(".assert_tr .icon-circle-arrow-up").last().click(trmoveToUp);
            $(".assert_tr .icon-circle-arrow-down").last().click(trmoveToDown);
        }
    });
}