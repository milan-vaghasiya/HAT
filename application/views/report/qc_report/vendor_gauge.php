<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <h4 class="card-title pageHeader"><?=$pageHeader?></h4>
                            </div>
							<div class="col-md-2">
                                <input type="date" name="from_date" id="from_date" class="form-control" value="<?=date('Y-m-01')?>" />
								<div class="error fromDate"></div>
							</div> 
							<div class="col-md-2">
                                <input type="date" name="to_date" id="to_date" class="form-control" value="<?=date('Y-m-d')?>" />
								<div class="error toDate"></div>
						    </div>   
                            <div class="col-md-4">  
                                <div class="input-group">
                                    <select name="party_id" id="party_id" class="form-control single-select" style="width:70%">
                                        <option value="">Select Vendor</option>
                                        <option value="0">In House</option>
                                        <?php
    										foreach($vendorData as $row):
    											echo '<option value="'.$row->id.'">'.$row->party_name.'</option>';
    										endforeach;  
                                        ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
    								        <i class="fas fa-sync-alt"></i> Load
    							        </button>
                                    </div>
                                </div>
                                
                            </div> 
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">.
									<tr class="text-center">
										<th colspan="10">VENDOR GAUGE REPORT</th>
									</tr>
									<tr class="text-center">
										<th style="min-width:50px;">Sr No.</th>
										<th style="min-width:100px;">Challan No.</th>
										<th style="min-width:50px;">Challan Date</th>
										<th style="min-width:50px;">Party Name</th>
										<th style="min-width:50px;">Item name</th>
										<th style="min-width:50px;">Qty</th>
										<th style="min-width:100px;">Return Date</th>
										<th style="min-width:100px;">Return Qty</th>
										<th style="min-width:100px;">Pending Qty</th>
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
	jpReportTable('reportTable');
	$(document).on('click','.loaddata',function(e){
		$(".error").html("");
		var valid = 1;
		var party_id = $("#party_id").val();
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		$(".party_id").html("");
		if($("#party_id").val() == ""){$(".party_id").html("Vendor is required.");valid=0;}
		if($("#from_date").val() == ""){$(".fromDate").html("From Date is required.");valid=0;}
		if($("#to_date").val() == ""){$(".toDate").html("To Date is required.");valid=0;}
		if(valid)
		{
			$.ajax({
				url: base_url + controller + '/getVendorGaugeData',
				data: {party_id:party_id, from_date:from_date, to_date:to_date},
				type: "POST",
				dataType:'json',
				success:function(data){
					if(data.status===0){
						$(".error").html("");
						$.each( data.message, function( key, value ) {$("."+key).html(value);});
					} else {
						$("#reportTable").dataTable().fnDestroy();
						$("#theadData").html(data.theadData);
						$("#tbodyData").html(data.tbody);
						jpReportTable('reportTable');
					}
				}
			});
		}
	});
});
</script>