<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <!-- <h4 class="card-title"></h4> -->
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="statusTab('salesEnquiryTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('salesEnquiryTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">Sales Quotation</h4>
                            </div>
                            <div class="col-md-4"> 
							    <?php if($shortYear == CURRENT_FYEAR): ?>
                                    <a href="<?=base_url($headData->controller."/addSalesQuotation")?>" class="btn waves-effect waves-light btn-outline-primary float-right permission-write" ><i class="fa fa-plus"></i> Add Quotation</a>
                                <?php endif; ?>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='salesEnquiryTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Quotation Revision</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_so" method="post" action="">
                <div class="modal-body">
                    <div class="col-md-12"><b>Quatation No : <span id="sq_no"></span></b></div>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Revised No</th>
                                        <th class="text-center">Date</th>
                                    </tr>
                                </thead>
                                <tbody id="orderData">
                                    <tr>
                                        <td class="text-center" colspan="3">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="SalesorderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel1">Create Sales Order</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<form id="party_salesOrder" method="post" action="">
				<div class="modal-body">
					<div class="col-md-12"><b>Party Name : <span id="partyName"></span></b></div>
					<input type="hidden" name="party_id" id="party_id_so" value="">
					<input type="hidden" name="party_name" id="party_name_so" value="">
                    <input type="hidden" name="qo_id" id="qo_id_so" value="" />
					<input type="hidden" name="from_entry_type" id="from_entry_type" value="2">
					<div class="col-md-12">
						<div class="error general"></div>
						<div class="table-responsive">
							<table id="orderTable" class="table table-bordered">
								<thead class="thead-info">
									<tr>
										<th class="text-center">#</th>
										<th class="text-center">SQ. No.</th>
										<th class="text-center">SQ. Date</th>
										<th class="text-center">Part Code</th>
										<th class="text-center">Qty.</th>
									</tr>
								</thead>
								<tbody id="salesOrderData">
									<tr>
										<td class="text-center" colspan="5">No Data Found</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
					<button type="submit" class="btn waves-effect waves-light btn-outline-success" id="btn-create"><i class="fa fa-check"></i> Create Challan</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/sales-quotation.js?v=<?=time()?>"></script>