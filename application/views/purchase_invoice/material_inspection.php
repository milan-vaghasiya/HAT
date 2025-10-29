<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Material Inspection On Goods Received</h4>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='purchaseInvoiceMaterialInspectionTable' class="table table-bordered ssTable" data-url='/purchaseMaterialInspectionList'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="inspectionModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Material Inspaction</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="inspectedMaterial">
                    <div class="col-md-12">
						<div class="row">
							<div class="col-md-3">
								<label for="">Inv No. : </label>
								<input type="text" id="invNo" class="form-control" value="" readonly />
								<input type="hidden" name="purchase_id" id="purchase_id" value="" />
							</div>
							<div class="col-md-3">
								<label for="">Inv Date</label>
								<input type="text" id="invDate" class="form-control" value="" readonly />
							</div>
							<div class="col-md-6">
								<label for="">Item Name</label>
								<input type="text" id="itemName" class="form-control" value="" readonly />
							</div>
						</div>
					</div>
					<hr>
					<div class="col-md-12">
						<div class="row">
							<div class="table-responsive">
								<table class="table table-bordered align-items-center">
									<thead class="thead-info">
										<tr>
											<th style="width:5%;">#</th>
											<th>Received Qty</th>
											<th>OK Qty</th>
											<th>Reject Qty</th>
											<th>Scrape Qty</th>
                                            <th>Short Qty</th>
										</tr>
									</thead>
									<tbody id="recivedItems">
										<tr>
											<td class="text-center" colspan="6">No data available in table</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
                </form>
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save" onclick="inspectedMaterialSave('inspectedMaterial');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/purchase-material-inspection.js?v=<?=time()?>"></script>