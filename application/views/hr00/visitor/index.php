
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
    <!-- Combo Select -->
    <link href="<?=base_url()?>assets/extra-libs/comboSelect/combo.select.css" rel="stylesheet" type="text/css">
</head>

<body>

    <!-- loader -->
    <div id="loader"><img src="<?=base_url();?>assets/mobile_assets/img/loading-icon.png" alt="icon" class="loading-icon"></div>
    <!-- * loader -->
    <form id="appointmentForm" style="height:100vh;background:#FFFFFF;">  
    <!-- App Capsule -->
    <div id="appCapsule" style="padding: 0px;">
        <div class="text-center"><img src="<?=base_url()?>assets/mobile_assets/img/logo.png" alt="icon" style="width:70%;padding-top:10px;"></div>
        <div class="text-center mt-1" style="background: #005da9;color: #FFFFFF;">Fill The Form To Take Appointment</div>
        <div class="section">
              
            <div class="form-group basic animated">
                <div class="input-wrapper">
                    <label class="label" for="vname">Your name</label>
                    <input type="text" class="form-control" id="vname" name="vname" placeholder="Your name">
                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                </div>
                <div class="error vname"></div>
            </div>
            <div class="form-group basic animated">
                <div class="input-wrapper">
                    <label class="label" for="contact_no">Contact Number</label>
                    <input type="text" class="form-control" id="contact_no" name="contact_no" placeholder="Contact Number">
                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                </div>
                <div class="error contact_no"></div>
            </div>
            <div class="form-group basic animated">
                <div class="input-wrapper">
                    <label class="label" for="company_name">Company name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Company name">
                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                </div>
                <div class="error company_name"></div>
            </div>
            <div class="form-group basic animated">
                <div class="input-wrapper">
                    <label class="label" for="address">Your Address</label>
                    <input type="text" class="form-control" id="address" name="address" placeholder="Your Address">
                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                </div>
                <div class="error address"></div>
            </div>
            <!--<div class="form-group basic animated">
                <div class="input-wrapper">
                    <label class="label" for="dept_id">Department</label>
                    <select class="form-control custom-selec" id="dept" name="dept_id">
                        <option value="">Select Department</option>
                        <option value="1">Administration</option>
                        <option value="2">Production</option>
                    </select>
                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                </div>
                <div class="error vname"></div>
            </div>-->
            <div class="form-group basic animated">
                <div class="input-wrapper">
                    <label class="label" for="wtm">Whome To Meet?</label>
                    <select class="form-control custom-selec single-select" id="wtm" name="wtm">
                        <option value="">Select Person</option>
                        <?php
                            if(!empty($empList))
                            {
                                foreach($empList as $row)
                                {
                                    echo '<option value="'.$row->id.'">'.$row->emp_name.'</option>';
                                }
                            }
                        ?>
                    </select>
                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                </div>
                <div class="error wtm"></div>
            </div>

            <div class="form-group basic animated">
                <div class="input-wrapper">
                    <label class="label" for="purpose">Purpose</label>
                    <textarea id="purpose" name="purpose" rows="2" class="form-control" placeholder="Purpose"></textarea>
                    <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                </div>
                <div class="error purpose"></div>
            </div>
        </div>
    </div>
    <div class="mt-1">
        <button type="button" class="btn btn-success btn-block" onclick="store('appointmentForm')" style="border-radius:0px;height:40px;">Get Appointment</button>
    </div>
    </form>
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
        $(document).ready(function(){
            //$(".single-select").comboSelect();
        });
        function store(formId){
        	// var fd = $('#'+formId).serialize();
        	var form = $('#'+formId)[0];
        	var fd = new FormData(form);
        	$.ajax({
        		url: base_url + 'api/v1/visitorLogs/save',
        		data:fd,
        		type: "POST",
        		processData:false,
        		contentType:false,
        		dataType:"json",
        	}).done(function(data){
        		if(data.status===0){
        			$(".error").html("");
        			$.each( data.message, function( key, value ) {$("."+key).html(value);});
        		}else{
        			window.location.href = base_url + 'api/v1/visitorLogs/waitingPage/' + data.insert_id;
        		}
        				
        	});
        }
    </script>

</body>

</html>
