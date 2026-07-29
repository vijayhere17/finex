@php use Illuminate\Support\Facades\Auth; @endphp
@extends('users.master')
@section('extra')
<style>
    .fx-stat-card {
        border-radius: 14px;
        border: 1px solid var(--gt-card-border, #2e2920);
        background: var(--gt-card-bg, #1a1610);
        height: 100%;
    }
    .fx-stat-card .fx-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--gt-text-muted, #9c9488);
        margin-bottom: 0.35rem;
    }
    .fx-stat-card .fx-value {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--gt-heading, #f6efdd);
        margin: 0;
    }
    .fx-stat-card .fx-value.text-gold { color: #f8ce4e; }
    .fx-hero {
        background: linear-gradient(120deg, #a5731c, #e6ad1f 55%, #f8ce4e);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        color: #0d0b07;
        margin-bottom: 1.25rem;
    }
    .fx-hero h2 { margin: 0; font-weight: 800; color: #0d0b07; }
    .fx-hero p { margin: 0.35rem 0 0; opacity: 0.85; font-weight: 500; }
</style>
@endsection
@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="fx-hero">
            <h2>Welcome, {{ Auth::user()->firstname }}</h2>
            <p>Finex Slot Plan — activate slots, grow directs, earn Daily ROI up to 12%.</p>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Current Slot</div>
                    <p class="fx-value">{{ $fx->current_slot > 0 ? 'Slot '.$fx->current_slot : 'None' }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Next Slot</div>
                    <p class="fx-value text-info">{{ $fx->next_slot > 0 ? 'Slot '.$fx->next_slot.' ($'.$fx->next_slot_amount.')' : 'Complete' }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Activation Status</div>
                    <p class="fx-value">{{ ucfirst($fx->activation_status) }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Qualified Directs</div>
                    <p class="fx-value">{{ $fx->qualified_active_directs }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Current Daily ROI %</div>
                    <p class="fx-value text-gold">{{ number_format($fx->direct_roi_percent, 0) }}%</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Today's ROI</div>
                    <p class="fx-value">${{ number_format($fx->today_roi, 2) }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Total ROI</div>
                    <p class="fx-value">${{ number_format($fx->total_roi, 2) }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Level ROI Income</div>
                    <p class="fx-value">${{ number_format($fx->level_roi, 2) }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Auto Upgrade Wallet</div>
                    <p class="fx-value text-gold">${{ number_format($fx->auto_upgrade_balance, 2) }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Available Wallet</div>
                    <p class="fx-value">${{ number_format($fx->available_wallet, 2) }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Total Withdrawn</div>
                    <p class="fx-value">${{ number_format($fx->total_withdrawn, 2) }}</p>
                </div></div>
            </div>
            <div class="col-6 col-md-4 col-xl-3">
                <div class="card fx-stat-card mb-0"><div class="card-body">
                    <div class="fx-label">Quick Action</div>
                    <a href="{{ URL::to('/') }}/buy-robo" class="btn btn-primary btn-sm mt-1">Activate Next Slot</a>
                </div></div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('jscontent')
@endsection
