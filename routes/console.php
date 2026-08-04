<?php

use App\Console\Commands\BackupDatabase;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:backup')->dailyAt('03:00');
