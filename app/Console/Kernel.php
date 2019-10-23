<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /*
         * Note: The only thing we should be putting on a schedule here is QUEUED JOBS or QUEUED CONSOLE COMMANDS.
         * This schedule is triggered with the `php artisan schedule:run` command from `MotoMenus-SaaS-API-Cron`.
         * The stack defined in `MotoMenus-SaaS-API-Cron` is not suitable for running anything significant, has the bare-minimum as far as PHP extensions, and is not designed to do any heavy lifting.
         * The `MotoMenus-SaaS-API-Queue` service is well-equipped with enough memory and all the required PHP extensions and such to run any scheduled processes/tasks/jobs we need.
         * Since `MotoMenus-SaaS-API-Cron` is the one that ultimately calls this schedule, anything put here should be a queued job that will run in the `MotoMenus-SaaS-API-Queue` service.
         */

        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
