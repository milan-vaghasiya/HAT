<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>
                            <div class="col-md-3">
                                <select name="fg_item_id" id="fg_item_id" class="form-control single-select">
                                    <option value="">Select Finish Good</option>
                                    <?php   
										foreach($itemDataList as $row): 
											echo '<option value="'.$row->id.'"> '.$row->item_code.'</option>';
										endforeach; 
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">  
								<div class="input-group">
									<input type="text" name="fg_qty"  id="fg_qty" class="form-control" placeholder="Enter Qty" value="" />
									<div class="input-group-append ml-2">
										<button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" data-type="0" title="Load Data">
											<i class="fas fa-sync-alt"></i> Load
										</button>
									</div>
                                    <div class="input-group-append ml-2">
                                        <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right loaddata" data-type="1" title="Load Data">
											<i class="fa fa-print"></i> Print
										</button>
                                        <!-- <a href="<?= base_url($headData->controller) ?>/printMRP" class="btn waves-effect waves-light btn-outline-primary float-right mr-2" target="_blank"><i class="fa fa-print"></i> Print</a> -->
									</div>
								</div>
								<div class="error fg_qty_error"></div>
							</div>                              
                        </div>                                         
                    </div>
                    
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th>#</th>
										<th>Item Code</th>
										<th>Description</th>
										<th>Material Type</th>
										<th>Unit</th>
										<th>Req. Qty.</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<?=$floatingMenu?>
<script>
$(document).ready(function(){
    $(document).on('click','.loaddata',function(e){
        var type = $(this).data('type');
		$(".error").html("");
		var valid = 1;
        
		var fg_item_id = $('#fg_item_id').val();
		var fg_qty = $('#fg_qty').val();
	
		if($("#fg_item_id").val() == ""){$(".fg_item_id").html("Item is required.");valid=0;}
		if($("#fg_qty").val() == ""){$(".fg_qty_error").html("Qty is required.");valid=0;}

        var sendData = {fg_item_id:fg_item_id, fg_qty:fg_qty, type:type};
		if(valid)
		{
            if(type == 1){
				var url =  base_url + controller + '/getMaterialReqPlan/' + encodeURIComponent(window.btoa(JSON.stringify(sendData)));
				window.open(url);
				
			}else{
                $.ajax({
                    url: base_url + controller + '/getMaterialReqPlan',
                    data: sendData,
                    type: "POST",
                    dataType:'json',
                    success:function(data){
                        $("#tbodyData").html("");
                        $("#tbodyData").html(data.tbody);
                    }
                });
            }
        }
    });   
});
</script>