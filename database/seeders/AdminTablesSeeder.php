<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // base tables
        \Encore\Admin\Auth\Database\Menu::truncate();
        \Encore\Admin\Auth\Database\Menu::insert(
            [
                [
                    "parent_id" => 0,
                    "order" => 1,
                    "title" => "仪表盘",
                    "icon" => "fa-dashboard",
                    "uri" => "/",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 2,
                    "title" => "活跃统计",
                    "icon" => "fa-area-chart",
                    "uri" => NULL,
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 3,
                    "title" => "实时在线人数",
                    "icon" => "fa-area-chart",
                    "uri" => "/active-statistics/user-online",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 4,
                    "title" => "注册统计",
                    "icon" => "fa-bar-chart",
                    "uri" => "/active-statistics/user-register",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 5,
                    "title" => "登录统计",
                    "icon" => "fa-bar-chart",
                    "uri" => "/active-statistics/user-login",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 6,
                    "title" => "存活统计",
                    "icon" => "fa-bar-chart",
                    "uri" => "/active-statistics/user-survival",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 2,
                    "order" => 7,
                    "title" => "每日在线时长",
                    "icon" => "fa-bar-chart",
                    "uri" => "/active-statistics/daily-online-time",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 8,
                    "title" => "充值统计",
                    "icon" => "fa-line-chart",
                    "uri" => NULL,
                    "permission" => NULL
                ],
                [
                    "parent_id" => 8,
                    "order" => 9,
                    "title" => "每日充值统计",
                    "icon" => "fa-bar-chart",
                    "uri" => "/recharge-statistics/daily-recharge",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 8,
                    "order" => 10,
                    "title" => "充值排行",
                    "icon" => "fa-bar-chart",
                    "uri" => "/recharge-statistics/recharge-rank",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 8,
                    "order" => 11,
                    "title" => "充值比例",
                    "icon" => "fa-pie-chart",
                    "uri" => "/recharge-statistics/recharge-ratio",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 8,
                    "order" => 12,
                    "title" => "充值区间分布",
                    "icon" => "fa-pie-chart",
                    "uri" => "/recharge-statistics/recharge-distribution",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 8,
                    "order" => 13,
                    "title" => "首充时间分布",
                    "icon" => "fa-pie-chart",
                    "uri" => "/recharge-statistics/first-recharge-time-distribution",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 14,
                    "title" => "游戏数据",
                    "icon" => "fa-save",
                    "uri" => NULL,
                    "permission" => NULL
                ],
                [
                    "parent_id" => 14,
                    "order" => 15,
                    "title" => "玩家数据",
                    "icon" => "fa-user-plus",
                    "uri" => "/game-data/user-data",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 14,
                    "order" => 16,
                    "title" => "配置数据",
                    "icon" => "fa-tags",
                    "uri" => "/game-data/configure-data",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 14,
                    "order" => 17,
                    "title" => "日志数据",
                    "icon" => "fa-history",
                    "uri" => "/game-data/log-data",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 14,
                    "order" => 18,
                    "title" => "客户端错误日志",
                    "icon" => "fa-warning",
                    "uri" => "/game-data/client-error-log",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 19,
                    "title" => "配置管理",
                    "icon" => "fa-database",
                    "uri" => NULL,
                    "permission" => NULL
                ],
                [
                    "parent_id" => 19,
                    "order" => 20,
                    "title" => "配置表",
                    "icon" => "fa-list-ol",
                    "uri" => "/configure-data/configure-table",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 19,
                    "order" => 21,
                    "title" => "服务器配置(erl)",
                    "icon" => "fa-server",
                    "uri" => "/configure-data/erl-configure",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 19,
                    "order" => 22,
                    "title" => "客户端配置(lua)",
                    "icon" => "fa-desktop",
                    "uri" => "/configure-data/lua-configure",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 19,
                    "order" => 23,
                    "title" => "客户端配置(js)",
                    "icon" => "fa-tv",
                    "uri" => "/configure-data/js-configure",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 24,
                    "title" => "服务器管理",
                    "icon" => "fa-gears",
                    "uri" => NULL,
                    "permission" => NULL
                ],
                [
                    "parent_id" => 24,
                    "order" => 25,
                    "title" => "服务器列表",
                    "icon" => "fa-list-ul",
                    "uri" => "/server-manage/server-list",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 24,
                    "order" => 26,
                    "title" => "服务器调整",
                    "icon" => "fa-cog",
                    "uri" => "/server-manage/server-tuning",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 24,
                    "order" => 27,
                    "title" => "开服",
                    "icon" => "fa-clone",
                    "uri" => "/server-manage/open-server",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 24,
                    "order" => 28,
                    "title" => "合服",
                    "icon" => "fa-copy",
                    "uri" => "/server-manage/merge-server",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 29,
                    "title" => "运营管理",
                    "icon" => "fa-user-plus",
                    "uri" => NULL,
                    "permission" => NULL
                ],
                [
                    "parent_id" => 29,
                    "order" => 30,
                    "title" => "封号/禁言",
                    "icon" => "fa-sliders",
                    "uri" => "/operation/user-manage",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 29,
                    "order" => 31,
                    "title" => "邮件",
                    "icon" => "fa-envelope-o",
                    "uri" => "/operation/game-mail",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 29,
                    "order" => 32,
                    "title" => "公告",
                    "icon" => "fa-edit",
                    "uri" => "/operation/game-notice",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 29,
                    "order" => 33,
                    "title" => "维护公告",
                    "icon" => "fa-bullhorn",
                    "uri" => "/operation/maintain-notice",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 29,
                    "order" => 34,
                    "title" => "举报信息",
                    "icon" => "fa-info-circle",
                    "uri" => "/operation/impeach",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 29,
                    "order" => 35,
                    "title" => "敏感词",
                    "icon" => "fa-filter",
                    "uri" => "/operation/sensitive-word",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 36,
                    "title" => "工具",
                    "icon" => "fa-wrench",
                    "uri" => NULL,
                    "permission" => NULL
                ],
                [
                    "parent_id" => 36,
                    "order" => 37,
                    "title" => "配表助手",
                    "icon" => "fa-magic",
                    "uri" => "/assistant/configure-assistant",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 36,
                    "order" => 38,
                    "title" => "SSH Key生成",
                    "icon" => "fa-key",
                    "uri" => "/assistant/key-assistant",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 0,
                    "order" => 39,
                    "title" => "管理员",
                    "icon" => "fa-tasks",
                    "uri" => NULL,
                    "permission" => NULL
                ],
                [
                    "parent_id" => 39,
                    "order" => 40,
                    "title" => "用户",
                    "icon" => "fa-users",
                    "uri" => "auth/users",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 39,
                    "order" => 41,
                    "title" => "角色",
                    "icon" => "fa-user",
                    "uri" => "auth/roles",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 39,
                    "order" => 42,
                    "title" => "权限",
                    "icon" => "fa-ban",
                    "uri" => "auth/permissions",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 39,
                    "order" => 43,
                    "title" => "菜单",
                    "icon" => "fa-bars",
                    "uri" => "auth/menu",
                    "permission" => NULL
                ],
                [
                    "parent_id" => 39,
                    "order" => 44,
                    "title" => "操作日志",
                    "icon" => "fa-history",
                    "uri" => "auth/logs",
                    "permission" => NULL
                ]
            ]
        );

        \Encore\Admin\Auth\Database\Permission::truncate();
        \Encore\Admin\Auth\Database\Permission::insert(
            [
                [
                    "name" => "All permission",
                    "slug" => "*",
                    "http_method" => "",
                    "http_path" => "*"
                ],
                [
                    "name" => "Dashboard",
                    "slug" => "dashboard",
                    "http_method" => "GET",
                    "http_path" => "/\r\n/switch-server"
                ],
                [
                    "name" => "Login",
                    "slug" => "auth.login",
                    "http_method" => "",
                    "http_path" => "/auth/login\r\n/auth/logout"
                ],
                [
                    "name" => "User setting",
                    "slug" => "auth.setting",
                    "http_method" => "GET,PUT",
                    "http_path" => "/auth/setting"
                ],
                [
                    "name" => "Auth management",
                    "slug" => "auth.management",
                    "http_method" => "",
                    "http_path" => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs"
                ]
            ]
        );

        \Encore\Admin\Auth\Database\Role::truncate();
        \Encore\Admin\Auth\Database\Role::insert(
            [
                [
                    "name" => "管理员",
                    "slug" => "Administrator"
                ],
                [
                    "name" => "服务端",
                    "slug" => "Backend"
                ],
                [
                    "name" => "客户端",
                    "slug" => "Frontend"
                ],
                [
                    "name" => "策划",
                    "slug" => "Product"
                ],
                [
                    "name" => "运营",
                    "slug" => "Operation"
                ]
            ]
        );

        // pivot tables
        DB::table('admin_role_menu')->truncate();
        DB::table('admin_role_menu')->insert(
            [
                [
                    "role_id" => 1,
                    "menu_id" => 39
                ]
            ]
        );

        DB::table('admin_role_permissions')->truncate();
        DB::table('admin_role_permissions')->insert(
            [
                [
                    "role_id" => 1,
                    "permission_id" => 1
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 2
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 3
                ],
                [
                    "role_id" => 2,
                    "permission_id" => 4
                ],
                [
                    "role_id" => 3,
                    "permission_id" => 2
                ],
                [
                    "role_id" => 3,
                    "permission_id" => 3
                ],
                [
                    "role_id" => 3,
                    "permission_id" => 4
                ],
                [
                    "role_id" => 4,
                    "permission_id" => 2
                ],
                [
                    "role_id" => 4,
                    "permission_id" => 3
                ],
                [
                    "role_id" => 4,
                    "permission_id" => 4
                ],
                [
                    "role_id" => 5,
                    "permission_id" => 2
                ],
                [
                    "role_id" => 5,
                    "permission_id" => 3
                ],
                [
                    "role_id" => 5,
                    "permission_id" => 4
                ]
            ]
        );

        // finish
    }
}
