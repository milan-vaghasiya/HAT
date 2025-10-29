<?php 
    $this->load->view('includes/header');
	$etype = "6,7,8";
?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
								<div class="input-group ">
									<a href="<?=base_url("reports/accountingReport/salesRegisterReport")?>" class="btn btn-info float-right" style="border-radius:0px;">Bill Wise</a>
									<a href="<?=base_url("reports/accountingReport/salesRegisterReportItemWise")?>" class="btn btn-outline-info float-right" style="border-radius:0px;">Item Wise</a>
								</div>
                            </div>
							<div class="col-md-8"> 
							    <div class="input-group ">
							        <select id="state_code" name="state_code" class="form-control single-select" style="width: 17%;">
										<option value="">All States</option>
										<option value="1">IntraState</option>
										<option value="2">InterState</option>
									</select>
									<select name="sales_type" id="sales_type" class="form-control single-select sales_type" style="width:30%">
										<option value="">Select All</option>
										<option value="1">Manufacturing (Domestics)</option>
										<option value="2">Manufacturing (Export)</option>
										<option value="3">Jobwork (Domestics)</option>
									</select>
                                    <input type="date" name="from_date" id="from_date" class="form-control" value="<?=$startDate?>" />
                                    <div class="error fromDate"></div>
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?=$endDate?>" />
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
									        <i class="fas fa-sync-alt"></i> Load
								        </button>
                                    </div>
                                </div>
                                <div class="error toDate"></div>
                            </div>                
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='salesInvoiceTable' class="table table-bordered dtTable dtTable-cf" data-ninput='[0]' data-url='/getDTRows/'>
								<thead class="thead-info">
                                    <tr>
                                        <th class="text-center" style="width:5%;">#</th>
                                        <th class="text-center" style="width:11%;">Invoice No.</th>
                                        <th class="text-center" style="width:9%;">Invoice Date</th>
                                        <th class="text-left">Customer Name</th>
                                        <th class="text-right" style="width:12%;">Taxable Amount</th>
                                        <th class="text-right" style="width:12%;">GST</th>
                                        <th class="text-right" style="width:12%;">Net Amount</th>
                                    </tr>
                                </thead>
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
	    
		setTimeout(function(){initInvTable();},100);

		$(document).on("change","#sales_type",function(){
			$("#salesInvoiceTable").attr("data-url",'/getDTRows/'+$(this).val());
			//initInvTable();
		});
		
		$(document).on('click','.loaddata',function(){initInvTable();});
		$(document).on('change','#party_id',function(){initInvTable();});
	});

	function closeModal(modalId)
	{
		$("#"+ modalId).modal('hide');
		
		<?php if(!empty($printID)): ?>
			window.location = base_url + controller;
		<?php endif; ?>
	}
	function dtTablePdf(){initInvTable('pdf')}
	function initInvTable(pdf=""){
		var from_date = $('#from_date').val();
		var to_date = $('#to_date').val();
		var sales_type = $('#sales_type').val();
		var state_code = $('#state_code').val();
		var party_id="";
		if($('#party_id').val()){party_id = $('#party_id').val();}
		if(pdf == "")
		{
    		$('.dtTable').DataTable().clear().destroy();
    		var dataSet = {from_date:from_date, to_date:to_date,party_id:party_id,state_code:state_code}
		    defaultDtTable($('.dtTable'),dataSet);
		}
		var ctb= getInvoiceSummary(sales_type,from_date,to_date,party_id,state_code,pdf);
	}
	function getInvoiceSummary(sales_type,from_date,to_date,party_id,state_code,pdf){
		
		if(sales_type == ""){sales_type = '';}if(party_id == ""){party_id = '';}if(state_code == ""){state_code = '';}
		var postData = {sales_type:sales_type,from_date:from_date,to_date:to_date,party_id:party_id,state_code:state_code,pdf:pdf};
		//console.log(postData);
		if(pdf == "")
		{
		    var ctb = '';
			$.ajax({
				url : base_url + controller + '/getInvoiceSummary',
				type: 'post',
				data:postData,
				dataType:'json',
				success:function(data){
					var tamt = (data.taxable_amount != null) ? inrFormat(data.taxable_amount) : 0;
					var gamt = (data.gst_amount != null) ? inrFormat(data.gst_amount) : 0;//<span class="inv-head-stat">Total Gst : '+gamt+'</span>
					var namt = (data.net_amount != null) ? inrFormat(data.net_amount) : 0;
					ctb = '<span class="inv-head-select">'+data.custOption+'</span><span class="inv-head-stat">Taxable : '+tamt+'</span><span class="inv-head-stat">Net Sales : '+namt+'</span>';
					//copt = ;
					if(ctb != ""){$('div.ctbtr').html(ctb);}$('#party_id').comboSelect()
					//if(data.custOption != ""){$('div.cstfilter').html(data.custOption);$('#party_id').comboSelect();}
					//console.log('copt = '+data.custOption);
				}
			});
		}
		else
		{
		    var u = window.btoa(JSON.stringify(postData)).replace(/=+$/, "");
			var url = base_url + controller + '/getInvoiceSummary/' + encodeURIComponent(u);
			window.open(url);
		}
	}
</script>