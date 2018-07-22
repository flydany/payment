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

 Date: 22/07/2018 15:33:59
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
INSERT INTO `admin` VALUES (1, 'root', '$2y$13$G0CohVc8f.SzLAZ5UHZeIOHanKLmlR0R852yZNv3vc2IYU/iOPjTi', 1, 'Super manager', '13761665439', 'flydany@yeah.net', '2018-12-01', 0, 1481164840, 1481164840);
INSERT INTO `admin` VALUES (2, 'admin', '$2y$13$naC7Ga1i.YJmECNTfEuM5e6D07AnFsRpqYiSZyVxvQBp4RKdLE4Eq', 1, 'Ganganadi-valuka', '13761665437', 'flydany@qq.com', '2018-12-01', 0, 1481164840, 1531456105);
INSERT INTO `admin` VALUES (3, 'flydany', '$2y$13$StfnlztfYA2Aaz5FhOitX.mlQLWk5.3f9XmhFUX9kUrrRqe/sBEPS', 2, 'Infinite number', '13761665438', '841175841@qq.com', '2018-12-01', 0, 1481164840, 1531456085);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='权限详细表';

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
COMMIT;

-- ----------------------------
-- Table structure for admin_resource
-- ----------------------------
DROP TABLE IF EXISTS `admin_resource`;
CREATE TABLE `admin_resource` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) unsigned NOT NULL COMMENT '数据源类型',
  `identity` varchar(64) NOT NULL COMMENT '标识',
  `item_id` int(11) unsigned NOT NULL COMMENT '数据源编号',
  `updated_at` int(11) unsigned DEFAULT NULL,
  `created_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_resource
-- ----------------------------
BEGIN;
INSERT INTO `admin_resource` VALUES (9, 1, 'credit.manager', 100001, 1532099258, 1532099258);
INSERT INTO `admin_resource` VALUES (10, 1, 'system.keeper', 100001, 1532099258, 1532099258);
INSERT INTO `admin_resource` VALUES (11, 1, '2', 100001, 1532099258, 1532099258);
INSERT INTO `admin_resource` VALUES (12, 1, '1', 100001, 1532099258, 1532099258);
INSERT INTO `admin_resource` VALUES (13, 1, 'super', 100001, 1532128636, 1532128636);
INSERT INTO `admin_resource` VALUES (14, 1, 'verify.borrow', 100001, 1532128636, 1532128636);
INSERT INTO `admin_resource` VALUES (15, 1, '3', 100001, 1532128636, 1532128636);
INSERT INTO `admin_resource` VALUES (16, 2, '3', 1, 1532128636, 1532128636);
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
INSERT INTO `admin_role` VALUES (2, 'borrow verifyer', 'verify.borrow', 0, 'borrow order verify', 0, 1481164840, 1531456032);
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
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `platform_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '通道',
  `merchant_number` varchar(64) NOT NULL DEFAULT '' COMMENT '商户号',
  `paytype` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '类型',
  `request_uri` varchar(255) NOT NULL DEFAULT '' COMMENT '请求地址',
  `private_key` text COMMENT '私钥内容',
  `private_password` varchar(64) DEFAULT '' COMMENT '私钥密码',
  `private_type` varchar(8) DEFAULT '' COMMENT '私钥类型',
  `public_key` text COMMENT '公钥内容',
  `configuration` text COMMENT '其他配置',
  `base_fee` varchar(255) DEFAULT '0' COMMENT '基础费用',
  `rate` varchar(255) DEFAULT '0' COMMENT '费率',
  `min` varchar(255) DEFAULT '0' COMMENT '最低费用',
  `max` varchar(255) DEFAULT '0' COMMENT '费用上限',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `status` int(11) unsigned DEFAULT '0' COMMENT '状态',
  `deleted_at` int(11) unsigned DEFAULT '0',
  `created_at` int(11) unsigned DEFAULT '0',
  `updated_at` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `index_platform_merchant_paytype` (`platform_id`,`merchant_number`,`paytype`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商户号配置表';

-- ----------------------------
-- Records of merchant
-- ----------------------------
BEGIN;
INSERT INTO `merchant` VALUES (1, '极速钱包宝付快捷支付商户号', 3, '1161195', 1, 'http://www.baofoo.com/api/tnz/api', NULL, '', '', NULL, NULL, '0', '0', '100', '0', '', 0, 0, 1530164509, 1530164509);
COMMIT;

-- ----------------------------
-- Table structure for merchant_bank
-- ----------------------------
DROP TABLE IF EXISTS `merchant_bank`;
CREATE TABLE `merchant_bank` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录id',
  `platform_id` int(11) NOT NULL COMMENT '通道id 1.联动 2.连连 3.易宝 4.新联动 5.金运通 6.快钱 7.宝付 8.宝易 9.盛付通 10.富友 11.即信 12.银生宝',
  `merchant_number` varchar(32) DEFAULT '' COMMENT '商户号',
  `paytype` tinyint(4) NOT NULL DEFAULT '0' COMMENT '支付方式',
  `bank_id` int(11) NOT NULL COMMENT '银行id',
  `priority` int(11) NOT NULL COMMENT '优先级',
  `holiday_priority` tinyint(4) DEFAULT '0' COMMENT '周五周六优先级',
  `scl` bigint(20) NOT NULL COMMENT '单笔限额',
  `sdl` bigint(20) NOT NULL COMMENT '单日限额',
  `sml` bigint(11) NOT NULL COMMENT '单月限额',
  `rand_keep` tinyint(4) DEFAULT '100' COMMENT '分流阀值',
  `sdt` int(11) NOT NULL COMMENT '单日次数',
  `smt` int(11) NOT NULL DEFAULT '0' COMMENT '单月次数',
  `holiday_times` varchar(512) DEFAULT '' COMMENT '节假日提现时间范围json数组',
  `workday_times` varchar(512) DEFAULT '' COMMENT '工作日提现时间范围json数组',
  `dayoff_times` varchar(512) DEFAULT '' COMMENT '休息日提现时间范围json数组',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作者id',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态0:正常1:删除',
  `remark` varchar(128) DEFAULT NULL COMMENT '备注',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='支付通道配置';

-- ----------------------------
-- Records of merchant_bank
-- ----------------------------
BEGIN;
INSERT INTO `merchant_bank` VALUES (1, 1, '', 2, 1, 1, 1, 200000, 1000000, 30000000, 100, 0, 0, '[{\"btime\":\"00:00:00\",\"etime\":\"24:00:00\"}]', '[{\"btime\":\"00:00:00\",\"etime\":\"24:00:00\"}]', '[{\"btime\":\"00:00:00\",\"etime\":\"24:00:00\"}]', 1, 0, '', 0, 0);
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
  `title` varchar(255) NOT NULL COMMENT '名称',
  `public_key` varchar(1024) NOT NULL COMMENT '公钥串',
  `effect_date` varchar(16) NOT NULL DEFAULT '' COMMENT '有效期',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `deleted_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100002 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of project
-- ----------------------------
BEGIN;
INSERT INTO `project` VALUES (100001, 'speed purse', '1', '2200-01-01', 'speed purse app', 1, 0, 1530164509, 1532128916);
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
  `project_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '项目',
  `merchant_id` int(11) unsigned NOT NULL COMMENT '商户号编号',
  `paytype` tinyint(4) unsigned NOT NULL COMMENT '支付类型',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `deleted_at` int(11) unsigned DEFAULT '0' COMMENT '删除时间',
  `created_at` int(11) unsigned DEFAULT NULL,
  `updated_at` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index_project_paytype` (`project_id`,`paytype`) USING BTREE,
  KEY `index_merchant` (`merchant_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of project_merchant
-- ----------------------------
BEGIN;
INSERT INTO `project_merchant` VALUES (1, 1, 1, 1, '极速钱包宝付快捷支付', 0, 0, 1530164509, 1530164509);
COMMIT;

-- ----------------------------
-- Table structure for recharge
-- ----------------------------
DROP TABLE IF EXISTS `recharge`;
CREATE TABLE `recharge` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(32) NOT NULL COMMENT '订单号',
  `project_id` int(11) unsigned NOT NULL COMMENT '项目',
  `project_merchant_id` int(11) unsigned NOT NULL COMMENT '项目商户号',
  `bind_card_id` int(11) unsigned NOT NULL COMMENT '绑卡记录',
  `bank_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '银行',
  `amount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '充值金额',
  `fee` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '手续费',
  `source_order_number` varchar(128) NOT NULL COMMENT '来源订单号',
  `outer_order_number` varchar(32) NOT NULL COMMENT '第三方订单号',
  `success_date` varchar(16) NOT NULL DEFAULT '' COMMENT '成功日期',
  `success_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '充值完成时间',
  `remark` varchar(128) NOT NULL DEFAULT '' COMMENT '备注',
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
INSERT INTO `recharge` VALUES (1, '2345123441', 1, 1, 1, 4, 100000, 100, 'S1234441', 'O1234111', '', 0, '', 0, 0, 1531475879, 1531475879);
COMMIT;

-- ----------------------------
-- Table structure for recharge_log
-- ----------------------------
DROP TABLE IF EXISTS `recharge_log`;
CREATE TABLE `recharge_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `recharge_id` int(11) unsigned NOT NULL COMMENT '充值编号',
  `operator` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '管理员',
  `ip` varchar(64) NOT NULL COMMENT 'ip地址',
  `operation` varchar(255) NOT NULL COMMENT '备注',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_recharge_id` (`recharge_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值日志表';

SET FOREIGN_KEY_CHECKS = 1;
