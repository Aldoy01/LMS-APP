<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lesson_materials', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('downloadable');
        });
    }

    public function down()
    {
        Schema::table('lesson_materials', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
