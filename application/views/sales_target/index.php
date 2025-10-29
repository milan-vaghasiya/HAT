<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="card-title"><?=$headData->pageTitle?></h4>
                            </div>
                            <!-- <div class="col-md-4 form-group">
								<select name="sales_executive" id="sales_executive" class="form-control single-select">
									<option value="">Sales Executive</option>
									<?php   
										foreach($salesExecutives as $row): 
											echo '<option value="'.$row->id.'">'.$row->emp_name.'</option>';
										endforeach; 
									?>
								</select>
								<div class="error sales_executive"></div>
							</div> -->
                            <div class="col-md-2 form-group">
								<input type= "hidden"  name="sales_executive" id="sales_executive" value="">
								<select name="month" id="month" class="form-control single-select">
									<option value="">Month</option>
									<?php   
										foreach($monthData as $row): 
											echo '<option value="'.$row['val'].'">'.$row['label'].'</option>';
										endforeach; 
									?>
								</select>
								<div class="error month"></div>
							</div>
                            <div class="col-md-2 form-group">  
                                <button type="button" class="btn waves-effect waves-light btn-success btn-block loaddata" title="Load Data"><i class="fas fa-sync-alt"></i> Load</button>
                            </div>       
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <form id="targetDataForm">
							<div class="hiddenInputs"></div>
							<div class="table-responsive">
								<table id='targetTable' class="table table-bordered jpDataTable">
									<thead class="thead-info">
										<tr>
											<th style="width:5%;">#</th>
											<th>Customer</th>
											<th>Contact Detail</th>
											<th>Business Target</th>
											<!--<th>Recovery Target</th>-->
										</tr>
									</thead>
									<tbody class="salesTargetData"></tbody>
									<tfoot id="tfootData">
										<th></th>
										<th></th>
										<th></th>
										<th></th>
									</tfoot>
								</table>
							</div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<div class="bottomBtn bottom-25 right-25">
    <button type="button" class="btn btn-primary btn-rounded font-bold permission-write save-form saveTargets" style="letter-spacing:1px;">Submit</button>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
	jpDataTable('targetTable');
	$(document).on("click",".loaddata",function(){
        var sales_executive = $("#sales_executive").val();
        var month = $("#month").val();

		$.ajax({
			url:base_url + controller + '/getTargetRows',
			type:'post',
			data:{sales_executive:sales_executive,month:month},
			dataType:'json',
			success:function(data)
			{
				if(data.status===0){
					$(".error").html("");
					$.each( data.message, function( key, value ) {$("."+key).html(value);});
				}else {
					$("#salesTargetData").html("");$(".hiddenInputs").html("");
					$('#targetTable').DataTable().clear().destroy();
					$(".salesTargetData").html(data.targetData);
					$(".hiddenInputs").html(data.hiddenInputs);
					$("#tfootData").html(data.tfoot);
					jpDataTable('targetTable');
				}
				
			}
		});
	});
	
	$(document).on("click",".saveTargets",function(){
        var form = $('#targetDataForm')[0];
		var fd = new FormData(form);
		$.ajax({
			url: base_url + controller + '/saveTargets',
			data:fd,
			type: "POST",
			processData:false,
			contentType:false,
			dataType:"json",
		}).done(function(data){
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		});
	});
});
</script>