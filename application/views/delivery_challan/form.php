<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Delivery Challan</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveDeliveryChallan">
                            <div class="col-md-12">
								<input type="hidden" name="dc_id" value="<?=(!empty($challanData->id))?$challanData->id:""?>" />

								<input type="hidden" name="entry_type" value="5">

								<input type="hidden" name="reference_entry_type" value="<?=(!empty($challanData->from_entry_type))?$challanData->from_entry_type:$from_entry_type?>">

								<input type="hidden" name="reference_id" value="<?=(!empty($challanData->ref_id))?$challanData->ref_id:$ref_id?>">

								<div class="row form-group">
									<div class="col-md-2">
										<label for="dc_no">DC No.</label>
                                        <div class="input-group">
                                            <input type="text" name="dc_prefix" id="dc_prefix" class="form-control req" value="<?=(!empty($challanData->trans_prefix))?$challanData->trans_prefix:$trans_prefix?>" />
										    <input type="text" name="dc_no" class="form-control" placeholder="Enter DC No." value="<?=(!empty($challanData->trans_no))?$challanData->trans_no:$nextTransNo?>" readonly />
                                        </div>
										
									</div>
									<div class="col-md-2">
										<label for="dc_date">DC Date</label>
										<input type="date" id="dc_date" name="dc_date" class=" form-control req" placeholder="dd-mm-yyyy" value="<?=(!empty($challanData->trans_date))?$challanData->trans_date:$maxDate?>"  min="<?=$startYearDate?>" max="<?=$maxDate?>"  />
										
									</div>
									<div class="col-md-2">
										<label for="order_type">Order Type</label>
										<select name="order_type" id="order_type" class="form-control req">
											<option value="1" <?=(!empty($challanData->order_type) && $challanData->order_type == 1)?"selected":((!empty($orderMaster->order_type) && $orderMaster->order_type == 1)?"selected":"")?>>Manufacturing</option>
											<option value="2" <?=(!empty($challanData->order_type) && $challanData->order_type == 2)?"selected":((!empty($orderMaster->order_type) && $orderMaster->order_type == 2)?"selected":"")?>>Job Work</option>
										</select>
									</div>
									<div class="col-md-6">
										<label for="party_id">Party Name</label>
										<div for="party_id1" class="float-right">	
											<!--<a href="javascript:void(0)" class="text-primary font-bold createDCFromGRN permission-write1" id="grnDiv" datatip="GRN" flow="down">+ GRN</a>-->
											<a href="javascript:void(0)" class="text-primary font-bold createDeliveryChallan permission-write1" id="soDiv0" datatip="Sales Order" flow="down">+ Sales Order</a>
										</div>
										<select name="party_id" id="party_id" class="form-control single-select partyOptions req">
											<option value="">Select Party</option>
											<?php
												foreach($customerData as $row):
													$selected = (!empty($challanData->party_id) && $challanData->party_id == $row->id)?"selected":((!empty($orderMaster->party_id) && $orderMaster->party_id == $row->id)?"selected":"");
													
													$party_name = (!empty($row->party_code))?"[".$row->party_code."] ".$row->party_name:$row->party_name;
													echo "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".$party_name."</option>";
												endforeach;
											?>
										</select>
										<input type="hidden" name="party_name" id="party_name" value="<?=(!empty($challanData->party_name))?$challanData->party_name:((!empty($orderMaster->party_name))?$orderMaster->party_name:"")?>">

										<input type="hidden" name="party_state_code" id="party_state_code" value="<?=(!empty($challanData->party_state_code))?$challanData->party_state_code:((!empty($orderMaster->gstin))?substr($orderMaster->gstin,0,2):"")?>">
									</div>
								</div>
								<div class="row form-group">
									
									

                                    <div class="col-md-3 form-group">
										<label>Dispatched Through (Transport)</label>
										<select name="dispatched_through" id="dispatched_through" class="form-control single-select">
											<option value="">Select Transport</option> 
											<?php
												foreach($transportData as $row):
													echo '<option value="'.$row->transport_name.'">'.$row->transport_name.'</option>';
												endforeach;
											?>
										</select>
									</div>
									<div class="col-md-3">
										<label for="so_no">SO. NO.</label>
										<input type="text" name="so_no" class="form-control" placeholder="Enter SO. NO." value="<?= (!empty($challanData->doc_no))?$challanData->doc_no:(!empty($soTransNo)?$soTransNo:"")?>" />
									</div>
									<div class="col-md-3">
										<label>L.R. No.</label>
										<input type="text" name="lr_no" id="lr_no" value="<?=(!empty($challanData->lr_no))?$challanData->lr_no:''?>" class="form-control" />
										
									</div>
                                    <div class="col-md-3">
										<label>Vehicle No.</label>
										<input type="text" name="vehicle_no" id="vehicle_no" value="<?=(!empty($challanData->vehicle_no))?$challanData->vehicle_no:''?>" class="form-control" />
									</div>
								</div>
                                <div class="row form-group">
									<div class="col-md-3">
										<label for="order_file">Box Type</label>
										<select name="order_file" id="order_file" class="form-control">
											<!--<option value="Crate" <?=(!empty($challanData->order_file) && $challanData->order_file == "Crate")?"selected":""?>>Crate</option>
											<option value="Box" <?=(!empty($challanData->order_file) && $challanData->order_file == "Box")?"selected":""?>>Box</option>
											<option value="Bag" <?=(!empty($challanData->order_file) && $challanData->order_file == "Bag")?"selected":""?>>Bag</option>-->
											<?php
												foreach($boxTypes as $value):
													$selected = (!empty($challanData->order_file) && $challanData->order_file == $value) ? "selected" : "";
													
													echo "<option value='".$value."' ".$selected.">".$value."</option>";
												endforeach;
											?>
										</select>
									</div>
                                    <div class="col-md-3 form-group">
										<label>No. Of Packets</label>
										<input type="text" name="total_packet" id="total_packet" value="<?=(!empty($challanData->total_packet))?$challanData->total_packet:''?>" class="form-control" />
									</div>
									<div class="col-md-3">
										<label>Total Weight</label>
										<input type="number" name="net_weight" id="net_weight" value="<?=(!empty($challanData->net_weight))?$challanData->net_weight:''?>" class="form-control floatOnly" />
									</div>
									<div class="col-md-3 form-group">
										<label>Process</label>
										<input type="text" name="vou_name_s" id="vou_name_s" value="<?=(!empty($challanData->vou_name_s))?$challanData->vou_name_s:''?>" class="form-control" />
									</div>
                                </div>
								<div class="row form-group">
									<div class="col-md-2">
										<label>Heat No.</label>
										<input type="text" name="vou_name_l" id="vou_name_l" value="<?=(!empty($challanData->vou_name_l))?$challanData->vou_name_l:(!empty($dcHeatNo)?$dcHeatNo:"")?>" class="form-control floatOnly" />
									</div>
                                    <div class="col-md-10 form-group">
                                        <label for="remark">Remark</label>
                                        <input type="text" name="remark" class="form-control" value="<?=(!empty($challanData->remark))?$challanData->remark:""?>"/>
                                    </div>
                                </div>
							</div>
							<hr>
							<div class="col-md-12 row">
                                <div class="col-md-6"><h4>Item Details : </h4></div>
                                <div class="col-md-6"><button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button></div>
                            </div>
							<div class="col-md-12 mt-3">
                                <div class="error item_name_error"></div>
								<div class="row form-group">
									<div class="table-responsive ">
										<table id="invoiceItems" class="table table-striped table-borderless">
											<thead class="table-info">
												<tr>
													<th style="width:5%;">#</th>
													<th>Item Name</th>
													<th>Qty.</th>
													<th>W.O. No.</th>
													<th>Remark</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
												<tr id="noData">
													<td colspan="6" class="text-center">No data available in table</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>								
							</div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveChallan('saveDeliveryChallan');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="itemModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Add or Update Item</h4>
				<!--<button type="button" id="grnItems" class="btn btn-sm waves-effect waves-light btn-outline-info float-right">GRN</button>-->
                <button type="button" class="close ml-1" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="challanItemForm">	
                    <div class="col-md-12">
                        <div class="row form-group">                          

							<div id="itemInputs">
								<input type="hidden" name="trans_id" id="trans_id" value="" />
								<input type="hidden" name="from_entry_type" id="from_entry_type" value="" />
								<input type="hidden" name="ref_id" id="ref_id" value="" />
								<input type="hidden" name="stock_eff" id="stock_eff" value="1">
								<input type="hidden" name="oldQty" id="oldQty" value="">
								
                                <input type="hidden" name="item_name" id="item_name" value="" />
								<input type="hidden" name="item_type" id="item_type" value="" />
								<input type="hidden" name="item_code" id="item_code" value="" />
								<input type="hidden" name="item_desc" id="item_desc" value="" />
                                <input type="hidden" name="unit_id" id="unit_id" value="">
								<input type="hidden" name="unit_name" id="unit_name" value="" />
                                <input type="hidden" name="hsn_code" id="hsn_code" value="" />
								<input type="hidden" name="gst_per" id="gst_per" value="" />
								<input type="hidden" name="price" id="price" value="" />
								<input type="hidden" name="row_index" id="row_index" value="">
                            </div>

                            <div class="col-md-12 form-group">
                                <label for="item_id">Product Name</label>
								<!--<div for="party_id1" class="float-right">	
									<span class="dropdown float-right">
										<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>
										<div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
											<div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
											
											<a class="dropdown-item leadActionStatic addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-lg" data-function="addProduct/1" data-controller="products" data-class_name="itemOptions" data-form_title="Add Product" > + Product</a>
										</div>
									</span>
								</div>-->
                                <select name="item_id" id="item_id" class="form-control single-select itemOptions req">
                                    <option value="">Select Product Name</option>
                                    <?php
                                        foreach($itemData as $row):		
                                            echo "<option value='".$row->id."' data-row='".json_encode($row)."'>[".$row->item_code."] ".$row->item_name."</option>";
                                        endforeach;                                        
                                    ?>
                                </select>								
                            </div>
							
                            <div class="col-md-6 form-group">
                                <label for="item_remark">Qty.</label>
                                <input type="text" name="qty" id="qty" class="form-control" value="" readonly/>
                            </div>
							<div class="col-md-6 form-group">
                                <label for="rej_qty">Rejected Qty.</label>
                                <input type="text" name="rej_qty" id="rej_qty" class="form-control" value="" />
                            </div>
							
                            <div class="col-md-12 form-group">
                                <label for="item_remark">Remark</label>
                                <textarea rows="2" name="item_remark" id="item_remark" class="form-control"></textarea>
                            </div>

                        </div>
						<hr>
						<div class="row form-group">
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead class="thead-info">
										<tr>
											<th>#</th>
											<th>Location</th>
											<th>Batch No.</th>
											<th>TC No.</th>
											<th>Stock Qty.</th>
											<th>Dispatch Qty.</th>
										</tr>
									</thead>
									<tbody id="batchData">
										<tr>
											<td colspan="6" class="text-center">No data available in table</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
                    </div>
					<input type="hidden" name="grn_data" id="grn_data" value="" />
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save" data-fn="save"><i class="fa fa-check"></i> Save</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-primary saveItem btn-save-close" data-fn="save_close"><i class="fa fa-check"></i> Save & Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="grnItemModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title">GRN Items</h4>
			</div>
			<div class="modal-body">
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-3 form-group">
							<label for="grn_id">Challan No.</label>
							<select name="grn_id" id="grn_id" class="form-control single-select">
								<option value="">Select Challan No.</option>
							</select>
						</div>
						<div class="col-md-4 form-group">
							<label for="grn_item_id">Item Name</label>
							<select name="grn_item_id" id="grn_item_id" class="form-control single-select">
								<option value="" data-remaining_qty="">Select Item Name</option>
							</select>
						</div>
						<div class="col-md-3 form-group">
							<label for="grn_qty">Qty</label>
							<input type="number" name="grn_qty" id="grn_qty" class="form-control floatOnly" min="0" value="0">
						</div>
						<div class="col-md-2 form-group">
							<label for="">&nbsp;</label>
							<button type="button" class="btn btn-outline-success waves-effect waves-light btn-block addGrnItem"><i class="fas fa-plus"></i> Add</button>
						</div>
					</div>
					<hr>
					<form id="grnItemForm">
						<div class="row">						
							<div class="col-md-12">
								<div class="table-responsive">
									<table id='grnItemTable' class="table table-bordered">
										<thead class="thead-info" id="theadData">
											<tr>
												<th>#</th>
												<th>GRN NO.</th>	
												<th>Item Name</th>
												<th>Qty.</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody id="grnItemTableData">
											<tr id="noData"><td class="text-center" colspan="5">No data available in table</td></tr>
										</tbody>								
									</table>
								</div>
							</div>						
						</div>
					</form>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn waves-effect waves-light btn-outline-success saveGrnItems" data-dismiss="modal"><i class="fa fa-check"></i> OK</button>
				<button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document" style="max-width:65%;">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Create Challan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_so" method="post" action="">
                <div class="modal-body">
                    <div class="col-md-12"><b>Party Name : <span id="partyName"></span></b></div>
                    <input type="hidden" name="party_id" id="party_id_so" value="">
                    <input type="hidden" name="party_name" id="party_name_so" value="">
                    <input type="hidden" name="from_entry_type" id="from_entry_type" value="4">
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">SO. No.</th>
                                        <th class="text-center">SO. Date</th>
                                        <th class="text-center">Cust. PO.NO.</th>
                                        <th class="text-center">W.O. No.</th>
                                        <th class="text-center">Part Code</th>
                                        <th class="text-center">Qty.</th>
                                    </tr>
                                </thead>
                                <tbody id="orderData">
                                    <tr>
                                        <td class="text-center" colspan="7">No Data Found</td>
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

<div class="modal fade" id="grnListModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document" style="max-width:65%;">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Create Challan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_grn" method="post" action="">
                <div class="modal-body">
                    <div class="col-md-12"><b>Party Name : <span id="partyNameDC"></span></b></div>
                    <input type="hidden" name="party_id" id="party_id_dc" value="">
                    <input type="hidden" name="party_name" id="party_name_dc" value="">
                    <input type="hidden" name="from_entry_type" id="from_entry_type" value="4">
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">GRN No.</th>
                                        <th class="text-center">GRN Date</th>
                                        <th class="text-center">Challan No.</th>
                                        <th class="text-center">Part Code</th>
                                        <th class="text-center">Qty.</th>
                                    </tr>
                                </thead>
                                <tbody id="grnData">
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
<script src="<?php echo base_url();?>assets/js/custom/delivery-challan-form.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>
<?php
	if(!empty($challanData->itemData)):
		foreach($challanData->itemData as $row):
			$row->row_index = "";
			$row->trans_id = $row->id;
			$row->oldQty = $row->qty;
			$row->location_id = explode(",",$row->location_id);
			$row->batch_no = explode(",",$row->batch_no);
			$row->batch_qty = explode(",",$row->batch_qty);
			$row->packing_trans_id = explode(",",$row->rev_no);
			$row = json_encode($row);
			echo '<script>AddRow('.$row.');</script>';
		endforeach;
	endif;

	if(!empty($orderItems)){
		foreach($orderItems as $row):
			$row->qty = $row->qty - $row->dispatch_qty;
			if(!empty($row->qty)):
				$row->trans_id = "";
				$row->row_index = "";
				$row->from_entry_type = $row->entry_type;
				$row->ref_id = $row->id;
				$row->hsn_code = (!empty($row->hsn_code))?$row->hsn_code:"";
				$row->location_id = "";
				$row->batch_no = "";
				$row->stock_eff = "1";
				$row->oldQty = $row->qty;
				$row = json_encode($row);
				echo '<script>AddRow('.$row.');</script>';
			endif;
		endforeach;
	}

	if(!empty($grnItems)){
		foreach($grnItems as $row):
			$row->qty = $row->qty - $row->dc_qty;
			if(!empty($row->qty)):
				$row->trans_id = "";
				$row->row_index = "";
				$row->from_entry_type = 0;
				$row->ref_id = $row->id;
				$row->hsn_code = (!empty($row->hsn_code))?$row->hsn_code:"";
				$row->location_id = "";
				$row->batch_no = "";
				$row->stock_eff = "1";
				$row->oldQty = $row->qty;
				$row = json_encode($row);
				echo '<script>AddRow('.$row.');</script>';
			endif;
		endforeach;
	}

	//Done By Karmi @15/04/2022
	if(!empty($challanData->order_type) && $challanData->order_type == 2){
		echo '<script>
			$("#soDiv").hide();
			$("#grnDiv").show();
		</script>';
	}
	else{
		echo '<script>
			$("#soDiv").show();
			$("#grnDiv").hide();
		</script>';
	}
?>