<?php

namespace Database\Seeders;

use App\Http\Models\NavigationModel;
use Illuminate\Database\Seeder;

class WebSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {

        NavigationModel::truncate();
        NavigationModel::insert(
            [
                [
                    "id" => 1,
                    "parent_id" => 0,
                    "order" => 0,
                    "icon" => "fa-brands fa-git-alt",
                    "color" => "#ed0d0c",
                    "title" => "git",
                    "content" => "",
                    "url" => ""
                ],
                [
                    "id" => 2,
                    "parent_id" => 1,
                    "order" => 0,
                    "icon" => "fa-brands fa-git-alt",
                    "color" => "",
                    "title" => "git - 使用用SSH Key",
                    "content" => "进入后台 - 工具 - SSH Key生成页面生成",
                    "url" => "/admin/assistant/key-assistant"
                ],
                [
                    "id" => 3,
                    "parent_id" => 0,
                    "order" => 0,
                    "icon" => "fa-gears",
                    "color" => "#9c57b6",
                    "title" => "svn",
                    "content" => "",
                    "url" => ""
                ],
                [
                    "id" => 4,
                    "parent_id" => 3,
                    "order" => 0,
                    "icon" => "fa-gears",
                    "color" => "",
                    "title" => "svn - 使用账号密码",
                    "content" => "账号密码为名字拼音全拼",
                    "url" => ""
                ],
                [
                    "id" => 5,
                    "parent_id" => 0,
                    "order" => 0,
                    "icon" => "fa-code-branch",
                    "color" => "#1a9f29",
                    "title" => "服务器",
                    "content" => "",
                    "url" => ""
                ],
                [
                    "id" => 6,
                    "parent_id" => 5,
                    "order" => 0,
                    "icon" => "fa-server",
                    "color" => "",
                    "title" => "服务器git地址",
                    "content" => "git@192.168.30.155:~/moco/server",
                    "url" => ""
                ],
                [
                    "id" => 7,
                    "parent_id" => 5,
                    "order" => 0,
                    "icon" => "fa-user",
                    "color" => "",
                    "title" => "后台git地址",
                    "content" => "git@192.168.30.155:~/moco/admin",
                    "url" => ""
                ],
                [
                    "id" => 8,
                    "parent_id" => 5,
                    "order" => 0,
                    "icon" => "fa-user",
                    "color" => "",
                    "title" => "后台",
                    "content" => "进入",
                    "url" => "/admin"
                ],
                [
                    "id" => 9,
                    "parent_id" => 0,
                    "order" => 0,
                    "icon" => "fa-handshake",
                    "color" => "#275fe4",
                    "title" => "协议",
                    "content" => "",
                    "url" => ""
                ],
                [
                    "id" => 10,
                    "parent_id" => 9,
                    "order" => 0,
                    "icon" => "fa-handshake",
                    "color" => "",
                    "title" => "协议svn地址",
                    "content" => "svn://192.168.30.155/moco/protocol",
                    "url" => ""
                ],
                [
                    "id" => 11,
                    "parent_id" => 9,
                    "order" => 0,
                    "icon" => "fa-handshake",
                    "color" => "",
                    "title" => "协议文档",
                    "content" => "进入",
                    "url" => "/protocol/Protocol.html"
                ],
                [
                    "id" => 12,
                    "parent_id" => 9,
                    "order" => 0,
                    "icon" => "fa-paper-plane",
                    "color" => "",
                    "title" => "API文档",
                    "content" => "进入",
                    "url" => "/api/documentation#/default"
                ],
                [
                    "id" => 13,
                    "parent_id" => 0,
                    "order" => 0,
                    "icon" => "fa-brands fa-unity",
                    "color" => "#a71b76",
                    "title" => "客户端",
                    "content" => "",
                    "url" => ""
                ],
                [
                    "id" => 14,
                    "parent_id" => 13,
                    "order" => 0,
                    "icon" => "fa-brands fa-unity",
                    "color" => "",
                    "title" => "客户端svn地址",
                    "content" => "svn://192.168.30.155/moco/client",
                    "url" => ""
                ],
                [
                    "id" => 15,
                    "parent_id" => 13,
                    "order" => 0,
                    "icon" => "fa-brands fa-unity",
                    "color" => "",
                    "title" => "客户端配置svn地址",
                    "content" => "svn://192.168.30.155/moco/configure",
                    "url" => ""
                ],
                [
                    "id" => 16,
                    "parent_id" => 13,
                    "order" => 0,
                    "icon" => "fa-tags",
                    "color" => "",
                    "title" => "客户端打包",
                    "content" => "进入",
                    "url" => "/build"
                ],
                [
                    "id" => 17,
                    "parent_id" => 0,
                    "order" => 0,
                    "icon" => "fa-book",
                    "color" => "#fced2b",
                    "title" => "策划",
                    "content" => "",
                    "url" => ""
                ],
                [
                    "id" => 18,
                    "parent_id" => 17,
                    "order" => 0,
                    "icon" => "fa-book",
                    "color" => "",
                    "title" => "文档svn目录",
                    "content" => "svn://192.168.30.155/moco/doc",
                    "url" => ""
                ],
                [
                    "id" => 19,
                    "parent_id" => 0,
                    "order" => 0,
                    "icon" => "fa-palette",
                    "color" => "#00c6c8",
                    "title" => "美术",
                    "content" => "",
                    "url" => ""
                ],
                [
                    "id" => 20,
                    "parent_id" => 19,
                    "order" => 0,
                    "icon" => "fa-palette",
                    "color" => "",
                    "title" => "美术资源文件目录",
                    "content" => "svn://192.168.30.155/moco/res",
                    "url" => ""
                ]
            ]
        );
    }
}
