-- MariaDB dump 10.17  Distrib 10.4.13-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: laravel
-- ------------------------------------------------------
-- Server version	10.4.13-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_menu`
--

DROP TABLE IF EXISTS `admin_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_menu`
--

LOCK TABLES `admin_menu` WRITE;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;
INSERT INTO `admin_menu` VALUES (1,0,1,'Dashboard','fa-bar-chart','/',NULL,NULL,NULL),(2,0,32,'管理员','fa-tasks',NULL,NULL,NULL,'2020-06-14 10:49:44'),(3,2,33,'用户','fa-users','auth/users',NULL,NULL,'2020-06-14 10:49:44'),(4,2,34,'角色','fa-user','auth/roles',NULL,NULL,'2020-06-14 10:49:44'),(5,2,35,'权限','fa-ban','auth/permissions',NULL,NULL,'2020-06-14 10:49:44'),(6,2,36,'菜单','fa-bars','auth/menu',NULL,NULL,'2020-06-14 10:49:44'),(7,2,37,'操作日志','fa-history','auth/logs',NULL,NULL,'2020-06-14 10:49:44'),(8,0,2,'活跃统计','fa-bars',NULL,NULL,'2020-05-24 06:00:57','2020-06-13 15:12:40'),(9,0,8,'充值统计','fa-bars',NULL,NULL,'2020-05-24 06:01:08','2020-05-30 03:24:22'),(10,0,13,'游戏数据','fa-bars',NULL,NULL,'2020-05-24 06:01:25','2020-06-14 10:49:44'),(11,0,18,'配置数据','fa-bars',NULL,NULL,'2020-05-24 06:01:31','2020-06-14 10:49:44'),(12,0,23,'服务器管理','fa-bars',NULL,NULL,'2020-05-24 06:01:41','2020-06-14 10:49:44'),(13,8,3,'玩家在线','fa-bars','/user-online',NULL,'2020-05-24 06:02:30','2020-05-24 06:03:29'),(14,8,4,'玩家注册','fa-bars','/user-register',NULL,'2020-05-24 06:02:53','2020-05-24 06:03:29'),(15,8,5,'玩家登录','fa-bars','/user-login',NULL,'2020-05-24 06:03:17','2020-05-24 06:03:29'),(16,8,6,'玩家存活','fa-bars','/user-survival',NULL,'2020-05-24 06:04:00','2020-05-30 03:24:22'),(17,8,7,'玩家流失','fa-bars','/user-loss',NULL,'2020-05-24 06:04:19','2020-05-30 03:24:22'),(18,9,9,'玩家充值','fa-bars','/user-recharge',NULL,'2020-05-24 06:05:04','2020-05-30 03:24:22'),(19,9,11,'充值分布','fa-bars','/recharge-distribution',NULL,'2020-05-24 06:05:24','2020-06-14 10:49:44'),(20,10,14,'玩家数据','fa-bars','/user-data',NULL,'2020-05-24 06:05:44','2020-06-14 10:49:44'),(21,10,15,'配置数据','fa-bars','/configure-data',NULL,'2020-05-24 06:06:00','2020-06-14 10:49:44'),(22,10,16,'日志数据','fa-bars','/log-data',NULL,'2020-05-24 06:06:15','2020-06-14 10:49:44'),(23,10,17,'客户端错误日志','fa-bars','/client-error-log',NULL,'2020-05-24 06:06:37','2020-06-14 10:49:44'),(24,11,19,'配置表','fa-bars','/configure-table',NULL,'2020-05-24 06:07:11','2020-06-14 10:49:44'),(25,11,20,'服务器配置(erl)','fa-bars','/erl-configure',NULL,'2020-05-24 06:07:48','2020-06-14 10:49:44'),(26,11,21,'客户端配置(lua)','fa-bars','/lua-configure',NULL,'2020-05-24 06:08:13','2020-06-14 10:49:44'),(27,11,22,'客户端配置(js)','fa-bars','/js-configure',NULL,'2020-05-24 06:10:21','2020-06-14 10:49:44'),(28,12,24,'服务器列表','fa-bars','/server-list-manage',NULL,'2020-05-24 06:10:41','2020-06-21 09:07:48'),(29,12,25,'玩家管理','fa-bars','/user-manage',NULL,'2020-05-24 06:10:57','2020-06-14 10:49:44'),(30,12,26,'服务器邮件','fa-bars','/server-mail',NULL,'2020-05-24 06:11:16','2020-06-14 10:49:44'),(31,12,27,'服务器公告','fa-bars','/server-notice',NULL,'2020-05-24 06:11:30','2020-06-14 10:49:44'),(32,12,28,'开服','fa-bars','/open-server',NULL,'2020-05-24 06:11:46','2020-06-14 10:49:44'),(33,12,29,'合服','fa-bars','/merge-server',NULL,'2020-05-24 06:12:06','2020-06-14 10:49:44'),(34,12,30,'玩家举报','fa-bars','/user-impeach',NULL,'2020-05-24 06:12:33','2020-06-14 10:49:44'),(35,12,31,'敏感词','fa-bars','/sensitive-word',NULL,'2020-05-24 06:12:47','2020-06-14 10:49:44'),(36,9,12,'首充时间分布','fa-bars','/first-recharge-time-distribution',NULL,'2020-06-14 10:14:45','2020-06-14 10:49:44'),(37,9,10,'充值比例','fa-bars','/recharge-ratio',NULL,'2020-06-14 10:49:31','2020-06-14 10:49:44');
/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_operation_log`
--

DROP TABLE IF EXISTS `admin_operation_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_operation_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `input` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_operation_log_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_operation_log`
--

LOCK TABLES `admin_operation_log` WRITE;
/*!40000 ALTER TABLE `admin_operation_log` DISABLE KEYS */;
INSERT INTO `admin_operation_log` VALUES (26,1,'/','GET','127.0.0.1','[]','2020-07-26 10:34:38','2020-07-26 10:34:38'),(27,1,'/','GET','127.0.0.1','[]','2020-07-26 10:35:24','2020-07-26 10:35:24'),(28,1,'/','GET','127.0.0.1','[]','2020-07-26 10:36:06','2020-07-26 10:36:06'),(29,1,'/','GET','127.0.0.1','[]','2020-07-26 10:36:38','2020-07-26 10:36:38'),(30,1,'/','GET','127.0.0.1','[]','2020-07-26 10:36:52','2020-07-26 10:36:52');
/*!40000 ALTER TABLE `admin_operation_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_permissions`
--

DROP TABLE IF EXISTS `admin_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `http_path` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_permissions_name_unique` (`name`),
  UNIQUE KEY `admin_permissions_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_permissions`
--

LOCK TABLES `admin_permissions` WRITE;
/*!40000 ALTER TABLE `admin_permissions` DISABLE KEYS */;
INSERT INTO `admin_permissions` VALUES (1,'All permission','*','','*',NULL,NULL),(2,'Dashboard','dashboard','GET','/',NULL,NULL),(3,'Login','auth.login','','/auth/login\r\n/auth/logout',NULL,NULL),(4,'User setting','auth.setting','GET,PUT','/auth/setting',NULL,NULL),(5,'Auth management','auth.management','','/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs',NULL,NULL);
/*!40000 ALTER TABLE `admin_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_role_menu`
--

DROP TABLE IF EXISTS `admin_role_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_role_menu` (
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_menu_role_id_menu_id_index` (`role_id`,`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_role_menu`
--

LOCK TABLES `admin_role_menu` WRITE;
/*!40000 ALTER TABLE `admin_role_menu` DISABLE KEYS */;
INSERT INTO `admin_role_menu` VALUES (1,2,NULL,NULL),(1,8,NULL,NULL),(1,9,NULL,NULL),(1,12,NULL,NULL),(5,8,NULL,NULL),(5,9,NULL,NULL),(4,12,NULL,NULL),(5,12,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_role_permissions`
--

DROP TABLE IF EXISTS `admin_role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_permissions_role_id_permission_id_index` (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_role_permissions`
--

LOCK TABLES `admin_role_permissions` WRITE;
/*!40000 ALTER TABLE `admin_role_permissions` DISABLE KEYS */;
INSERT INTO `admin_role_permissions` VALUES (1,1,NULL,NULL),(2,2,NULL,NULL),(2,3,NULL,NULL),(2,4,NULL,NULL),(3,2,NULL,NULL),(3,3,NULL,NULL),(3,4,NULL,NULL),(4,2,NULL,NULL),(4,3,NULL,NULL),(4,4,NULL,NULL),(5,2,NULL,NULL),(5,3,NULL,NULL),(5,4,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_role_users`
--

DROP TABLE IF EXISTS `admin_role_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_role_users` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_role_users_role_id_user_id_index` (`role_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_role_users`
--

LOCK TABLES `admin_role_users` WRITE;
/*!40000 ALTER TABLE `admin_role_users` DISABLE KEYS */;
INSERT INTO `admin_role_users` VALUES (1,1,NULL,NULL),(2,2,NULL,NULL),(3,3,NULL,NULL),(4,4,NULL,NULL),(5,5,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_roles`
--

DROP TABLE IF EXISTS `admin_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_roles_name_unique` (`name`),
  UNIQUE KEY `admin_roles_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_roles`
--

LOCK TABLES `admin_roles` WRITE;
/*!40000 ALTER TABLE `admin_roles` DISABLE KEYS */;
INSERT INTO `admin_roles` VALUES (1,'管理员','Administrator','2020-05-16 15:41:12','2020-06-13 15:13:12'),(2,'服务端','Backend','2020-06-13 14:49:00','2020-06-13 14:51:57'),(3,'客户端','Frontend','2020-06-13 14:49:25','2020-06-13 14:51:50'),(4,'策划','Product','2020-06-13 14:51:09','2020-06-13 14:51:44'),(5,'运营','Operation','2020-06-13 14:58:43','2020-06-13 14:58:43');
/*!40000 ALTER TABLE `admin_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_user_permissions`
--

DROP TABLE IF EXISTS `admin_user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_user_permissions` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `admin_user_permissions_user_id_permission_id_index` (`user_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_user_permissions`
--

LOCK TABLES `admin_user_permissions` WRITE;
/*!40000 ALTER TABLE `admin_user_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'admin','$2y$10$zQsAsXvy0ZkRZcdsyIrizuCJacEf5jEyI/fhZucQZnWwNSdxHy5B6','管理员',NULL,'o56L52CIpnPXPYaPkHDiq3EzuQtSEdAlXGplrnmI6HjgZaFARWuMlgNR4fAP','2020-05-16 15:41:12','2020-06-13 15:05:09'),(2,'bkd','$2y$10$puF3d3HWYuNVchwQtUImqeTvJdovy4La2GGrS.lCf9NJ/vW/zxDH.','bkd',NULL,'tePaVmOwhUuRJLBSFCeu2UvouFP6LAgArSQEhyasC7UjDooadGgdzlyZCXHV','2020-06-13 14:47:31','2020-06-13 15:00:08'),(3,'fnd','$2y$10$qZOyggM/t3ru1nUZ796yo.5ndD9ycFDPZ0Hpukwd.QMAyV9YnX1Li','fnd',NULL,NULL,'2020-06-13 14:59:49','2020-06-13 15:00:39'),(4,'pdt','$2y$10$ICR0yZqVzbVvgP3AJoTHVOuXa94SX8NUk60mbfKhVZszRWLkZmVUq','pdt',NULL,NULL,'2020-06-13 15:00:58','2020-06-13 15:00:58'),(5,'ort','$2y$10$UcbHF2JpmyV61.vc6Jll6eVd9xLLEKz029eY23D7/amIJDAlXFgmW','ort',NULL,'N6gdhjK4tnIZsR6mLpg9jkuIj9z45mUaJwg8WxrADDe45CpBfMBniRDrwIYA','2020-06-13 15:01:31','2020-06-13 15:01:31');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_users_third_pf_bind`
--

DROP TABLE IF EXISTS `admin_users_third_pf_bind`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_users_third_pf_bind` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `platform` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `third_user_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_users_third_pf_bind_platform_user_id_third_user_id_unique` (`platform`,`user_id`,`third_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users_third_pf_bind`
--

LOCK TABLES `admin_users_third_pf_bind` WRITE;
/*!40000 ALTER TABLE `admin_users_third_pf_bind` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_users_third_pf_bind` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_error_log`
--

DROP TABLE IF EXISTS `client_error_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client_error_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `server_id` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '服务器ID',
  `account` char(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '账号',
  `role_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '玩家ID',
  `role_name` char(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '玩家名',
  `env` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '环境',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '内容',
  `content_kernel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '内核内容',
  `ip` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'IP地址',
  `time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `role_id` (`role_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPRESSED COMMENT='客户端错误日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_error_log`
--

LOCK TABLES `client_error_log` WRITE;
/*!40000 ALTER TABLE `client_error_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `client_error_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `impeach`
--

DROP TABLE IF EXISTS `impeach`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `impeach` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `server_id` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '举报方玩家服号',
  `role_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '举报方玩家ID',
  `role_name` char(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '举报方玩家名字',
  `impeach_server_id` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '被举报玩家服号',
  `impeach_role_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '被举报玩家ID',
  `impeach_role_name` char(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '被举报玩家名字',
  `type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '举报类型(1:言语辱骂他人/2:盗取他人账号/3:非正规充值交易/4:其他)',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '举报内容',
  `time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `impeach_role_server` (`impeach_role_id`,`impeach_server_id`) USING BTREE,
  KEY `role_server` (`role_id`,`server_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='举报信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impeach`
--

LOCK TABLES `impeach` WRITE;
/*!40000 ALTER TABLE `impeach` DISABLE KEYS */;
/*!40000 ALTER TABLE `impeach` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2016_01_04_173148_create_admin_tables',1),(4,'2019_08_19_000000_create_failed_jobs_table',1),(5,'2020_04_28_000000_create_admin_oauth_tables',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sensitive_word_data`
--

DROP TABLE IF EXISTS `sensitive_word_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sensitive_word_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `word` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '敏感词',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='敏感词配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensitive_word_data`
--

LOCK TABLES `sensitive_word_data` WRITE;
/*!40000 ALTER TABLE `sensitive_word_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `sensitive_word_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `server_list_data`
--

DROP TABLE IF EXISTS `server_list_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `server_list_data` (
  `server_node` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '游戏服节点',
  `server_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '游戏服名',
  `server_host` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '游戏服域名',
  `server_ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '游戏服IP',
  `server_port` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '游戏服端口',
  `server_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '游戏服编号',
  `server_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '服务器类型',
  `open_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '开服时间',
  `center_node` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '中央服节点',
  `center_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '中央服名',
  `center_host` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '中央服域名',
  `center_ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '中央服IP',
  `center_port` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '中央服端口',
  `center_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '中央服编号',
  `world` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '连接大世界',
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '当前状态',
  `recommend` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '推荐',
  PRIMARY KEY (`server_node`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='服务器列表配置表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `server_list_data`
--

LOCK TABLES `server_list_data` WRITE;
/*!40000 ALTER TABLE `server_list_data` DISABLE KEYS */;
INSERT INTO `server_list_data` VALUES ('center','小跨服','','fake.me',0,100,'center',1577808000,'','','','',0,0,'','',''),('dev','开发服','','fake.me',11004,1004,'local',1577808000,'center','小跨服','','',0,0,'','','火爆'),('local','本地服','','fake.me',11001,1001,'local',1577808000,'center','小跨服','','',0,0,'','','新开'),('publish','版署服','','fake.me',11005,1005,'local',1577808000,'center','小跨服','','',0,0,'','','推荐'),('stable','稳定服','','fake.me',11002,1002,'local',1577808000,'center','小跨服','','',0,0,'','','新开'),('test','测试服','','fake.me',11003,1003,'local',1577808000,'center','小跨服','','',0,0,'','','新开'),('world','大世界','','fake.me',0,0,'world',1577808000,'','','','',0,0,'','','');
/*!40000 ALTER TABLE `server_list_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-08-22 13:15:28
