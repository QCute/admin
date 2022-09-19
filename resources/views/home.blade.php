<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Home</title>
        <script src="https://unpkg.com/vue@3.2.33/dist/vue.global.prod.js"></script>
        <!-- AntDesign -->
        <script src="https://unpkg.com/dayjs@1.11.2/dayjs.min.js"></script>
        <link href="https://unpkg.com/ant-design-vue@3.2.3/dist/antd.min.css" rel="stylesheet">
        <script src="https://unpkg.com/ant-design-vue@3.2.3/dist/antd.min.js"></script>
        <!-- FontAwesome -->
        <link href="https://unpkg.com/@fortawesome/fontawesome-free@6.1.1/css/all.min.css" rel="stylesheet">
    </head>
    <body>
        <div id="container"></div>
        <script>
            // view
            let app = Vue.createApp({
                setup() {
                    return {}
                },
                mounted() {

                },
                'methods': {
                },
                "template": `

<a-row justify="center">
    <a-col :span="8">
        <a-card hoverable style="">
            <a-card-meta>
                <template #avatar>
                    <i class="fa-brands fa-git-alt fa-3x"></i>
                </template>
                <template #title>
                    Git
                </template>
                <template #description>
                    <h3 style="color:red">git用ssh key(可进入后台 - 工具 - SSH Key生成页面生成)<h3>
                </template>
            </a-card-meta>
        </a-card>
    </a-col>
    <a-col :span="8">
        <a-card hoverable style="">
            <a-card-meta>
                <template #avatar>
                    <i class="fa fa-gears fa-3x" aria-hidden="true"></i>
                </template>
                <template #title>
                    SVN
                </template>
                <template #description>
                    <h3 style="color:red">账号名字拼音, 密码私聊发</h3>
                </template>
            </a-card-meta>
        </a-card>
    </a-col>
    <a-col :span="8">
        <a-card hoverable style="">
            <a-card-meta>
                <template #avatar>
                    <i class="fa-solid fa-gamepad fa-3x"></i>
                </template>
                <template #title>
                    协议调试
                </template>
                <template #description>
                    <h3><a href="/web/protocol.html">进入</a></h3>
                </template>
            </a-card-meta>
        </a-card>
    </a-col>
</a-row>
<a-row justify="center">
    <a-col :span="8">
        <a-card hoverable style="">
            <a-card-meta>
                <template #avatar>
                    <i class="fa-brands fa-laravel fa-3x"></i>
                </template>
                <template #title>
                    后台目录
                </template>
                <template #description>
                    <h3>git@192.168.30.155:~/moco/admin</h3>
                </template>
            </a-card-meta>
        </a-card>
    </a-col>
    <a-col :span="8">
        <a-card hoverable style="">
            <a-card-meta>
                <template #avatar>
                    <i class="fa fa-code-branch fa-3x" aria-hidden="true"></i>
                </template>
                <template #title>
                    服务器目录
                </template>
                <template #description>
                    <h3>git@192.168.30.155:~/moco/server</h3>
                </template>
            </a-card-meta>
        </a-card>
    </a-col>
    <a-col :span="8">
        <a-card hoverable style="">
            <a-card-meta>
                <template #avatar>
                    <i class="fa-brands fa-unity fa-3x"></i>
                </template>
                <template #title>
                    客户端目录
                </template>
                <template #description>
                    <h3>svn://192.168.30.155/moco/client</h3>
                </template>
            </a-card-meta>
        </a-card>
    </a-col>
</a-row>
<a-row justify="center">

    <a-col :span="8">
        <a-card hoverable style="">
            <a-card-meta>
                <template #avatar>
                    <i class="fa fa-code fa-3x" aria-hidden="true"></i>
                </template>
                <template #title>
                    协议目录
                </template>
                <template #description>
                    <h3>svn://192.168.30.155/moco/protocol</h3>
                </template>
            </a-card-meta>
        </a-card>
    </a-col>
    <a-col :span="8">
        <a-card hoverable style="">
            <a-card-meta>
                <template #avatar>
                    <i class="fa-brands fa-js fa-3x"></i>
                </template>
                <template #title>
                    客户端配置文件目录
                </template>
                <template #description>
                    <h3>svn://192.168.30.155/moco/configure</h3>
                </template>
            </a-card-meta>
        </a-card>
    </a-col>
    <a-col :span="8">
        <a-card hoverable style="">
            <a-card-meta>
                <template #avatar>
                    <i class="fa fa-book fa-3x" aria-hidden="true"></i>
                </template>
                <template #title>
                    文档文件目录
                </template>
                <template #description>
                    <h3>svn://192.168.30.155/moco/doc</h3>
                </template>
            </a-card-meta>
        </a-card>
    </a-col>
</a-row>
<a-row justify="center">
    <a-col :span="8">
        <a-card hoverable style="">
            <a-card-meta>
                <template #avatar>
                    <i class="fa-solid fa-file-image fa-3x"></i>
                </template>
                <template #title>
                    美术资源文件目录
                </template>
                <template #description>
                    <h3>svn://192.168.30.155/moco/res<h3>
                </template>
            </a-card-meta>
        </a-card>
    </a-col>
    <a-col :span="8">
        <a-card hoverable style="">
            <a-card-meta>
                <template #avatar>
                    <i class="fa fa-user-large fa-3x" aria-hidden="true"></i>
                </template>
                <template #title>
                    后台地址
                </template>
                <template #description>
                    <h3><a href="/admin">进入</a></h3>
                </template>
            </a-card-meta>
        </a-card>
    </a-col>
    <a-col :span="8">
        <a-card hoverable style="">
            <a-card-meta>
                <template #avatar>
                    <i class="fa-solid fa-paper-plane fa-3x"></i>
                </template>
                <template #title>
                    API文档
                </template>
                <template #description>
                    <h3><a href="/api/documentation#/default">进入</a></h3>
                </template>
            </a-card-meta>
        </a-card>
    </a-col>
</a-row>
                `,
            });
            app.use(antd.Avatar);
            app.use(antd.Card);
            app.use(antd.Col);
            app.use(antd.Row);
            app.use(antd.Form);
            app.use(antd.Input);
            app.use(antd.List);
            app.use(antd.Select);
            app.use(antd.Tabs);
            app.mount('#container');
        </script>
    </body>
</html>
