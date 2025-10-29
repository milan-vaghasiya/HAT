<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Sales Order</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveSalesOrder" enctype="multipart/form-data">
                        	<div class="col-md-12">
								<input type="hidden" name="order_id" id="order_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />

								<input type="hidden" name="reference_entry_type" value="<?=(!empty($dataRow->from_entry_type))?$dataRow->from_entry_type:$from_entry_type?>">

								<input type="hidden" name="reference_id" value="<?=(!empty($dataRow->ref_id))?$dataRow->ref_id:$ref_id?>">

								<input type="hidden" name="form_entry_type" value="4">

								<input type="hidden" name="gst_type" id="gst_type" value="<?=(!empty($dataRow->gst_type))?$dataRow->gst_type:"3"?>" />
								<div class="row">
									<div class="col-md-2 form-group">
										<label for="so_no">SO. No.</label>
										<input type="hidden" name="so_prefix" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$trans_prefix?>" />
										<input type="text" name="so_no" class="form-control req" placeholder="Enter SO No." value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$nextTransNo?>" readonly />										
									</div>
									<div class="col-md-2 form-group">
										<label for="so_date">SO. Date</label>
										<input type="date" id="so_date" name="so_date" class=" form-control req" placeholder="dd-mm-yyyy" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />	
									</div>
									<div class="col-md-5 form-group">
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
										<select name="party_id" id="party_id" class="form-control select2 partyOptions req" >
											<option value="">Select Party</option>
											<?php
												foreach($customerData as $row):
													$selected = (!empty($dataRow->party_id) && $dataRow->party_id == $row->id)?"selected":((!empty($orderData->party_id) && $orderData->party_id == $row->id)?"selected":"");
													echo "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".$row->party_name."</option>";
												endforeach;
											?>
										</select>
										<input type="hidden" name="party_name" id="party_name" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:((!empty($orderData->party_name))?$orderData->party_name:"")?>">										
										<input type="hidden" name="party_state_code" id="party_state_code" value="<?=(!empty($dataRow->party_state_code))?$dataRow->party_state_code:((!empty($orderData->party_state_code))?$orderData->party_state_code:"")?>">										
									</div>

                                    <div class="col-md-3 form-group">
										<label for="reference_by">Reference By</label>
										<input type="text" name="reference_by" class="form-control" placeholder="Enter Reference" value="<?=(!empty($dataRow->ref_by))?$dataRow->ref_by:((!empty($orderData->ref_by))?$orderData->ref_by:"")?>" />
									</div>

									<input type="hidden" name="order_type" id="order_type" value="<?=(!empty($dataRow->order_type))?$dataRow->order_type:1?>">

									<div class="col-md-3 form-group">
										<label for="gst_applicable">GST Applicable</label>
										<select name="gst_applicable" id="gst_applicable" class="form-control req">
											<option value="1" <?=(!empty($dataRow) && $dataRow->gst_applicable == 1)?"selected":""?>>Yes</option>
											<option value="0" <?=(!empty($dataRow) && $dataRow->gst_applicable == 0)?"selected":""?>>No</option>
										</select>
									</div>

									<div class="col-md-3 form-group">
										<label for="sales_type">Sales Type</label>
										<select name="sales_type" id="sales_type" class="form-control">
											<option value="1" <?=(!empty($dataRow->sales_type) && $dataRow->sales_type == 1)?"selected":""?>>Manufacturing (Domestics)</option>
											<option value="2" <?=(!empty($dataRow->sales_type) && $dataRow->sales_type == 2)?"selected":""?>>Manufacturing (Export)</option>
											<option value="3" <?=(!empty($dataRow->sales_type) && $dataRow->sales_type == 3)?"selected":""?>>Jobwork (Domestics)</option>
										</select>
									</div>
									<div class="col-md-3 form-group">
										<label for="cust_po_no">Cust. PO. No.</label>
										<?php
										    $cpono = "";
										    if(!empty($dataRow->doc_no)){$cpono = $dataRow->doc_no;}elseif(!empty($soTransNo)){$cpono = $soTransNo;}
										?>
										<input type="text" name="cust_po_no" id="cust_po_no" class="form-control" placeholder="Enter SO No." value="<?=$cpono?>" />
									</div>
									<div class="col-md-3 form-group">
										<label for="cust_po_date">Cust. PO. Date</label>
										<input type="date" id="cust_po_date" name="cust_po_date" class=" form-control" placeholder="dd-mm-yyyy" value="<?=(!empty($dataRow->doc_date))?$dataRow->doc_date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />	
									</div>
									<div class="col-md-4 form-group">
										<label for="trans_mode">Contact Person</label>
										<input type="text" name="trans_mode" id="trans_mode" class="form-control" placeholder="Contact Parson" value="<?=(!empty($dataRow->trans_mode))?$dataRow->trans_mode:''?>" />
									</div>
									<div class="col-md-4 form-group">
										<label for="currency">Currency</label>
										<select name="currency" id="currency" class="form-control single-select" >
											<option value="">Select Currency</option>
											<?php $i=1; foreach($currencyData as $row):
												$selected = (!empty($dataRow->currency) && trim($dataRow->currency) == trim($row->currency)) ? "selected" : "";
												if(empty($dataRow->currency) && trim($row->currency) == "INR"){$selected = "selected";}
											?>
											<option value="<?=trim($row->currency)?>" <?=$selected?> ><?=$row->currency?> [<?=$row->code2000?> - <?=$row->currency_name?>]</option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="col-md-4 form-group">
										<label for="order_image">Attachment</label>
										<input type="file" class="form-control-file" name="order_image">
									</div>

								</div>
							</div>
							<hr>
							<div class="col-md-12 row">
                                <div class="col-md-6"><h4>Item Details : </h4></div>
                                <div class="col-md-6">
									<button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button>
								</div>
                            </div>
							<div class="col-md-12 mt-3">
                                <div class="error item_name_error"></div>
								<div class="row form-group">
									<div class="table-responsive">
										<table id="salesOrderItems" class="table table-striped table-borderless">
											<thead class="thead-info">
												<tr>
													<th style="width:5%;">#</th>
													<th>Item Name</th>
													<th>HSN Code</th>
													<th>Qty.</th>
													<th>Unit</th>
													<th>Price</th>
													<th>Packing Charge</th>
													<th class="igstCol">IGST</th>
													<th class="cgstCol">CGST</th>
													<th class="sgstCol">SGST</th>
													<th class="amountCol">Amount</th>
													<th class="netAmtCol">Amount</th>
													<th>Remark</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
												<?php 
													if(!empty($dataRow->items)): 
													$i=1;
													foreach($dataRow->items as $row):
												?>
													<tr>
														<td style="width:5%;">
															<?=$i++?>
														</td>
														<td>
															<?=$row->item_name?>
															<input type="hidden" name="item_id[]" value="<?=$row->item_id?>">
															<input type="hidden" name="trans_id[]" value="<?=$row->id?>">
															<input type="hidden" name="from_entry_type[]" value="<?=$row->from_entry_type?>">
															<input type="hidden" name="ref_id[]" value="<?=$row->ref_id?>">
															<input type="hidden" name="delivery_date[]" value="<?=$row->cod_date?>">
															<input type="hidden" name="target_date[]" value="<?=$row->target_date?>">
															<input type="hidden" name="item_name[]" value="<?=htmlentities($row->item_name)?>" />

															<input type="hidden" name="item_type[]" value="<?=$row->item_type?>" />
															<input type="hidden" name="item_code[]" value="<?=$row->item_code?>" />
															<input type="hidden" name="item_desc[]" value="<?=$row->item_desc?>" />
															<input type="hidden" name="item_alias[]" value="<?=$row->item_alias?>" />
															<input type="hidden" name="material_grade[]" value="<?=$row->material_grade?>" />
															<input type="hidden" name="gst_per[]" value="<?=$row->gst_per?>" />
														</td>
														<td>
															<?=$row->hsn_code?>
															<input type="hidden" name="hsn_code[]" value="<?=$row->hsn_code?>">
															<input type="hidden" name="drg_rev_no[]" value="<?=$row->drg_rev_no?>">
														</td>
														<td>
															<?=$row->qty?>
															<input type="hidden" name="qty[]" value="<?=$row->qty?>">
															<input type="hidden" name="qty_kg[]" value="<?=$row->qty_kg?>">
														</td>
														<td>
                                                            <?=$row->unit_name?>
															<input type="hidden" name="unit_id[]" value="<?=$row->unit_id?>">
															<input type="hidden" name="unit_name[]" value="<?=$row->unit_name?>">
														</td>
														<td>
															<?=$row->price?>
															<input type="hidden" name="price[]" value="<?=$row->price?>">
															<input type="hidden" name="org_price[]" value="<?=$row->org_price?>">
															<input type="hidden" name="disc_per[]" value="<?=$row->disc_per?>">
															<input type="hidden" name="disc_amt[]" value="<?=$row->disc_amount?>">
														</td>
														<td>
															<?=$row->packing_charge?>
															<input type="hidden" name="packing_charge[]" value="<?=$row->packing_charge?>">
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
														<td class="amountCol">	
															<?=$row->taxable_amount?>
															<input type="hidden" name="amount[]" value="<?=$row->taxable_amount?>">
														</td>
														<td class="netAmtCol">
															<?=$row->net_amount?>
															<input type="hidden" name="total_amount[]" value="<?=$row->net_amount?>">
														</td>
														<td>
															<?=$row->item_remark?>
															<input type="hidden" name="item_remark[]" value="<?=$row->item_remark?>">
														</td>
														<td class="text-center" style="width:10%;">
                                                            <?php if($row->trans_status == 0):
                                                                 
                                                                $row->trans_id = $row->id;
																$row->delivery_date = $row->cod_date;
																$production_qty = $row->production_qty;
																$row = json_encode($row); 
																
																if($production_qty <= 0): ?>
																	<button type="button" onclick='Edit(<?=$row?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>
																	
																	<button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
															<?php elseif($this->loginId == 16): ?>
																	<button type="button" onclick='EditItem(<?=$row?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>
															<?php endif;
                                                          	endif; ?>
														</td>
													</tr>
												<?php endforeach; 
												endif; ?>
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
												<input type="text" name="freight" id="freight" class="form-control floatOnly" min="0" value="<?=(!empty($dataRow->freight_amount))?$dataRow->freight_amount:"0"?>" />
											</div>
											<div class="col-md-6 form-group">
												<label for="apply_round">Apply Round Off</label>
												<select name="apply_round" id="apply_round" class="form-control single-select">
													<option value="0" <?=(!empty($dataRow) && $dataRow->apply_round == 0)?"selected":""?>>Yes</option>
													<option value="1" <?=(!empty($dataRow) && $dataRow->apply_round == 1)?"selected":""?>>No</option>
												</select>
											</div>
											<div class="col-md-12 form-group">
												<label for="remark">Note</label>
												<input type="text" name="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>"/>
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
													<td class="subTotal" style="width:30%;"><?=(!empty($dataRow->taxable_amount))?number_format($dataRow->taxable_amount,2):"0.00"?></td>
												</tr>
												<tr>
													<th class="text-right">Freight :</th>
													<td class="freight_amt" style="width:30%;"><?=(!empty($dataRow->freight_amount))?$dataRow->freight_amount:"0.00"?></td>
												</tr>
												<tr>
													<th class="text-right">Round Off :</th>
													<td class="roundOff" style="width:30%;"><?=(!empty($dataRow->round_off))?$dataRow->round_off:"0.00"?></td>
												</tr>
											</tbody>
											<tfoot>
												<tr>
													<th class="text-right">Net Amount :</th>
													<td class="netAmountTotal" style="width:30%;"><?=(!empty($dataRow->net_amount))?$dataRow->net_amount:"0.00"?></td>
												</tr>
											</tfoot>
										</table>
										<div id="hiddenInputs">
											<input type="hidden" name="amount_total" id="amount_total" value="<?=(!empty($dataRow->taxable_amount))?$dataRow->taxable_amount:"0.00"?>" />
											<input type="hidden" name="freight_amt" id="freight_amt" value="<?=(!empty($dataRow->freight_amount))?$dataRow->freight_amount:"0.00"?>" />
											<input type="hidden" name="disc_amt_total" id="disc_amt_total" value="<?=(!empty($dataRow->disc_amount))?$dataRow->disc_amount:"0.00"?>" />
											<input type="hidden" name="igst_amt_total" id="igst_amt_total" value="<?=(!empty($dataRow->igst_amount))?$dataRow->igst_amount:"0.00"?>" />
											<input type="hidden" name="cgst_amt_total" id="cgst_amt_total" value="<?=(!empty($dataRow->cgst_amount))?$dataRow->cgst_amount:"0.00"?>" />
											<input type="hidden" name="sgst_amt_total" id="sgst_amt_total" value="<?=(!empty($dataRow->sgst_amount))?$dataRow->sgst_amount:"0.00"?>" />
											<input type="hidden" name="round_off" id="round_off" value="<?=(!empty($dataRow->round_off_amount))?$dataRow->round_off_amount:"0.00"?>" />
											<input type="hidden" name="net_amount_total" id="net_amount_total" value="<?=(!empty($dataRow->net_amount))?$dataRow->net_amount:"0.00"?>" />
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
													<tbody class="scrollable" style="height:20vh;">
														<?php
															if(!empty($terms)):
																$termaData = (!empty($dataRow->terms_conditions))?json_decode($dataRow->terms_conditions):array();
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
																	if(empty($dataRow->id) AND $row->default_terms == 1):
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
            											<tr><td class="text-center" colspan="3">No data available in table</td></tr>
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
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveOrder('saveSalesOrder');" ><i class="fa fa-check"></i> Save</button>
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
            <div class="modal-header" style="display:block;"><h4 class="modal-title">Add or Update Item</h4></div>
            <div class="modal-body">
                <form id="orderItemForm">
                    <div class="col-md-12">

                        <div class="row form-group">
							<div id="itemInputs">
								<input type="hidden" name="trans_id" id="trans_id" value="" />
								<input type="hidden" name="from_entry_type" id="from_entry_type" value="" />
                                <input type="hidden" name="ref_id" id="ref_id" value="" />
                                
								<input type="hidden" name="item_type" id="item_type" value="" />
								<input type="hidden" name="item_code" id="item_code" value="" />
                                <input type="hidden" name="item_name" id="item_name" value="" />
								<input type="hidden" name="item_desc" id="item_desc" value="" />
								<input type="hidden" name="unit_id" id="unit_id" value="" >
                                <input type="hidden" name="unit_name" id="unit_name" value="" />
                                <input type="hidden" name="hsn_code" id="hsn_code" value="" />
								<input type="hidden" name="gst_per" id="gst_per" value="" />
								<input type="hidden" name="production_qty" id="production_qty" value="" />
								<input type="hidden" name="qty_kg" id="qty_kg" value="0" />
								<input type="hidden" name="drg_rev_no" id="drg_rev_no" value="" />
								<input type="hidden" name="row_index" id="row_index" value="">
								<input type="hidden" name="disc_per" id="disc_per" value="0" />
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
                                <select name="item_id" id="item_id" class="form-control select2 itemOptions req">
                                    <option value="">Select Product Name</option>
                                    <?php
                                        foreach ($itemData as $row):
                                            $itmSize = (!empty($row->size)) ? ' | Size: '.$row->size : '';		
                            				$drgNo = (!empty($row->drawing_no)) ? ' | Drg. No: '.$row->drawing_no : '';	
                            				$revNo = (!empty($row->rev_no)) ? ' | Rev No: '.$row->rev_no : '';	
                            				$item_name = (!empty($row->item_code))?'['.$row->item_code.'] '.$row->item_name:$row->item_name;
                            				
                                            echo "<option value='".$row->id."' data-row='".json_encode($row)."'>".$item_name.$itmSize.$drgNo.$revNo."</option>";
                                        endforeach;                                        
                                    ?>
                                </select>                                
                            </div>
							<div class="col-md-12 form-group exp_field">
								<label for="item_alias">Product Alias</label>
								<input type="text" name="item_alias" id="item_alias" class="form-control req" maxlength="255" value="">
							</div>
							<div class="col-md-6 form-group">
                                <label for="material_grade">Material Grade</label>
                                <select name="material_grade" id="material_grade" class="form-control single-select req">
                                    <option value="">Select Material Grade</option>
                                    <?php
                                        foreach($materialGrade as $row):
                                            echo '<option value="'.$row->material_grade.'">'.$row->material_grade.'</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="qty">Quantity</label>
                                <input type="text" name="qty" id="qty" class="form-control floatOnly req" value="0">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="price">Price</label>
                                <input type="text" name="price" id="price" class="form-control floatOnly req" value="0" />
                            </div>
                            <!--<div class="col-md-4 form-group">
                                <label for="disc_per">Disc Per.</label>
                                <input type="text" name="disc_per" id="disc_per" class="form-control floatOnly" value="0" />
                            </div>-->
							<div class="col-md-6 form-group">
                                <label for="packing_charge">Packing Ch.(amt)</label>
                                <input type="text" name="packing_charge" id="packing_charge" class="form-control floatOnly" value="0" />
                            </div>
                            <div class="col-md-6 form-group">
								<label for="delivery_date">Delivery Date</label>
								<input type="date" id="delivery_date" name="delivery_date" class=" form-control req" placeholder="dd-mm-yyyy" value="<?=$maxDate?>" min="<?=$startYearDate?>" />
							</div>
							<div class="col-md-6 form-group">
								<label for="target_date">Hat Target Date</label>
								<input type="date" id="target_date" name="target_date" class=" form-control req" placeholder="dd-mm-yyyy" value="<?=$maxDate?>" min="<?=$startYearDate?>" />
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
<script src="<?php echo base_url();?>assets/js/custom/sales-order-form.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>
<?php
	if(!empty($orderItems)){
		foreach($orderItems as $row):
			if(empty($row->trans_status)):
				if(!empty($row->confirm_by)):
					$row->trans_id = "";
					$row->from_entry_type = $row->entry_type;
					$row->ref_id = $row->id;
					$row->hsn_code = (!empty($row->hsn_code))?$row->hsn_code:"";
					$row->price = $row->org_price;
					$row->org_price = 0;
					$row->amount = $row->qty * $row->price;
					$row->disc_amt = 0;
					$row->cgst_per = 0;
					$row->cgst_amt = 0;
					$row->sgst_per = 0;
					$row->sgst_amt = 0;
					$row->igst_per = 0;
					$row->igst_amt = 0;
					$row->net_amount = $row->amount;
					$row->delivery_date = date("Y-m-d");
					$row = json_encode($row);
					echo '<script>AddRow('.$row.');</script>';
				endif;
			endif;
		endforeach;
	}
?>