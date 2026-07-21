<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Extend existing MLM schema for ROI Override (200), Reward Salary,
     * Locked Reward Bonus, and registration fee — no duplicate modules/tables.
     */
    public function up(): void
    {
        // ---- users: registration fee + locked reward bonus columns ----
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'registration_fee')) {
                $table->decimal('registration_fee', 18, 2)->default(1);
            }
            if (!Schema::hasColumn('users', 'reward_id')) {
                $table->unsignedBigInteger('reward_id')->default(0);
            }
            if (!Schema::hasColumn('users', 'locked_reward_bonus')) {
                $table->decimal('locked_reward_bonus', 18, 2)->default(0);
            }
            if (!Schema::hasColumn('users', 'unlocked_reward_bonus')) {
                $table->decimal('unlocked_reward_bonus', 18, 2)->default(0);
            }
            if (!Schema::hasColumn('users', 'expired_reward_bonus')) {
                $table->decimal('expired_reward_bonus', 18, 2)->default(0);
            }
            if (!Schema::hasColumn('users', 'locked_reward_lock_date')) {
                $table->dateTime('locked_reward_lock_date')->nullable();
            }
            if (!Schema::hasColumn('users', 'locked_reward_expiry_date')) {
                $table->dateTime('locked_reward_expiry_date')->nullable();
            }
            if (!Schema::hasColumn('users', 'sponsor_unlock_done')) {
                $table->tinyInteger('sponsor_unlock_done')->default(0);
            }
        });

        // ---- turnover_reward_masters: qualification + weekly salary ----
        Schema::table('turnover_reward_masters', function (Blueprint $table) {
            if (!Schema::hasColumn('turnover_reward_masters', 'title')) {
                $table->string('title', 100)->nullable();
            }
            if (!Schema::hasColumn('turnover_reward_masters', 'required_directs')) {
                $table->unsignedInteger('required_directs')->default(0);
            }
            if (!Schema::hasColumn('turnover_reward_masters', 'required_team')) {
                $table->unsignedInteger('required_team')->default(0);
            }
            if (!Schema::hasColumn('turnover_reward_masters', 'required_self_business')) {
                $table->decimal('required_self_business', 18, 2)->default(0);
            }
            if (!Schema::hasColumn('turnover_reward_masters', 'required_team_business')) {
                $table->decimal('required_team_business', 18, 2)->default(0);
            }
            if (!Schema::hasColumn('turnover_reward_masters', 'weekly_salary')) {
                $table->decimal('weekly_salary', 18, 2)->default(0);
            }
        });

        // ---- turnover_reward_achievers: weekly salary payout tracking ----
        Schema::table('turnover_reward_achievers', function (Blueprint $table) {
            if (!Schema::hasColumn('turnover_reward_achievers', 'weekly_salary')) {
                $table->decimal('weekly_salary', 18, 2)->default(0);
            }
            if (!Schema::hasColumn('turnover_reward_achievers', 'directs_count')) {
                $table->unsignedInteger('directs_count')->default(0);
            }
            if (!Schema::hasColumn('turnover_reward_achievers', 'team_count')) {
                $table->unsignedInteger('team_count')->default(0);
            }
            if (!Schema::hasColumn('turnover_reward_achievers', 'self_business')) {
                $table->decimal('self_business', 18, 2)->default(0);
            }
            if (!Schema::hasColumn('turnover_reward_achievers', 'team_business')) {
                $table->decimal('team_business', 18, 2)->default(0);
            }
            if (!Schema::hasColumn('turnover_reward_achievers', 'return_date')) {
                $table->date('return_date')->nullable();
            }
            if (!Schema::hasColumn('turnover_reward_achievers', 'last_paid_at')) {
                $table->dateTime('last_paid_at')->nullable();
            }
            if (!Schema::hasColumn('turnover_reward_achievers', 'weeks_paid')) {
                $table->unsignedInteger('weeks_paid')->default(0);
            }
            if (!Schema::hasColumn('turnover_reward_achievers', 'status')) {
                // 0 = active (highest / earning weekly), 1 = superseded by higher reward
                $table->tinyInteger('status')->default(0)->index();
            }
        });

        // Seed / upsert the 7 business-plan rewards (extend existing master, do not duplicate module)
        $rewards = [
            ['milestone_order' => 1, 'title' => 'Reward 1', 'required_directs' => 5,  'required_team' => 20,   'required_self_business' => 100,  'required_team_business' => 5000,    'weekly_salary' => 10,  'cash_reward' => 10],
            ['milestone_order' => 2, 'title' => 'Reward 2', 'required_directs' => 6,  'required_team' => 50,   'required_self_business' => 200,  'required_team_business' => 12000,   'weekly_salary' => 20,  'cash_reward' => 20],
            ['milestone_order' => 3, 'title' => 'Reward 3', 'required_directs' => 7,  'required_team' => 100,  'required_self_business' => 300,  'required_team_business' => 20000,   'weekly_salary' => 50,  'cash_reward' => 50],
            ['milestone_order' => 4, 'title' => 'Reward 4', 'required_directs' => 8,  'required_team' => 200,  'required_self_business' => 500,  'required_team_business' => 50000,   'weekly_salary' => 100, 'cash_reward' => 100],
            ['milestone_order' => 5, 'title' => 'Reward 5', 'required_directs' => 9,  'required_team' => 500,  'required_self_business' => 700,  'required_team_business' => 100000,  'weekly_salary' => 200, 'cash_reward' => 200],
            ['milestone_order' => 6, 'title' => 'Reward 6', 'required_directs' => 10, 'required_team' => 1000, 'required_self_business' => 1000, 'required_team_business' => 250000,  'weekly_salary' => 300, 'cash_reward' => 300],
            ['milestone_order' => 7, 'title' => 'Reward 7', 'required_directs' => 14, 'required_team' => 2000, 'required_self_business' => 1500, 'required_team_business' => 500000,  'weekly_salary' => 500, 'cash_reward' => 500],
        ];

        foreach ($rewards as $row) {
            $existing = DB::table('turnover_reward_masters')
                ->where('milestone_order', $row['milestone_order'])
                ->first();

            $payload = [
                'title' => $row['title'],
                'required_directs' => $row['required_directs'],
                'required_team' => $row['required_team'],
                'required_self_business' => $row['required_self_business'],
                'required_team_business' => $row['required_team_business'],
                'turnover_amount' => $row['required_team_business'],
                'weekly_salary' => $row['weekly_salary'],
                'cash_reward' => $row['cash_reward'],
                'updated_at' => now(),
            ];

            if ($existing) {
                DB::table('turnover_reward_masters')->where('id', $existing->id)->update($payload);
            } else {
                $payload['milestone_order'] = $row['milestone_order'];
                $payload['created_at'] = now();
                DB::table('turnover_reward_masters')->insert($payload);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turnover_reward_achievers', function (Blueprint $table) {
            foreach (['weekly_salary', 'directs_count', 'team_count', 'self_business', 'team_business', 'return_date', 'last_paid_at', 'weeks_paid', 'status'] as $col) {
                if (Schema::hasColumn('turnover_reward_achievers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('turnover_reward_masters', function (Blueprint $table) {
            foreach (['required_directs', 'required_team', 'required_self_business', 'required_team_business', 'weekly_salary', 'title'] as $col) {
                if (Schema::hasColumn('turnover_reward_masters', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'registration_fee', 'reward_id', 'locked_reward_bonus', 'unlocked_reward_bonus',
                'expired_reward_bonus', 'locked_reward_lock_date', 'locked_reward_expiry_date', 'sponsor_unlock_done',
            ] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
