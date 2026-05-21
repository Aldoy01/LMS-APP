<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->string('category')->default('Basic')->after('summary');
            $table->unsignedInteger('duration_minutes')->default(0)->after('category');
        });
    }

    public function down()
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->dropColumn(['category', 'duration_minutes']);
        });
    }
};
