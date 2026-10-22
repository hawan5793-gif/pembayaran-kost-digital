<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateOwner extends Command
{
    protected $signature = 'app:create-owner';

    protected $description = 'Create the Yani Kost owner login';

    public function handle(): int
    {
        $name = trim((string) $this->ask('Nama pemilik'));
        $email = Str::lower(trim((string) $this->ask('Email login')));

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Nama dan alamat email yang valid wajib diisi.');

            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('Akun dengan email tersebut sudah ada.');

            return self::FAILURE;
        }

        $password = (string) $this->secret('Kata sandi (minimal 12 karakter)');
        $confirmation = (string) $this->secret('Ulangi kata sandi');

        if (strlen($password) < 12 || ! hash_equals($password, $confirmation)) {
            $this->error('Kata sandi harus minimal 12 karakter dan kedua isian harus sama.');

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info("Akun pemilik {$email} berhasil dibuat.");

        return self::SUCCESS;
    }
}
