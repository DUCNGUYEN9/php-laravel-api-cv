<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('technology');
            $table->string('contact');
            $table->decimal('salary', 10, 2);
            $table->date('expired_date')->nullable();
            $table->foreignId('carreer_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');


            $table->timestamps();
        });
        //add constranint expired_date > now
        // DB::statement("ALTER TABLE posts ADD CONSTRAINT chk_expired_date CHECK (expired_date > NOW())");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
        //delete constranint
        // DB::statement("ALTER TABLE candidates DROP CONSTRAINT chk_expired_date");
    }
};
