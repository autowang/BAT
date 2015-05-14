## BAT能做什么
* 支持HTTP,HTTPS等协议的接口测试工具
* 0编程，操作体验友好
* 包含API管理；测试数据管理；测试用例编写、执行，用例的复用；测试归回计划指定等功能。

####更多信息<a href="https://github.com/autowang/BAT/blob/master/Doc/BAT%E4%BD%BF%E7%94%A8%E6%89%8B%E5%86%8C.docx?raw=true">点击这里</a>查看使用手册
####交流QQ群：188734537

=====
## BAT运行环境要求
* JDK 1.6+ ，请配置好JAVA_HOME的环境变量
* PHP 5.4+ 
* apache
* windows or linux
* mysql

注意：项目BAT/Doc/下有初始化的sql语句，记得执行哦，亲~

运行环境搭建略过，可以<a href="http://www.php100.com/html/itnews/it/2013/0219/12062.html" title="Title">点击这里</a>查看参考

开始使用
=============
系统初始管理员账户：admin admin
### 添加APP
--------
在项目管理>APP管理中添加你的业务线，如下图：

![APP管理](https://github.com/autowang/Static/blob/master/images/add-app.jpg)

### 添加用户
--------
在项目管理>用户管理中添加系统用户：
将添加的用户与所属的APP进行关联，一个user可以拥有多个APP空间权限

![用户管理](https://github.com/autowang/Static/blob/master/images/user-manager.jpg)

注：手机和邮箱请填写正确，涉及后续测试结果的短信或者邮件通知。
新增用户的默认初始密码是：123456，用户登录系统后可以自助修改密码。


