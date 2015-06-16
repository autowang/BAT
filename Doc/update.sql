ALTER TABLE `t_module`
ADD COLUMN `updateTime` TIMESTAMP  NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'update时间' AFTER `createTime`,
ADD COLUMN `name` VARCHAR(50)  NOT NULL COMMENT 'module名称' AFTER `id`;

UPDATE `t_module`
SET `name` = `desc`;