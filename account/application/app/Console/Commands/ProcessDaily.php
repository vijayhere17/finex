<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
use Carbon\Carbon;

class ProcessDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'act:processdaily';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Actions which will be executed daily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       	$stakeCon = app('App\Http\Controllers\Users\StakeController');
       	$rewardCon = app('App\Http\Controllers\Users\TurnoverRewardController');

       	// Log::info('process referral start...');
       	// $stakeCon->runReferralEarning();
		// Log::info('process referral end...');

		// ROI accrues Monday-Friday only - date('N') is 6=Saturday, 7=Sunday.
		if(!in_array(date('N'), [6, 7]))
		{
			Log::info('process daily roi start...');
			$stakeCon->runDailyROI();
			Log::info('process daily roi end...');
		}

		// Reward Qualification (daily)
		Log::info('process reward qualification start...');
		$rewardCon->runTurnoverAchiever();
		Log::info('process reward qualification end...');

		// Weekly Reward Salary — due rows only (return_date <= today), highest reward only
		Log::info('process reward salary start...');
		$rewardCon->runRewardSalary();
		Log::info('process reward salary end...');

		// Locked Reward Expiry (30-day validity)
		Log::info('process locked reward expiry start...');
		$stakeCon->runLockedRewardExpiry();
		Log::info('process locked reward expiry end...');

		//Log::info('process booster evaluation start...');
		//$stakeCon->runBoosterEvaluation();
		//Log::info('process booster evaluation end...');

		// Legacy rank-based Salary income is replaced by Reward Salary (config/income.php).
		// Kept here behind a flag rather than deleted, per business decision - flip legacy_salary_enabled to re-enable.
		/* if(config('income.legacy_salary_enabled', false))
		{
			$salaryCon = app('App\Http\Controllers\Users\SalaryController');

			Log::info('process salary achiever start...');
			$salaryCon->runSalaryAchiever();
			Log::info('process salary achiever end...');

			if(date("D") == 'Mon')
			{
				Log::info('process salary earning start...');
				$salaryCon->runSalaryEarning();
				Log::info('process salary earning end...');
			}
		} */
    }
}
