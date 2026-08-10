<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Simula leituras de sensores em ambiente local, para o dashboard/alertas terem
// dados de teste enquanto o hardware físico não chega.
if (app()->environment('local')) {
    Schedule::command('sensores:simular-leitura')->everyFiveMinutes();
}
