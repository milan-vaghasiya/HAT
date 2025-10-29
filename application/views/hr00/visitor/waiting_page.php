
<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title>JAY JALARAM PRECISION COMPONENT LLP</title>
    <meta name="description" content="Finapp HTML Mobile Template">
    <meta name="keywords" content="bootstrap, wallet, banking, fintech mobile template, cordova, phonegap, mobile, html, responsive" />
    <link rel="icon" type="image/png" href="<?=base_url();?>assets/mobile_assets/img/favicon.png" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="<?=base_url();?>assets/mobile_assets/img/icon/192x192.png">
    <link rel="stylesheet" href="<?=base_url();?>assets/mobile_assets/css/style.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/mobile_assets/css/panda_form.css">
    <!-- Combo Select 
    <link href="<?=base_url()?>assets/extra-libs/comboSelect/combo.select.css" rel="stylesheet" type="text/css">-->
</head>

<body>

    <!-- loader -->
    <div id="loader"><img src="<?=base_url();?>assets/mobile_assets/img/loading-icon.png" alt="icon" class="loading-icon"></div>
    <!-- * loader -->
	<div class="text-center"><img src="<?=base_url()?>assets/mobile_assets/img/logo.png" alt="icon" style="width:60%;padding-top:10px;"></div>
	<div class="text-center mt-1" style="background: #005da9;color: #FFFFFF;">Please wait...Your request sent Successfully</div>
    <!-- App Capsule -->
    <div id="appCapsule" style="padding: 0px 20px;">
        <div class="panda" style="margin-top:-15px;">
			<div class="ear"></div>
			<div class="face">
				<div class="eye-shade"></div>
				<div class="eye-white">
					<div class="eye-ball"></div>
				</div>
				<div class="eye-shade rgt"></div>
				<div class="eye-white rgt">
					<div class="eye-ball"></div>
				</div>
				<div class="nose"></div>
				<div class="mouth"></div>
			</div>
			<div class="body"> </div>
			<div class="foot">
				<div class="finger"></div>
			</div>
			<div class="foot rgt">
				<div class="finger"></div>
			</div>
		</div>
		<form class="panda_form">
			<div class="hand"></div>
			<div class="hand rgt"></div>
			<h1 class="panda_thanks">Thanks For Your Patience</h1>
			<div class="text-center checkIMG">
				<img src="<?=base_url()?>assets/mobile_assets/img/loader_1.gif" alt="icon" style="width:50%;">
				<p class="text-black" style="font-size:1.1rem;">We are Working</p>
			</div>
			
		</form>
    </div>
    <!-- * App Capsule -->


    <!-- ========= JS Files =========  -->
    <!-- Bootstrap -->
    <script src="<?=base_url();?>assets/mobile_assets/js/lib/bootstrap.bundle.min.js"></script>
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <!-- Splide -->
    <script src="<?=base_url();?>assets/mobile_assets/js/plugins/splide/splide.min.js"></script>
    <!-- Base Js File -->
    <script src="<?=base_url();?>assets/mobile_assets/js/base.js"></script>
    <!-- Combo Select 
    <script src="<?=base_url()?>assets/extra-libs/comboSelect/jquery.combo.select.js"></script> -->
    <script src="<?=base_url()?>assets/libs/jquery/dist/jquery.min.js"></script>
    <script>
        var base_url = '<?=base_url();?>';
        var request_id = '<?=$request_id?>';
        $(document).ready(function(){
			// Panda Eye move
			$(document).on( "mousemove", function( event ) {
			  var dw = $(document).width() / 15;
			  var dh = $(document).height() / 15;
			  var x = event.pageX/ dw;
			  var y = event.pageY/ dh;
			  $('.eye-ball').css({
				width : x,
				height : y
			  });
			});
			
			setInterval(function()
			{
				$.ajax({
					url: base_url + 'api/v1/visitorLogs/checkForApproval',
					data:{id:request_id},
					type: "POST",
					dataType:"json",
				}).done(function(data){
					if(data.status===1){
						$('.panda_thanks').html(data.approved_at);
						$('.panda_thanks').addClass('approved_time');
						$('.checkIMG').html('<img src="'+base_url + 'assets/mobile_assets/img/check_animation.gif" style="width:100%;"><p class="text-black" style="font-size:1.1rem;">Approved</p>');
					}else{
					}
							
				});
			}, 2000);
        });
    </script>

</body>

</html>
