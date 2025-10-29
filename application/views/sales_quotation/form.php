<?php $this->load->view('includes/header'); ?>

<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Sales Quotation</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveSalesEnquiry">
                            <div class="col-md-12">

								<input type="hidden" name="quote_id" id="quote_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
								
								<input type="hidden" name="quote_rev_no" id="quote_rev_no" value="<?=(!empty($dataRow->quote_rev_no))?$dataRow->quote_rev_no:0?>" />
								
								<input type="hidden" name="is_revision" id="is_revision" value="<?=(!empty($is_revision))?$is_revision:0?>" />
								
								<input type="hidden" name="form_entry_type" id="form_entry_type" value="2" />

								<input type="hidden" name="reference_entry_type" id="reference_entry_type" value="<?=(!empty($dataRow->from_entry_type))?$dataRow->from_entry_type:$from_entry_type?>" />

								<input type="hidden" name="reference_id" id="reference_id" value="<?=(!empty($dataRow->ref_id))?$dataRow->ref_id:$ref_id?>">

								<input type="hidden" name="gst_type" id="gst_type" value="<?=(!empty($dataRow))?$dataRow->gst_type:"1"?>">

								<div class="row form-group">

									<div class="col-md-3">
										<label for="quote_no">Quote No.</label>
										<div class="input-group mb-3">
											<input type="text" name="quote_prefix" class="form-control req" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$trans_prefix?>" readonly />
											<input type="text" name="quote_no" class="form-control" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$nextTransNo?>" readonly />
										</div>
									</div>

									<div class="col-md-2">
										<label for="trans_date">Quotation Date</label>
										<input type="date" id="trans_date" name="trans_date" class=" form-control req" placeholder="dd-mm-yyyy" aria-describedby="basic-addon2" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" <?=(!empty($is_revision) || !empty($dataRow->quote_rev_no))?'readonly':''?> />	
									</div>

									<div class="col-md-4">
										<label for="party_id">Customer Name</label>
										<select name="party_id" id="party_id" class="form-control select2 req">
											<option value="">Select or Enter Customer Name</option>
											<?php
												foreach($customerData as $row):
													$selected = "";
													if(!empty($dataRow->party_id) && $dataRow->party_id == $row->id){$selected = "selected";}
													if(!empty($lead_id) && $lead_id == $row->id){$selected = "selected";}
													if(!empty($quotationData->party_id) && $quotationData->party_id == $row->id){$selected="selected";}

													echo "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".$row->party_name."</option>";
												endforeach;
												if(!empty($dataRow) && $dataRow->party_id == 0):
													echo '<option value="0" data-row="" selected>'.$dataRow->party_name.'</option>';
												endif;

												if(!empty($quotationData) && $quotationData->party_id == 0):
													echo '<option value="0" data-row="" selected>'.$quotationData->party_name.'</option>';
												endif;
											?>
										</select>

										<input type="hidden" name="party_name" id="party_name" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:((!empty($quotationData->party_name))?$quotationData->party_name:"")?>" />
									    <div class="error party_id"></div>
									</div>
									<div class="col-md-3 form-group">
										<label for="contact_person">Contact Person</label>
										<input type="text" name="contact_person" id="contact_person" class="form-control req" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:((!empty($quotationData->contact_person))?$quotationData->contact_person:"")?>" />
									</div>
									
								<?php if(empty($is_revision) && empty($dataRow->quote_rev_no)):?>
									<div class="col-md-3 form-group">
										<label for="contact_no">Contact Number</label>
										<input type="text" name="contact_no" id="contact_no" class="form-control req" value="<?=(!empty($dataRow->party_mobile))?$dataRow->party_mobile:((!empty($quotationData->party_mobile))?$quotationData->party_mobile:"")?>" />
									</div>
									<div class="col-md-3 form-group">
										<label for="contact_email">Contact Email</label>
										<input type="text" name="contact_email" id="contact_email" class="form-control req" value="<?=(!empty($dataRow->contact_email))?$dataRow->contact_email:((!empty($quotationData->contact_email))?$quotationData->contact_email:"")?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="party_phone">Party Phone</label>
										<input type="text" name="party_phone" id="party_phone" class="form-control" value="<?=(!empty($dataRow->party_phone))?$dataRow->party_phone:((!empty($quotationData->party_phone))?$quotationData->party_phone:"")?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="party_email">Party Email</label>
										<input type="text" name="party_email" id="party_email" class="form-control" value="<?=(!empty($dataRow->party_email))?$dataRow->party_email:((!empty($quotationData->party_email))?$quotationData->party_email:"")?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="sales_executive">Sales Executive</label>
										<select name="sales_executive" id="sales_executive" class="form-control single-select" >
											<option value="0">Sales Executive</option>
											<?php
												foreach($salesExecutives as $row):
													$selected = (!empty($dataRow->sales_executive) && $dataRow->sales_executive == $row->id)?"selected":"";
													echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
												endforeach;
											?>
										</select>
									</div>
									
								<?php else: ?>
								
									<div class="col-md-2 form-group">
										<label for="doc_date">Revision Date</label>
										<input type="date" id="doc_date" name="doc_date" class=" form-control req" placeholder="dd-mm-yyyy" aria-describedby="basic-addon2" min="<?=(!empty($dataRow->doc_date))?$dataRow->doc_date:date("Y-m-d")?>" max="<?=date("Y-m-d")?>" value="<?=(!empty($dataRow->doc_date))?$dataRow->doc_date:date("Y-m-d")?>" />	
									</div>
									<div class="col-md-2 form-group">
										<label for="contact_no">Contact Number</label>
										<input type="text" name="contact_no" id="contact_no" class="form-control req" value="<?=(!empty($dataRow->party_mobile))?$dataRow->party_mobile:((!empty($quotationData->party_mobile))?$quotationData->party_mobile:"")?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="contact_email">Contact Email</label>
										<input type="text" name="contact_email" id="contact_email" class="form-control req" value="<?=(!empty($dataRow->contact_email))?$dataRow->contact_email:((!empty($quotationData->contact_email))?$quotationData->contact_email:"")?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="party_phone">Party Phone</label>
										<input type="text" name="party_phone" id="party_phone" class="form-control" value="<?=(!empty($dataRow->party_phone))?$dataRow->party_phone:((!empty($quotationData->party_phone))?$quotationData->party_phone:"")?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="party_email">Party Email</label>
										<input type="text" name="party_email" id="party_email" class="form-control" value="<?=(!empty($dataRow->party_email))?$dataRow->party_email:((!empty($quotationData->party_email))?$quotationData->party_email:"")?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="sales_executive">Sales Executive</label>
										<select name="sales_executive" id="sales_executive" class="form-control single-select" >
											<option value="0">Sales Executive</option>
											<?php
												foreach($salesExecutives as $row):
													$selected = (!empty($dataRow->sales_executive) && $dataRow->sales_executive == $row->id)?"selected":"";
													echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
												endforeach;
											?>
										</select>
									</div>
									
								<?php endif; ?>
								
									
									<div class="col-md-3 form-group">
										<label for="lr_no">Currency</label>
										<select name="lr_no" id="lr_no" class="form-control single-select" >
											<option value="">Select Currency</option>
											<?php $i=1; foreach($currencyData as $row):
												$selected = (!empty($dataRow->lr_no) && trim($dataRow->lr_no) == trim($row->currency)) ? "selected" : "";
												if(empty($dataRow->lr_no) && trim($row->currency) == "INR"){$selected = "selected";}
											?>
											<option value="<?=trim($row->currency)?>" <?=$selected?> ><?=$row->currency?> [<?=$row->code2000?> - <?=$row->currency_name?>]</option>
											<?php endforeach; ?>
										</select>
									</div>
									<div class="col-md-3 form-group">
										<label for="party_pincode">Pincode</label>
										<input type="text" id="party_pincode" name="party_pincode" class=" form-control" value="<?=(!empty($dataRow->party_pincode))?$dataRow->party_pincode:((!empty($quotationData->party_pincode))?$quotationData->party_pincode:"")?>" />	
									</div>
									<div class="col-md-2 form-group">
										<label for="ref_by">Referance By</label>
                                        <input type="text" id="ref_by" name="ref_by" class=" form-control" value="<?=(!empty($dataRow->ref_by))?$dataRow->ref_by:((!empty($quotationData->ref_by))?$quotationData->ref_by:"")?>" />	
									</div>
									<div class="col-md-2 form-group">
										<label for="challan_no">Dev. Apply</label>
										<select name="challan_no" id="challan_no" class="form-control singel-select">
											<option value="1" data-charge="0" <?=(!empty($dataRow->challan_no) && $dataRow->challan_no == 1)?"selected":""?>>NO</option>	
											<option value="2" data-charge="<?=(!empty($devCharge))?$devCharge:""?>" <?=(!empty($dataRow->challan_no) && $dataRow->challan_no == 2)?"selected":""?>>YES</option>
										</select>
									</div>
									<div class="col-md-2 form-group">
										<label for="dev_charge">Dev. Charge</label>
										<input type="text" name="dev_charge" id="dev_charge" class="form-control floatOnly" value="<?=(!empty($dataRow->net_weight))?$dataRow->net_weight:"0"?>" <?=(!empty($dataRow->challan_no) && $dataRow->challan_no == 2)?"":"readonly"?> />
									</div>
									<div class="col-md-2 form-group">
										<label for="gst_applicable">GST Applicable</label>
										<select name="gst_applicable" id="gst_applicable" class="form-control req">
											<option value="1" <?=(!empty($dataRow) && $dataRow->gst_applicable == 1)?"selected":""?>>Yes</option>
											<option value="0" <?=(!empty($dataRow) && $dataRow->gst_applicable == 0)?"selected":""?>>No</option>
										</select>
									</div>
									<div class="col-md-5 form-group">
										<label for="remark">Remark</label>
										<input type="text" name="remark" id="remark" class="form-control" placeholder="Enter Remark" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
									</div>
									<div class="col-md-5 form-group">
										<label for="party_address">Address</label>
										<input type="text" id="party_address" name="party_address" class=" form-control" value="<?=(!empty($dataRow->party_address))?$dataRow->party_address:((!empty($quotationData->party_address))?$quotationData->party_address:"")?>" />	
									</div>
								</div>
							</div>
							<hr>
                            <div class="col-md-12 row">
                                <div class="col-md-6"><h4>Item Details : </h4></div>
								<!-- <div class="col-md-4 error term_id"></div> -->
								<div class="col-md-6">
									<button type="button" class="btn btn-outline-success waves-effect float-right add-item" data-toggle="modal" data-target="#itemModel"><i class="fa fa-plus"></i> Add Item</button>
									<!-- <button type="button" class="btn btn-outline-success waves-effect float-right mr-2" data-toggle="modal" data-target="#termModel">Terms & Conditions (<span id="termsCounter">0</span>)</button> -->
								</div>
							</div>														
							<div class="col-md-12 mt-3">
								<div class="error item_name_error"></div>
								<div class="row form-group">
									<div class="table-responsive ">
										<table id="salesEnqItems" class="table table-striped table-borderless">
											<thead class="thead-info">
												<tr>
													<th style="width:5%;">#</th>
													<th>Item Name</th>
													<th>Drg. No.</th>
													<th>Rev. No.</th>
													<th>Part No.</th>
													<th>Qty.</th>
													<th>Unit</th>
													<th>Price</th>
													<th class="igstCol">IGST</th>
													<th class="cgstCol">CGST</th>
													<th class="sgstCol">SGST</th>
													<th class="discCol">Disc.</th>
													<th class="amountCol">Amount</th>
													<th class="netAmtCol">Amount</th>
													<th style="width:15%;">Remark</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
												<?php 										
													if(!empty($dataRow->itemData)): 
													$i=1;
													foreach($dataRow->itemData as $row):
												?>
													<tr>
														<td style="width:5%;">
															<?=$i?>
														</td>
														<td>
															<?="[ ".$row->item_code." ] ".$row->item_name?>
															<input type="hidden" name="item_name[]" value="<?=htmlentities($row->item_name)?>">
															<input type="hidden" name="item_id[]" value="<?=$row->item_id?>">
															<input type="hidden" name="trans_id[]" value="<?=$row->id?>">
															<input type="hidden" name="from_entry_type[]" value="<?=$row->from_entry_type?>">
															<input type="hidden" name="ref_id[]" value="<?=$row->ref_id?>">

															<input type="hidden" name="item_type[]" value="<?=$row->item_type?>" />
															<input type="hidden" name="item_code[]" value="<?=$row->item_code?>" />
															<input type="hidden" name="item_desc[]" value="<?=$row->item_desc?>" />
															<input type="hidden" name="hsn_code[]" value="<?=$row->hsn_code?>" />
															<input type="hidden" name="gst_per[]" value="<?=$row->gst_per?>" />
															
														</td>
														<td>
															<?=$row->drg_rev_no?>
															<input type="hidden" name="drg_rev_no[]" value="<?=$row->drg_rev_no?>">
														</td>
														<td>
															<?=$row->rev_no?>
															<input type="hidden" name="rev_no[]" value="<?=$row->rev_no?>">
														</td>
														<td>
															<?=$row->batch_no?>
															<input type="hidden" name="batch_no[]" value="<?=$row->batch_no?>">
														</td>
														<td>
															<?=$row->qty?>
															<input type="hidden" name="qty[]" class="form-control" value="<?=$row->qty?>">
														</td>
														<td>
															<?=$row->unit_name?>
															<input type="hidden" name="unit_id[]" value="<?=$row->unit_id?>">
															<input type="hidden" name="unit_name[]" value="<?=$row->unit_name?>">
														</td>
														<td>
															<?=$row->price?>
															<input type="hidden" name="price[]" value="<?=$row->price?>">
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
														<td class="discCol">
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
															<input type="hidden" name="grn_data[]" value="<?=$row->grn_data?>">
														</td>
														
														<td class="text-center" style="width:10%;">
															<?php if(empty($row->confirm_by)): ?>
															    <?php 
																	unset($row->quote_rev_no);
                                                                    $row->trans_id = $row->id;
                                                                    $row = json_encode($row);
                                                                ?>
                                                                <button type="button" onclick='Edit(<?=$row?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>
                                                                
															    <button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
															<?php endif; ?>
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
							</div>
							<hr>
							<div class="col-md-12">
								<div class="row form-group">
									<div class="col-md-6">
										<div class="row">
											<div class="col-md-6 form-group">
												<label class="freight">Freight</label>
												<input type="number" name="freight" id="freight" class="form-control floatOnly" min="0" value="<?=(!empty($dataRow->freight_amount))?$dataRow->freight_amount:"0"?>" />
											</div>
											<div class="col-md-6 form-group">
												<label for="apply_round">Apply Round Off</label>
												<select name="apply_round" id="apply_round" class="form-control single-select">
													<option value="0" <?=(!empty($dataRow) && $dataRow->apply_round == 0)?"selected":""?>>Yes</option>
													<option value="1" <?=(!empty($dataRow) && $dataRow->apply_round == 1)?"selected":""?>>No</option>
												</select>
											</div>
											<div class="col-md-12 form-group">
												<label for="remark">Remark</label>
												<textarea name="remark" class="form-control" rows="2"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
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
													<td class="roundOff" style="width:30%;"><?=(!empty($dataRow->round_off_amount))?$dataRow->round_off_amount:"0.00"?></td>
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
													<tbody>
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
																	<textarea rows="1" type="text" name="condition[]" id="condition<?=$i?>" class="form-control" <?=$disabled?> ><?=$row->conditions?></textarea>
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
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveEnquiry('saveSalesEnquiry');" ><i class="fa fa-check"></i> Save</button>
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
                <form id="quotationItemForm">
                    <div class="col-md-12">

                        <div class="row form-group">
                            <input type="hidden" name="trans_id" id="trans_id" value="" />
							<input type="hidden" name="from_entry_type" id="from_entry_type" value="0">
							<input type="hidden" name="ref_id" id="ref_id" value="0">
							<input type="hidden" name="item_type" id="item_type" value="1" />
							<input type="hidden" name="item_code" id="item_code" value="" />
							<input type="hidden" name="item_name" id="item_name" value="" />
							<input type="hidden" name="item_desc" id="item_desc" value="" />
							<input type="hidden" name="hsn_code" id="hsn_code" value="" />
							<input type="hidden" name="gst_per" id="gst_per" value="" />
							<input type="hidden" name="unit_name" id="unit_name" value="" />
							<input type="hidden" name="row_index" id="row_index" value="">
							
                            <!-- <div class="col-md-12 form-group">
                                <label for="item_name">Item Name</label>
								<input type="text" name="item_name" id="item_name" class="form-control" value="" />
								<input type="hidden" name="item_id" id="item_id" value="" />									
                            </div> -->
							<div class="col-md-12 form-group">
                                <label for="item_id">Product Name</label>
								<!-- <div for="party_id1" class="float-right">	
									<span class="dropdown float-right">
										<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>
										<div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
											<div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
																						
											<a class="dropdown-item leadActionStatic addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-lg" data-function="addProduct/1" data-controller="products" data-class_name="itemOptions" data-form_title="Add Product" > + Product</a>											
										</div>
									</span>
								</div> -->
                                <select name="item_id" id="item_id" class="form-control select2 itemOptions req">
                                    <option value="">Select Product Name</option>
                                    <?php
                                        foreach($itemData as $row):
                                            echo "<option value='".$row->id."' data-row='".json_encode($row)."'>[".$row->item_code."] ".htmlentities($row->item_name)."</option>";
                                        endforeach;                                        
                                    ?>
                                </select>   
								<div class="error item_name"></div>                             
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="qty">Quantity</label>
                                <input type="number" name="qty" id="qty" class="form-control floatOnly req" value="0">
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="unit_id">Unit</label>                                
								<select name="unit_id" id="unit_id" class="form-control single-select req">
									<option value="">--</option>
									<?php
										foreach($unitData as $row):
											echo '<option value="'.$row->id.'">'.$row->unit_name.'</option>';
										endforeach;
									?>
								</select>		
                            </div>

                            <div class="col-md-4 form-group">
								<label for="price">Price</label>
								<input type="number" name="price" id="price" class="form-control floatOnly req" value="0">
							</div>
							
							<div class="col-md-4 form-group">
								<label for="drg_rev_no">Drg. No.</label>
								<input type="text" name="drg_rev_no" id="drg_rev_no" class="form-control" value="" />
							</div>
							<div class="col-md-4 form-group">
								<label for="rev_no">Rev. No.</label>
								<input type="text" name="rev_no" id="rev_no" class="form-control" value="" />
							</div>
							<div class="col-md-4 form-group">
								<label for="batch_no">Part No.</label>
								<input type="text" name="batch_no" id="batch_no" class="form-control" value="" />
							</div>

                            <div class="col-md-12 form-group">
                                <label for="item_remark">Item Remark</label>
                                <input type="text" name="item_remark" id="item_remark" class="form-control" value="">
                            </div>
							
							<div class="col-md-12 form-group">
                                <label for="grn_data">Product Description</label>
                                <input type="text" name="grn_data" id="grn_data" class="form-control" value="">
                            </div>

                        </div>
                    </div>          
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save" data-fn="save"><i class="fa fa-check"></i> Save</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-warning saveItem btn-save-close" data-fn="save_close"><i class="fa fa-check"></i> Save & Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close btn-efclose" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/sales-quotation-form.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>
<?php
	if(!empty($quotationData->itemData)){
		foreach($quotationData->itemData as $row):
			unset($row->quote_rev_no);
			if(empty($row->trans_status) AND $row->feasible == "Yes"):
				$row->trans_id = "";
				$row->from_entry_type = $row->entry_type;
				$row->ref_id = $row->id;				
				$row->amount = $row->qty * $row->price;
				$row->disc_amt = 0;
				$row->cgst_per = 0;
				$row->cgst_amt = 0;
				$row->sgst_per = 0;
				$row->sgst_amt = 0;
				$row->igst_per = 0;
				$row->igst_amt = 0;
				$row = json_encode($row);
				echo '<script>AddRow('.$row.');</script>';
			endif;
		endforeach;
	}
?>