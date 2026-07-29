<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $page_titel }}</title>

    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- [Favicon] icon -->
    <link rel="icon" type="image/svg+xml" href="{{ URL::to('/') }}/assets/images/finex-mark.svg"> <!-- [Font] Family -->
    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/fonts/inter/inter.css" id="main-font-link" />

    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/fonts/tabler-icons.min.css" >

    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/fonts/feather.css" >

    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/fonts/fontawesome.css" >

    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/fonts/material.css" >

    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/css/style.css" id="main-style-link" >
    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/css/style-preset.css" >

    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/common/alert/cute-style.css" >

    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/css/bg-animation.css" >

    <link rel="stylesheet" href="{{ URL::to('/') }}/assets/css/finex-theme.css" >

    <style>
        .toggle-password {
            float: right;
            cursor: pointer;
            margin-right: 20px;
            margin-top: -33px;
        }
    </style>
</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-8" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light">
   
    <div class="glow-container">
       <div class="ball"></div>
       <div class="ball" style="--delay:-12s;--size:0.35;--speed:25s;"></div>
       <div class="ball" style="--delay:-10s;--size:0.3;--speed:15s;"></div> 
    </div>

    <input type="hidden" value="{{ URL::to('/') }}" id="basePath"/>
    <input type="hidden" id="token" value="{{ csrf_token() }}"/>
    
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    <div class="auth-main">
        <div class="auth-wrapper v1">
            <div class="auth-form">
                <div class="card my-5">
                    <div class="card-body">
                        <div class="text-center">
                           <x-logo />
                           <div class="fx-brand-sub">Join Finex and access powerful trading tools, expert market insights, and a secure platform designed to help you trade with confidence.</div>
                           <div class="fx-brand-year">2026</div>
                           <div class="fx-brand-web">www.finex.world</div>
                        </div>

                        <div class="saprator my-3">
                            <span>Finex</span>
                        </div>

                        <h4 class="text-center f-w-500 mb-3">Login your account</h4>

                        <x-input type="text" name="userwallet" id="userwallet" placeholder="Connect wallet address" value="" />

                        <div class="d-grid mt-4">
                            <button type="button" class="btn btn-info btn-connect">Connect Wallet</button>
                            <button type="button" class="btn btn-primary btn-submit" style="display: none;">Login</button>
                        </div>

                        <div class="d-flex justify-content-between align-items-end mt-4">
                            <h6 class="f-w-500 mb-0">Don't have an Account?</h6> <a href="{{ URL::to('/') }}/sign-up" class="link-primary">Create Account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Required Js -->
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script src="{{ URL::to('/') }}/assets/js/plugins/popper.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/plugins/simplebar.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/plugins/bootstrap.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/fonts/custom-font.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/pcoded.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/plugins/feather.min.js"></script>

    <script src="{{ URL::to('/') }}/assets/common/js/ScrollMagic.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/particles.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/particles_set.0.3.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/alert/cute-alert.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/web3.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/ethers-v4.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/jquery.blockUI.js"></script>
    <script src="{{ URL::to('/') }}/assets/common/js/common.0.8.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/users/sign-in.0.4.js"></script>
    
    <script>
        $(".toggle-password").click(function() {
            
            $(this).toggleClass("fa-eye fa-eye-slash");
            
            input = $(this).parent().find("input");
            
            if (input.attr("type") == "password") {
                
                input.attr("type", "text");
                
            } else {
                
                input.attr("type", "password");
                
            }
        });
    </script>
</body>
</html>