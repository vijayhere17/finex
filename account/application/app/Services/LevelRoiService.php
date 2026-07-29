<?php

namespace App\Services;

use App\Models\DailyRoiLog;
use App\Models\LevelRoiLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Level Income on ROI — paid from downline Daily ROI only.
 * Unlock: N qualified active directs unlocks Level N (max 12).
 * Ladder: L1=12% … L12=1% of the Daily ROI amount.
 */
class LevelRoiService
{
    public function distributeFromDailyRoi(DailyRoiLog $dailyLog): void
    {
        $from = User::find($dailyLog->member_id);
        if ($from == null || $from->referral_id <= 0) {
            return;
        }

        $baseAmount = (float) $dailyLog->amount;
        if ($baseAmount <= 0) {
            return;
        }

        $ladder = config('income.level_roi.ladder', []);
        $maxDepth = (int) config('income.level_roi.max_depth', 12);
        $earningType = (int) config('income.level_roi.earning_type', 4);
        $roiDate = $dailyLog->roi_date;
        $walletCon = app('App\Http\Controllers\Users\EarningWalletController');

        $uplineId = (int) $from->referral_id;
        $level = 1;

        while ($uplineId > 0 && $level <= $maxDepth) {
            $upline = User::find($uplineId);
            if ($upline == null) {
                break;
            }

            $percent = (float) ($ladder[$level] ?? 0);
            $qualified = (int) ($upline->qualified_active_directs ?? 0);

            // Unlock rule: N directs unlock Level N
            if ($percent > 0
                && ($upline->activation_status ?? '') === DirectRoiService::STATUS_ACTIVE
                && $qualified >= $level
            ) {
                $commission = ($baseAmount * $percent) / 100.0;

                if ($commission > 0) {
                    // Duplicate prevention
                    $exists = LevelRoiLog::where('member_id', $upline->id)
                        ->where('from_id', $from->id)
                        ->where('level', $level)
                        ->where('roi_date', $roiDate)
                        ->where('daily_roi_log_id', $dailyLog->id)
                        ->exists();

                    if (!$exists) {
                        LevelRoiLog::create([
                            'member_id' => $upline->id,
                            'from_id' => $from->id,
                            'daily_roi_log_id' => $dailyLog->id,
                            'level' => $level,
                            'percent' => $percent,
                            'base_amount' => $baseAmount,
                            'amount' => $commission,
                            'roi_date' => $roiDate,
                        ]);

                        $desc = 'Level '.$level.' ROI Income ('.$percent.'%) from '.obscureAddress($from->username);
                        $walletCon->addearningwalletlog(
                            $upline->id,
                            1,
                            $earningType,
                            $desc,
                            $commission,
                            0,
                            0,
                            $roiDate.' '.date('H:i:s')
                        );
                    }
                }
            }

            $uplineId = (int) $upline->referral_id;
            $level++;
        }
    }
}
