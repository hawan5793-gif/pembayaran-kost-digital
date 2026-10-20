<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable()->after('room_id');
        });

        DB::table('tenants')
            ->join('rooms', 'tenants.room_id', '=', 'rooms.id')
            ->update(['tenants.price' => DB::raw('rooms.price')]);
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
