<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('m_olt')) {
            Schema::create('m_olt', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('hostname', 100)->nullable();
                $table->string('ip_address', 50);
                $table->string('vendor', 100);
                $table->string('model', 100)->nullable();
                $table->string('status', 20)->default('Up');
                $table->integer('snmp_port')->default(161);
                $table->string('snmp_version', 20)->default('v2c');
                $table->string('snmp_community', 100)->default('public');
                $table->string('location', 255)->nullable();
                $table->text('description')->nullable();
                $table->string('user_create', 50)->nullable();
                $table->string('user_update', 50)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_olt');
    }
};
