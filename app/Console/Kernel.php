<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\File as SystemFile;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\User;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // This task deletes guest accounts 24 hours after they were created. Run via cron.
        $schedule->call(function () {
            $time = now()->subDay();
            $users = User::where('guest', true)->where('created_at', '<=', $time);

            $users->each(function ($user, $key) {
                SystemFile::deleteDirectory('storage/app/public/media/videos/'.$user->username);
                SystemFile::deleteDirectory('storage/app/public/media/audios/'.$user->username);
                $user->rooms()->delete();
            });

            $users->delete();

        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
