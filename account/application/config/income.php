<?php

// Build 200-level ROI Override ladder from business plan ranges.
$level_income_ladder = [
    1 => 10,
    2 => 5,
    3 => 5,
    4 => 4,
    5 => 4,
    6 => 3,
    7 => 3,
    8 => 2,
    9 => 2,
];

for ($i = 10; $i <= 20; $i++) {
    $level_income_ladder[$i] = 1;
}
for ($i = 21; $i <= 50; $i++) {
    $level_income_ladder[$i] = 0.5;
}
for ($i = 51; $i <= 100; $i++) {
    $level_income_ladder[$i] = 0.25;
}
for ($i = 101; $i <= 200; $i++) {
    $level_income_ladder[$i] = 0.10;
}

return [

    // Registration fee (USD) - recorded at signup; package activation remains unchanged.
    'registration_fee' => 1,

    // Company receiving wallet for Registration Fee + Topup (USDT BEP20 transfers)
    'deposit_wallet' => '0x5a0fc2285a37c1682dc3f351ca59a043b1a41050',

    // USDT (BEP20) on BSC mainnet - 18 decimals
    'usdt_contract' => '0x55d398326f99059fF775485246999027B3197955',

    // Direct Sponsor Income - % of investment amount, level => percent
    'direct_sponsor_levels' => [
        1 => 3,
        2 => 2,
        3 => 1,
    ],

    // Level Income ("ROI Override") - % of each daily ROI payout, up to 200 levels
    'level_income_ladder' => $level_income_ladder,

    // Max depth for ROI Override / Level Income
    'level_income_max_depth' => 200,

    // Active-direct qualification thresholds for ROI Override levels
    // Levels 1-9 require equal active directs (handled in code as level number).
    'level_income_direct_rules' => [
        ['from' => 1,  'to' => 9,   'mode' => 'equal'],
        ['from' => 10, 'to' => 20,  'directs' => 5],
        ['from' => 21, 'to' => 50,  'directs' => 10],
        ['from' => 51, 'to' => 100, 'directs' => 15],
        ['from' => 101,'to' => 200, 'directs' => 20],
    ],

    // Booster Income - directs sponsored within 48hrs of own activation => extra daily ROI percent
    'booster_tiers' => [
        10 => 0.25,
        7 => 0.20,
        5 => 0.15,
        3 => 0.10,
    ],

    'booster_window_hours' => 48,

    // Booster Income (one-time) - sponsor this many directs of the same-or-higher package within
    // booster_window_hours of own activation => 100% of own first topup credited once (earning_type 8)
    'booster_required_directs' => 3,

    // Reward qualification - top 3 sponsoring legs (not binary)
    'reward_leg1_percent' => 40,
    'reward_leg2_percent' => 30,
    'reward_leg3_percent' => 30,

    // Locked Reward Bonus - allocated once on first package activation
    'locked_reward_bonus' => 1000,
    'locked_reward_validity_days' => 30,
    'locked_reward_unlock_percent' => 10,

    // Earning cap multipliers
    'working_cap_multiplier' => 3,
    'non_working_cap_multiplier' => 2,

    // Withdrawal charge tiers (income withdrawals), days elapsed => charge percent
    'withdrawal_charge_tiers' => [
        60 => 0,
        30 => 5,
        0 => 10,
    ],

    // Capital withdrawal
    'capital_withdrawal_charge_percent' => 30,
    'capital_withdrawal_window_months' => 8,

    // Set to true to re-enable the legacy rank-based Salary cron (runSalaryAchiever/runSalaryEarning).
    // Left in place, not deleted, per business decision to replace Salary with Reward Salary income.
    'legacy_salary_enabled' => false,

    // earning_type allocations used across the app (documentation only, not read programmatically)
    // 1  = Direct Sponsor Income
    // 2  = Daily ROI
    // 3  = Cashback
    // 4  = Level Income (ROI Override)
    // 5  = Legacy Salary (dormant) / Reward Salary alias in some menus
    // 6  = DMC Leadership
    // 7  = Reward Salary (weekly)
    // 8  = Booster Income
    // 9  = Life Time Reward
    // 10 = Locked Reward Unlock
];
