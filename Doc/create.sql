-- --------------------------------------------------------
-- 主机:                           localhost
-- 服务器版本:                        5.6.17 - MySQL Community Server (GPL)
-- 服务器操作系统:                      Win64
-- HeidiSQL 版本:                  8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 导出 bat 的数据库结构
CREATE DATABASE IF NOT EXISTS `bat` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `bat`;


-- 导出  表 bat.t_api 结构
CREATE TABLE IF NOT EXISTS `t_api` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `desc` varchar(1000) NOT NULL COMMENT 'api描述',
  `host` varchar(100) NOT NULL COMMENT '域名',
  `port` int(8) NOT NULL COMMENT '端口',
  `path` varchar(1000) NOT NULL COMMENT 'url的path部分',
  `protocol` varchar(8) NOT NULL COMMENT '协议类型',
  `method` varchar(8) NOT NULL COMMENT '方法类型',
  `requestContent` text NOT NULL COMMENT '请求头信息',
  `params` text COMMENT '参数列表，存json',
  `header` varchar(2000) DEFAULT NULL COMMENT 'http原始信息头信息',
  `status` int(1) NOT NULL COMMENT '0-无效，1-有效',
  `appId` int(8) NOT NULL,
  `userId` int(8) NOT NULL,
  `mid` int(8) NOT NULL COMMENT '模块ID',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。


-- 导出  表 bat.t_app 结构
CREATE TABLE IF NOT EXISTS `t_app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appName` varchar(50) NOT NULL DEFAULT '0' COMMENT 'app名称',
  `appDesc` varchar(100) NOT NULL DEFAULT '0' COMMENT 'app描述',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='app列表';

-- 数据导出被取消选择。


-- 导出  表 bat.t_case 结构
CREATE TABLE IF NOT EXISTS `t_case` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `desc` varchar(500) NOT NULL COMMENT '用例描述',
  `type` int(11) NOT NULL COMMENT '1-功能用例，2-异常用例;，3-方法用例',
  `threadNum` int(8) NOT NULL COMMENT '线程数',
  `rampUpPeriod` int(8) NOT NULL COMMENT '多少秒达到指定的线程数',
  `times` int(8) NOT NULL COMMENT '执行次数',
  `runTime` int(8) NOT NULL COMMENT '执行时间',
  `debugHost` varchar(16) NOT NULL,
  `appId` int(8) NOT NULL,
  `userId` int(8) NOT NULL,
  `status` int(8) NOT NULL COMMENT '0-无效；1-有效',
  `mid` int(8) NOT NULL COMMENT '模块ID',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。


-- 导出  表 bat.t_data 结构
CREATE TABLE IF NOT EXISTS `t_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL COMMENT '查找的key',
  `value` varchar(2000) NOT NULL COMMENT '测试数据的值',
  `desc` varchar(500) NOT NULL COMMENT '描述',
  `appId` int(50) NOT NULL COMMENT '所属app的id',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。


-- 导出  表 bat.t_detail 结构
CREATE TABLE IF NOT EXISTS `t_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caseId` int(11) NOT NULL DEFAULT '0',
  `apiId` int(11) NOT NULL,
  `requestHeader` text NOT NULL COMMENT '请求信息',
  `responseHeader` text NOT NULL COMMENT '响应信息',
  `responseBody` longtext NOT NULL COMMENT '响应正文',
  `latency` varchar(50) NOT NULL COMMENT '耗时',
  `assertContent` varchar(2000) NOT NULL COMMENT '断言内容',
  `result` int(1) NOT NULL COMMENT '1-成功，2-失败',
  `reportId` int(11) NOT NULL COMMENT '报告主表id',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。


-- 导出  表 bat.t_module 结构
CREATE TABLE IF NOT EXISTS `t_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desc` varchar(500) DEFAULT NULL COMMENT '节点名称',
  `status` int(11) DEFAULT '1' COMMENT '1-有效，2-无效',
  `appId` int(11) DEFAULT NULL,
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。


-- 导出  表 bat.t_plan 结构
CREATE TABLE IF NOT EXISTS `t_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desc` varchar(500) NOT NULL DEFAULT '0' COMMENT '计划描述',
  `appId` varchar(500) NOT NULL DEFAULT '0' COMMENT '所属App',
  `userId` varchar(500) NOT NULL DEFAULT '0',
  `period` varchar(50) NOT NULL DEFAULT '0' COMMENT '执行策略',
  `caseIds` varchar(1000) NOT NULL DEFAULT '0' COMMENT '需要执行的用例id',
  `data` varchar(1000) NOT NULL DEFAULT '0' COMMENT '专属与该测试计划的测试数据，覆盖共有数据',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '0-无效；1-有效',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。


-- 导出  表 bat.t_report 结构
CREATE TABLE IF NOT EXISTS `t_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `objId` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL COMMENT '1-执行中，2-执行完成',
  `type` int(11) DEFAULT NULL COMMENT '1-测试用例，2-测试计划',
  `log` longtext COMMENT '存放执行时控制台输出的log',
  `prid` int(11) DEFAULT '-1' COMMENT '如果type=1，则该字段存放的是该条记录所述测试计划的reportId',
  `result` int(11) DEFAULT '-1' COMMENT '1-成功，2-失败',
  `createTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。


-- 导出  表 bat.t_session 结构
CREATE TABLE IF NOT EXISTS `t_session` (
  `session_id` varchar(255) NOT NULL,
  `session_expire` int(11) NOT NULL,
  `session_data` blob,
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。


-- 导出  表 bat.t_step 结构
CREATE TABLE IF NOT EXISTS `t_step` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caseId` int(11) NOT NULL DEFAULT '0' COMMENT '测试用例id',
  `stepId` int(11) NOT NULL DEFAULT '0' COMMENT '步骤编号',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '1-http;2-assert;3-regex;4-case',
  `content` varchar(5000) NOT NULL DEFAULT '0' COMMENT '步骤内容',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用例步骤详情表';

-- 数据导出被取消选择。


-- 导出  表 bat.t_user 结构
CREATE TABLE IF NOT EXISTS `t_user` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '密码，MD5加密存储',
  `phone` varchar(16) NOT NULL COMMENT '电话',
  `email` varchar(50) NOT NULL COMMENT '邮箱',
  `role` varchar(50) NOT NULL COMMENT '1-管理员，2-测试人员，3-开发人员',
  `defaultApp` int(10) NOT NULL DEFAULT '-1' COMMENT '默认的appid',
  `createTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。


-- 导出  表 bat.t_user_app 结构
CREATE TABLE IF NOT EXISTS `t_user_app` (
  `userId` int(11) NOT NULL,
  `appId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 数据导出被取消选择。
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
