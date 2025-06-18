<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MaintainerSchedulerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:maintainer-scheduler-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoff = Carbon::now()->subDays(30);
        $deletedCount = Task::onlyTrashed()->where('deleted_at', '>=', $cutoff)->forceDelete();

        Log::info("Deleted $deletedCount tasks older than 30 days");
    }
}
