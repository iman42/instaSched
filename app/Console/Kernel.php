<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use \App\Account;

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
        $schedule->call(function(){
            $tasks = DB::table('tasks')->where('timestamp', '<=', time())->get();
            foreach($tasks as $task){
                if($task->error){
                }
                else{
                    $account = Account::where('id', '=', $task->account)->first();
                    $error = $account->uploadFile($task->filepath, $task->caption, true);
                    if($error){
                        DB::table('tasks')->where('id', '=', $task->id)->update([
                            'error' => $error->getMessage(),
                            // 'error' => $error,
                        ]);
                    }
                    else{
                        DB::table('tasks')->where('id', '=', $task->id)->delete();
                    }
                }
            }
        })->name('the_sched_part')->everyMinute()->withoutOverlapping();
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
