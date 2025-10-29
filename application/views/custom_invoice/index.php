<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="statusTab('customInvoiceTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('customInvoiceTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">Custom Invoice</h4>
                            </div>
                            <div class="col-md-4"> 
								<a href="<?=base_url($headData->controller."/addInvoice")?>" class="btn waves-effect waves-light btn-outline-primary float-right permission-write"><i class="fa fa-plus"></i> Add Custom Invoice</a>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='customInvoiceTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<div class="modal fade" id="print_dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" style="min-width:30%;">
		<div class="modal-content animated zoomIn border-light">
			<div class="modal-header bg-light">
                <h5 class="modal-title text-dark"><i class="fa fa-print"></i> Print Scomet</h5>
				<button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="printModel" method="post" action="<?=base_url($headData->controller.'/scometPrint')?>" target="_blank">
				<div class="modal-body">
                <div class="col-md-12">
                    <div class="row">
                        <input type="hidden" name="id" id="id" value="" />
                        <div class="col-md-12 form-group">
                        <select name="shipment" id="shipment" class="form-control req">
                            <option value="By Air" >By Air</option>
                            <option value="By Sea" >By Sea</option>
                        </select>
                        </div>
                     </div>
                    </div>
				</div>
				<div class="modal-footer">
					<a href="#" data-dismiss="modal" class="btn btn-secondary"><i class="fa fa-times"></i> Close</a>
					<button type="submit" class="btn btn-success" onclick="closeModal('print_dialog');"><i class="fa fa-print"></i> Print</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
    $(document).ready(function(){
    
    	<?php if(!empty($printID)): ?>
    		$("#printModel").attr('action',base_url + controller + '/scometPrint');
    		$("#printsid").val(<?=$printID?>);
    		$("#print_dialog").modal();
    	<?php endif; ?>
    
    	$(document).on("click",".printScomet",function(){
    		$("#printModel").attr('action',base_url + controller + '/scometPrint');
    		$("#id").val($(this).data('id'));
    		$("#print_dialog").modal();
    	});		
    });
</script>