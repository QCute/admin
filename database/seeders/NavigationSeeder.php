<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // base tables
        DB::table('navigation')->truncate();
        DB::table('navigation')->insert(
            [
                [
                    "parent_id" => 0,
                    "order" => 1,
                    "icon" => "fa-brands fa-git-alt",
                    "color" => "#ed0d0c",
                    "title" => "git",
                    "content" => "",
                    "url" => ""
                ],
                [
                    "parent_id" => 1,
                    "order" => 1,
                    "icon" => "fa-brands fa-git-alt",
                    "color" => "",
                    "title" => "git - 使用用SSH Key",
                    "content" => "进入后台 - 工具 - SSH Key生成页面生成",
                    "url" => "/admin/assistant/key-assistant"
                ],
                [
                    "parent_id" => 0,
                    "order" => 1,
                    "icon" => "fa-gears",
                    "color" => "#9c57b6",
                    "title" => "svn",
                    "content" => "",
                    "url" => ""
                ],
                [
                    "parent_id" => 3,
                    "order" => 1,
                    "icon" => "fa-gears",
                    "color" => "",
                    "title" => "svn - 使用账号密码",
                    "content" => "账号密码为名字拼音全拼",
                    "url" => ""
                ],
                [
                    "parent_id" => 0,
                    "order" => 1,
                    "icon" => "fa-code-branch",
                    "color" => "#1a9f29",
                    "title" => "服务器",
                    "content" => "",
                    "url" => ""
                ],
                [
                    "parent_id" => 5,
                    "order" => 1,
                    "icon" => "fa-server",
                    "color" => "",
                    "title" => "服务器git地址",
                    "content" => "git@192.168.30.155:~/moco/server",
                    "url" => ""
                ],
                [
                    "parent_id" => 5,
                    "order" => 1,
                    "icon" => "fa-user",
                    "color" => "",
                    "title" => "后台git地址",
                    "content" => "git@192.168.30.155:~/moco/admin",
                    "url" => ""
                ],
                [
                    "parent_id" => 5,
                    "order" => 1,
                    "icon" => "fa-user",
                    "color" => "",
                    "title" => "后台",
                    "content" => "进入",
                    "url" => "/admin"
                ],
                [
                    "parent_id" => 0,
                    "order" => 1,
                    "icon" => "fa-handshake",
                    "color" => "#275fe4",
                    "title" => "协议",
                    "content" => "",
                    "url" => ""
                ],
                [
                    "parent_id" => 9,
                    "order" => 1,
                    "icon" => "fa-handshake",
                    "color" => "",
                    "title" => "协议svn地址",
                    "content" => "svn://192.168.30.155/moco/protocol",
                    "url" => ""
                ],
                [
                    "parent_id" => 9,
                    "order" => 1,
                    "icon" => "fa-handshake",
                    "color" => "",
                    "title" => "协议文档",
                    "content" => "进入",
                    "url" => "/protocol/Protocol.html"
                ],
                [
                    "parent_id" => 9,
                    "order" => 1,
                    "icon" => "fa-paper-plane",
                    "color" => "",
                    "title" => "API文档",
                    "content" => "进入",
                    "url" => "/api/documentation#/default"
                ],
                [
                    "parent_id" => 0,
                    "order" => 1,
                    "icon" => "fa-brands fa-unity",
                    "color" => "#a71b76",
                    "title" => "客户端",
                    "content" => "",
                    "url" => ""
                ],
                [
                    "parent_id" => 13,
                    "order" => 1,
                    "icon" => "fa-brands fa-unity",
                    "color" => "",
                    "title" => "客户端svn地址",
                    "content" => "svn://192.168.30.155/moco/client",
                    "url" => ""
                ],
                [
                    "parent_id" => 13,
                    "order" => 1,
                    "icon" => "fa-brands fa-unity",
                    "color" => "",
                    "title" => "客户端配置svn地址",
                    "content" => "svn://192.168.30.155/moco/configure",
                    "url" => ""
                ],
                [
                    "parent_id" => 13,
                    "order" => 1,
                    "icon" => "fa-tags",
                    "color" => "",
                    "title" => "客户端打包",
                    "content" => "进入",
                    "url" => "/build"
                ],
                [
                    "parent_id" => 0,
                    "order" => 1,
                    "icon" => "fa-book",
                    "color" => "#fced2b",
                    "title" => "策划",
                    "content" => "",
                    "url" => ""
                ],
                [
                    "parent_id" => 17,
                    "order" => 1,
                    "icon" => "fa-book",
                    "color" => "",
                    "title" => "文档svn目录",
                    "content" => "svn://192.168.30.155/moco/doc",
                    "url" => ""
                ],
                [
                    "parent_id" => 0,
                    "order" => 1,
                    "icon" => "fa-palette",
                    "color" => "#00c6c8",
                    "title" => "美术",
                    "content" => "",
                    "url" => ""
                ],
                [
                    "parent_id" => 19,
                    "order" => 1,
                    "icon" => "fa-palette",
                    "color" => "",
                    "title" => "美术资源文件目录",
                    "content" => "svn://192.168.30.155/moco/res",
                    "url" => ""
                ]
            ]
        );

        // finish
    }
}
