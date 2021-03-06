/*
 Navicat MySQL Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50640
 Source Host           : localhost:3306
 Source Schema         : payment

 Target Server Type    : MySQL
 Target Server Version : 50640
 File Encoding         : 65001

 Date: 03/08/2018 18:41:02
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL COMMENT '登录名',
  `password_digest` varchar(64) NOT NULL COMMENT '密码',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属权组',
  `realname` varchar(16) NOT NULL,
  `mobile` varchar(16) NOT NULL COMMENT '手机号码',
  `email` varchar(128) NOT NULL COMMENT '邮箱',
  `effect_date` varchar(16) NOT NULL DEFAULT '0000-00-00' COMMENT '帐号过期时间',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created_at` int(11) unsigned NOT NULL COMMENT '添加时间',
  `updated_at` int(11) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_login_name` (`username`,`role_id`,`mobile`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- ----------------------------
-- Records of admin
-- ----------------------------
BEGIN;
INSERT INTO `admin` VALUES (0, 'system', '0', 0, 'system', 'system', 'admin@system.com', '0000-00-00', 0, 1481164840, 1481164840);
INSERT INTO `admin` VALUES (1, 'root', '$2y$13$G0CohVc8f.SzLAZ5UHZeIOHanKLmlR0R852yZNv3vc2IYU/iOPjTi', 1, 'Super manager', '13761665439', 'flydany@yeah.net', '2018-12-01', 0, 1481164840, 1481164840);
INSERT INTO `admin` VALUES (2, 'admin', '$2y$13$naC7Ga1i.YJmECNTfEuM5e6D07AnFsRpqYiSZyVxvQBp4RKdLE4Eq', 1, 'Ganganadi-valuka', '13761665437', 'flydany@qq.com', '2018-12-01', 0, 1481164840, 1531456105);
INSERT INTO `admin` VALUES (3, 'flydany', '$2y$13$Tq83VwDK6qXvOfkbqNu9I.9IVpILdgww8Uzn9G3j2Qvdvkly4KjpG', 2, 'Infinite number', '13761665438', '841175841@qq.com', '2018-12-01', 0, 1481164840, 1532335346);
COMMIT;

-- ----------------------------
-- Table structure for admin_group
-- ----------------------------
DROP TABLE IF EXISTS `admin_group`;
CREATE TABLE `admin_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned NOT NULL COMMENT '管理员',
  `identity` varchar(128) NOT NULL COMMENT '权组标识',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_identify` (`identity`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='权组表';

-- ----------------------------
-- Records of admin_group
-- ----------------------------
BEGIN;
INSERT INTO `admin_group` VALUES (1, 1, 'super', 1531440000, 1531440000);
INSERT INTO `admin_group` VALUES (2, 3, 'system.keeper', 1531440000, 1531440000);
COMMIT;

-- ----------------------------
-- Table structure for admin_permission
-- ----------------------------
DROP TABLE IF EXISTS `admin_permission`;
CREATE TABLE `admin_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `identity` varchar(64) NOT NULL COMMENT '管理编号',
  `controller` varchar(128) NOT NULL COMMENT '菜单路径，格式->CONTROLLER~METHOD',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `index_identify_navigator_path_admin_permission` (`identity`,`controller`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='权限详细表';

-- ----------------------------
-- Records of admin_permission
-- ----------------------------
BEGIN;
INSERT INTO `admin_permission` VALUES (1, 'super', 'super', 1481164840, 1481164840);
INSERT INTO `admin_permission` VALUES (2, 'system.keeper', 'admin', 1514699131, 1514699131);
INSERT INTO `admin_permission` VALUES (3, 'verify.borrow', 'recharge', 1514699142, 1514699142);
INSERT INTO `admin_permission` VALUES (4, '3', 'project', 1514699299, 1514699299);
INSERT INTO `admin_permission` VALUES (5, '3', 'recharge/list', 1514699299, 1514699299);
INSERT INTO `admin_permission` VALUES (6, '3', 'admin-resource', 1514699299, 1514699299);
INSERT INTO `admin_permission` VALUES (7, '3', 'platform', 1514699299, 1514699299);
INSERT INTO `admin_permission` VALUES (8, '3', 'recharge/detail', 1514699299, 1514699299);
INSERT INTO `admin_permission` VALUES (9, '3', 'recharge/logs', 1514699299, 1514699299);
INSERT INTO `admin_permission` VALUES (10, '3', 'recharge/update', 1514699299, 1514699299);
COMMIT;

-- ----------------------------
-- Table structure for admin_resource
-- ----------------------------
DROP TABLE IF EXISTS `admin_resource`;
CREATE TABLE `admin_resource` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) unsigned NOT NULL COMMENT '数据源类型',
  `identity` varchar(64) NOT NULL COMMENT '标识',
  `power` varchar(64) NOT NULL DEFAULT '0' COMMENT '权限信息',
  `updated_at` int(11) unsigned DEFAULT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_resource
-- ----------------------------
BEGIN;
INSERT INTO `admin_resource` VALUES (9, 1, 'credit.manager', '100001', 1532099258, 1532099258);
INSERT INTO `admin_resource` VALUES (10, 1, 'system.keeper', '100001', 1532099258, 1532099258);
INSERT INTO `admin_resource` VALUES (11, 1, '2', '100001', 1532099258, 1532099258);
INSERT INTO `admin_resource` VALUES (12, 1, '1', '100001', 1532099258, 1532099258);
INSERT INTO `admin_resource` VALUES (13, 1, 'super', '100001', 1532128636, 1532128636);
INSERT INTO `admin_resource` VALUES (14, 1, 'verify.borrow', '100001', 1532128636, 1532128636);
INSERT INTO `admin_resource` VALUES (15, 1, '3', '100001', 1532128636, 1532128636);
INSERT INTO `admin_resource` VALUES (20, 2, '3', '3.1161195', 1532766717, 1532766717);
INSERT INTO `admin_resource` VALUES (21, 1, '3', '100002', 1532915709, 1532915709);
INSERT INTO `admin_resource` VALUES (22, 2, '3', '1', 1533021166, 1533021166);
INSERT INTO `admin_resource` VALUES (23, 2, '3', '2', 1533026310, 1533026310);
INSERT INTO `admin_resource` VALUES (24, 2, '1', '3', 1533026310, 1533026310);
INSERT INTO `admin_resource` VALUES (27, 2, 'system.keeper', '1', 1533026321, 1533026321);
INSERT INTO `admin_resource` VALUES (28, 2, 'credit.manager', '1', 1533292759, 1533292759);
INSERT INTO `admin_resource` VALUES (29, 2, '2', '1', 1533292759, 1533292759);
INSERT INTO `admin_resource` VALUES (30, 2, '1', '1', 1533292759, 1533292759);
COMMIT;

-- ----------------------------
-- Table structure for admin_role
-- ----------------------------
DROP TABLE IF EXISTS `admin_role`;
CREATE TABLE `admin_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `identity` varchar(128) NOT NULL COMMENT '权组标识',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created_at` int(11) unsigned NOT NULL,
  `updated_at` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_identify` (`identity`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='权组表';

-- ----------------------------
-- Records of admin_role
-- ----------------------------
BEGIN;
INSERT INTO `admin_role` VALUES (1, 'super', 'super', 0, 'I\'m Super dot', 0, 1481164840, 1529993565);
INSERT INTO `admin_role` VALUES (2, 'borrow verifyer', 'verify.borrower', 0, 'borrow order verify', 0, 1481164840, 1531456032);
INSERT INTO `admin_role` VALUES (3, 'credit manager', 'credit.manager', 0, 'credit platform CURD', 0, 1481164840, 1531456010);
INSERT INTO `admin_role` VALUES (4, 'system keeper', 'system.keeper', 0, 'system keeper', 0, 1481164840, 1531456017);
COMMIT;

-- ----------------------------
-- Table structure for bind_card
-- ----------------------------
DROP TABLE IF EXISTS `bind_card`;
CREATE TABLE `bind_card` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_merchant_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '项目商户号',
  `paytype` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '支付类型',
  `bank_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '银行',
  `card_no` varchar(32) NOT NULL DEFAULT '' COMMENT '卡号',
  `realname` varchar(64) NOT NULL DEFAULT '' COMMENT '姓名',
  `id_card` varchar(32) NOT NULL DEFAULT '' COMMENT '身份证',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '手机号',
  `order_number` varchar(32) NOT NULL DEFAULT '' COMMENT '订单号',
  `protocol_number` varchar(128) NOT NULL DEFAULT '' COMMENT '协议号',
  `success_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '成功时间',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '扩展参数',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_project_merchant_paytype` (`project_merchant_id`,`paytype`),
  KEY `index_protocol_number` (`protocol_number`),
  KEY `index_order_number` (`order_number`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bind_card
-- ----------------------------
BEGIN;
INSERT INTO `bind_card` VALUES (1, 1, 1, 4, '6217001210042615144', '孙标', '341222198911241433', '13761665439', 'B123445543', 'PT348689798572374', 1531475879, '', '', 90, 0, 1530164509, 1530164509);
COMMIT;

-- ----------------------------
-- Table structure for card
-- ----------------------------
DROP TABLE IF EXISTS `card`;
CREATE TABLE `card` (
  `id` int(11) unsigned NOT NULL,
  `project_id` int(11) unsigned NOT NULL COMMENT '项目编号',
  `bank_id` int(11) unsigned NOT NULL COMMENT '银行编号',
  `card_no` varchar(32) NOT NULL COMMENT '银行卡号',
  `realname` varchar(64) NOT NULL COMMENT '姓名',
  `id_card` varchar(32) NOT NULL COMMENT '身份证号',
  `mobile` varchar(32) NOT NULL COMMENT '预留手机号',
  `province` varchar(32) DEFAULT NULL COMMENT '省',
  `city` varchar(32) DEFAULT NULL COMMENT '市',
  `district` varchar(64) DEFAULT NULL COMMENT '地区',
  `branch` varchar(64) DEFAULT NULL COMMENT '支行',
  `branch_code` varchar(8) DEFAULT NULL COMMENT '支行编码',
  `deleted_at` int(11) unsigned DEFAULT '0' COMMENT '删除时间',
  `updated_at` int(11) unsigned DEFAULT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_mobile` (`mobile`),
  KEY `index_card_no` (`card_no`),
  KEY `index_realname` (`realname`),
  KEY `index_id_card` (`id_card`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of card
-- ----------------------------
BEGIN;
INSERT INTO `card` VALUES (1, 1, 7, '6217001210042615144', '孙标', '341222198911241433', '13761665439', '上海市', '上海市', '普陀区', '中潭路支行', NULL, 0, 1530164509, 1530164509);
COMMIT;

-- ----------------------------
-- Table structure for merchant
-- ----------------------------
DROP TABLE IF EXISTS `merchant`;
CREATE TABLE `merchant` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '标题',
  `platform_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '通道',
  `merchant_number` varchar(64) NOT NULL DEFAULT '' COMMENT '商户号',
  `paytype` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '类型',
  `domain` varchar(255) NOT NULL DEFAULT '' COMMENT '请求地址',
  `private_key` text NOT NULL COMMENT '私钥内容',
  `private_password` varchar(64) NOT NULL DEFAULT '' COMMENT '私钥密码',
  `private_type` varchar(8) NOT NULL DEFAULT '' COMMENT '私钥类型',
  `public_key` text NOT NULL COMMENT '公钥内容',
  `parameters` text NOT NULL COMMENT '其他配置',
  `base_fee` varchar(255) NOT NULL DEFAULT '0' COMMENT '基础费用',
  `rate` varchar(255) NOT NULL DEFAULT '0' COMMENT '费率',
  `min` varchar(255) NOT NULL DEFAULT '0' COMMENT '最低费用',
  `max` varchar(255) NOT NULL DEFAULT '0' COMMENT '费用上限',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `index_platform_merchant_paytype` (`platform_id`,`merchant_number`,`paytype`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商户号配置表';

-- ----------------------------
-- Records of merchant
-- ----------------------------
BEGIN;
INSERT INTO `merchant` VALUES (1, 'speed purse` baofoo', 3, '1161195', 1, 'http://www.baidu.com', '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', '123456', 'pfx', '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', '{\"username\":\"Mr.sun\",\"password\":\"Ms.sun\"}', '0', '0', '100', '0', '`fast wallet` recharge on baofoo platform with merchant 1161195', 0, 0, 1530164509, 1533197886);
INSERT INTO `merchant` VALUES (2, 'speed purse` baofoo', 3, '1161195', 2, 'http://www.baidu.com', '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', '123456', 'pfx', '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', '{\"username\":\"Mr.sun\",\"password\":\"Ms.sun\"}', '0', '0', '100', '0', '`fast wallet` withdraw on baofoo platform with merchant 1161195', 0, 0, 1532401283, 1533197204);
INSERT INTO `merchant` VALUES (3, 'speed purse` baofoo', 3, '1161195', 3, 'http://www.baidu.com', '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', '123456', 'pfx', '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', '{\"username\":\"Mr.sun\",\"password\":\"Ms.sun\"}', '0', '0', '100', '0', '`fast wallet` agreement on baofoo platform with merchant 1161195', 0, 0, 1532401732, 1532419352);
INSERT INTO `merchant` VALUES (4, 'koudai` baofoo', 1, '1203002', 0, 'http://www.baidu.com', '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', '123456', 'pfx', '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', '{\"username\":\"Mr.sun\",\"password\":\"Ms.sun\"}', '0', '0', '100', '0', '`fast wallet` withdraw on baofoo platform with merchant 1203002', 0, 0, 1532401732, 1532419352);
COMMIT;

-- ----------------------------
-- Table structure for merchant_bank
-- ----------------------------
DROP TABLE IF EXISTS `merchant_bank`;
CREATE TABLE `merchant_bank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '通道编号',
  `merchant_number` varchar(32) NOT NULL DEFAULT '' COMMENT '商户号',
  `paytype` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支付方式',
  `bank_id` int(11) unsigned NOT NULL COMMENT '银行id',
  `priority` int(11) unsigned NOT NULL COMMENT '优先级',
  `weekend_priority` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '周五周六优先级',
  `priority_threshold` tinyint(4) unsigned NOT NULL DEFAULT '100' COMMENT '分流阀值',
  `single_amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '单笔限额',
  `day_amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '单日限额',
  `month_amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '单月限额',
  `day_count` int(11) unsigned NOT NULL COMMENT '单日次数',
  `month_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '单月次数',
  `weekday_times` varchar(512) NOT NULL DEFAULT '' COMMENT '工作日可用时间',
  `weekend_times` varchar(512) NOT NULL DEFAULT '' COMMENT '休息日可用时间',
  `holiday_times` varchar(512) NOT NULL DEFAULT '' COMMENT '节假日可用时间',
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作者id',
  `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `remark` varchar(128) NOT NULL DEFAULT '' COMMENT '备注',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_platform_merchant` (`platform_id`,`merchant_number`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='支付通道配置';

-- ----------------------------
-- Records of merchant_bank
-- ----------------------------
BEGIN;
INSERT INTO `merchant_bank` VALUES (1, 3, '', 2, 1, 5, 5, 100, 200000, 1000000, 30000000, 3, 90, '[{\"start\":\"00:00:00\",\"end\":\"24:00:00\"}]', '[{\"start\":\"00:00:00\",\"end\":\"24:00:00\"}]', '[{\"start\":\"00:00:00\",\"end\":\"24:00:00\"}]', 3, 0, '', 0, 1532681007, 0);
INSERT INTO `merchant_bank` VALUES (2, 3, '1161195', 1, 4, 5, 5, 100, 500000, 500000, 15000000, 3, 90, '[{\"start\":\"00:00\",\"end\":\"12:00\"},{\"start\":\"12:30\",\"end\":\"24:00\"}]', '[{\"start\":\"00:00\",\"end\":\"24:00\"}]', '[{\"start\":\"00:00\",\"end\":\"24:00\"}]', 3, 0, 'baofoo recharge. merchant: 1161195, bank: jianshe', 0, 1532678960, 1532673461);
COMMIT;

-- ----------------------------
-- Table structure for merchant_bank_maintain
-- ----------------------------
DROP TABLE IF EXISTS `merchant_bank_maintain`;
CREATE TABLE `merchant_bank_maintain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '通道编号',
  `merchant_number` varchar(32) NOT NULL DEFAULT '' COMMENT '商户号',
  `paytype` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支付方式',
  `bank_id` int(11) unsigned NOT NULL COMMENT '银行id',
  `single_amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '单笔限额',
  `day_amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '单日限额',
  `month_amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '单月限额',
  `begin_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开始时间',
  `finish_at` varchar(512) NOT NULL DEFAULT '' COMMENT '结束时间',
  `times` varchar(512) NOT NULL DEFAULT '' COMMENT '节假日可用时间',
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作者id',
  `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `remark` varchar(128) NOT NULL DEFAULT '' COMMENT '备注',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_platform_merchant_paytype` (`platform_id`,`merchant_number`,`paytype`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='支付通道配置';

-- ----------------------------
-- Records of merchant_bank_maintain
-- ----------------------------
BEGIN;
INSERT INTO `merchant_bank_maintain` VALUES (1, 3, '', 1, 0, 0, 0, 0, 1532937014, '1533023414', '[]', 3, 0, '', 0, 1532942954, 1532941059);
INSERT INTO `merchant_bank_maintain` VALUES (2, 3, '1161195', 2, 0, 0, 0, 0, 1532937014, '1533023414', '[]', 3, 0, '', 0, 1532941418, 1532941131);
COMMIT;

-- ----------------------------
-- Table structure for message
-- ----------------------------
DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '消息类型',
  `sender_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户',
  `receiver_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '消息来源编号',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `content` varchar(1024) NOT NULL DEFAULT '' COMMENT '消息体',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_type` (`type`,`receiver_id`) USING BTREE,
  KEY `index_user` (`receiver_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='站内信表';

-- ----------------------------
-- Table structure for navigator
-- ----------------------------
DROP TABLE IF EXISTS `navigator`;
CREATE TABLE `navigator` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `title` varchar(16) NOT NULL COMMENT '导航标题',
  `top_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '顶级父栏目',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级元素编号',
  `path` varchar(64) NOT NULL DEFAULT ',0,' COMMENT '导航路径',
  `controller` varchar(128) NOT NULL COMMENT '控制器名称/方法',
  `icon_class` varchar(64) NOT NULL DEFAULT 'icon-double-angle-right' COMMENT '小图标',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '导航类型：0->功能，1->菜单',
  `target` enum('_self','_blank') NOT NULL DEFAULT '_self' COMMENT '菜单打开方式',
  `sort` int(4) unsigned NOT NULL DEFAULT '1000' COMMENT '排序',
  `flag` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of navigator
-- ----------------------------
BEGIN;
INSERT INTO `navigator` VALUES (1, '用户管理', 0, 0, ',0,', 'member', 'icon-user', 1, '_self', 1, 1, '', 0, 1481164840, 1514698375);
INSERT INTO `navigator` VALUES (2, '管理员列表', 6, 6, ',0,6,', 'admin/admin-list', 'icon-user', 1, '_self', 12, 1, '', 0, 1481164840, 1516199721);
INSERT INTO `navigator` VALUES (3, '财务系统', 0, 0, ',0,', 'finance', 'icon-leaf', 1, '_self', 2, 1, '', 0, 1481164840, 1514698382);
INSERT INTO `navigator` VALUES (4, '雇佣管理', 0, 0, ',0,', 'employment', 'icon-leaf', 1, '_self', 1, 1, '', 0, 1481164840, 1515901828);
INSERT INTO `navigator` VALUES (5, '充值列表', 3, 3, ',0,3,', 'recharge/recharge-list', 'icon-download-alt', 1, '_self', 1, 1, '', 0, 1481164840, 1521005421);
INSERT INTO `navigator` VALUES (6, '系统设置', 0, 0, ',0,', 'system', 'icon-gear', 1, '_self', 1000, 1, '', 0, 1481164840, 1514697398);
INSERT INTO `navigator` VALUES (7, '分类管理', 6, 6, ',0,6,', 'category-list', 'icon-folder-open', 1, '_self', 5, 1, '', 0, 1481164840, 1514698514);
INSERT INTO `navigator` VALUES (8, '导航管理', 6, 6, ',0,6,', 'navigator-list', 'icon-sitemap', 1, '_self', 1, 1, '', 0, 1481164840, 1514698526);
INSERT INTO `navigator` VALUES (9, '更新管理员', 6, 6, ',0,6,', 'admin/admin-update', 'icon-edit', 0, '_self', 15, 1, '', 0, 1481164840, 1516807829);
INSERT INTO `navigator` VALUES (10, '删除管理员', 6, 6, ',0,6,', 'admin/admin-delete', 'icon-trash', 0, '_self', 15, 1, '', 0, 1481164840, 1514698708);
INSERT INTO `navigator` VALUES (11, '查看管理员', 6, 6, ',0,6,', 'admin/admin-detail', 'icon-eye-open', 0, '_self', 13, 1, '', 0, 1481164840, 1514698633);
INSERT INTO `navigator` VALUES (12, '创建导航', 6, 6, ',0,6,', 'navigator-insert', 'icon-plus', 0, '_self', 2, 1, '', 0, 1481164840, 1514699046);
INSERT INTO `navigator` VALUES (13, '更新导航', 6, 6, ',0,6,', 'navigator-update', 'icon-edit', 0, '_self', 3, 1, '', 0, 1481164840, 1516807825);
INSERT INTO `navigator` VALUES (14, '创建分类', 6, 6, ',0,6,', 'category-insert', 'icon-plus', 0, '_self', 6, 1, '', 0, 1481164840, 1514699051);
INSERT INTO `navigator` VALUES (15, '更新分类', 6, 6, ',0,6,', 'category-update', 'icon-edit', 0, '_self', 7, 1, '', 0, 1481164840, 1516807858);
INSERT INTO `navigator` VALUES (16, '删除分类', 6, 6, ',0,6,', 'category-delete', 'icon-trash', 0, '_self', 8, 1, '', 0, 1481164840, 1514698516);
INSERT INTO `navigator` VALUES (17, '删除导航', 6, 6, ',0,6,', 'navigator-delete', 'icon-trash', 0, '_self', 4, 1, '', 0, 1481164840, 1514698539);
INSERT INTO `navigator` VALUES (19, '设计师列表', 1, 1, ',0,1,', 'user/user-designer', 'icon-user', 1, '_self', 1, 1, '', 0, 1481164840, 1516199699);
INSERT INTO `navigator` VALUES (20, '查看用户', 1, 1, ',0,1,', 'user/user-detail', 'icon-eye-open', 0, '_self', 2, 1, '', 0, 1481164840, 1514698422);
INSERT INTO `navigator` VALUES (21, '更新用户', 1, 1, ',0,1,', 'user/user-update', 'icon-edit', 0, '_self', 3, 1, '', 0, 1481164840, 1516807889);
INSERT INTO `navigator` VALUES (22, '删除用户', 1, 1, ',0,1,', 'user/user-delete', 'icon-trash', 0, '_self', 4, 1, '', 0, 1481164840, 1514698427);
INSERT INTO `navigator` VALUES (23, '雇佣列表', 4, 4, ',0,4,', 'employment-list', 'icon-leaf', 1, '_self', 1, 1, '', 0, 1481164840, 1516199707);
INSERT INTO `navigator` VALUES (24, '查看雇佣', 4, 4, ',0,4,', 'employment-detail', 'icon-eye-open', 0, '_self', 2, 1, '', 0, 1481164840, 1515901829);
INSERT INTO `navigator` VALUES (25, '更新雇佣', 4, 4, ',0,4,', 'employment-update', 'icon-edit', 0, '_self', 3, 1, '', 0, 1481164840, 1516807883);
INSERT INTO `navigator` VALUES (26, '删除雇佣', 4, 4, ',0,4,', 'employment-delete', 'icon-trash', 0, '_self', 4, 1, '', 0, 1481164840, 1515901831);
INSERT INTO `navigator` VALUES (27, '设置管理员权限', 6, 6, ',0,6,', 'admin/admin-permission-edit', 'icon-bell-alt', 0, '_self', 15, 1, '', 0, 1481164840, 1514698701);
INSERT INTO `navigator` VALUES (28, '权组列表', 6, 6, ',0,6,', 'admin/admin-role-list', 'icon-group', 1, '_self', 16, 1, '', 0, 1481164840, 1514698662);
INSERT INTO `navigator` VALUES (29, '查看权组', 6, 6, ',0,6,', 'admin/admin-role-detail', 'icon-eye-open', 0, '_self', 17, 1, '', 0, 1481164840, 1514698666);
INSERT INTO `navigator` VALUES (30, '更新权组', 6, 6, ',0,6,', 'admin/admin-role-update', 'icon-edit', 0, '_self', 19, 1, '', 0, 1481164840, 1516807848);
INSERT INTO `navigator` VALUES (31, '删除权组', 6, 6, ',0,6,', 'admin/admin-role-delete', 'icon-trash', 0, '_self', 20, 1, '', 0, 1481164840, 1516807847);
INSERT INTO `navigator` VALUES (32, '内容管理', 0, 0, ',0,', 'information', 'icon-bullhorn', 1, '_self', 3, 1, '', 0, 1481164840, 1516512356);
INSERT INTO `navigator` VALUES (35, '文章列表', 32, 32, ',0,32,', 'article/article-list', 'icon-file-alt', 1, '_self', 1, 1, '', 0, 1481164840, 1514698442);
INSERT INTO `navigator` VALUES (36, '查看文章', 32, 32, ',0,32,', 'article/article-detail', 'icon-eye-open', 0, '_self', 2, 1, '', 0, 1481164840, 1515224897);
INSERT INTO `navigator` VALUES (37, '更新文章', 32, 32, ',0,32,', 'article/article-update', 'icon-edit', 0, '_self', 4, 1, '', 0, 1481164840, 1516807817);
INSERT INTO `navigator` VALUES (38, '删除文章', 32, 32, ',0,32,', 'article/article-delete', 'icon-trash', 0, '_self', 5, 1, '', 0, 1481164840, 1514698448);
INSERT INTO `navigator` VALUES (39, '系统配置单', 6, 6, ',0,6,', 'system-init', 'icon-gear', 1, '_self', 9, 1, '', 0, 1481173333, 1514698590);
INSERT INTO `navigator` VALUES (40, '文章分类JS模版化', 6, 6, ',0,6,', 'article-category-init', 'icon-file-alt', 0, '_self', 11, 1, '', 0, 1481173401, 1514698609);
INSERT INTO `navigator` VALUES (41, '充值详情/审核', 3, 3, ',0,3,', 'recharge/recharge-detail', 'icon-eye-open', 0, '_self', 2, 1, '', 0, 1481380050, 1521005421);
INSERT INTO `navigator` VALUES (42, '提现列表', 3, 3, ',0,3,', 'withdraw/withdraw-list', 'icon-upload-alt', 1, '_self', 5, 1, '', 0, 1481380051, 1516803347);
INSERT INTO `navigator` VALUES (43, '提现详情/审核', 3, 3, ',0,3,', 'withdraw/withdraw-detail', 'icon-eye-open', 0, '_self', 6, 1, '', 0, 1481380053, 1516803348);
INSERT INTO `navigator` VALUES (44, '模型分类JS模版化', 6, 6, ',0,6,', 'pattern-category-init', 'icon-file-alt', 0, '_self', 10, 1, '', 0, 1509237429, 1514698605);
INSERT INTO `navigator` VALUES (45, '薪酬发放列表', 4, 4, ',0,4,', 'salary-list', 'icon-leaf', 1, '_self', 5, 1, '', 0, 1509249503, 1516802836);
INSERT INTO `navigator` VALUES (46, '添加薪酬', 4, 4, ',0,4,', 'salary-insert', 'icon-plus', 0, '_self', 7, 1, '', 0, 1509249529, 1516802837);
INSERT INTO `navigator` VALUES (47, '更新薪酬', 4, 4, ',0,4,', 'salary-update', 'icon-edit', 0, '_self', 8, 1, '', 0, 1509249624, 1516807880);
INSERT INTO `navigator` VALUES (48, '删除薪酬', 4, 4, ',0,4,', 'salary-delete', 'icon-trash', 0, '_self', 9, 1, '', 0, 1509249625, 1516802839);
INSERT INTO `navigator` VALUES (49, '创建管理员', 6, 6, ',0,6,', 'admin/admin-insert', 'icon-plus', 0, '_self', 14, 1, '', 0, 1509249737, 1514699059);
INSERT INTO `navigator` VALUES (50, '创建权组', 6, 6, ',0,6,', 'admin/admin-role-insert', 'icon-plus', 0, '_self', 18, 1, '', 0, 1509249792, 1516807847);
INSERT INTO `navigator` VALUES (51, '创建用户', 1, 1, ',0,1,', 'user/user-insert', 'icon-plus', 0, '_self', 5, 1, '', 0, 1509249794, 1514699026);
INSERT INTO `navigator` VALUES (52, '通过充值申请', 3, 3, ',0,3,', 'recharge/success', 'icon-ok', 0, '_self', 3, 1, '', 0, 1509249836, 1521005420);
INSERT INTO `navigator` VALUES (53, '创建雇佣', 4, 4, ',0,4,', 'employment-insert', 'icon-plus', 0, '_self', 3, 1, '', 0, 1509249862, 1515901830);
INSERT INTO `navigator` VALUES (54, '查看薪酬记录', 4, 4, ',0,4,', 'salary-detail', 'icon-eye-open', 0, '_self', 6, 1, '', 0, 1509249888, 1516802837);
INSERT INTO `navigator` VALUES (55, '创建文章', 32, 32, ',0,32,', 'article/article-insert', 'icon-plus', 0, '_self', 3, 1, '', 0, 1509249914, 1515224897);
INSERT INTO `navigator` VALUES (56, '作品列表', 1, 1, ',0,1,', 'design/design-list', 'icon-lightbulb', 1, '_self', 6, 1, '', 0, 1514898575, 1514903306);
INSERT INTO `navigator` VALUES (57, '查看作品', 1, 1, ',0,1,', 'design/design-detail', 'icon-eye-open', 0, '_self', 7, 1, '', 0, 1514898605, 1514903307);
INSERT INTO `navigator` VALUES (58, '更新作品', 1, 1, ',0,1,', 'design/design-update', 'icon-edit', 0, '_self', 8, 1, '', 0, 1514898624, 1514903307);
INSERT INTO `navigator` VALUES (59, '删除作品', 1, 1, ',0,1,', 'design/design-delete', 'icon-trash', 0, '_self', 9, 1, '', 0, 1514898644, 1514903308);
INSERT INTO `navigator` VALUES (60, '站内信列表', 1, 1, ',0,1,', 'message/message-list', 'icon-comments', 1, '_self', 10, 1, '', 0, 1514984125, 1516105088);
INSERT INTO `navigator` VALUES (61, '查看站内信', 1, 1, ',0,1,', 'message/message-detail', 'icon-eye-open', 0, '_self', 11, 1, '', 0, 1514984126, 1516105058);
INSERT INTO `navigator` VALUES (62, '添加站内信', 1, 1, ',0,1,', 'message/message-insert', 'icon-plus', 0, '_self', 12, 1, '', 0, 1515166794, 1516105061);
INSERT INTO `navigator` VALUES (63, '更新站内信', 1, 1, ',0,1,', 'message/message-update', 'icon-edit', 0, '_self', 13, 1, '', 0, 1515166794, 1516105065);
INSERT INTO `navigator` VALUES (64, '招聘列表', 4, 4, ',0,4,', 'recruit/recruit-list', 'icon-lightbulb', 1, '_self', 10, 1, '', 0, 1516019174, 1520867917);
INSERT INTO `navigator` VALUES (65, '查看招聘', 4, 4, ',0,4,', 'recruit/recruit-detail', 'icon-eye-open', 0, '_self', 11, 1, '', 0, 1516019174, 1520867917);
INSERT INTO `navigator` VALUES (66, '创建招聘', 4, 4, ',0,4,', 'recruit/recruit-insert', 'icon-plus', 0, '_self', 12, 1, '', 0, 1516019175, 1520867916);
INSERT INTO `navigator` VALUES (67, '更新招聘', 4, 4, ',0,4,', 'recruit/recruit-edit', 'icon-edit', 0, '_self', 13, 1, '', 0, 1516019176, 1520867915);
INSERT INTO `navigator` VALUES (68, '删除招聘', 4, 4, ',0,4,', 'recruit/recruit-delete', 'icon-trash', 0, '_self', 14, 1, '', 0, 1516019177, 1520867915);
INSERT INTO `navigator` VALUES (69, '删除站内信', 1, 1, ',0,1,', 'message/message-delete', 'icon-trash', 0, '_self', 14, 1, '', 0, 1516105070, 1516105070);
INSERT INTO `navigator` VALUES (70, '雇主列表', 1, 1, ',0,1,', 'user/user-employer', 'icon-github', 1, '_self', 1, 1, '', 0, 1516199682, 1516199682);
INSERT INTO `navigator` VALUES (71, '市场运营', 0, 0, ',0,', 'marketing', 'icon-shopping-cart', 1, '_self', 2, 1, '', 0, 1516445829, 1523090463);
INSERT INTO `navigator` VALUES (72, '优惠劵管理', 71, 71, ',0,71,', 'coupon/coupon-list', 'icon-qrcode', 1, '_self', 1, 1, '', 0, 1516445923, 1516512402);
INSERT INTO `navigator` VALUES (73, '查看优惠劵', 71, 71, ',0,71,', 'coupon/coupon-detail', 'icon-eye-open', 0, '_self', 2, 1, '', 0, 1516509890, 1516512403);
INSERT INTO `navigator` VALUES (74, '添加优惠劵', 71, 71, ',0,71,', 'coupon/coupon-insert', 'icon-plus', 0, '_self', 3, 1, '', 0, 1516509892, 1516512403);
INSERT INTO `navigator` VALUES (75, '更新优惠劵', 71, 71, ',0,71,', 'coupon/coupon-update', 'icon-edit', 0, '_self', 4, 1, '', 0, 1516509893, 1516512404);
INSERT INTO `navigator` VALUES (76, '删除优惠劵', 71, 71, ',0,71,', 'coupon/coupon-delete', 'icon-trash', 0, '_self', 5, 1, '', 0, 1516509895, 1516512404);
INSERT INTO `navigator` VALUES (77, '拒绝充值申请', 3, 3, ',0,3,', 'recharge/refuse', 'icon-remove', 0, '_self', 4, 1, '', 0, 1516803159, 1521005420);
INSERT INTO `navigator` VALUES (78, '审核提现', 3, 3, ',0,3,', 'withdraw/withdrawing', 'icon-random', 0, '_self', 7, 1, '', 0, 1516803278, 1516803278);
INSERT INTO `navigator` VALUES (79, '通过提现申请', 3, 3, ',0,3,', 'withdraw/success', 'icon-ok', 0, '_self', 8, 1, '', 0, 1516803321, 1516803321);
INSERT INTO `navigator` VALUES (80, '拒绝提现申请', 3, 3, ',0,3,', 'withdraw/refuse', 'icon-remove', 0, '_self', 9, 1, '', 0, 1516803321, 1516803321);
INSERT INTO `navigator` VALUES (81, '合作者列表', 32, 32, ',0,32,', 'cooperator/cooperator-list', 'icon-group', 1, '_self', 6, 1, '', 0, 1516807772, 1516844288);
INSERT INTO `navigator` VALUES (82, '查看合作者', 32, 32, ',0,32,', 'cooperator/cooperator-detail', 'icon-eye-open', 0, '_self', 7, 1, '', 0, 1516807773, 1516807773);
INSERT INTO `navigator` VALUES (83, '添加和作者', 32, 32, ',0,32,', 'cooperator/cooperator-insert', 'icon-plus', 0, '_self', 8, 1, '', 0, 1516807773, 1516807773);
INSERT INTO `navigator` VALUES (84, '更新合作者', 32, 32, ',0,32,', 'cooperator/cooperator-update', 'icon-edit', 0, '_self', 9, 1, '', 0, 1516807774, 1516807819);
INSERT INTO `navigator` VALUES (85, '删除合作者', 32, 32, ',0,32,', 'cooperator/cooperator-delete', 'icon-trash', 0, '_self', 10, 1, '', 0, 1516807774, 1516807774);
INSERT INTO `navigator` VALUES (86, '外链列表', 32, 32, ',0,32,', 'external-link/external-link-list', 'icon-exchange', 1, '_self', 11, 1, '', 0, 1516807972, 1516807972);
INSERT INTO `navigator` VALUES (87, '查看外链', 32, 32, ',0,32,', 'external-link/external-link-detail', 'icon-eye-open', 0, '_self', 12, 1, '', 0, 1516807974, 1516807974);
INSERT INTO `navigator` VALUES (88, '添加外链', 32, 32, ',0,32,', 'external-link/external-link-insert', 'icon-plus', 0, '_self', 13, 1, '', 0, 1516807974, 1516807974);
INSERT INTO `navigator` VALUES (89, '更新外链', 32, 32, ',0,32,', 'external-link/external-link-update', 'icon-edit', 0, '_self', 14, 1, '', 0, 1516807975, 1516807975);
INSERT INTO `navigator` VALUES (90, '删除外链', 32, 32, ',0,32,', 'external-link/external-link-delete', 'icon-trash', 0, '_self', 15, 1, '', 0, 1516807975, 1516807975);
INSERT INTO `navigator` VALUES (91, '用户资金流水', 3, 3, ',0,3,', 'finance/account-change-list', 'icon-shopping-cart', 1, '_self', 10, 1, '', 0, 1516886224, 1516886657);
COMMIT;

-- ----------------------------
-- Table structure for project
-- ----------------------------
DROP TABLE IF EXISTS `project`;
CREATE TABLE `project` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL COMMENT '名称',
  `effect_date` varchar(16) NOT NULL DEFAULT '' COMMENT '有效期',
  `key` varchar(64) NOT NULL DEFAULT '' COMMENT '密钥',
  `public_key` varchar(1024) NOT NULL COMMENT '公钥串',
  `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100003 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of project
-- ----------------------------
BEGIN;
INSERT INTO `project` VALUES (100001, 'speed purse', '2200-01-01', '123456', 'this is public key.', 0, 'speed purse app', 0, 1530164509, 1533275618);
INSERT INTO `project` VALUES (100002, 'koudailc', '2200-01-01', '123456', 'DF5xfyF8MXMgIICQrvEhoFQmxhbmsiAwi3GlIYCAEdx/9/PyX//38/Lf//fz81AACAP2ABUgEaNFtjVj8l3wdWPy3xBVY/LhoAJLKCEj8lsIISPy0BBS4aACRMoL0+JUqgvT4tAQouGgAQAAAAACUBBQAtAQVyGgAQAABgAVIBghzk2Kw+JXz6QUacABx9gic6JdAzIkYaADBc/ys0JXjH7D4ts6g6MpwAIJo0lDolBj2aPgGCMhoAMEhp5j4lJY99Py11iWoyGgAwF4u0PSXSpGc/LX7ZTzIaADAf7WQ0JZHaKD8tmYIdMhoAMKYBjDsl1vL3Pi2Eou0yBAEw6d0IPyWX9Xo/LQRinTIaADDLw8E+JVEnWT8tSidZMhoANIeN5z0l3pkxPy3UHOE6LjgBMBj5pTslP7LiPi1xoeMyGgAwY99/PyUR0Hw/LQOvzTJOADBBcHs/JbvpYj8tlCtJMhoAIMlgeT8llwg7PyEeOqABFIA/JYvkE1oaAByRtxY/LZKMDTIEAUFRICXYBsg+LcdxnDJoADTHn24/Jb4QCj4thrA+PS7QACAvTjU/JfsIuz1WggAc6yMNPy2bqkYyaAAwgxNwPyXYj74+LfH7JzIaADAh70s/JfIdJj4tMC/3MoIAMAF7GT8lH4jIPS2Agr0RGjCiBtQKCl4SXAgAEiQKYnwCABVBvEHLAD8NJlJuAgAVgVwFJgAdAQogIAAqBRXky5ZAMmAAUloCQmAAKFIQCDQl6vH3Pi2aWUySYABSUgJCYABSRAKSYABSSgJCYAAhAEI8ApJgAFJCAkJgAAAvTjQCkmAAUjoCQmAAUiwCXmAAGBI==', 0, 'koudailc', 0, 1532400191, 1533205791);
COMMIT;

-- ----------------------------
-- Table structure for project_api
-- ----------------------------
DROP TABLE IF EXISTS `project_api`;
CREATE TABLE `project_api` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) unsigned NOT NULL COMMENT '项目编号',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `api` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '接口',
  `times` varchar(512) NOT NULL DEFAULT '' COMMENT '有效时间段',
  `parameters` text NOT NULL COMMENT '参数配置',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作员',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_project_api` (`project_id`,`api`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of project_api
-- ----------------------------
BEGIN;
INSERT INTO `project_api` VALUES (5, 100001, 'speed purse` recharge', 1, '[{\"start\":\"00:00\",\"end\":\"23:30\"}]', '[]', 'speed purse` recharge api', 0, 0, 0, 1533275527, 1533288060);
INSERT INTO `project_api` VALUES (6, 100001, 'speed purse` withdraw', 2, '[{\"start\":\"00:00\",\"end\":\"24:00\"}]', '{\"single_amount\":\"1000\",\"day_amount\":\"1000000\",\"day_count\":\"1\"}', 'speed purse` withdraw api', 0, 0, 0, 1533286640, 1533286640);
INSERT INTO `project_api` VALUES (7, 100001, 'speed purse` agreement', 3, '[{\"start\":\"00:00\",\"end\":\"24:00\"}]', '[]', 'speed purse` agreement api', 0, 0, 0, 1533286662, 1533286662);
COMMIT;

-- ----------------------------
-- Table structure for project_contacts
-- ----------------------------
DROP TABLE IF EXISTS `project_contacts`;
CREATE TABLE `project_contacts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) unsigned NOT NULL COMMENT '项目编号',
  `identity` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '身份类型',
  `name` varchar(64) NOT NULL COMMENT '标识',
  `mobile` varchar(32) NOT NULL COMMENT '公钥串',
  `email` varchar(255) NOT NULL COMMENT '备注',
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_project` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of project_contacts
-- ----------------------------
BEGIN;
INSERT INTO `project_contacts` VALUES (1, 100001, 2, 'Mr.sun', '13761665439', 'sunbiao@koudailc.com', 1530164509, 1532163969);
INSERT INTO `project_contacts` VALUES (4, 100001, 1, 'Ms.sun', '13651702353', 'sun@koudailc.com', 1532128950, 1532163962);
COMMIT;

-- ----------------------------
-- Table structure for project_merchant
-- ----------------------------
DROP TABLE IF EXISTS `project_merchant`;
CREATE TABLE `project_merchant` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `project_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '项目',
  `platform_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '通道号',
  `merchant_id` int(11) unsigned NOT NULL COMMENT '商户号编号',
  `paytype` tinyint(4) unsigned NOT NULL COMMENT '支付类型',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_project_paytype` (`project_id`,`paytype`) USING BTREE,
  KEY `index_merchant` (`merchant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of project_merchant
-- ----------------------------
BEGIN;
INSERT INTO `project_merchant` VALUES (1, 'koudailc` baofoo recharge', 100002, 1, 4, 1, '`koudailc` recharge on baofoo quick payment（temporary use）', 0, 0, 1530164509, 1533197908);
INSERT INTO `project_merchant` VALUES (2, 'speed purse` baofoo agreement', 100001, 3, 3, 3, '`fast wallet` agreement on baofoo agreement', 0, 0, 1532342048, 1532403727);
INSERT INTO `project_merchant` VALUES (3, 'speed purse` baofoo withdraw', 100001, 3, 2, 2, '`fast wallet` withdraw on baofoo withdraw', 0, 0, 1532342093, 1532403722);
INSERT INTO `project_merchant` VALUES (4, 'speed purse` baofoo recharge', 100001, 3, 1, 1, '`fast wallet` recharge on baofoo quick payment', 0, 0, 1532342112, 1532403715);
COMMIT;

-- ----------------------------
-- Table structure for recharge
-- ----------------------------
DROP TABLE IF EXISTS `recharge`;
CREATE TABLE `recharge` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(32) NOT NULL DEFAULT '' COMMENT '订单号',
  `project_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '项目',
  `platform_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '通道',
  `project_merchant_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '项目商户号',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户编号',
  `bind_card_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '绑卡记录',
  `bank_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '银行',
  `amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '充值金额',
  `fee` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '手续费',
  `source_order_number` varchar(64) NOT NULL DEFAULT '' COMMENT '来源订单号',
  `outer_order_number` varchar(32) NOT NULL DEFAULT '' COMMENT '第三方订单号',
  `success_date` varchar(16) NOT NULL DEFAULT '' COMMENT '成功日期',
  `success_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '充值完成时间',
  `error_code` varchar(64) NOT NULL DEFAULT '' COMMENT '错误码',
  `postscript` varchar(64) NOT NULL DEFAULT '' COMMENT '附言',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_order` (`order_number`) USING BTREE,
  KEY `index_user_created_at` (`project_id`,`created_at`) USING BTREE,
  KEY `index_created_at_user` (`created_at`,`project_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='充值记录表';

-- ----------------------------
-- Records of recharge
-- ----------------------------
BEGIN;
INSERT INTO `recharge` VALUES (1, '2345123441', 100001, 0, 2, 204, 1, 4, 100000, 100, 'S1234441', 'O1234111', '', 0, '', 'recharge', '', 22, 0, 1531475879, 1532591111);
COMMIT;

-- ----------------------------
-- Table structure for recharge_log
-- ----------------------------
DROP TABLE IF EXISTS `recharge_log`;
CREATE TABLE `recharge_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recharge_id` int(11) unsigned NOT NULL COMMENT '充值编号',
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '管理员',
  `event` varchar(64) NOT NULL DEFAULT '' COMMENT '事件',
  `ip` varchar(64) NOT NULL COMMENT 'ip地址',
  `operation` varchar(255) NOT NULL COMMENT '备注',
  `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_recharge_id` (`recharge_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='充值日志表';

-- ----------------------------
-- Records of recharge_log
-- ----------------------------
BEGIN;
INSERT INTO `recharge_log` VALUES (1, 1, 1, 'creator', '::1', 'create recharge order', 0, 1514699299, 1514699299);
INSERT INTO `recharge_log` VALUES (2, 1, 0, 'editor', '::1', 'edit recharge order', 0, 1514699299, 1514699299);
INSERT INTO `recharge_log` VALUES (3, 1, 3, 'editor', '127.0.0.1', '{\"project_merchant_id\":3,\"user_id\":204,\"bind_card_id\":1,\"amount\":100000,\"fee\":100,\"status\":0,\"updated_at\":1532588311}', 0, 1532588347, 1532588347);
INSERT INTO `recharge_log` VALUES (4, 1, 3, 'editor', '127.0.0.1', 'change attributes: {\"project_merchant_id\":3,\"user_id\":204,\"bind_card_id\":1,\"amount\":100000,\"fee\":100,\"status\":0,\"updated_at\":1532588347}', 0, 1532590374, 1532590374);
INSERT INTO `recharge_log` VALUES (5, 1, 3, 'editor', '127.0.0.1', 'change attributes: {\"project_merchant_id\":2,\"user_id\":204,\"bind_card_id\":1,\"amount\":100000,\"fee\":100,\"status\":0,\"updated_at\":1532590374}', 0, 1532590420, 1532590420);
INSERT INTO `recharge_log` VALUES (6, 1, 3, 'editor', '127.0.0.1', 'change attributes: {\"project_merchant_id\":2,\"user_id\":204,\"bind_card_id\":1,\"amount\":100000,\"fee\":100,\"status\":90,\"updated_at\":1532590420}', 0, 1532590590, 1532590590);
INSERT INTO `recharge_log` VALUES (7, 1, 3, 'editor', '127.0.0.1', 'change attributes: {\"updated_at\":{\"before\":1532591100,\"after\":1532590998}}', 0, 1532591100, 1532591100);
INSERT INTO `recharge_log` VALUES (8, 1, 3, 'editor', '127.0.0.1', 'change attributes: {\"status\":{\"before\":\"22\",\"after\":20},\"updated_at\":{\"before\":1532591111,\"after\":1532591100}}', 0, 1532591111, 1532591111);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
