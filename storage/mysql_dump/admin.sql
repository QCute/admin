/*
 Navicat Premium Data Transfer

 Source Server         : windows
 Source Server Type    : MariaDB
 Source Server Version : 100508
 Source Host           : localhost:3306
 Source Schema         : admin

 Target Server Type    : MariaDB
 Target Server Version : 100508
 File Encoding         : 65001

 Date: 31/01/2021 14:33:54
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `permission` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 54 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_menu
-- ----------------------------
INSERT INTO `admin_menu` VALUES (1, 0, 1, '仪表盘', 'fa-dashboard', '/', NULL, NULL, '2020-11-29 17:30:37');
INSERT INTO `admin_menu` VALUES (2, 0, 38, '管理员', 'fa-tasks', NULL, NULL, NULL, '2021-01-23 22:20:27');
INSERT INTO `admin_menu` VALUES (3, 2, 39, '用户', 'fa-users', 'auth/users', NULL, NULL, '2021-01-23 22:20:27');
INSERT INTO `admin_menu` VALUES (4, 2, 40, '角色', 'fa-user', 'auth/roles', NULL, NULL, '2021-01-23 22:20:27');
INSERT INTO `admin_menu` VALUES (5, 2, 41, '权限', 'fa-ban', 'auth/permissions', NULL, NULL, '2021-01-23 22:20:27');
INSERT INTO `admin_menu` VALUES (6, 2, 42, '菜单', 'fa-bars', 'auth/menu', NULL, NULL, '2021-01-23 22:20:27');
INSERT INTO `admin_menu` VALUES (7, 2, 43, '操作日志', 'fa-history', 'auth/logs', NULL, NULL, '2021-01-23 22:20:27');
INSERT INTO `admin_menu` VALUES (8, 0, 2, '活跃统计', 'fa-area-chart', NULL, NULL, '2020-05-24 14:00:57', '2020-11-29 18:24:18');
INSERT INTO `admin_menu` VALUES (9, 0, 8, '充值统计', 'fa-line-chart', NULL, NULL, '2020-05-24 14:01:08', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (10, 0, 14, '游戏数据', 'fa-save', NULL, NULL, '2020-05-24 14:01:25', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (11, 0, 19, '配置管理', 'fa-database', NULL, NULL, '2020-05-24 14:01:31', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (12, 0, 24, '服务器管理', 'fa-gears', NULL, NULL, '2020-05-24 14:01:41', '2021-01-21 20:42:25');
INSERT INTO `admin_menu` VALUES (13, 8, 3, '实时在线人数', 'fa-area-chart', '/user-online', NULL, '2020-05-24 14:02:30', '2020-12-12 15:37:17');
INSERT INTO `admin_menu` VALUES (14, 8, 4, '注册统计', 'fa-bar-chart', '/user-register', NULL, '2020-05-24 14:02:53', '2020-12-12 21:12:53');
INSERT INTO `admin_menu` VALUES (15, 8, 5, '登录统计', 'fa-bar-chart', '/user-login', NULL, '2020-05-24 14:03:17', '2020-12-12 21:13:03');
INSERT INTO `admin_menu` VALUES (16, 8, 6, '玩家存活', 'fa-bar-chart', '/user-survival', NULL, '2020-05-24 14:04:00', '2020-11-29 20:05:15');
INSERT INTO `admin_menu` VALUES (17, 8, 7, '每日在线时长', 'fa-bar-chart', '/daily-online-time', NULL, '2020-05-24 14:04:19', '2020-12-13 00:00:44');
INSERT INTO `admin_menu` VALUES (18, 9, 9, '每日充值统计', 'fa-bar-chart', '/daily-recharge', NULL, '2020-05-24 14:05:04', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (19, 9, 12, '充值区间分布', 'fa-pie-chart', '/recharge-distribution', NULL, '2020-05-24 14:05:24', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (20, 10, 15, '玩家数据', 'fa-user-plus', '/user-data', NULL, '2020-05-24 14:05:44', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (21, 10, 16, '配置数据', 'fa-tags', '/configure-data', NULL, '2020-05-24 14:06:00', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (22, 10, 17, '日志数据', 'fa-history', '/log-data', NULL, '2020-05-24 14:06:15', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (23, 10, 18, '客户端错误日志', 'fa-warning', '/client-error-log', NULL, '2020-05-24 14:06:37', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (24, 11, 20, '配置表', 'fa-list-ol', '/configure-table', NULL, '2020-05-24 14:07:11', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (25, 11, 21, '服务器配置(erl)', 'fa-server', '/erl-configure', NULL, '2020-05-24 14:07:48', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (26, 11, 22, '客户端配置(lua)', 'fa-desktop', '/lua-configure', NULL, '2020-05-24 14:08:13', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (27, 11, 23, '客户端配置(js)', 'fa-tv', '/js-configure', NULL, '2020-05-24 14:10:21', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (28, 12, 25, '服务器列表', 'fa-list-ul', '/server-list-manage', NULL, '2020-05-24 14:10:41', '2021-01-21 20:42:25');
INSERT INTO `admin_menu` VALUES (29, 45, 29, '封号/禁言', 'fa-sliders', '/user-manage', NULL, '2020-05-24 14:10:57', '2021-01-24 13:49:01');
INSERT INTO `admin_menu` VALUES (30, 45, 30, '邮件', 'fa-envelope-o', '/game-mail', NULL, '2020-05-24 14:11:16', '2021-01-24 12:13:22');
INSERT INTO `admin_menu` VALUES (31, 45, 31, '公告', 'fa-edit', '/game-notice', NULL, '2020-05-24 14:11:30', '2021-01-24 12:13:47');
INSERT INTO `admin_menu` VALUES (32, 12, 26, '开服', 'fa-clone', '/open-server', NULL, '2020-05-24 14:11:46', '2021-01-21 22:34:47');
INSERT INTO `admin_menu` VALUES (33, 12, 27, '合服', 'fa-copy', '/merge-server', NULL, '2020-05-24 14:12:06', '2021-01-21 22:34:47');
INSERT INTO `admin_menu` VALUES (34, 45, 33, '举报信息', 'fa-info-circle', '/impeach', NULL, '2020-05-24 14:12:33', '2021-01-23 22:20:27');
INSERT INTO `admin_menu` VALUES (35, 45, 34, '敏感词', 'fa-filter', '/sensitive-word', NULL, '2020-05-24 14:12:47', '2021-01-23 22:20:27');
INSERT INTO `admin_menu` VALUES (36, 9, 13, '首充时间分布', 'fa-pie-chart', '/first-recharge-time-distribution', NULL, '2020-06-14 18:14:45', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (37, 9, 11, '充值比例', 'fa-pie-chart', '/recharge-ratio', NULL, '2020-06-14 18:49:31', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (38, 0, 44, '计划任务', 'fa-clock-o', 'scheduling', NULL, '2020-11-29 21:52:01', '2021-01-23 22:20:27');
INSERT INTO `admin_menu` VALUES (39, 9, 10, '充值排行', 'fa-bar-chart', '/recharge-rank', NULL, '2020-12-05 21:36:04', '2021-01-12 20:57:49');
INSERT INTO `admin_menu` VALUES (40, 43, 36, '配表助手', 'fa-magic', '/configure-assistant', NULL, '2020-12-12 14:44:27', '2021-01-23 22:20:27');
INSERT INTO `admin_menu` VALUES (43, 0, 35, '工具', 'fa-wrench', NULL, NULL, '2021-01-12 20:56:52', '2021-01-23 22:20:27');
INSERT INTO `admin_menu` VALUES (44, 43, 37, 'SSH Key生成', 'fa-key', '/key-assistant', NULL, '2021-01-12 20:58:37', '2021-01-23 22:20:27');
INSERT INTO `admin_menu` VALUES (45, 0, 28, '运营管理', 'fa-user-plus', NULL, NULL, '2021-01-21 20:45:18', '2021-01-21 22:34:47');
INSERT INTO `admin_menu` VALUES (46, 45, 32, '维护公告', 'fa-bullhorn', '/maintain-notice', NULL, '2021-01-23 22:20:03', '2021-01-23 22:20:27');
INSERT INTO `admin_menu` VALUES (47, 0, 45, '日志查看器', 'fa-search-plus', 'logs', NULL, '2021-01-23 23:06:39', '2021-01-23 23:13:09');
INSERT INTO `admin_menu` VALUES (48, 0, 46, 'Helpers', 'fa-gears', '', NULL, '2021-01-23 23:14:23', '2021-01-23 23:20:13');
INSERT INTO `admin_menu` VALUES (49, 48, 47, 'Scaffold', 'fa-keyboard-o', 'helpers/scaffold', NULL, '2021-01-23 23:14:23', '2021-01-23 23:20:13');
INSERT INTO `admin_menu` VALUES (50, 48, 48, 'Database terminal', 'fa-database', 'helpers/terminal/database', NULL, '2021-01-23 23:14:23', '2021-01-23 23:20:13');
INSERT INTO `admin_menu` VALUES (51, 48, 49, 'Laravel artisan', 'fa-terminal', 'helpers/terminal/artisan', NULL, '2021-01-23 23:14:23', '2021-01-23 23:20:13');
INSERT INTO `admin_menu` VALUES (52, 48, 50, 'Routes', 'fa-list-alt', 'helpers/routes', NULL, '2021-01-23 23:14:23', '2021-01-23 23:20:13');
INSERT INTO `admin_menu` VALUES (53, 48, 51, 'Api tester', 'fa-sliders', 'api-tester', NULL, '2021-01-23 23:17:29', '2021-01-23 23:20:13');

-- ----------------------------
-- Table structure for admin_operation_log
-- ----------------------------
DROP TABLE IF EXISTS `admin_operation_log`;
CREATE TABLE `admin_operation_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `input` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `admin_operation_log_user_id_index`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_operation_log
-- ----------------------------

-- ----------------------------
-- Table structure for admin_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_permissions`;
CREATE TABLE `admin_permissions`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `http_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admin_permissions_name_unique`(`name`) USING BTREE,
  UNIQUE INDEX `admin_permissions_slug_unique`(`slug`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_permissions
-- ----------------------------
INSERT INTO `admin_permissions` VALUES (1, 'All permission', '*', '', '*', NULL, NULL);
INSERT INTO `admin_permissions` VALUES (2, 'Dashboard', 'dashboard', 'GET', '/', NULL, NULL);
INSERT INTO `admin_permissions` VALUES (3, 'Login', 'auth.login', '', '/auth/login\r\n/auth/logout', NULL, NULL);
INSERT INTO `admin_permissions` VALUES (4, 'User setting', 'auth.setting', 'GET,PUT', '/auth/setting', NULL, NULL);
INSERT INTO `admin_permissions` VALUES (5, 'Auth management', 'auth.management', '', '/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs', NULL, NULL);
INSERT INTO `admin_permissions` VALUES (7, 'Scheduling', 'ext.scheduling', '', '/scheduling*', '2020-11-29 21:52:01', '2020-11-29 21:52:01');
INSERT INTO `admin_permissions` VALUES (8, 'Logs', 'ext.log-viewer', '', '/logs*', '2021-01-23 23:06:39', '2021-01-23 23:06:39');
INSERT INTO `admin_permissions` VALUES (9, 'Admin helpers', 'ext.helpers', '', '/helpers/*', '2021-01-23 23:14:23', '2021-01-23 23:14:23');
INSERT INTO `admin_permissions` VALUES (10, 'Api tester', 'ext.api-tester', '', '/api-tester*', '2021-01-23 23:17:29', '2021-01-23 23:17:29');

-- ----------------------------
-- Table structure for admin_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_menu`;
CREATE TABLE `admin_role_menu`  (
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  INDEX `admin_role_menu_role_id_menu_id_index`(`role_id`, `menu_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_role_menu
-- ----------------------------
INSERT INTO `admin_role_menu` VALUES (1, 2, NULL, NULL);
INSERT INTO `admin_role_menu` VALUES (1, 8, NULL, NULL);
INSERT INTO `admin_role_menu` VALUES (1, 9, NULL, NULL);
INSERT INTO `admin_role_menu` VALUES (1, 12, NULL, NULL);
INSERT INTO `admin_role_menu` VALUES (5, 8, NULL, NULL);
INSERT INTO `admin_role_menu` VALUES (5, 9, NULL, NULL);
INSERT INTO `admin_role_menu` VALUES (5, 12, NULL, NULL);
INSERT INTO `admin_role_menu` VALUES (1, 38, NULL, NULL);
INSERT INTO `admin_role_menu` VALUES (4, 12, NULL, NULL);

-- ----------------------------
-- Table structure for admin_role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_permissions`;
CREATE TABLE `admin_role_permissions`  (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  INDEX `admin_role_permissions_role_id_permission_id_index`(`role_id`, `permission_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_role_permissions
-- ----------------------------
INSERT INTO `admin_role_permissions` VALUES (1, 1, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (2, 3, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (2, 4, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (3, 2, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (3, 3, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (3, 4, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (4, 2, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (4, 3, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (4, 4, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (5, 2, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (5, 3, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (5, 4, NULL, NULL);
INSERT INTO `admin_role_permissions` VALUES (2, 2, NULL, NULL);

-- ----------------------------
-- Table structure for admin_role_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_users`;
CREATE TABLE `admin_role_users`  (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  INDEX `admin_role_users_role_id_user_id_index`(`role_id`, `user_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_role_users
-- ----------------------------
INSERT INTO `admin_role_users` VALUES (1, 1, NULL, NULL);
INSERT INTO `admin_role_users` VALUES (2, 2, NULL, NULL);
INSERT INTO `admin_role_users` VALUES (3, 3, NULL, NULL);
INSERT INTO `admin_role_users` VALUES (4, 4, NULL, NULL);
INSERT INTO `admin_role_users` VALUES (5, 5, NULL, NULL);

-- ----------------------------
-- Table structure for admin_roles
-- ----------------------------
DROP TABLE IF EXISTS `admin_roles`;
CREATE TABLE `admin_roles`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admin_roles_name_unique`(`name`) USING BTREE,
  UNIQUE INDEX `admin_roles_slug_unique`(`slug`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_roles
-- ----------------------------
INSERT INTO `admin_roles` VALUES (1, '管理员', 'Administrator', '2020-05-16 23:41:12', '2020-06-13 23:13:12');
INSERT INTO `admin_roles` VALUES (2, '服务端', 'Backend', '2020-06-13 22:49:00', '2020-06-13 22:51:57');
INSERT INTO `admin_roles` VALUES (3, '客户端', 'Frontend', '2020-06-13 22:49:25', '2020-06-13 22:51:50');
INSERT INTO `admin_roles` VALUES (4, '策划', 'Product', '2020-06-13 22:51:09', '2020-06-13 22:51:44');
INSERT INTO `admin_roles` VALUES (5, '运营', 'Operation', '2020-06-13 22:58:43', '2020-06-13 22:58:43');

-- ----------------------------
-- Table structure for admin_user_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_user_permissions`;
CREATE TABLE `admin_user_permissions`  (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  INDEX `admin_user_permissions_user_id_permission_id_index`(`user_id`, `permission_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_user_permissions
-- ----------------------------

-- ----------------------------
-- Table structure for admin_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admin_users_username_unique`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_users
-- ----------------------------
INSERT INTO `admin_users` VALUES (1, 'Admin', '$2y$10$zQsAsXvy0ZkRZcdsyIrizuCJacEf5jEyI/fhZucQZnWwNSdxHy5B6', '管理员', NULL, 'iaOlzw1e1w2GSPG9zIKigDyy6MCCTfjEThDCjqjyd016eqdsecWoL8ALgxJx', '2020-05-16 23:41:12', '2020-11-29 22:05:40');
INSERT INTO `admin_users` VALUES (2, 'Backend', '$2y$10$Liq.fqESpgmaVV0WhP0/reFdPwjmiJd6VlVVDBYJiz6zm.g2FTX8y', '服务端', NULL, 'ARwBxLfZdRWig5X0VqxRc6NPgTYuiESEQZ0Wg476qGAn9vKEeuYR1UTdgbVu', '2020-06-13 22:47:31', '2020-12-02 22:27:07');
INSERT INTO `admin_users` VALUES (3, 'Frontend', '$2y$10$0ibpv2RoCoKNVlbvjqj5VOexsPxBgNrKYdYGfm3NRVVfIrk7iNQjq', '客户端', NULL, NULL, '2020-06-13 22:59:49', '2020-12-02 22:35:56');
INSERT INTO `admin_users` VALUES (4, 'Product', '$2y$10$26HKN0.WffEx3JnZcMEic.hb4KW12lpC9bDl.yqw4Ulm/A/d1GRhm', '策划', NULL, NULL, '2020-06-13 23:00:58', '2020-12-02 22:36:11');
INSERT INTO `admin_users` VALUES (5, 'Operation', '$2y$10$LcjnhrjjmMZaiqG0LlJcQeLj8J0f6JXFpyCr2XL2IyEiusigBzs1i', '运营', NULL, 'N6gdhjK4tnIZsR6mLpg9jkuIj9z45mUaJwg8WxrADDe45CpBfMBniRDrwIYA', '2020-06-13 23:01:31', '2020-12-02 22:36:24');

-- ----------------------------
-- Table structure for admin_users_third_pf_bind
-- ----------------------------
DROP TABLE IF EXISTS `admin_users_third_pf_bind`;
CREATE TABLE `admin_users_third_pf_bind`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `platform` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `third_user_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `admin_users_third_pf_bind_platform_user_id_third_user_id_unique`(`platform`, `user_id`, `third_user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_users_third_pf_bind
-- ----------------------------

-- ----------------------------
-- Table structure for client_error_log
-- ----------------------------
DROP TABLE IF EXISTS `client_error_log`;
CREATE TABLE `client_error_log`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '编号',
  `server_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '服务器ID',
  `account` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '账号',
  `role_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '玩家ID',
  `role_name` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '玩家名',
  `device` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '设备',
  `env` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '环境',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '内容',
  `content_kernel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '内核内容',
  `ip` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'IP地址',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `role_id`(`role_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '客户端错误日志表' ROW_FORMAT = Compressed;

-- ----------------------------
-- Records of client_error_log
-- ----------------------------

-- ----------------------------
-- Table structure for impeach
-- ----------------------------
DROP TABLE IF EXISTS `impeach`;
CREATE TABLE `impeach`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '编号',
  `server_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '举报方玩家服号',
  `role_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '举报方玩家ID',
  `role_name` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '举报方玩家名字',
  `impeacher_server_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '被举报玩家服号',
  `impeacher_role_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '被举报玩家ID',
  `impeacher_role_name` char(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '被举报玩家名字',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '举报类型(1:言语辱骂他人/2:盗取他人账号/3:非正规充值交易/4:其他)',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '举报内容',
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'IP地址',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `impeach_role_server`(`impeacher_role_id`, `impeacher_server_id`) USING BTREE,
  INDEX `role_server`(`role_id`, `server_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '举报信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of impeach
-- ----------------------------

-- ----------------------------
-- Table structure for maintain_notice
-- ----------------------------
DROP TABLE IF EXISTS `maintain_notice`;
CREATE TABLE `maintain_notice`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '编号',
  `platform` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '平台类型',
  `title` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '公告标题',
  `content` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '公告内容',
  `start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '开始时间',
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '结束时间',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '维护公告表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of maintain_notice
-- ----------------------------

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  INDEX `password_resets_email_index`(`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_resets
-- ----------------------------

-- ----------------------------
-- Table structure for sensitive_word_data
-- ----------------------------
DROP TABLE IF EXISTS `sensitive_word_data`;
CREATE TABLE `sensitive_word_data`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `word` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '敏感词',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '敏感词配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sensitive_word_data
-- ----------------------------

-- ----------------------------
-- Table structure for server_list
-- ----------------------------
DROP TABLE IF EXISTS `server_list`;
CREATE TABLE `server_list`  (
  `server_node` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '游戏服节点',
  `server_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '游戏服名',
  `server_host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '游戏服域名',
  `server_ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '游戏服IP',
  `server_port` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '游戏服端口',
  `server_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '游戏服编号',
  `server_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '服务器类型',
  `open_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '开服时间',
  `tab_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分页名字',
  `center_node` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '中央服节点',
  `center_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '中央服名',
  `center_host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '中央服域名',
  `center_ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '中央服IP',
  `center_port` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '中央服端口',
  `center_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '中央服编号',
  `world` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '连接大世界',
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '当前状态',
  `recommend` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '推荐',
  PRIMARY KEY (`server_node`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '服务器列表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of server_list
-- ----------------------------
INSERT INTO `server_list` VALUES ('activity', '活动服', 'fake.me', 'fake.me', 11006, 1006, 'local', 1577808000, '二', '', '', '', '', 0, 0, ' ', '', '推荐');
INSERT INTO `server_list` VALUES ('center', '小跨服', 'fake.me', 'fake.me', 0, 100, 'center', 1577808000, '', '', '', '', '', 0, 0, '', '', '');
INSERT INTO `server_list` VALUES ('dev', '开发服', 'fake.me', 'fake.me', 11004, 1004, 'local', 1577808000, '一', '', '', '', '', 0, 0, '', '', '火爆');
INSERT INTO `server_list` VALUES ('local', '本地服', 'fake.me', 'fake.me', 11001, 1001, 'local', 1577808000, '一', 'center', '小跨服', '', '', 0, 0, '', '', '新开');
INSERT INTO `server_list` VALUES ('publish', '版署服', 'fake.me', 'fake.me', 11005, 1005, 'local', 1577808000, '二', 'center', '小跨服', '', '', 0, 0, '', '', '推荐');
INSERT INTO `server_list` VALUES ('stable', '稳定服', 'fake.me', 'fake.me', 11002, 1002, 'local', 1577808000, '一', 'center', '小跨服', '', '', 0, 0, '', '', '新开');
INSERT INTO `server_list` VALUES ('test', '测试服', 'fake.me', 'fake.me', 11003, 1003, 'local', 1577808000, '三', '', '', '', '', 0, 0, '', '', '新开');
INSERT INTO `server_list` VALUES ('world', '大世界', 'fake.me', 'fake.me', 0, 0, 'world', 1577808000, '', '', '', '', '', 0, 0, '', '', '');

-- ----------------------------
-- Table structure for table_import_log
-- ----------------------------
DROP TABLE IF EXISTS `table_import_log`;
CREATE TABLE `table_import_log`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `table_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '表名',
  `time` datetime NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP COMMENT '时间',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '配置表导入日志' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of table_import_log
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
