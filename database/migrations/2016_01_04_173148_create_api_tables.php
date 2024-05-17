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
        return config('api.database.connection') ?: config('database.default');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('api.database.server_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->smallInteger('channel_id')->default('')->comment('渠道id');
            $table->string('channel_name')->default('')->comment('渠道名');
            $table->smallInteger('server_id')->unsigned()->default(0)->comment('服务器ID');
            $table->string('server_name')->default('')->comment('服务器名');
            $table->string('server_protocol')->default('')->comment('服务器协议');
            $table->string('server_host')->default('')->comment('服务器地址');
            $table->smallInteger('server_port')->unsigned()->default(0)->comment('服务器端口');
            $table->integer('number')->unsigned()->default(0)->comment('数量');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('channel_id');
            $table->index('channel_name');
            $table->index('server_id');
            $table->index('server_name');
            $table->index('number');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
            $table->unique(['channel_id', 'server_id']);
            $table->engine = 'MEMORY';
        });

        Schema::create(config('api.database.maintain_notice_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->string('channel')->default('')->comment('渠道');
            $table->string('title', 1000)->default('')->comment('公告标题');
            $table->string('content', 1000)->default('')->comment('公告内容');
            $table->integer('start_time')->unsigned()->default(0)->comment('开始时间');
            $table->integer('end_time')->unsigned()->default(0)->comment('结束时间');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('api.database.feedback_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->smallInteger('server_id')->unsigned()->default(0)->comment('玩家服号');
            $table->bigInteger('role_id')->unsigned()->default(0)->comment('玩家ID');
            $table->char('role_name', 16)->default('')->comment('玩家名字');
            $table->string('content')->default('')->comment('内容');
            $table->string('ip')->default('')->comment('IP地址');
            $table->index(['role_id', 'server_id'], 'role_server');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('server_id');
            $table->index('role_id');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('api.database.impeach_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->smallInteger('server_id')->unsigned()->default(0)->comment('举报方玩家服号');
            $table->bigInteger('role_id')->unsigned()->default(0)->comment('举报方玩家ID');
            $table->char('role_name', 16)->default('')->comment('举报方玩家名字');
            $table->smallInteger('impeacher_server_id')->unsigned()->default(0)->comment('被举报玩家服号');
            $table->bigInteger('impeacher_role_id')->unsigned()->default(0)->comment('被举报玩家ID');
            $table->char('impeacher_role_name', 16)->default('')->comment('被举报玩家名字');
            $table->tinyInteger('type')->unsigned()->default(0)->comment('举报类型(1:言语辱骂他人/2:盗取他人账号/3:非正规充值交易/4:其他)');
            $table->string('content')->default('')->comment('举报内容');
            $table->string('ip')->default('')->comment('IP地址');
            $table->index(['impeacher_role_id', 'impeacher_server_id'], 'impeach_role_server');
            $table->index(['role_id', 'server_id'], 'role_server');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('server_id');
            $table->index('role_id');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('api.database.client_error_log_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->smallInteger('server_id')->unsigned()->default(0)->comment('服务器ID');
            $table->char('account', 16)->default('')->comment('账号');
            $table->bigInteger('role_id')->unsigned()->default(0)->comment('玩家ID');
            $table->char('role_name', 16)->default('')->comment('玩家名');
            $table->string('device')->default('')->comment('设备');
            $table->string('env', 1024)->default('')->comment('环境');
            $table->string('title')->default('')->comment('标题');
            $table->string('content', 4096)->default('')->comment('内容');
            $table->string('content_kernel', 4096)->default('')->comment('内核内容');
            $table->string('ip')->default('')->comment('IP地址');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('server_id');
            $table->index('role_id');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('api.database.sensitive_word_data_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->string('word')->default('')->comment('敏感词');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('api.database.log_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->integer('user_id')->unsigned()->default(0)->comment('');
            $table->string('path')->default('')->comment('');
            $table->string('method')->default('')->comment('');
            $table->string('ip')->default('')->comment('');
            $table->text('input')->comment('');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('user_id');
            $table->index('path');
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
        Schema::dropIfExists(config('api.database.server_table'));
        Schema::dropIfExists(config('api.database.maintain_notice_table'));
        Schema::dropIfExists(config('api.database.impeach_table'));
        Schema::dropIfExists(config('api.database.client_error_log_table'));
        Schema::dropIfExists(config('api.database.sensitive_word_data_table'));
        Schema::dropIfExists(config('api.database.log_table'));
    }
};
