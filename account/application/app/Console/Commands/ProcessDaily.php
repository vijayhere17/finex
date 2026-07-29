<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\DailyRoiService;

class ProcessDaily extends Command
{
    protected $signature = 'act:processdaily {--date= : Optional ROI date Y-m-d}';

    protected $description = 'Finex daily process: Daily ROI + Level ROI distribution';

    public function handle()
    {
        $date = $this->option('date') ?: null;

        Log::info('Finex Daily ROI start...');
        $this->info('Running Daily ROI distribution...');

        $summary = app(DailyRoiService::class)->distribute($date);

        Log::info('Finex Daily ROI end', $summary);
        $this->info('Paid: '.$summary['paid'].' | Skipped: '.$summary['skipped'].' | Closed: '.$summary['closed'].' | Date: '.$summary['roiDate']);

        // Legacy reward / locked-reward / salary crons disabled for new compensation plan.
        if (config('income.legacy_reward_cron_enabled', false)) {
            $rewardCon = app('App\Http\Controllers\Users\TurnoverRewardController');
            $rewardCon->runTurnoverAchiever();
            $rewardCon->runRewardSalary();
        }

        if (config('income.legacy_locked_reward_enabled', false)) {
            app('App\Http\Controllers\Users\StakeController')->runLockedRewardExpiry();
        }

        return Command::SUCCESS;
    }
}
