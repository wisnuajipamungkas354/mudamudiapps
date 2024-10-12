<?php

use App\Models\Mudamudi;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $dataMudaMudi = Mudamudi::all();
    foreach ($dataMudaMudi as $mm) {
        // if (Carbon::parse($mm->tgl_lahir)->isBirthday()) {
            Mudamudi::query()->where('id', $mm->id)->update([
                'usia' => Carbon::parse($mm->tgl_lahir)->age
            ]);
        // }
    }
    echo('Schedule Umur Berjalan!');
})->everyMinute();