<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Get the migration connection name.
     *
     * @return string|null
     */
    public function getConnection()
    {
        return config('admin.database.connection') ?: config('database.default');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('admin.database.user_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->string('username')->default('')->comment('');
            $table->string('password')->default('')->comment('');
            $table->string('name')->default('')->comment('');
            $table->string('avatar')->default('')->comment('');
            $table->string('remember_token')->default('')->comment('');
            $table->integer('created_time')->unsigned()->default(0)->comment('');
            $table->integer('updated_time')->unsigned()->default(0)->comment('');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('');
            $table->unique('username');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.user_role_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->integer('user_id')->unsigned()->default(0)->comment('');
            $table->integer('role_id')->unsigned()->default(0)->comment('');
            $table->integer('created_time')->unsigned()->default(0)->comment('');
            $table->integer('updated_time')->unsigned()->default(0)->comment('');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('');
            $table->unique(['user_id', 'role_id']);
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.role_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->string('name')->default('')->comment('');
            $table->string('tag')->default('')->comment('');
            $table->integer('created_time')->unsigned()->default(0)->comment('');
            $table->integer('updated_time')->unsigned()->default(0)->comment('');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('');
            $table->unique('name');
            $table->unique('tag');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.role_permission_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->integer('role_id')->unsigned()->default(0)->comment('');
            $table->integer('permission_id')->unsigned()->default(0)->comment('');
            $table->integer('created_time')->unsigned()->default(0)->comment('');
            $table->integer('updated_time')->unsigned()->default(0)->comment('');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('');
            $table->unique(['role_id', 'permission_id']);
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.permission_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->string('name')->default('')->comment('');
            $table->string('tag')->default('')->comment('');
            $table->string('http_method')->default('')->comment('');
            $table->string('http_path')->default('')->comment('');
            $table->integer('created_time')->unsigned()->default(0)->comment('');
            $table->integer('updated_time')->unsigned()->default(0)->comment('');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('');
            $table->unique('name');
            $table->unique('tag');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.menu_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->integer('parent_id')->unsigned()->default(0);
            $table->integer('type')->unsigned()->default(0);
            $table->integer('order')->unsigned()->default(0);
            $table->string('title')->default('')->comment('');
            $table->string('icon')->default('')->comment('');
            $table->string('url')->default('')->comment('');
            $table->integer('created_time')->unsigned()->default(0)->comment('');
            $table->integer('updated_time')->unsigned()->default(0)->comment('');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.operation_log_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->integer('user_id')->unsigned()->default(0)->comment('');
            $table->string('path')->default('')->comment('');
            $table->string('method')->default('')->comment('');
            $table->string('ip')->default('')->comment('');
            $table->text('input')->comment('');
            $table->integer('created_time')->unsigned()->default(0)->comment('');
            $table->integer('updated_time')->unsigned()->default(0)->comment('');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('');
            $table->index('user_id');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.channel_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->string('name')->default('')->comment('名字');
            $table->string('company')->default('')->comment('公司');
            $table->string('tag')->default('')->comment('标签');
            $table->string('permission')->default('')->comment('权限');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.role_channel_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->integer('role_id')->unsigned()->default(0)->comment('角色ID');
            $table->integer('channel_id')->unsigned()->default(0)->comment('渠道ID');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->unique(['role_id', 'channel_id']);
            $table->index('role_id');
            $table->index('channel_id');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.channel_server_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->integer('channel_id')->unsigned()->default(0)->comment('渠道ID');
            $table->integer('server_id')->unsigned()->default(0)->comment('服务器ID');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->unique(['channel_id', 'server_id']);
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.server_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->string('name')->default('')->comment('游戏服名');
            $table->string('tag')->default('')->comment('标签');
            $table->string('node')->default('')->comment('节点');
            $table->string('ssh_host')->default('')->comment('SSH地址');
            $table->string('ssh_pass')->default('')->comment('SSH密码');
            $table->string('server_root')->default('')->comment('服务器根目录');
            $table->string('configure_root')->default('')->comment('配置根目录');
            $table->string('protocol_root')->default('')->comment('协议根目录');
            $table->integer('server_id')->unsigned()->default(0)->comment('游戏服id');
            $table->string('server_protocol')->default('')->comment('服务器协议');
            $table->string('server_host')->default('')->comment('游戏服地址');
            $table->smallInteger('server_port')->unsigned()->default(0)->comment('游戏服端口');
            $table->string('db_host')->default('')->comment('游戏服数据库地址');
            $table->string('db_port')->default('')->comment('游戏服数据库端口');
            $table->string('db_name')->default('')->comment('游戏服数据库名');
            $table->string('db_username')->default('')->comment('游戏服数据库用户名');
            $table->string('db_password')->default('')->comment('游戏服数据库密码');
            $table->string('type')->default('')->comment('服务器类型');
            $table->string('cookie')->default('')->comment('服务器令牌');
            $table->integer('open_time')->unsigned()->default(0)->comment('开服时间');
            $table->string('center')->default('')->comment('中央服');
            $table->string('world')->default('')->comment('大世界');
            $table->string('icon')->default('')->comment('图标');
            $table->string('description')->default('')->comment('描述');
            $table->string('tab')->default('')->comment('分页');
            $table->string('recommend')->default('')->comment('推荐');
            $table->string('state')->default('')->comment('当前状态');
            $table->string('permission')->default('')->comment('权限');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.role_server_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->integer('role_id')->unsigned()->default(0)->comment('角色ID');
            $table->integer('server_id')->unsigned()->default(0)->comment('服务器ID');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->unique(['role_id', 'server_id']);
            $table->index('role_id');
            $table->index('server_id');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.configure_file_table'), function (Blueprint $table) {
            $table->integer('id')->comment('')->autoIncrement();
            $table->string('file')->default('')->comment('文件');
            $table->string('comment')->default('')->comment('注释');
            $table->string('tables')->default('')->comment('表');
            $table->string('operator')->default('')->comment('操作人');
            $table->string('name')->default('')->comment('操作人名');
            $table->string('state')->default('')->comment('状态');
            $table->string('cvs')->default('')->comment('CVS');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->unique('file');
            $table->index('comment');
            $table->index('operator');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.configure_table_table'), function (Blueprint $table) {
            $table->integer('id')->comment('')->autoIncrement();
            $table->string('table_schema')->default('')->comment('数据库');
            $table->string('table_name')->default('')->comment('表');
            $table->string('table_comment')->default('')->comment('注释');
            $table->string('operator')->default('')->comment('操作人');
            $table->string('name')->default('')->comment('操作人名');
            $table->string('state')->default('')->comment('状态');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->unique(['table_schema', 'table_name']);
            $table->index('table_schema');
            $table->index('table_name');
            $table->index('table_comment');
            $table->index('operator');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.import_log_table'), function (Blueprint $table) {
            $table->integer('id')->comment('')->autoIncrement();
            $table->string('table_schema')->default('')->comment('数据库');
            $table->string('table_name')->default('')->comment('表');
            $table->string('table_comment')->default('')->comment('注释');
            $table->string('file')->default('')->comment('文件');
            $table->string('comment')->default('')->comment('注释');
            $table->string('operator')->default('')->comment('操作人');
            $table->string('name')->default('')->comment('操作人名');
            $table->string('state')->default('')->comment('状态');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('table_schema');
            $table->index('table_name');
            $table->index('table_comment');
            $table->index('operator');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.user_manage_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->integer('user_id')->unsigned()->default(0)->comment('用户id');
            $table->json('server')->default('')->comment('服务器列表');
            $table->json('roles')->default('')->comment('角色列表');
            $table->string('operation')->default('')->comment('操作');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('user_id');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.user_chat_manage_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->integer('user_id')->unsigned()->default(0)->comment('用户id');
            $table->json('server')->default('')->comment('服务器');
            $table->json('roles')->default('')->comment('角色列表');
            $table->json('operations')->default('')->comment('操作列表');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('user_id');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.mail_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->integer('user_id')->unsigned()->default(0)->comment('用户id');
            $table->string('server')->default('')->comment('服务器');
            $table->json('roles')->default('')->comment('角色列表');
            $table->string('title')->default('')->comment('标题');
            $table->string('content')->default('')->comment('内容');
            $table->string('item')->default('')->comment('物品');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('user_id');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.notice_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->integer('user_id')->unsigned()->default(0)->comment('用户id');
            $table->string('server')->default('')->comment('服务器');
            $table->string('type')->default('')->comment('类型');
            $table->string('title')->default('')->comment('标题');
            $table->string('content')->default('')->comment('内容');
            $table->string('item')->default('')->comment('物品');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('user_id');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('admin.database.ssh_key_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->string('username')->default('')->comment('用户名');
            $table->string('type')->default('')->comment('类型');
            $table->string('passphrase')->default('')->comment('密码');
            $table->string('name')->default('')->comment('名字');
            $table->string('key', 4096)->default('')->comment('私钥');
            $table->string('pub_key', 4096)->default('')->comment('公钥');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('username');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('admin.database.user_table'));
        Schema::dropIfExists(config('admin.database.user_role_table'));
        Schema::dropIfExists(config('admin.database.role_table'));
        Schema::dropIfExists(config('admin.database.role_permission_table'));
        Schema::dropIfExists(config('admin.database.permission_table'));
        Schema::dropIfExists(config('admin.database.menu_table'));
        Schema::dropIfExists(config('admin.database.operation_log_table'));
        Schema::dropIfExists(config('admin.database.channel_table'));
        Schema::dropIfExists(config('admin.database.role_channel_table'));
        Schema::dropIfExists(config('admin.database.server_table'));
        Schema::dropIfExists(config('admin.database.role_server_table'));
        Schema::dropIfExists(config('admin.database.configure_table_table'));
        Schema::dropIfExists(config('admin.database.configure_file_table'));
        Schema::dropIfExists(config('admin.database.import_log_table'));
        Schema::dropIfExists(config('admin.database.ssh_key_table'));
    }
};
