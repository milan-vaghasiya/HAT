<!DOCTYPE html>
<html dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="author" content="SCUBEERP">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title><?=SITENAME?> - <?=(isset($headData->pageTitle)) ? $headData->pageTitle : '' ?></title>
    <meta name="description" content="<?=SITENAME?>">
    <meta name="keywords" content="Akshar Engineers,akshar" />
    <link rel="icon" type="image/png" href="<?=base_url();?>assets/app/img/favicon.png" sizes="32">
    <link rel="apple-touch-icon" sizes="180x180" href="<?=base_url();?>assets/app/img/icon/192.png">
    <link rel="stylesheet" href="<?=base_url();?>assets/app/css/style.css">
    <link rel="manifest" href="<?=base_url();?>manifest.json">
</head>
<body>
	<!-- loader -->
    <div id="loader">
        <img src="<?=base_url();?>assets/app/img/loading-icon.png" alt="icon" class="loading-icon">
    </div>
    <!-- * loader -->

    <!-- App Capsule -->
    <div id="appCapsule">

        <div class="section mt-2 text-center">
			<img src="<?=base_url()?>assets/app/img/logo.png" alt="logo" width="80%" />
            <h1 class="mt-4">Log in</h1>
        </div>
        <div class="section mb-5 p-2">
           

            <form id="loginform" action="<?=base_url('app/login/auth');?>" method="POST">
                <div class="card">
                    <div class="card-body pb-1">
						<?php if($errorMsg = $this->session->flashdata('loginError')): ?>
							<div class="error errorMsg"><?=$errorMsg?></div>
						<?php endif; ?>
                        <input type="hidden" name="fyear" id="fyear" value="3" >
                        <input type="hidden" name="web_token" id="web_token" value="">
                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="email1">Username</label>
                                <input type="text" class="form-control" id="user_name" name="user_name" placeholder="Username">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
							<?=form_error('user_name')?>
                        </div>

                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" autocomplete="off"
                                    placeholder="Your password">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
							<?=form_error('password')?>
                        </div>
                    </div>
                </div>

                <div class="form-button-group transparent">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">Log in</button>
                </div>

            </form>
        </div>

    </div>
    <!-- * App Capsule -->



    <!-- ========= JS Files =========  -->
    <!-- Bootstrap -->
    <script src="<?=base_url()?>assets/app/js/lib/jquery/dist/jquery.min.js"></script>
    <script src="<?=base_url();?>assets/app/js/lib/bootstrap.bundle.min.js"></script>
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <!-- Splide -->
    <script src="<?=base_url();?>assets/app/js/plugins/splide/splide.min.js"></script>
    <!-- Base Js File -->
    <script src="<?=base_url();?>assets/app/js/base.js"></script>
    
    <!-- Firebase App is always required and must be first -->
    <script src="https://www.gstatic.com/firebasejs/8.2.0/firebase-app.js"></script> 
    <script src="https://www.gstatic.com/firebasejs/8.2.0/firebase-messaging.js"></script>
    <script type="module" src="<?=base_url()?>assets/js/notification.js?v=<?=time()?>"></script>
    <script>
        // ============================================================== 
        // Login and Recover Password 
        // ============================================================== 
        $('#to-recover').on("click", function() {
            $("#loginform").slideUp();
            $("#recoverform").fadeIn();
        });
        $('#to-login').on("click", function() {
            $("#recoverform").fadeOut();
            $("#loginform").slideDown();            
        });
        
        
        if (Notification.permission !== "granted"){  
            Notification.requestPermission();  
            console.log("send permission req.");
        }
    </script>
</body>
</html>