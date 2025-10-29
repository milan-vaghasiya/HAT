<?php $this->load->view('app/includes/header');?>

<div class="appHeader bg-primary">
	<div class="left">
		<a href="#" class="headerButton goBack text-white">
			<ion-icon name="chevron-back-outline" role="img" class="md hydrated" aria-label="chevron back outline"></ion-icon>
		</a>
	</div>
	<div class="pageTitle text-white">Store</div>
	
</div>
<div class="extraHeader pe-0 ps-0 text-center">
    <ul class="nav nav-tabs lined" role="tablist">
        <li class="nav-item" >Product</li>
        <li class="nav-item" id="ProductItemName"><?=$product_name?></li>
        <li class="nav-item">Unstored Qty.</li>
        <li class="nav-item" id="unstoredQty"><?=$pending_qty?></li>
    </ul>
    
</div>

<div id="appCapsule" class=" extra-header-active full-height">
    <div class="card">
        <div class="card-body">
            <form id="storeLocation">
                <div class="row">
                    <input type="hidden" name="job_id" id="job_id" value="<?=$job_id?>">
                    <input type="hidden" name="ref_id" id="ref_id" value="<?=$ref_id?>">
                    <input type="hidden" name="batch_no" id="batch_no" value="<?=$jobNo?>" />
                    <input type="hidden" name="location_id" id="location_id">
                    <div class="col form-group boxed">
                        <label for="trans_date">Date</label>
                        <input type="date" name="trans_date" id="trans_date" class="form-control" max="<?=date("Y-m-d")?>" value="<?=date("Y-m-d")?>" min="<?=$dataRow->minDate?>" >
                    </div>
                </div>
                <div class="row">
                    <div class="col form-group boxed">
                        <label for="batch_no">Heat No</label>
                        <select name="batch_no" id="batch_no" class="form-control select2">
                            <option value="">Select Heat No</option>
                            <?php
                                if(!empty($heatData)){
                                    foreach($heatData as $row){
                                        ?><option value="<?=$row->tc_no?>"><?=$row->tc_no.' [Pending Material : '.($row->in_qty-$row->out_qty).']'?></option><?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col form-group boxed">
                        <label for="qty">Qty.</label>
                        <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="" />
                    </div>
                </div>
                <div class="row">
                    <div class="col form-group boxed">
                        <button type="button" class="btn btn-outline-success me-1 mb-1 save-form" onclick="saveStoreLocation('storeLocation');"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>
            </form>
            <hr>
            <div class="row">
                <div class="col">
                <label for="">Transactions : </label>
                <div class="table-responsive">
                    <table id='storeLocationTransTable' class="table table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th class="text-center" style="width: 5%;">#</th>
                                <th class="text-center">Batch No.</th>
                                <th class="text-center">Heat No.</th>
                                <th class="text-center">Location</th>
                                <th class="text-center">Qty.</th>
                                <th class="text-center" style="width: 8%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="storeLocationData">
                            <?=$transactionData['htmlData']?>
                        </tbody>
                    </table>
                </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('app/includes/bottom_menu');?>
<?php $this->load->view('app/includes/sidebar');?>
<?php $this->load->view('app/includes/add_to_home');?>
<?php $this->load->view('app/includes/footer');?>
<script>

function saveStoreLocation(formId){
    var fd = $('#'+formId).serialize();
    $.ajax({
		url: base_url + 'production/processMovement/saveStoreLocation',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			$("#storeLocationData").html(data.htmlData);
			$("#unstoredQty").html(data.unstored_qty);
			$('#'+formId)[0].reset();
            $(".select2").select2();
            $("#DialogIconedSuccess .modal-body").html(data.message);
            $("#DialogIconedSuccess").modal('show');
		}else{
			$("#DialogIconedDanger .modal-body").html(data.message);
            $("#DialogIconedDanger").modal('show');
		}				
	});
}


function trashStockTrans(id,name='Record'){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + 'production/processMovement/deleteStoreLocationTrans',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								$("#DialogIconedSuccess .modal-body").html(data.message);
                                $("#DialogIconedSuccess").modal('show');
							}
							else
							{
								$("#storeLocationData").html(data.htmlData);
								$("#unstoredQty").html(data.unstored_qty);
								//getProcessWiseData();
								$("#DialogIconedDanger .modal-body").html(data.message);
                                $("#DialogIconedDanger").modal('show');
                                $(".select2").select2();
							}
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

</script>