<?php
class ModuleModel extends CommonModel{

    protected $_auto = array (
        array('updateTime','getCurrentTime',3,'function'),
    );
    protected $_validate = array(
        //array('appId','require','关联App不能为空'),
        array('name','require','模块名称不能为空'),
        array('desc','require','描述内容不能为空'),
    );

    public function getModule($where="1=1"){
        return $this->where($where)->select();
    }
    /*
	 * 检查module是否符合规范
	 */
    public function checkModule($condition){
        //TODO
        return $condition;
    }

    /*
     * 检查Module是否已经存在
     */
    public function checkDuplicate($condition){
        return M('Module')->where($condition)->find();
    }

    /*
	 * 根据ModuleId返回Module名称
	 */
    public function getModuleNameByModuleId($moduleId)
    {
        $condition['id'] = $moduleId;
        $module = M('Module')->where($condition)->select();
        return $module[0]['name'];
    }
}