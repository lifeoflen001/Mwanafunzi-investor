<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:make-admin {email} {--name=} {--password=}', function (string $email) {
    $name = $this->option('name') ?: $this->ask('Name');
    $password = $this->option('password') ?: $this->secret('Password');
    $user = User::updateOrCreate(['email' => $email], ['name' => $name, 'password' => $password, 'is_admin' => true]);
    $this->info("Admin access enabled for {$user->email}.");
})->purpose('Create or promote a user for the content management area');
