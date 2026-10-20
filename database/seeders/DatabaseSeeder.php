<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $rooms = [];
        for ($i = 1; $i <= 10; $i++) {
            $rooms[$i] = \App\Models\Room::create([
                'number' => 'Kamar ' . $i,
                'price' => 650000,
                'status' => 'terisi',
            ]);
        }

        // 10 Penghuni
        $tenantsData = [
            ['name' => 'Sari', 'room' => 1, 'status' => 'lunas', 'unpaid' => 0, 'due' => '2026-10-05', 'method' => 'Transfer', 'pay_date' => '2026-10-17', 'pay_label' => 'Kemarin'],
            ['name' => 'Bayu', 'room' => 2, 'status' => 'belum_bayar', 'unpaid' => 1, 'due' => '2026-10-05'],
            ['name' => 'Juna', 'room' => 3, 'status' => 'lunas', 'unpaid' => 0, 'due' => '2026-10-05', 'method' => 'Tunai', 'pay_date' => '2026-10-18', 'pay_label' => 'Hari ini'],
            ['name' => 'Raka', 'room' => 4, 'status' => 'lunas', 'unpaid' => 0, 'due' => '2026-10-05', 'method' => 'Transfer', 'pay_date' => '2026-10-13', 'pay_label' => '13 Okt'],
            ['name' => 'Dimas', 'room' => 5, 'status' => 'belum_bayar', 'unpaid' => 2, 'due' => '2026-10-05'],
            ['name' => 'Nadia', 'room' => 6, 'status' => 'lunas', 'unpaid' => 0, 'due' => '2026-10-05', 'method' => 'Tunai', 'pay_date' => '2026-10-11', 'pay_label' => '11 Okt'],
            ['name' => 'Fajar', 'room' => 7, 'status' => 'lunas', 'unpaid' => 0, 'due' => '2026-10-05', 'method' => 'Transfer', 'pay_date' => '2026-10-09', 'pay_label' => '9 Okt'],
            ['name' => 'Putri', 'room' => 8, 'status' => 'belum_bayar', 'unpaid' => 3, 'due' => '2026-10-05'],
            ['name' => 'Ayu', 'room' => 9, 'status' => 'lunas', 'unpaid' => 0, 'due' => '2026-10-05', 'method' => 'Transfer', 'pay_date' => '2026-10-07', 'pay_label' => '7 Okt'],
            ['name' => 'Rian', 'room' => 10, 'status' => 'lunas', 'unpaid' => 0, 'due' => '2026-10-05', 'method' => 'Tunai', 'pay_date' => '2026-10-05', 'pay_label' => '5 Okt'],
        ];

        foreach ($tenantsData as $t) {
            $tenant = \App\Models\Tenant::create([
                'room_id' => $rooms[$t['room']]->id,
                'name' => $t['name'],
                'phone' => '08' . rand(1111111111, 9999999999),
                'entry_date' => '2026-01-01',
                'due_date' => $t['due'],
                'unpaid_months' => $t['unpaid'],
                'status' => $t['status'],
            ]);

            if ($t['status'] === 'lunas' && isset($t['method'])) {
                \App\Models\Payment::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $rooms[$t['room']]->id,
                    'amount' => 650000,
                    'month_period' => 'Okt 2026',
                    'payment_date' => $t['pay_date'],
                    'method' => $t['method'],
                    'status' => 'Lunas',
                    'note' => $t['pay_label'],
                ]);
            }
        }
    }
}
