<!DOCTYPE html>
<html dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url()?>assets/images/favicon.png">
    <title>FEEDBACK - <?=(!empty(SITENAME))?SITENAME:""?></title>
    <!-- Custom CSS -->
    <link href="<?=base_url()?>assets/css/style.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/css/jp_helper.css" rel="stylesheet">
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <link href="<?=base_url()?>assets/libs/raty-js/lib/jquery.raty.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/css/rating.css" rel="stylesheet">
</head>

<body>
    <div class="main-wrapper">
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>
        <img  class="backdrop" src="<?=base_url('assets/images/background/akshar_bg.png')?>" alt="" />
        <section id="contact">
            <form id="feddbackForm">
                <div class="contact-box">
                    <div class="contact-links">
                        <div class="h2">
                            <img src="<?=base_url('assets/images/logo_text.png')?>" style="height:60px;" /> 
                            <span class="float-right text-right">GIVE YOUR FEEDBACK <br> <span class="fs-17">M/S. <?=((!empty($feedbackData->party_name)) ? $feedbackData->party_name : '')?></span></span>
                        </div>
                    </div>
                    <div class="contact-form-wrapper">
                        <?php if(empty($feedbackData->feedback_by)){ ?>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label class="text-right" style="width:100%;"><b>Survey Period : </b><?=formatDate($feedbackData->survey_from)?> To <?=formatDate($feedbackData->survey_to)?></label>
                                    <label style="width:100%;">Feedback Filled By : <strong class="float-right">#<?=$feedbackData->trans_prefix.sprintf('%04d',$feedbackData->trans_no)?></strong></label>
                                    <input type="text" name="feedback_by" id="feedback_by" class="form-control req" required></textarea>
                                    <input type="hidden" id="id" name="id" value="<?=((!empty($id)) ? $id : '')?>" >
                                    <div class="error feedback_by"></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="error feedback_grade"></div>
                                    <table class="table table-bordered table-striped">
                                        <tr class="thead-info"><th>#</th><th>Parameter</th><th>Feedback</th></tr>
                                        <?php
                                            $i=1;
                                            if(!empty($param))
                                            {
                                                foreach($param as $row)
                                                {
                                                    echo '<tr class="text-center">';
                                                        echo '<td>'.$i.'</td>';
                                                        echo '<td class="text-left"><a href="javascript:void()" data-id="'.$row->id.'" class="copyLink">'.$row->parameter.'</a></td>';
                                                        echo '<td>
                                                                <div id="p'.$i.'" data-id="'.$row->id.'"></div>
                                                                <input type="hidden" id="ftrans_id'.$row->id.'" name="ftrans_id[]" value="'.$row->id.'" >
                                                              </td>';
                                                    echo '</tr>';
                                                    $i++;
                                                }
                                            }
                                        ?>
                                    </table>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Any other suggestions for betterment of mutual business relation :</label>
                                    <textarea name="other_suggestions" id="other_suggestions" class="form-control" rows="2" ></textarea>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Comments (if any) :</label>
                                    <textarea name="comments" id="comments" class="form-control" rows="2" ></textarea>
                                </div>
                            </div>
                            <center><button type="button" class="btn btn-success mt-4 saveFeedback">SUBMIT FEEDBACK</button></center>
                        <?php } else { echo '<h2 class="text-center">You have submitted Feedback successfully</h2>'; }?>
                    </div>
                </div>
            </form>
        </section>
        
    </div>
<script src="<?=base_url()?>assets/libs/jquery/dist/jquery.min.js"></script>
<script src="<?=base_url()?>assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?=base_url()?>assets/libs/raty-js/lib/jquery.raty.js"></script>

<script>
    var base_url = '<?=base_url();?>';
    $.fn.raty.defaults.path = 'https://akshar.nativebittechnologies.com/';
    $(".preloader").fadeOut();
    $(document).ready(function() {
        var hintsArr = ['Needs Improvement','Satisfactory','Excellent'];
        var totalParam = '<?=count($param)?>';
        for(var i=1;i<=totalParam;i++){$('#p'+i).raty({number:3,scoreName: 'rating[p'+i+']',hints: hintsArr});}
        
        /*$('#p1').raty({number:3,scoreName: 'rating[p1]',hints: hintsArr});
        $('#p2').raty({number:3,scoreName: 'rating[p2]',hints: hintsArr});
        $('#p3').raty({number:3,scoreName: 'rating[p3]',hints: hintsArr});
        $('#p4').raty({number:3,scoreName: 'rating[p4]',hints: hintsArr});
        $('#p5').raty({number:3,scoreName: 'rating[p5]',hints: hintsArr});
        $('#p6').raty({number:3,scoreName: 'rating[p6]',hints: hintsArr});
        $('#p7').raty({number:3,scoreName: 'rating[p7]',hints: hintsArr});
        $('#p8').raty({number:3,scoreName: 'rating[p8]',hints: hintsArr});
        $('#p9').raty({number:3,scoreName: 'rating[p9]',hints: hintsArr});*/
        
        $(document).on('click','.saveFeedback',function() {
            var fd = $('#feddbackForm').serialize();
            
        	$.ajax({
        		url: base_url + 'customerFeedback/saveFeedback',
        		data:fd,
        		type: "POST",
        		dataType:"json",
        	}).done(function(data){
        		if(data.status===0){
        			$(".error").html("");
        			$.each( data.message, function( key, value ) {
        				$("."+key).html(value);
        			});
        		}else if(data.status==1){
        		    window.location.reload();
        			//toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        		}else{
        			//toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        		}
        				
        	});
        });
        $(document).on('click','.copyLink',function() {
            var id= $(this).data('id');
            var copyText = "https://akshar.nativebittechnologies.com/customerFeedback/getFeedback/" + id;
            copyText.execCommand("copy");
        });
    });
    
    

</script>
</body>

</html>