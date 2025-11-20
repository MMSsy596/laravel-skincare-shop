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
        if (Schema::hasTable('chat_messages')) {
            // Check if columns exist, if not add them
            if (!Schema::hasColumn('chat_messages', 'session_id')) {
                Schema::table('chat_messages', function (Blueprint $table) {
                    $table->string('session_id')->nullable()->after('user_id');
                    $table->string('mode', 20)->default('standard')->after('message');
                });
            }
            return;
        }
        
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Nullable for guest users
            $table->string('session_id')->nullable(); // For guest users to track sessions
            $table->enum('type', ['user', 'ai']); // Message type
            $table->text('message'); // Message content
            $table->string('mode', 20)->default('standard'); // Chat mode: standard or gemini
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['session_id', 'created_at']);
        });
        
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
};
