@php use Illuminate\Support\Facades\Auth; @endphp
@extends('users.master')
@section('extra')
<style>
    h3, .h3 {
        font-size: 1rem;
    }

    .example-box {
        width: 100%;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        position: relative;
        overflow: hidden;
        background-size: cover;
        color: white;
        font-family: sans-serif;
        font-weight: 200;
        z-index: 1;
    }

    .example-box * {
        z-index: 2;
    }

    .background-shapes {
        content: "";
        position: absolute;
        z-index: 2;
        left: 0;
        top: 0;
        width: 100%;
        height: 5076px;
        background-size: 100%;
        animation: 120s infiniteScroll linear infinite;
        background-repeat-x: repeat;
        background-image: url({{ URL::to('/') }}/assets/images/circles.svg);
    }

    @-webkit-keyframes infiniteScroll {
        0% { -webkit-transform: translate3d(0, 0, 0); transform: translate3d(0, 0, 0); }
        100% { -webkit-transform: translate3d(0, -1692px, 0); transform: translate3d(0, -1692px, 0); }
    }
    @keyframes infiniteScroll {
        0% { -webkit-transform: translate3d(0, 0, 0); transform: translate3d(0, 0, 0); }
        100% { -webkit-transform: translate3d(0, -1692px, 0); transform: translate3d(0, -1692px, 0); }
    }

    img.vert-move {
        -webkit-animation: mover 1s infinite alternate;
        animation: mover 1s infinite alternate;
    }
    @-webkit-keyframes mover {
        0% { transform: translateY(0); }
        100% { transform: translateY(-10px); }
    }
    @keyframes mover {
        0% { transform: translateY(0); }
        100% { transform: translateY(-10px); }
    }

    .modal-open .modal-backdrop {
        backdrop-filter: blur(7px);
        background-color: rgba(0, 0, 0, 0.6);
        opacity: 1 !important;
    }

    .custom-alert {
        background: linear-gradient(120deg, #a5731c, #e6ad1f 55%, #f8ce4e);
        border-left: 4px solid #a5731c;
        border-radius: 10px;
        padding: 11px 16px;
        color: #0d0b07;
        font-weight: 500;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(230, 173, 31, 0.15);
        font-size: 12.5px;
        max-width: 100%;
        width: 100%;
        position: relative;
        margin-bottom: 20px;
    }
    .custom-alert a { color: #0d0b07 !important; text-decoration: underline; }
    .custom-alert strong { font-weight: 700; }

    .dash-coin-hero {
        width: 100%;
        max-width: 190px;
        aspect-ratio: 1;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at 35% 30%, #2a2113, #0d0b07 75%);
        border: 3px solid rgba(230, 173, 31, 0.55);
        box-shadow: 0 0 60px rgba(230, 173, 31, 0.35), inset 0 0 30px rgba(0, 0, 0, 0.25);
    }
    .dash-coin-hero svg { width: 45%; height: 45%; color: #e6ad1f; }
    .dash-coin-hero img { width: 70%; height: auto; filter: drop-shadow(0 4px 14px rgba(230, 173, 31, 0.4)); }

    .gt-hero-tagline {
        font-size: 0.62rem;
        text-transform: uppercase;
        letter-spacing: 0.22em;
        color: var(--gt-gold-2);
        font-weight: 600;
    }

    .progress-thin { height: 8px; border-radius: 6px; background: rgba(255,255,255,0.12); }

    .rank-tier-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 20px;
        background: rgba(230, 173, 31, 0.14);
        color: var(--gt-gold-2, #e6ad1f);
        font-size: 0.7rem;
        font-weight: 600;
    }

    /* ==== Dashboard density & typography scale ==== */
    .pc-content .card {
        border-radius: 14px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .pc-content .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(230, 173, 31, 0.14);
    }
    .pc-content .card .card-body { padding: 16px 18px; }
    .pc-content .card .mb-3 { margin-bottom: 0.65rem !important; }

    .pc-content h4, .pc-content .h4 { font-size: 1rem; font-weight: 700; }
    .pc-content .card .btn { font-size: 0.78rem; padding: 8px 12px; }

    .gt-card-title {
        font-size: 0.7rem !important;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        font-weight: 700 !important;
        color: var(--gt-gold-2) !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .gt-card-title::before {
        content: "";
        width: 3px;
        height: 12px;
        border-radius: 2px;
        background: linear-gradient(180deg, #f8ce4e, #a5731c);
        flex-shrink: 0;
    }

    .pc-content .list-group-item {
        padding-top: 8px;
        padding-bottom: 8px;
        font-size: 0.8rem;
    }
    .pc-content .list-group-item > span:last-child,
    .pc-content .list-group-item > div + span {
        font-weight: 600;
        max-width: 60%;
        text-align: right;
        word-break: break-word;
    }
    .pc-content .list-group-item .text-muted { font-size: 0.72rem; }

    .pc-content .bg-body.rounded {
        padding: 12px !important;
        border: 1px solid var(--gt-border);
    }
    .pc-content .bg-body.rounded h4 { font-size: 0.95rem; margin-bottom: 2px; word-break: break-all; }
    .pc-content .bg-body.rounded p {
        font-size: 0.62rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 600;
    }

    .gt-mini-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }
    .gt-mini-stat {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--gt-border);
        border-radius: 10px;
        padding: 10px 6px;
        text-align: center;
        min-width: 0;
    }
    .gt-mini-stat .v {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--gt-heading);
        word-break: break-all;
        line-height: 1.2;
    }
    .gt-mini-stat .l {
        display: block;
        margin-top: 2px;
        font-size: 0.6rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--gt-text-muted);
    }

    .gt-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 26px 10px;
        color: var(--gt-text-muted);
        font-size: 0.78rem;
    }
    .gt-empty i {
        font-size: 1.7rem;
        color: var(--gt-gold-3);
        opacity: 0.9;
    }
    
    ::-webkit-scrollbar {
        display: none;
    }
    
    /* Firefox */
    html {
        scrollbar-width: none;
    }
    
    /* IE & Old Edge */
    body {
        -ms-overflow-style: none;
    }
    
    /* Ensure scrolling is enabled */
    html,
    body {
        overflow-y: auto;
        overflow-x: hidden; /* optional */
    }
</style>
@endsection
@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ Refer link banner ] -->
        <div class="row">
            <div class="col-md-12 col-xxl-12">
                <div class="custom-alert">
                    <strong>Refer Link :.</strong> Use your referral link to spread the good vibes and earn some perks too! Let's build something amazing together!&nbsp;&nbsp;<a href="javascript:toClip(`{{ URL::to('/') }}/sign-up?ref={{ Auth::user()->username }}`)">Copy Link...</a>
                </div>
            </div>
        </div>

        <!-- [ Profile / Package / Quick Actions ] -->
        <div class="row">
            <div class="col-md-6 col-xxl-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <img src="{{ URL::to('/') }}/assets/images/user/avatar-1.jpg" alt="user" class="user-avtar wid-50 rounded-circle" />
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="mb-0">Wallet Address</h5>
                                <p class="text-muted mb-0">{{ obscureAddress(Auth::user()->username) }}</p>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted">Email</span>
                                <span>{{ Auth::user()->email ?? '-' }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted">Account Status</span>
                                @if(Auth::user()->kit)
                                    <span class="badge bg-light-success">Active</span>
                                @else
                                    <span class="badge bg-light-warning">Inactive</span>
                                @endif
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted">Member Since</span>
                                <span>{{ date('d M Y', strtotime(Auth::user()->created_at)) }}</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <span class="text-muted">Current Rank</span>
                                @if($object->current_rank)
                                    <span class="rank-tier-pill"><i class="ti ti-award"></i> {{ $object->current_rank->rank }}</span>
                                @else
                                    <span class="rank-tier-pill">Not Ranked Yet</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xxl-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0 gt-card-title">Package Details</h5>
                        </div>
                        @if(Auth::user()->kit)
                            <ul class="list-group list-group-flush" style="height: 185px; overflow: scroll;">
                                <!--<li class="list-group-item px-0 d-flex justify-content-between align-items-center">-->
                                <!--    <span class="text-muted">Package</span>-->
                                <!--    <span>{{ Auth::user()->kit->name }}</span>-->
                                <!--</li>-->
                                <!--<li class="list-group-item px-0 d-flex justify-content-between align-items-center">-->
                                <!--    <span class="text-muted">Invested Amount</span>-->
                                <!--    <span>{{ Auth::user()->kit->amount }}</span>-->
                                <!--</li>-->
                                <!--<li class="list-group-item px-0 d-flex justify-content-between align-items-center">-->
                                <!--    <span class="text-muted">Daily ROI</span>-->
                                <!--    <span>{{ Auth::user()->kit->percantage }}%</span>-->
                                <!--</li>-->
                                
                                @foreach($object->list_self_investment as $lpd)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                    <span class="text-muted">
                                        {{ $lpd->kit->name }}<br>
                                        <small>{{ date("d-m-Y H:i:s", strtotime($lpd->created_at)) }}</small>
                                    </span>
                                    <span>${{ $lpd->paid_amount }}</span>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="gt-empty">
                                <i class="ti ti-package"></i>
                                <span>No active package yet.</span>
                            </div>
                            <div class="d-grid">
                                <a href="{{ URL::to('/') }}/buy-robo" class="btn btn-primary btn-sm">Topup Now</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-xxl-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avtar avtar-s bg-light-primary">
                                <svg class="pc-icon"><use xlink:href="#custom-wallet-2"></use></svg>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 gt-card-title">Wallet Balance</h6>
                            </div>
                        </div>
                        <div class="bg-body p-3 mt-3 rounded">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h4 class="mb-0">Earning Wallet</h4>
                                </div>
                                <div class="col-6 text-end">
                                    <h4 class="mb-0 text-primary">{{ $object->total_balance }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="bg-body p-3 mt-2 rounded">    
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h4 class="mb-0">Total Income</h4>
                                </div>
                                <div class="col-6 text-end">
                                    <h4 class="mb-0 text-primary">{{ $object->total_earning }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="bg-body p-3 mt-2 rounded">       
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h4 class="mb-0">Remaining Income</h4>
                                </div>
                                <div class="col-6 text-end">
                                    <h4 class="mb-0 text-primary">{{ $object->total_2x_remain }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- [ Wallet / Income / Team stats ] -->
        <div class="row">
            
            <div class="col-md-12 col-xxl-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0 gt-card-title">Booster Timer Status</h5>
                        </div>
                        <div class="row g-2">
                            <div class="col-12">
                                @if(Auth::user()->kit_id > 0)
                                    @if(Auth::user()->is_booster == 0) 
                                        <div class="col-md-12 col-xxl-12 mt-3">
                                            <div class="alert alert-primary d-flex align-items-center" role="alert">
                                                <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                                                    <use xlink:href="#custom-calendar-1"></use>
                                                </svg>
                                                <div style="font-size: 18px;">
                                                    <span id="booster_timer" style="font-weight: 900;">0 Days 00:00:00</span>
                                                </div>
                                            </div>
                                        </div>  
                                    @else    
                                        <div class="col-md-12 col-xxl-12 mt-3">
                                            <div class="alert alert-error d-flex align-items-center" role="alert">
                                                <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                                                    <use xlink:href="#custom-star-bold"></use>
                                                </svg>
                                                <div style="font-size: 18px;">Congratulations! You will achieve Booster</div>
                                            </div>
                                        </div>    
                                    @endif
                                @else     
                                    <div class="col-md-12 col-xxl-12 mt-3">
                                        <div class="alert alert-primary d-flex align-items-center" role="alert">
                                            <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                                                <use xlink:href="#custom-star-bold"></use>
                                            </svg>
                                            <div style="font-size: 18px;">Please Active Your ID.</div>
                                        </div>
                                    </div>  
                                @endif  
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-xxl-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avtar avtar-s bg-light-primary">
                                <svg class="pc-icon"><use xlink:href="#custom-profile-2user-outline"></use></svg>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 gt-card-title">Direct Team</h6>
                            </div>
                        </div>
                        <div class="bg-body p-3 mt-3 rounded">
                            <div class="row align-items-center text-center">
                                <div class="col-4">
                                    <h4 class="mb-0">{{ $object->total_referral }}</h4>
                                    <p class="text-primary mb-0">Total</p>
                                </div>
                                <div class="col-4">
                                    <h4 class="mb-0">{{ $object->total_a_referral }}</h4>
                                    <p class="text-primary mb-0">Active</p>
                                </div>
                                <div class="col-4">
                                    <h4 class="mb-0">{{ $object->total_ia_referral }}</h4>
                                    <p class="text-primary mb-0">Inactive</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-xxl-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avtar avtar-s bg-light-primary">
                                <svg class="pc-icon"><use xlink:href="#custom-profile-2user-outline"></use></svg>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 gt-card-title">Total Team</h6>
                            </div>
                        </div>
                        <div class="bg-body p-3 mt-3 rounded">
                            <div class="row align-items-center text-center">
                                <div class="col-4">
                                    <h4 class="mb-0">{{ $object->total_team }}</h4>
                                    <p class="text-primary mb-0">Total</p>
                                </div>
                                <div class="col-4">
                                    <h4 class="mb-0">{{ $object->total_a_team }}</h4>
                                    <p class="text-primary mb-0">Active</p>
                                </div>
                                <div class="col-4">
                                    <h4 class="mb-0">{{ $object->total_ia_team }}</h4>
                                    <p class="text-primary mb-0">Inactive</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xxl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar avtar-s bg-light-primary">
                                    <span class="pc-micon">
                                        <svg class="pc-icon">
                                            <use xlink:href="#custom-dollar-square"></use>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">My Business</h6>
                            </div>
                        </div>
                        <div class="bg-body p-3 mt-3 rounded">
                            <div class="mt-3 row align-items-center">
                                <div class="col-12 text-end">
                                    <h3 class="mb-1">${{ $object->total_t_investment }}</h3>
                                    <p class="text-primary mb-0">Total Downline</p>
                                </div>
                            </div>
                            <div class="mt-3 row align-items-center">
                                <div class="col-6">
                                    <h3 class="mb-1">${{ $object->total_r_investment }}</h3>
                                    <p class="text-primary mb-0">Total Referral</p>
                                </div>
                                <div class="col-6 text-end">
                                    <h3 class="mb-1">${{ $object->total_t_investment }}</h3>
                                    <p class="text-primary mb-0">Total Business</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="card bg-grey available-balance-card" style="background-color: rgb(22 166 181) !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="mb-0 text-white text-opacity-75">Coin Price</p>
                                <h4 class="mb-0 text-white">${{ getcoinrate() }}</h4>
                            </div>
                            <div class="avtar">
                                <img src="{{ URL::to('/') }}/assets/images/logo-lg.png" alt="user-image" class="user-avtar wid-35" />
                            </div>
                        </div>
                    </div>
                </div>
            
                <div class="card bg-primary available-balance-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="mb-0 text-white text-opacity-75">Total Withdrawal</p>
                                <h4 class="mb-0 text-white">{{ $object->total_withdrawal }}</h4>
                            </div>
                            <div class="avtar">
                                <i class="ti ti-arrows-left-right f-18"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    
                
            
            <!--<div class="col-md-6 col-xxl-4 mb-4">-->
            <!--    <div class="card h-100">-->
            <!--        <div class="card-body">-->
            <!--            <div class="d-flex align-items-center justify-content-between mb-3">-->
            <!--                <h5 class="mb-0 gt-card-title">Bonus Summary</h5>-->
            <!--            </div>-->
            <!--            <ul class="list-group list-group-flush">-->
            <!--                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">-->
            <!--                    <span class="text-muted">Direct Sponsor Income</span>-->
            <!--                    <span>{{ $object->total_referral_bonus }}</span>-->
            <!--                </li>-->
            <!--                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">-->
            <!--                    <span class="text-muted">Daily ROI Income</span>-->
            <!--                    <span>{{ $object->total_daily_roi_bonus }}</span>-->
            <!--                </li>-->
            <!--                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">-->
            <!--                    <span class="text-muted">Daily Level Income</span>-->
            <!--                    <span>{{ $object->total_daily_level_bonus }}</span>-->
            <!--                </li>-->
            <!--                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">-->
            <!--                    <span class="text-muted">Team Level Income</span>-->
            <!--                    <span>{{ $object->total_team_level_bonus }}</span>-->
            <!--                </li>-->
            <!--                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">-->
            <!--                    <span class="text-muted">Reward Bonus</span>-->
            <!--                    <span>{{ $object->total_salary_bonus }}</span>-->
            <!--                </li>-->
            <!--            </ul>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
        </div>
        
        <div class="row">
            <!-- [ form-element ] start -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Reward Achievement</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="row g-3 mt-0">
                            <div class="col-sm-4">
                                <div class="bg-body p-3 rounded">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="flex-shrink-0">
                                            <span class="p-1 d-block bg-primary rounded-circle">
                                                <span class="visually-hidden">Power Leg (40%)</span>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="mb-0">Power Leg (40%)</p>
                                        </div>
                                    </div>
                                    <h6 class="mb-0">$ {{ $object->leg_data['leg_1_business'] }} <small class="text-muted">{{ $object->leg_data['leg_1_username'] }}</small></h6>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-body p-3 rounded">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="flex-shrink-0">
                                            <span class="p-1 d-block bg-warning rounded-circle">
                                                <span class="visually-hidden">Power Leg (30%)</span>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="mb-0">Power Leg (30%)</p>
                                        </div>
                                    </div>
                                    <h6 class="mb-0">$ {{ $object->leg_data['leg_2_business'] }} <small class="text-muted">{{ $object->leg_data['leg_2_username'] }}</small>
                                    </h6>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-body p-3 rounded">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="flex-shrink-0">
                                            <span class="p-1 d-block bg-success rounded-circle">
                                                <span class="visually-hidden">Power Leg (30%)</span>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <p class="mb-0">Power Leg (30%)</p>
                                        </div>
                                    </div>
                                    <h6 class="mb-0">$ {{ $object->leg_data['leg_3_business'] }} <small class="text-muted">{{ $object->leg_data['leg_3_username'] }}</small>
                                    </h6>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive mt-4">
                            <table class="table" id="tableList">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Rank</th>
                                        <th>Business</th>
                                        <th>Instant Reward</th>
                                        <th>Status</th>
                                        <th>Achieve Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($object->allsalary as $rank)
                                    
                                    @php
                                        $salaryCon = app('App\Http\Controllers\Users\SalaryController');
                                        $status = $salaryCon->getstatus(Auth::user()->id, $rank->id);
                                    @endphp
                                    <tr>
                                        <td>{{ $rank->id }}</td>
                                        <td>{{ $rank->rank }}</td>
                                        <td>${{ $rank->business }}</td>
                                        <td>${{ $rank->bonus }}</td>
                                        <td>
                                            @if($status == null)
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-success">Achieve</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($status == null)
                                                --/--/---- --:--:--
                                            @else
                                                {{ date("d/m/Y H:i:s", strtotime($status->created_at)) }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> 
        </div>

    </div>
</div>
@endsection
@section('jscontent')

@if(Auth::user()->kit_id > 0)
    @if(Auth::user()->is_booster == 0) 
        <script>
            var countDownDate = new Date("<?php echo date('M d, Y H:i:s', strtotime(Auth::user()->activation_date. ' + 48 hours')); ?>").getTime();
           
            var x = setInterval(function() {
                var now = new Date().getTime();
                
                var distance = countDownDate - now;

                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById("booster_timer").innerHTML = days+' Days '+hours+ ":"+minutes+":"+seconds;

                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("booster_timer").innerHTML = "Expired";
                }
            }, 1000);
        </script>
    @endif  
@endif 

<script>
    function toClip(text) {
        var copy = document.createElement("textarea");
        document.body.appendChild(copy);
        copy.value = text;
        copy.select();
        document.execCommand("copy");
        document.body.removeChild(copy);

        successalert('Refer link copy successfylly!')
    }
</script>
@endsection
