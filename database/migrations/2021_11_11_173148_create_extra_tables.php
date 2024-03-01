<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_channels', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->unsignedInteger('role_id')->default(0)->comment('角色ID');
            $table->string('channel')->default('')->comment('渠道');
            $table->timestamp('created_at')->default('0000-00-00 00:00:00')->comment('创建时间');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00')->comment('更新时间');
            $table->unique(['role_id', 'channel'], 'role_channel');
        });

        Schema::create('server_list', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->string('channel')->default('')->comment('渠道');
            $table->string('channel_name')->default('')->comment('渠道名');
            $table->unsignedInteger('server_id')->default(0)->comment('游戏服ID');
            $table->string('server_node')->default('')->index('server_node')->comment('游戏服节点名');
            $table->string('server_name')->default('')->index('server_name')->comment('游戏服名');
            $table->string('ssh_host')->default('')->comment('SSH地址');
            $table->string('ssh_pass')->default('')->comment('SSH密码');
            $table->string('server_root')->default('')->comment('服务器根目录');
            $table->string('configure_root')->default('')->comment('配置根目录');
            $table->string('protocol_root')->default('')->comment('协议根目录');
            $table->string('server_host')->default('')->comment('游戏服地址');
            $table->unsignedInteger('server_port')->default(0)->comment('游戏服端口');
            $table->string('db_host')->default('')->comment('游戏服数据库地址');
            $table->string('db_port')->default('')->comment('游戏服数据库端口');
            $table->string('db_name')->default('')->comment('游戏服数据库名');
            $table->string('db_username')->default('')->comment('游戏服数据库用户名');
            $table->string('db_password')->default('')->comment('游戏服数据库密码');
            $table->string('server_type')->default('')->comment('服务器类型');
            $table->string('server_cookie')->default('')->comment('服务器令牌');
            $table->unsignedInteger('open_time')->default(0)->comment('开服时间');
            $table->string('tab_name')->default('')->comment('分页名字');
            $table->string('center')->default('')->comment('中央服');
            $table->string('world')->default('')->comment('大世界');
            $table->string('state')->default('')->comment('当前状态');
            $table->string('recommend')->default('')->comment('推荐');
            $table->timestamp('created_at')->default('0000-00-00 00:00:00')->comment('创建时间');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00')->comment('更新时间');
            $table->unique(['channel', 'server_node'], 'channel_server');
        });

        Schema::create('statistics_block_list', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->string('channel')->default('')->comment('渠道');
            $table->string('channel_name')->default('')->comment('渠道名');
            $table->unsignedInteger('server_id')->default(0)->index('server_id')->comment('游戏服ID');
            $table->string('server_name')->default('')->comment('游戏服名');
            $table->unsignedInteger('role_id')->default(0)->index('role_id')->comment('角色ID');
            $table->string('role_name')->default('')->comment('角色名');
            $table->timestamp('created_at')->default('0000-00-00 00:00:00')->comment('创建时间');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00')->comment('更新时间');
            $table->unique(['channel', 'server_id', 'role_id'], 'channel_server_role');
        });

        Schema::create('table_import_log', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->string('user_name')->default('')->comment('用户名');
            $table->string('table_schema')->default('')->comment('数据库');
            $table->string('table_name')->default('')->comment('表名');
            $table->string('table_comment')->default('')->comment('名称');
            $table->dateTime('time')->useCurrent()->comment('时间');
            $table->unsignedTinyInteger('state')->default(0)->comment('状态');
            $table->timestamp('created_at')->default('0000-00-00 00:00:00')->comment('创建时间');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00')->comment('更新时间');
        });

        Schema::create('sensitive_word_data', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->string('word')->default('')->comment('敏感词');
            $table->timestamp('created_at')->default('0000-00-00 00:00:00')->comment('创建时间');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00')->comment('更新时间');
        });

        Schema::create('maintain_notice', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->string('channel')->default('')->comment('渠道');
            $table->string('title', 1000)->default('')->comment('公告标题');
            $table->string('content', 1000)->default('')->comment('公告内容');
            $table->timestamp('start_time')->default('0000-00-00 00:00:00')->comment('开始时间');
            $table->timestamp('end_time')->default('0000-00-00 00:00:00')->comment('结束时间');
            $table->timestamp('created_at')->default('0000-00-00 00:00:00')->comment('创建时间');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00')->comment('更新时间');
        });

        Schema::create('client_error_log', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->unsignedSmallInteger('server_id')->default(0)->comment('服务器ID');
            $table->char('account', 16)->default('')->comment('账号');
            $table->unsignedBigInteger('role_id')->default(0)->index('role_id')->comment('玩家ID');
            $table->char('role_name', 16)->default('')->comment('玩家名');
            $table->string('device')->default('')->comment('设备');
            $table->string('env')->default('')->comment('环境');
            $table->string('title')->default('')->comment('标题');
            $table->string('content')->default('')->comment('内容');
            $table->string('content_kernel')->default('')->comment('内核内容');
            $table->string('ip', 16)->default('')->comment('IP地址');
            $table->dateTime('time')->default('0000-00-00 00:00:00')->comment('时间');
            $table->timestamp('created_at')->default('0000-00-00 00:00:00')->comment('创建时间');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00')->comment('更新时间');
        });

        Schema::create('impeach', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->unsignedSmallInteger('server_id')->default(0)->comment('举报方玩家服号');
            $table->unsignedBigInteger('role_id')->default(0)->comment('举报方玩家ID');
            $table->char('role_name', 16)->default('')->comment('举报方玩家名字');
            $table->unsignedSmallInteger('impeacher_server_id')->default(0)->comment('被举报玩家服号');
            $table->unsignedBigInteger('impeacher_role_id')->default(0)->comment('被举报玩家ID');
            $table->char('impeacher_role_name', 16)->default('')->comment('被举报玩家名字');
            $table->unsignedTinyInteger('type')->default(0)->comment('举报类型(1:言语辱骂他人/2:盗取他人账号/3:非正规充值交易/4:其他)');
            $table->string('content')->default('')->comment('举报内容');
            $table->string('ip')->default('')->comment('IP地址');
            $table->dateTime('time')->default('0000-00-00 00:00:00')->comment('时间');
            $table->index(['impeacher_role_id', 'impeacher_server_id'], 'impeach_role_server');
            $table->index(['role_id', 'server_id'], 'role_server');
            $table->timestamp('created_at')->default('0000-00-00 00:00:00')->comment('创建时间');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00')->comment('更新时间');
        });

        Schema::create('ssh_key', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->string('username')->default('')->comment('用户名');
            $table->string('type')->default('')->comment('类型');
            $table->string('passphrase')->default('')->comment('密码');
            $table->string('name')->default('')->comment('名字');
            $table->string('key', 4096)->default('')->comment('私钥');
            $table->string('pub_key', 4096)->default('')->comment('公钥');
            $table->timestamp('created_at')->default('0000-00-00 00:00:00')->comment('创建时间');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00')->comment('更新时间');
        });

        Schema::create('server_role_number', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->string('server_name')->default('')->comment('服务器名');
            $table->unsignedSmallInteger('server_id')->index('server_id')->default(0)->comment('服务器ID');
            $table->string('server_host')->default('')->comment('服务器地址');
            $table->unsignedSmallInteger('server_port')->default(0)->comment('服务器端口');
            $table->unsignedInteger('role_number')->index('role_number')->default(0)->comment('数量');
            $table->engine = 'MEMORY';
        });

        Schema::create('navigation', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('parent_id')->default(0)->comment('父ID');
            $table->integer('order')->default(0)->comment('顺序');
            $table->string('icon')->default('')->comment('图标');
            $table->string('color')->default('')->comment('颜色');
            $table->string('title')->default('')->comment('标题');
            $table->string('content')->default('')->comment('内容');
            $table->string('url')->default('')->comment('URL地址');
            $table->timestamp('created_at')->default('0000-00-00 00:00:00')->comment('创建时间');
            $table->timestamp('updated_at')->default('0000-00-00 00:00:00')->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('server_list');
        Schema::dropIfExists('table_import_log');
        Schema::dropIfExists('sensitive_word_data');
        Schema::dropIfExists('maintain_notice');
        Schema::dropIfExists('client_error_log');
        Schema::dropIfExists('impeach');
        Schema::dropIfExists('ssh_key');
        Schema::dropIfExists('server_role_number');
    }
}
