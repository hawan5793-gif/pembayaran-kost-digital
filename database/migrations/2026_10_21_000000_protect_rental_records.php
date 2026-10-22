<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('payments')
            ->select('tenant_id', 'month_period')
            ->groupBy('tenant_id', 'month_period')
            ->havingRaw('COUNT(*) > 1')
            ->limit(5)
            ->get();

        if ($duplicates->isNotEmpty()) {
            throw new RuntimeException(
                'Migration dibatalkan: ada pembayaran ganda untuk penghuni/periode yang sama. Tinjau tabel payments sebelum menjalankan migrasi lagi.'
            );
        }

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->foreign('room_id')->references('id')->on('rooms')->restrictOnDelete();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['room_id']);
            $table->foreign('tenant_id')->references('id')->on('tenants')->restrictOnDelete();
            $table->foreign('room_id')->references('id')->on('rooms')->restrictOnDelete();
            $table->unique(['tenant_id', 'month_period'], 'payments_tenant_period_unique');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique('payments_tenant_period_unique');
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['room_id']);
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('room_id')->references('id')->on('rooms')->cascadeOnDelete();
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->foreign('room_id')->references('id')->on('rooms')->cascadeOnDelete();
        });
    }
};
