<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Proforma Invoice</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveProformaInvoice">
                            <div class="col-md-12">
								<input type="hidden" name="proforma_id" value="<?=(!empty($invoiceData->id))?$invoiceData->id:""?>" />

								<input type="hidden" name="entry_type" value="9">

								<input type="hidden" name="reference_entry_type" id="reference_entry_type" value="<?=(!empty($invoiceData->from_entry_type))?$invoiceData->from_entry_type:$from_entry_type?>">

								<input type="hidden" name="reference_id" value="<?=(!empty($invoiceData->ref_id))?$invoiceData->ref_id:$ref_id?>">

								<input type="hidden" name="gst_type" id="gst_type" value="<?=(!empty($invoiceData->gst_type))?$invoiceData->gst_type:$gst_type?>">

								<div class="row form-group">
									<div class="col-md-3">
										<label for="inv_no">PI No.</label>
                                        <div class="input-group">
                                            <input type="text" name="inv_prefix" id="inv_prefix" class="form-control req" value="<?=(!empty($invoiceData->trans_prefix))?$invoiceData->trans_prefix:$trans_prefix?>" />
										    <input type="text" name="inv_no" class="form-control" placeholder="Enter Invoice No." value="<?=(!empty($invoiceData->trans_no))?$invoiceData->trans_no:$nextTransNo?>" readonly />
                                        </div>
										
									</div>
									<div class="col-md-3">
										<label for="inv_date">PI Date</label>
										<input type="date" id="inv_date" name="inv_date" class=" form-control req" placeholder="dd-mm-yyyy" value="<?=(!empty($invoiceData->trans_date))?$invoiceData->trans_date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />
									</div>
									<div class="col-md-6">
										<label for="party_id">Party Name</label>
										<div for="party_id1" class="float-right">	
											<span class="dropdown float-right">
												<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>
												<div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
													<div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
													
													<a class="dropdown-item leadAction addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-xl" data-function="addParty/1" data-controller="parties" data-class_name="partyOptions" data-form_title="Add Customer">+ Customer</a>
													
												</div>
											</span>
										</div>
										<select name="party_id" id="party_id" class="form-control single-select partyOptions req">
											<option value="">Select Party</option>
											<?php
												foreach($customerData as $row):
													$selected = (!empty($invoiceData->party_id) && $invoiceData->party_id == $row->id)?"selected":((!empty($invMaster->id) && $invMaster->id == $row->id)?"selected":"");
													echo "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".$row->party_name."</option>";
												endforeach;
											?>
										</select>

										<input type="hidden" name="party_name" id="party_name" value="<?=(!empty($invoiceData->party_name))?$invoiceData->party_name:((!empty($invMaster->party_name))?$invMaster->party_name:"")?>">

										<input type="hidden" name="party_state_code" id="party_state_code" value="<?=(!empty($invoiceData->party_state_code))?$invoiceData->party_state_code:((!empty($invMaster->gstin))?substr($invMaster->gstin,0,2):"")?>">
									</div>
								</div>
								<div class="row form-group">
									<div class="col-md-3 form-group">
										<label for="gst_applicable">GST Applicable</label>
										<select name="gst_applicable" id="gst_applicable" class="form-control req">
											<option value="1" <?=(!empty($invoiceData) && $invoiceData->gst_applicable == 1)?"selected":""?>>Yes</option>
											<option value="0" <?=(!empty($invoiceData) && $invoiceData->gst_applicable == 0)?"selected":""?>>No</option>
										</select>
									</div>

									<div class="col-md-3 form-group">
										<label for="sales_type">Sales Type</label>
										<select name="sales_type" id="sales_type" class="form-control">
											<option value="1" <?=(!empty($invoiceData->sales_type) && $invoiceData->sales_type == 1)?"selected":""?>>Manufacturing (Domestics)</option>
											<option value="2" <?=(!empty($invoiceData->sales_type) && $invoiceData->sales_type == 2)?"selected":""?>>Manufacturing (Export)</option>
											<option value="3" <?=(!empty($invoiceData->sales_type) && $invoiceData->sales_type == 3)?"selected":""?>>Jobwork (Domestics)</option>
										</select>
									</div>
									<!--<div class="col-md-3">
										<label for="challan_no">Challan No.</label>
										<input type="text" name="challan_no" class="form-control" placeholder="Enter Challan No." value="<?=(!empty($invoiceData->challan_no))?$invoiceData->challan_no:""?>" />
									</div>-->
									<div class="col-md-3 form-group">
										<label for="so_no">SO. NO.</label>
										<input type="text" name="so_no" class="form-control" placeholder="Enter SO. NO." value="<?=(!empty($invoiceData->doc_no))?$invoiceData->doc_no:(isset($orderData) &&!empty(($orderData->so_no))?$orderData->so_no:"")?>" />
									</div>
									<div class="col-md-4">
										<label>Referance By</label>
										<input type="text" name="ref_by" id="ref_by" value="<?=(!empty($invoiceData->ref_by))?$invoiceData->ref_by:''?>" class="form-control" />
									</div>
								</div>
								<!--<div class="row form-group">
									<div class="col-md-3">
										<label>Total Packets</label>
										<input type="text" name="total_packet" id="total_packet" value="<?=(!empty($invoiceData->total_packet))?$invoiceData->total_packet:''?>" class="form-control" />
										
									</div>
									<div class="col-md-3 form-group">
										<label>Dispatched Through (Transport)</label>
										<input type="text" name="transport" id="transport" value="<?=(!empty($invoiceData->transport_name))?$invoiceData->transport_name:''?>" class="form-control" />
										
									</div>
									<div class="col-md-6 form-group">
										<label>Destination</label>
										<input type="text" name="supply_place" id="supply_place" value="<?=(!empty($invoiceData->supply_place))?$invoiceData->supply_place:""?>" class="form-control" />
										
									</div>
								</div>-->
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
													<th>HSN Code</th>
													<th>Qty.</th>
													<th>Unit</th>
													<th>Price</th>
													<th class="igstCol">IGST</th>
													<th class="cgstCol">CGST</th>
													<th class="sgstCol">SGST</th>
													<th>Disc.</th>
													<th class="amountCol">Amount</th>
													<th class="netAmtCol">Amount</th>
													<th>Remark</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
												<?php 
													if(!empty($invoiceData->itemData)): 
													$i=1;
													foreach($invoiceData->itemData as $row):
														if($this->uri->segment(2) == "addSalesInvoiceOnSalesOrder"):
															$row->id = "";
														endif;
												?>
													<tr>
														<td style="width:5%;">
															<?=$i?>
														</td>
														<td>
															<?=$row->item_name?>
															<input type="hidden" name="item_id[]" value="<?=$row->item_id?>">
															<input type="hidden" name="item_name[]" value="<?=htmlentities($row->item_name)?>">
															<input type="hidden" name="trans_id[]" value="<?=$row->id?>">
															<input type="hidden" name="from_entry_type[]" value="<?=$row->from_entry_type?>">
															<input type="hidden" name="ref_id[]" value="<?=$row->ref_id?>">
															<input type="hidden" name="stock_eff[]" value="<?=$row->stock_eff?>">
															
															<input type="hidden" name="item_type[]" value="<?=$row->item_type?>" /><input type="hidden" name="item_code[]" value="<?=$row->item_code?>" /><input type="hidden" name="item_desc[]" value="<?=$row->item_desc?>" /><input type="hidden" name="gst_per[]" value="<?=$row->gst_per?>" />
															<div class="error batch_no<?=$i?>"></div>
														</td>
														<td>
															<?=$row->hsn_code?>
															<input type="hidden" name="hsn_code[]" value="<?=$row->hsn_code?>">
														</td>
														<td>
															<?=$row->qty?>
															<input type="hidden" name="qty[]" value="<?=$row->qty?>">
															<div class="error qty<?=$i?>"></div>
														</td>
														<td>
															<?=$row->unit_name?>
															<input type="hidden" name="unit_id[]" value="<?=$row->unit_id?>">
															<input type="hidden" name="unit_name[]" value="<?=$row->unit_name?>">
														</td>
														<td>
															<?=$row->price?>
															<input type="hidden" name="price[]" value="<?=$row->price?>">
															<div class="error price<?=$i?>"></div>
														</td>
														<td class="cgstCol">
															<?=$row->cgst_amount?>(<?=$row->cgst_per?>%) 
															<input type="hidden" name="cgst_amt[]" value="<?=$row->cgst_amount?>">
															<input type="hidden" name="cgst[]" value="<?=$row->cgst_per?>">
														</td>
														<td class="sgstCol">
															<?=$row->sgst_amount?>(<?=$row->sgst_per?>%) 
															<input type="hidden" name="sgst_amt[]" value="<?=$row->sgst_amount?>">
															<input type="hidden" name="sgst[]" value="<?=$row->sgst_per?>">
														</td>
														<td class="igstCol">
															<?=$row->igst_amount?>(<?=$row->igst_per?>%) 
															<input type="hidden" name="igst_amt[]" value="<?=$row->igst_amount?>">
															<input type="hidden" name="igst[]" value="<?=$row->igst_per?>">
														</td>
														<td>
															<?=$row->disc_amount?>(<?=$row->disc_per?>%)  
															<input type="hidden" name="disc_per[]" value="<?=$row->disc_per?>">
															<input type="hidden" name="disc_amt[]" value="<?=$row->disc_amount?>">
														</td>
														<td class="amountCol">	
															<?=$row->taxable_amount?>
															<input type="hidden" name="amount[]" value="<?=$row->taxable_amount?>">
														</td>
														<td class="netAmtCol">
															<?=$row->net_amount?>
															<input type="hidden" name="net_amount[]" value="<?=$row->net_amount?>">
														</td>
														<td>
															<?=$row->item_remark?>
															<input type="hidden" name="item_remark[]" value="<?=$row->item_remark?>">
														</td>
														<td class="text-center" style="width:10%;">
															<?php 
																$row->trans_id = $row->id;
																$row = json_encode($row);
															?>
															<button type="button" onclick='Edit(<?=$row?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>
															
															<button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
														</td>
													</tr>
												<?php $i++; endforeach; else: ?>
    												<!--<tr id="noData">-->
    												<!--	<td colspan="13" class="text-center">No data available in table</td>-->
    												<!--</tr>-->
												<?php endif; ?>
											</tbody>
										</table>
									</div>
								</div>
								<hr>
								<div class="row form-group">
									<div class="col-md-6">
										<div class="row">
											<div class="col-md-6 form-group">
												<label class="freight">Freight</label>
												<input type="number" name="freight" id="freight" class="form-control floatOnly" min="0" value="<?=(!empty($invoiceData->freight_amount))?$invoiceData->freight_amount:"0"?>" />
											</div>
											<div class="col-md-6 form-group">
												<label for="apply_round">Apply Round Off</label>
												<select name="apply_round" id="apply_round" class="form-control single-select">
													<option value="0" <?=(!empty($invoiceData) && $invoiceData->apply_round == 0)?"selected":""?>>Yes</option>
													<option value="1" <?=(!empty($invoiceData) && $invoiceData->apply_round == 1)?"selected":""?>>No</option>
												</select>
											</div>
											<div class="col-md-12 form-group">
												<label for="remark">Remark</label>
												<textarea name="remark" class="form-control" rows="2"><?=(!empty($invoiceData->remark))?$invoiceData->remark:""?></textarea>
											</div>
											<div class="col-md-6 form-group">
												<button type="button" class="btn btn-outline-success waves-effect btn-block" data-toggle="modal" data-target="#termModel">Terms & Conditions (<span id="termsCounter">0</span>)</button>
												<div class="error term_id"></div>
											</div>
										</div>
									</div>
									<div class="col-md-6 text-right">
										<table class="table table-borderless text-right">
											<tbody id="summery">
												<tr>
													<th class="text-right">Sub Total :</th>
													<td class="subTotal" style="width:30%;"><?=(!empty($invoiceData->taxable_amount))?number_format($invoiceData->taxable_amount,2):"0.00"?></td>
												</tr>
												<tr>
													<th class="text-right">Freight :</th>
													<td class="freight_amt" style="width:30%;"><?=(!empty($invoiceData->freight_amount))?$invoiceData->freight_amount:"0.00"?></td>
												</tr>
												<tr>
													<th class="text-right">Round Off :</th>
													<td class="roundOff" style="width:30%;"><?=(!empty($invoiceData->round_off_amount))?$invoiceData->round_off_amount:"0.00"?></td>
												</tr>
											</tbody>
											<tfoot>
												<tr>
													<th class="text-right">Net Amount :</th>
													<td class="netAmountTotal" style="width:30%;"><?=(!empty($invoiceData->net_amount))?$invoiceData->net_amount:"0.00"?></td>
												</tr>
											</tfoot>
										</table>
										<div id="hiddenInputs">
											<input type="hidden" name="amount_total" id="amount_total" value="<?=(!empty($invoiceData->taxable_amount))?$invoiceData->taxable_amount:"0.00"?>" />
											<input type="hidden" name="freight_amt" id="freight_amt" value="<?=(!empty($invoiceData->freight_amount))?$invoiceData->freight_amount:"0.00"?>" />
											<input type="hidden" name="disc_amt_total" id="disc_amt_total" value="<?=(!empty($invoiceData->disc_amount))?$invoiceData->disc_amount:"0.00"?>" />
											<input type="hidden" name="igst_amt_total" id="igst_amt_total" value="<?=(!empty($invoiceData->igst_amount))?$invoiceData->igst_amount:"0.00"?>" />
											<input type="hidden" name="cgst_amt_total" id="cgst_amt_total" value="<?=(!empty($invoiceData->cgst_amount))?$invoiceData->cgst_amount:"0.00"?>" />
											<input type="hidden" name="sgst_amt_total" id="sgst_amt_total" value="<?=(!empty($invoiceData->sgst_amount))?$invoiceData->sgst_amount:"0.00"?>" />
											<input type="hidden" name="round_off" id="round_off" value="<?=(!empty($invoiceData->round_off_amount))?$invoiceData->round_off_amount:"0.00"?>" />
											<input type="hidden" name="net_amount_total" id="net_amount_total" value="<?=(!empty($invoiceData->net_amount))?$invoiceData->net_amount:"0.00"?>" />
										</div>
									</div>
								</div>
							</div>
							<div class="modal fade" id="termModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
								<div class="modal-dialog modal-lg" role="document" style="max-width:70%;">
									<div class="modal-content animated slideDown">
										<div class="modal-header">
											<h4 class="modal-title">Terms & Conditions</h4>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										</div>
										<div class="modal-body">
											<div class="col-md-12 mb-10">
												<table id="terms_condition" class="table table-bordered dataTable no-footer">
													<thead class="thead-info">
														<tr>
															<th style="width:10%;">#</th>
															<th style="width:25%;">Title</th>
															<th style="width:65%;">Condition</th>
														</tr>
													</thead>
													<tbody>
														<?php
															if(!empty($terms)):
																$termaData = (!empty($invoiceData->terms_conditions))?json_decode($invoiceData->terms_conditions):array();
																$i=1;$j=0;
																foreach($terms as $row):
																	$checked = "";
																	$disabled = "disabled";
																	if(in_array($row->id,array_column($termaData,'term_id'))):
																		$checked = "checked";
																		$disabled = "";
																		$row->conditions = $termaData[$j]->condition;
																		$j++;
																	endif;
																	if(empty($invoiceData->id) AND $row->default_terms == 1):
    																	$checked = "checked";
    																	$disabled = "";
    																endif;
														?>
															<tr>
																<td style="width:10%;">
																	<input type="checkbox" id="md_checkbox<?=$i?>" class="filled-in chk-col-success termCheck" data-rowid="<?=$i?>" check="<?=$checked?>" <?=$checked?> />
																	<label for="md_checkbox<?=$i?>"><?=$i?></label>
																</td>
																<td style="width:25%;">
																	<?=$row->title?>
																	<input type="hidden" name="term_id[]" id="term_id<?=$i?>" value="<?=$row->id?>" <?=$disabled?> />
																	<input type="hidden" name="term_title[]" id="term_title<?=$i?>" value="<?=$row->title?>" <?=$disabled?> />
																</td>
																<td style="width:65%;">
																	<input type="text" name="condition[]" id="condition<?=$i?>" class="form-control" value="<?=$row->conditions?>" <?=$disabled?> />
																</td>
															</tr>
														<?php
																	$i++;
																endforeach;
															else:
														?>
														<tr>
															<td class="text-center" colspan="3">No data available in table</td>
														</tr>
														<?php
															endif;
														?>
													</tbody>
												</table>
											</div>
										</div>
										<div class="modal-footer">	
											<button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
											<button type="button" class="btn waves-effect waves-light btn-outline-success" data-dismiss="modal"><i class="fa fa-check"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveInvoice('saveProformaInvoice');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="itemModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Add or Update Item</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="invoiceItemForm">
                    <div class="col-md-12">

                        <div class="row form-group">

							<div id="itemInputs">
								<input type="hidden" name="trans_id" id="trans_id" value="" />
								<input type="hidden" name="from_entry_type" id="from_entry_type" value="">
								<input type="hidden" name="ref_id" id="ref_id" value="">
								<input type="hidden" name="stock_eff" id="stock_eff" value="1">

								<input type="hidden" name="item_name" id="item_name" value="" />
								<input type="hidden" name="item_type" id="item_type" value="" />
								<input type="hidden" name="item_code" id="item_code" value="" />
								<input type="hidden" name="item_desc" id="item_desc" value="" />
								<input type="hidden" name="hsn_code" id="hsn_code" value="" />
								<input type="hidden" name="gst_per" id="gst_per" value="" />
								<input type="hidden" name="row_index" id="row_index" value="">
                            </div> 

                            <div class="col-md-12 form-group">
                                <label for="item_id">Product Name</label>
								<div for="party_id1" class="float-right">	
									<span class="dropdown float-right">
										<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>
										<div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
											<div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
																						
											<a class="dropdown-item leadActionStatic addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-lg" data-function="addProduct/1" data-controller="products" data-class_name="itemOptions" data-form_title="Add Product" > + Product</a>
											
										</div>
									</span>
								</div>
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
                                <label for="unit_id">Unit</label>
                                <input type="text" name="unit_name" id="unit_name" class="form-control" value="" readonly/>
								<input type="hidden" name="unit_id" id="unit_id" value="" >
                            </div>

							<div class="col-md-6 form-group">
                                <label for="qty">Quantity</label>
                                <input type="number" name="qty" id="qty" class="form-control floatOnly req" value="0">
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="price">Price</label>
                                <input type="number" name="price" id="price" class="form-control floatOnly req" value="0" />                                 
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="disc_per">Disc Per.</label>
                                <input type="number" name="disc_per" id="disc_per" class="form-control floatOnly" value="0" />
                            </div>

                            <div class="col-md-12 form-group">
                                <label for="item_remark">Remark</label>
                                <input type="text" name="item_remark" id="item_remark" class="form-control" value="" />
                            </div>
                        </div>
                    </div>          
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save" data-fn="save"><i class="fa fa-check"></i> Save</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-warning saveItem btn-save-close" data-fn="save_close"><i class="fa fa-check"></i> Save & Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/proforma-invoice-form.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>
<?php
	if(!empty($invItems)){
		foreach($invItems as $row):
			$row->qty = $row->qty - $row->dispatch_qty;
			if(!empty($row->qty)):
				$row->trans_id = "";
				$row->from_entry_type = $row->entry_type;
				$row->ref_id = $row->id;
				$row->hsn_code = (!empty($row->hsn_code))?$row->hsn_code:"";
				$row->gst_type = $gst_type;
				if(empty($row->disc_per)):
					$row->disc_per = 0;
					$row->disc_amt = 0;
					$row->amount = round($row->qty * $row->price,2);
				else:
					$row->disc_amt = round((($row->qty * $row->price) * $row->disc_per)/100,2);
					$row->amount = round(($row->qty * $row->price) - $row->disc_amt,2);
				endif;	
				$row->igst_per = $row->gst_per;	
				$row->igst_amt = round(($row->amount * $row->igst_per)/100,2);	
				
				$row->cgst_per = round(($row->igst_per/2),2);
				$row->cgst_amt = round(($row->igst_amt/2),2);
				$row->sgst_per = round(($row->igst_per/2),2);
				$row->sgst_amt = round(($row->igst_amt/2),2);
				
				$row->net_amount = $row->amount + $row->igst_amt;
				$row->stock_eff = ($row->stock_eff == 1)?0:1;
				$row = json_encode($row);
				echo '<script>AddRow('.$row.');</script>';
			endif;
		endforeach;
	}
?>