@php
    $dashboardCon = app('App\Http\Controllers\Users\DashboardController');
    $earning =  0; // $dashboardCon->mytotalearning();
@endphp
@extends('users.master')
@section('extra')
@endsection
@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('/') }}/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="javascript:">New Topup</a></li>
                            <li class="breadcrumb-item" aria-current="page">{{ $page_titel }}</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">{{ $page_titel }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="row">

            <div class="col-md-6 col-lg-6">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                @foreach($packages as $data)
                                <div class="price-check border rounded p-3 mb-3">
                                    <div class="form-check">
                                        <input type="radio" name="package" class="form-check-input input-primary" id="package_{{ $data->id }}" stakeid="{{ $data->id }}" stakeamount="{{ $data->amount }}" cap="{{ $data->cap_multiplier }}" minamount="{{ $data->amount }}" maxamount="{{ $data->max_amount }}" style="margin-top: 12.5px; margin-right: 10px;" onclick="getcalculation();">
                                        <label class="form-check-label d-block" for="package_{{ $data->id }}">
                                            <span class="row align-items-center">
                                                <span class="col-6">
                                                    
                                                    <span class="text-muted mb-0">{{ $data->name }}</span>
                                                </span>
                                                <span class="col-6 text-end">
                                                    <span class="price-price h4">${{ $data->amount }}@if($data->max_amount > 0) <span class="text-muted text-sm">- ${{ $data->max_amount }}</span>@else<span class="text-muted text-sm">+</span>@endif</span>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div> 
                                @endforeach 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        
                        <div class="alert alert-info">
                            <strong>USDT (BEP20) Live Rate : </strong>$<span>{{ $coin_rate }}</span>
                        </div>
                        
                        <div class="col-md-12">
                            <x-input type="text" name="topup_amount" id="topup_amount" placeholder="Topup Amount ($)" value="" />
                        </div>
                        
                        <div id="amount_error" class="text-danger mb-2" style="display: none; font-weight: 600;"></div>

                        <ul class="list-group list-group-flush product-check-list">
                            <li class="list-group-item enable">
Current Month ROI
<span class="float-end fw-bold text-success" id="txt_apy">
0.00%
</span>
</li>

<li class="list-group-item">
Maximum Return
<span class="float-end fw-bold" id="txt_cap">
0.00X
</span>
</li>

<li class="list-group-item">
Estimated Maximum Income
<span class="float-end fw-bold text-primary">
$
<span id="txt_max_income">0.00</span>
</span>
</li>
                            <li class="list-group-item enable">Daily Income : $<span id="txt_daily_income">0.00</span></li>
                            <li class="list-group-item">Note : Enter a stake amount only $50 multiple</li>
                        </ul>

                        <br>

                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="price-check border rounded p-3 mb-3">
                                    <div class="form-check">
                                        <input type="radio" name="paymentmode" class="form-check-input input-primary" id="payment_alc" data="1" contract="0x55d398326f99059fF775485246999027B3197955" decimal="18" value="1">
                                        <label class="form-check-label d-block" for="payment_alc">
                                            <span class="row align-items-center">
                                                <span class="col-12">
                                                    <span class="h5 mb-0 d-block">Pay USDT (BEP20)</span>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div> 
                            </div>
                        </div>
                        
                        <ul class="list-group list-group-flush product-check-list">
                            <li class="list-group-item enable">Payable USDT : <span id="txt_payable">0.00000000</span></li>
                        </ul>
                        
                        <hr>
                        
                        <button type="submit" class="btn btn-primary btn-submit" style="width: 100%;">Buy Now</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@section('jscontent')

<script>
window.monthlyROI = {{ $currentMonthlyROI ? $currentMonthlyROI->daily_roi : 0 }};
</script>
<script src="{{ URL::to('/') }}/assets/js/users/buy-bot.0.15.js?v=5"></script>
<script>
    connectwallet();
</script>
@endsection
