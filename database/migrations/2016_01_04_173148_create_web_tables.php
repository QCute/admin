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
        return config('web.database.connection') ?: config('database.default');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('web.database.navigation_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->integer('parent_id')->unsigned()->default(0)->comment('父id');
            $table->integer('order')->unsigned()->default(0)->comment('排序');
            $table->string('icon')->default('')->comment('图标');
            $table->string('color')->default('')->comment('颜色');
            $table->string('title')->default('')->comment('标题');
            $table->string('content')->default('内容');
            $table->string('url')->default('')->comment('地址');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('deleted_time')->unsigned()->default(0)->comment('删除时间');
            $table->index('parent_id');
            $table->index('created_time');
            $table->index('updated_time');
            $table->index('deleted_time');
        });

        Schema::create(config('web.database.log_table'), function (Blueprint $table) {
            $table->integer('id')->unsigned()->comment('')->autoIncrement();
            $table->integer('user_id')->unsigned()->default(0)->comment('用户id');
            $table->string('path')->default('')->comment('路径');
            $table->string('method')->default('')->comment('方法');
            $table->string('ip')->default('')->comment('IP');
            $table->text('input')->comment('输入');
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
        Schema::dropIfExists(config('web.database.navigation_table'));
        Schema::dropIfExists(config('web.database.log_table'));
    }
};
