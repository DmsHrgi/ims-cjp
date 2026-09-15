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
        } else {
            Schema::table('m_olt', function (Blueprint $table) {
                if (!Schema::hasColumn('m_olt', 'name')) {
                    $table->string('name', 100)->default('');
                }
                if (!Schema::hasColumn('m_olt', 'hostname')) {
                    $table->string('hostname', 100)->nullable();
                }
                if (!Schema::hasColumn('m_olt', 'ip_address')) {
                    $table->string('ip_address', 50)->default('');
                }
                if (!Schema::hasColumn('m_olt', 'vendor')) {
                    $table->string('vendor', 100)->default('');
                }
                if (!Schema::hasColumn('m_olt', 'model')) {
                    $table->string('model', 100)->nullable();
                }
                if (!Schema::hasColumn('m_olt', 'status')) {
                    $table->string('status', 20)->default('Up');
                }
                if (!Schema::hasColumn('m_olt', 'snmp_port')) {
                    $table->integer('snmp_port')->default(161);
                }
                if (!Schema::hasColumn('m_olt', 'snmp_version')) {
                    $table->string('snmp_version', 20)->default('v2c');
                }
                if (!Schema::hasColumn('m_olt', 'snmp_community')) {
                    $table->string('snmp_community', 100)->default('public');
                }
                if (!Schema::hasColumn('m_olt', 'location')) {
                    $table->string('location', 255)->nullable();
                }
                if (!Schema::hasColumn('m_olt', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('m_olt', 'user_create')) {
                    $table->string('user_create', 50)->nullable();
                }
                if (!Schema::hasColumn('m_olt', 'user_update')) {
                    $table->string('user_update', 50)->nullable();
                }
                if (!Schema::hasColumn('m_olt', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('m_olt', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
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
