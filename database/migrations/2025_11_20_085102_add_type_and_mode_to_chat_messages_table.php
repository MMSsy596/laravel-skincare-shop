<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_messages', 'session_id')) {
                $table->string('session_id')->nullable()->after('user_id');
            }

            if (!Schema::hasColumn('chat_messages', 'type')) {
                $table->enum('type', ['user', 'ai'])->default('user')->after('session_id');
            }

            if (!Schema::hasColumn('chat_messages', 'message')) {
                $table->text('message')->nullable()->after('type');
            }

            if (!Schema::hasColumn('chat_messages', 'mode')) {
                $table->string('mode', 20)->default('standard')->after('message');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            if (Schema::hasColumn('chat_messages', 'mode')) {
                $table->dropColumn('mode');
            }

            if (Schema::hasColumn('chat_messages', 'message')) {
                $table->dropColumn('message');
            }

            if (Schema::hasColumn('chat_messages', 'type')) {
                $table->dropColumn('type');
            }

            if (Schema::hasColumn('chat_messages', 'session_id')) {
                $table->dropColumn('session_id');
            }
        });
    }
};
