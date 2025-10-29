<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header text-center">
						<h4><u>Tax Invoice</u></h4>
					</div>
					<div class="card-body">
						<form autocomplete="off" id="saveSalesInvoice">
							<div class="col-md-12">
								<input type="hidden" name="sales_id" id="inv_id" value="<?= (!empty($invoiceData->id)) ? $invoiceData->id : "" ?>" />

								<input type="hidden" name="entry_type" id="entry_type" value="<?= (!empty($invoiceData->entry_type)) ? $invoiceData->entry_type : ((!empty($entry_type))?$entry_type:"6") ?>">

								<input type="hidden" name="reference_entry_type" id="reference_entry_type" value="<?= (!empty($invoiceData->from_entry_type)) ? $invoiceData->from_entry_type : $from_entry_type ?>">

								<input type="hidden" name="reference_id" value="<?= (!empty($invoiceData->ref_id)) ? $invoiceData->ref_id : $ref_id ?>">

								<input type="hidden" name="gst_type" id="gst_type" value="<?= (!empty($invoiceData->gst_type)) ? $invoiceData->gst_type : $gst_type ?>">

								<div class="row form-group">
									<div class="col-md-3">
										<label for="sales_type">Sales Type</label>
										<select name="sales_type" id="sales_type" class="form-control">
											<option value="1" <?= (!empty($invoiceData->sales_type) && $invoiceData->sales_type == 1) ? "selected" : ((!empty($sales_type) && $sales_type == 1)?"selected":"") ?>>Manufacturing (Domestics)</option>
											<option value="2" <?= (!empty($invoiceData->sales_type) && $invoiceData->sales_type == 2) ? "selected" : ((!empty($sales_type) && $sales_type == 2)?"selected":"") ?>>Manufacturing (Export)</option>
											<option value="3" <?= (!empty($invoiceData->sales_type) && $invoiceData->sales_type == 3) ? "selected" : ((!empty($sales_type) && $sales_type == 3)?"selected":"") ?>>Jobwork (Domestics)</option>
										</select>
									</div>

								    <div class="col-md-5">
										<label for="party_id">Party Name</label>
										<!--<div for="party_id1" class="float-right">	
											<span class="dropdown float-right">
												<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>
												<div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
													<div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
													
													<a class="dropdown-item leadAction addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-xl" data-function="addParty/1" data-controller="parties" data-class_name="partyOptions" data-form_title="Add Customer">+ Customer</a>
													
												</div>
											</span>
										</div>-->
										<div id="so_div" class="float-right">
											<a href="javascript:void(0)" class="text-primary font-bold createSalesInvoice permission-write1" datatip="Sales Order" flow="down">+ Sales Order</a>
										</div>

										<div id="cum_inv_div" class="float-right">
											<a href="javascript:void(0)" class="text-primary font-bold createSalesInvoiceOnCustomInv permission-write1" datatip="Custom Invoice" flow="down">+ Custom Invoice</a>
										</div>
										<select name="party_id" id="party_id" class="form-control single-select partyOptions req">
											<option value="">Select Party</option>
											<?php
											foreach ($customerData as $row) :
												$selected = (!empty($invoiceData->party_id) && $invoiceData->party_id == $row->id) ? "selected" : ((!empty($invMaster->id) && $invMaster->id == $row->id) ? "selected" : "");
												echo "<option data-row='" . json_encode($row) . "' value='" . $row->id . "' " . $selected . ">" . $row->party_name . "</option>";
												if (!empty($selected)) :
													$partyData = $row;
												endif;
											endforeach;
											?>
										</select>

										<input type="hidden" name="party_name" id="party_name" value="<?= (!empty($invoiceData->party_name)) ? $invoiceData->party_name : ((!empty($invMaster->party_name)) ? $invMaster->party_name : "") ?>">

										<input type="hidden" name="party_state_code" id="party_state_code" value="<?= (!empty($invoiceData->party_state_code)) ? $invoiceData->party_state_code : ((!empty($invMaster->gstin)) ? substr($invMaster->gstin, 0, 2) : "") ?>">
									</div>
									
									<div class="col-md-2">
										<label for="inv_no">Invoice No.</label>
										<div class="input-group">
											<input type="text" name="inv_prefix" id="inv_prefix" class="form-control req" value="<?= (!empty($invoiceData->trans_prefix)) ? $invoiceData->trans_prefix : $trans_prefix ?>" />
											<input type="text" name="inv_no" id="inv_no" class="form-control" placeholder="Enter Invoice No." value="<?= (!empty($invoiceData->trans_no)) ? $invoiceData->trans_no : $nextTransNo ?>" readonly1 />
										</div>

									</div>
									<div class="col-md-2">
										<label for="inv_date">Invoice Date</label>
										<input type="date" id="inv_date" name="inv_date" class=" form-control req" placeholder="dd-mm-yyyy" value="<?= (!empty($invoiceData->trans_date)) ? $invoiceData->trans_date : ((!empty($trans_date))?$trans_date:$maxDate) ?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />
									</div>
								
								</div>
								<div class="row form-group expInv">
								    <div class="col-md-3">
										<label for="sp_acc_id">Sales A/c.</label>
										<select name="sp_acc_id" id="sp_acc_id" class="form-control single-select req">
											<option value="">Select Account</option>
											<?php
												foreach($spAccounts as $row):
													if($row->system_code != "SALESACC"):
														$selected = (!empty($invoiceData->sp_acc_id) && $invoiceData->sp_acc_id == $row->id)?"selected":((!empty($sp_acc_id) && $sp_acc_id == $row->id)?"selected":"");
														echo "<option value='".$row->id."' ".$selected.">".$row->party_name."</option>";
													endif;
												endforeach;
											?>
										</select>
									</div>
									<div class="col-md-3 form-group">
										<label for="gstin">GST No.</label>
										<select name="gstin" id="gstin" class="form-control ">
											<option value="" data-pincode='' data-address=''>Select GSTIN</option>
											<?php
											if (!empty($invMaster)) :
												$json_data = json_decode($invMaster->json_data);
												foreach ($json_data as $key => $row) :
													$selected = (!empty($invoiceData->gstin) && $invoiceData->gstin == $key) ? "selected" : ((!empty($invMaster->gstin) && $invMaster->gstin == $key) ? "selected" : "");
													echo '<option value="' . $key . '" data-pincode="'.$row->delivery_pincode.'" data-address="'.$row->delivery_address.'" ' . $selected . '>' . $key . '</option>';
												endforeach;
											endif;
											?>
										</select>
									</div>
									<div class="col-md-2 form-group">
										<label for="gst_applicable">GST Applicable</label>
										<select name="gst_applicable" id="gst_applicable" class="form-control req">
											<option value="1" <?= (!empty($invoiceData) && $invoiceData->gst_applicable == 1) ? "selected" : ((isset($gst_applicable) && $gst_applicable == 1)?"selected":"") ?>>Yes</option>
											<option value="0" <?= (!empty($invoiceData) && $invoiceData->gst_applicable == 0) ? "selected" : ((isset($gst_applicable) && $gst_applicable == 0)?"selected":"") ?>>No</option>
										</select>
									</div>

									
									<div class="col-md-2">
										<label for="challan_no">Challan No.</label>
										<input type="text" name="challan_no" class="form-control" placeholder="Enter Challan No." value="<?= (!empty($invoiceData->challan_no)) ? $invoiceData->challan_no : (!empty($challanNo)?$challanNo: "") ?>" />
									</div>
									<div class="col-md-2">
										<label for="so_no">SO. NO.</label>
										<input type="text" name="so_no" class="form-control" placeholder="Enter SO. NO." value="<?= (!empty($invoiceData->doc_no))?$invoiceData->doc_no:(!empty($soTransNo)?$soTransNo:(isset($orderData) && !empty(($orderData->so_no)) ? $orderData->so_no : ""))?>" />
									</div>
									
								</div>
								<div class="row form-group expInv">
									<div class="col-md-3">
										<label>Gross Weight (Kg.)</label>
										<input type="number" name="gross_weight" id="gross_weight" value="<?= (!empty($invoiceData->gross_weight)) ? $invoiceData->gross_weight : '' ?>" class="form-control price-input1" />

									</div>
									<div class="col-md-3">
										<label>Total Packets</label>
										<input type="text" name="total_packet" id="total_packet" value="<?= (!empty($invoiceData->total_packet)) ? $invoiceData->total_packet : '' ?>" class="form-control" />

									</div>

									<div class="col-md-3">
										<label>E-Way Bill No.</label>
										<input type="text" name="eway_bill_no" id="eway_bill_no" value="<?= (!empty($invoiceData->eway_bill_no)) ? $invoiceData->eway_bill_no : '' ?>" class="form-control" />

									</div>
									<div class="col-md-3">
										<label>L.R. No.</label>
										<input type="text" name="lrno" id="lrno" value="<?= (!empty($invoiceData->lr_no)) ? $invoiceData->lr_no : '' ?>" class="form-control" />

									</div>
								</div>
								<div class="row form-group expInv">
								    <div class="col-md-3">
										<label>Dispatched Through (Transport)</label>
										<select name="transport" id="transport" class="form-control single-select float-right">
											<option value="">Select Transport</option> 
											<?php
												foreach($transportData as $row):
													echo '<option value="'.$row->transport_name.'">'.$row->transport_name.'</option>';
												endforeach;
											?>
										</select>
									</div>
									<div class="col-md-7 form-group">
										<label>Destination</label>
										<input type="text" name="supply_place" id="supply_place" value="<?= (!empty($invoiceData->supply_place)) ? $invoiceData->supply_place : "" ?>" class="form-control" />

									</div>
									<div class="col-md-2 ">
										<label for="apply_round">Apply Round Off</label>
										<select name="apply_round" id="apply_round" class="form-control single-select">
											<option value="0" <?= (!empty($invoiceData) && $invoiceData->apply_round == 0) ? "selected" : "" ?>>Yes</option>
											<option value="1" <?= (!empty($invoiceData) && $invoiceData->apply_round == 1) ? "selected" : "" ?>>No</option>
										</select>
									</div>	
								</div>
								<div class="row form-group">
									<div class="col-md-2 form-group expDiv">
										<label for="currency">Party Currency</label>
										<input type="text" name="currency" id="currency" class="form-control" value="<?=(!empty($invoiceData->currency))?$invoiceData->currency:((!empty($invMaster->currency))?$invMaster->currency:"")?>" readonly>
									</div>

									<div class="col-md-2 form-group expDiv">
										<label for="inrrate">INR Rate</label>
										<input type="number" class="form-control req" name="inrrate" id="inrrate" value="<?=(!empty($invoiceData->inrrate))?$invoiceData->inrrate:((!empty($invMaster->inrrate))?$invMaster->inrrate:"0")?>">
									</div>
								</div>
							</div>
							<hr>
							<div class="col-md-12 row">
								<div class="col-md-6">
									<h4>Item Details : </h4>									
								</div>								
								<div class="col-md-6 text-right"><button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button></div>
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
												<tr id="noData">
													<td colspan="13" class="text-center">No data available in table</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<hr>
								<div class="col-md-12 row mb-3">
									<h4>Summary Details : </h4>
								</div>
								<!-- Created By Mansee @ 29-12-2021 -->
								<div class="row form-group">

									<div style="width:100%;">
										<table id="summaryTable" class="table" >
											<thead class="table-info">
												<tr>
													<th style="width: 30%;">Descrtiption</th>
													<th style="width: 30%;">Ledger</th>
													<th style="width: 10%;">Percentage</th>
													<th style="width: 10%;">Amount</th>
													<th style="width: 20%;">Net Amount</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>Sub Total</td>
													<td></td>
													<td></td>
													<td></td>
													<td>
														<input type="text" name="taxable_amount" id="taxable_amount" class="form-control summaryAmount" value="0" readonly />
													</td>
												</tr>
												<?php
												$beforExp = "";
												$afterExp = "";
												$tax = "";
												$invExpenseData = (!empty($invoiceData->expenseData))?$invoiceData->expenseData:array();
												
												foreach ($expenseList as $row) :

													$expPer = 0;
													$expAmt = 0;
													$perFiledName = $row->map_code."_per"; 
													$amtFiledName = $row->map_code."_amount";
													if(!empty($invExpenseData) && $row->map_code != "roff"):	
														$expPer = $invExpenseData->{$perFiledName};
														$expAmt = $invExpenseData->{$amtFiledName};
													endif;

													$options = '<select class="form-control single-select" name="' . $row->map_code . '_acc_id" id="' . $row->map_code . '_acc_id">';
														
														foreach ($ledgerList as $ledgerRow) :
															if ($ledgerRow->group_code != "DT") :
																$filedName = $row->map_code."_acc_id";
																if(!empty($invExpenseData->{$filedName})):
																	if($row->map_code != "roff"):
																		$selected = ($ledgerRow->id == $invExpenseData->{$filedName})?"selected":(($ledgerRow->id == $row->acc_id) ? 'selected' : '');
																	else:
																		$selected = ($ledgerRow->id == $invoiceData->round_off_acc_id)?"selected":(($ledgerRow->id == $row->acc_id) ? 'selected' : '');
																	endif;
																else:
																	$selected = ($ledgerRow->id == $row->acc_id) ? 'selected' : '';
																endif;

																$options .= '<option value="' . $ledgerRow->id . '" ' . $selected . '>' . $ledgerRow->party_name . '</option>';
															endif;
														endforeach;
														$options .= '</select>';

													if ($row->position == 1) :														
														$beforExp .= '<tr>
															<td>' . $row->exp_name .'</td>
															<td>' . $options . '</td>
															<td>';
														
														$readonly = "";
														$perBoxType = "number";
														$calculateSummaryPer = "calculateSummary";
														$calculateSummaryAmt = "calculateSummary";
														if($row->calc_type != 1):
															$perBoxType = "number";
															$readonly = "readonly";
															$calculateSummaryPer = "calculateSummary";
															$calculateSummaryAmt = "";
														else:
															$perBoxType = "hidden";
															$readonly = "";
															$calculateSummaryPer = "";
															$calculateSummaryAmt = "calculateSummary";
														endif;

														

														$beforExp .= "<input type='".$perBoxType."' name='" . $row->map_code . "_per' id='" . $row->map_code . "_per' data-row='".json_encode($row)."' value='".$expPer."' class='form-control ".$calculateSummaryPer."'> ";

														$beforExp .= "</td>
														<td><input type='number' id='".$row->map_code."_amt' class='form-control ".$calculateSummaryAmt."' data-sm_type='exp' data-row='".json_encode($row)."' value='".$expAmt."' ".$readonly."></td>
														<td><input type='number' name='" . $row->map_code . "_amount' id='" . $row->map_code . "_amount'  value='0' class='form-control summaryAmount' readonly /> <input type='hidden' id='other_" . $row->map_code . "_amount' class='otherGstAmount' value='0'> </td>
														</tr>";

													else :
														
														$afterExp .= '<tr>
															<td>' . $row->exp_name . '</td>
															<td>' . $options . '</td><td>';

														$readonly = "";
														$perBoxType = "number";
														$calculateSummaryPer = "calculateSummary";
														$calculateSummaryAmt = "calculateSummary";
														if($row->map_code != "roff" && $row->calc_type != 1):
															$perBoxType = "number";
															$readonly = "readonly";
															$calculateSummaryPer = "calculateSummary";
															$calculateSummaryAmt = "";
														else:
															$perBoxType = "hidden";
															$readonly = "";
															$calculateSummaryPer = "";
															$calculateSummaryAmt = "calculateSummary";
														endif;

														$afterExp .= "<input type='".$perBoxType."' name='" . $row->map_code . "_per' id='" . $row->map_code . "_per' data-row='".json_encode($row)."' value='".$expPer."' class='form-control ".$calculateSummaryPer."'> ";

														$readonly = ($row->map_code == "roff")?"readonly":$readonly;
														$amtType = ($row->map_code == "roff")?"hidden":"number";
														$afterExp .= "</td>
														<td><input type='".$amtType."' id='".$row->map_code."_amt' class='form-control ".$calculateSummaryAmt."' data-sm_type='exp' data-row='".json_encode($row)."' value='".$expAmt."' ".$readonly."></td>
														<td><input type='number' name='" . $row->map_code . "_amount' id='" . $row->map_code . "_amount' value='0' class='form-control ".(($row->map_code == "roff")?"":"summaryAmount")."' readonly /> </td>
														</tr>";
													endif;
												endforeach;

												foreach ($taxList as $taxRow) :
													$options = '<select class="form-control single-select" name="' . $taxRow->map_code . '_acc_id" id="' . $taxRow->map_code . '_acc_id">';

													foreach ($ledgerList as $ledgerRow) :
														if ($ledgerRow->group_code == "DT") :
															$filedName = $taxRow->map_code."_acc_id";
															if(!empty($invoiceData->{$filedName})):
																$selected = ($ledgerRow->id == $invoiceData->{$filedName})?"selected":(($ledgerRow->id == $taxRow->acc_id) ? 'selected' : '');
															else:
																$selected = ($ledgerRow->id == $taxRow->acc_id) ? 'selected' : '';
															endif;

															$options .= '<option value="' . $ledgerRow->id . '" ' . $selected . '>' . $ledgerRow->party_name . '</option>';
														endif;
													endforeach;
													$options .= '</select>';

													$taxClass = "";
													$perBoxType = "number";
													$calculateSummary = "calculateSummary";
													$taxPer = 0;
													$taxAmt = 0;
													if(!empty($invoiceData->id)):
														$taxPer = $invoiceData->{$taxRow->map_code.'_per'};
														$taxAmt = $invoiceData->{$taxRow->map_code.'_amount'};
													endif;
													if($taxRow->map_code == "cgst"):
														$taxClass = "cgstCol";
														$perBoxType = "hidden";
														$calculateSummary = "";
													elseif($taxRow->map_code == "sgst"):
														$taxClass = "sgstCol";
														$perBoxType = "hidden";
														$calculateSummary = "";
													elseif($taxRow->map_code == "igst"):
														$taxClass = "igstCol";
														$perBoxType = "hidden";
														$calculateSummary = "";
													endif;

													$tax .= '<tr class="'.$taxClass.'">
														<td>' . $taxRow->name . '</td>
														<td>' . $options . '</td>
														<td>';

													$tax .= "<input type='".$perBoxType."' name='" . $taxRow->map_code . "_per' id='" . $taxRow->map_code . "_per' data-row='".json_encode($taxRow)."' value='".$taxPer."' class='form-control ".$calculateSummary."'> ";
														
													$tax .= "</td>
														<td><input type='".$perBoxType."' id='".$taxRow->map_code."_amt' class='form-control' data-sm_type='tax'data-row='".json_encode($taxRow)."' value='".$taxAmt."' readonly ></td>
														<td><input type='number' name='" . $taxRow->map_code . "_amount' id='" . $taxRow->map_code . "_amount'  value='0' class='form-control summaryAmount' readonly /> </td>
													</tr>";
												endforeach;

												echo $beforExp;
												echo $tax;
												echo $afterExp;
												?>
												
											</tbody>
											<tfoot class="table-info">
												<tr >
													<th>Net. Amount</th>
													<th></th>
													<th></th>
													<th></th>
													<td>
														<input type="text" name="net_inv_amount" id="net_inv_amount" class="form-control" value="0" readonly />
													</td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<hr>
								<div class="row form-group">

									<div class="col-md-12">
										<div class="row">


											<div class="col-md-9 form-group">
												<label for="remark">Remark</label>
												<input type="text" name="remark" class="form-control" value="<?= (!empty($invoiceData->remark)) ? $invoiceData->remark : "" ?>" />
											</div>
											<div class="col-md-3 form-group">
												<label for="">&nbsp;</label>	
												<button type="button" class="btn btn-outline-success waves-effect btn-block" data-toggle="modal" data-target="#termModel">Terms & Conditions (<span id="termsCounter">0</span>)</button>
												<div class="error term_id"></div>
											</div>
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
														if (!empty($terms)) :
															$termaData = (!empty($invoiceData->terms_conditions)) ? json_decode($invoiceData->terms_conditions) : array();
															$i = 1;
															$j = 0;
															foreach ($terms as $row) :
																$checked = "";
																$disabled = "disabled";
																if (in_array($row->id, array_column($termaData, 'term_id'))) :
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
																		<input type="checkbox" id="md_checkbox<?= $i ?>" class="filled-in chk-col-success termCheck" data-rowid="<?= $i ?>" check="<?= $checked ?>" <?= $checked ?> />
																		<label for="md_checkbox<?= $i ?>"><?= $i ?></label>
																	</td>
																	<td style="width:25%;">
																		<?= $row->title ?>
																		<input type="hidden" name="term_id[]" id="term_id<?= $i ?>" value="<?= $row->id ?>" <?= $disabled ?> />
																		<input type="hidden" name="term_title[]" id="term_title<?= $i ?>" value="<?= $row->title ?>" <?= $disabled ?> />
																	</td>
																	<td style="width:65%;">
																		<input type="text" name="condition[]" id="condition<?= $i ?>" class="form-control" value="<?= $row->conditions ?>" <?= $disabled ?> />
																	</td>
																</tr>
															<?php
																$i++;
															endforeach;
														else :
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
							<button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveInvoice('saveSalesInvoice');"><i class="fa fa-check"></i> Save</button>
							<a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
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
								<!--<input type="hidden" name="gst_per" id="gst_per" value="" />-->
								<input type="hidden" name="org_price" id="org_price" value="" />
								<input type="hidden" name="row_index" id="row_index" value="">	
								<input type="hidden" name="so_ref_id" id="so_ref_id" value="">

							</div>

							<div class="col-md-12 form-group">
								<label for="item_id">Product Name</label>
								<div for="party_id1" class="float-right">
									<span class="dropdown float-right">
										<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>
										<div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
											<div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>

											<a class="dropdown-item leadActionStatic addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-lg" data-function="addProduct/1" data-controller="products" data-class_name="itemOptions" data-form_title="Add Product"> + Product</a>

										</div>
									</span>
								</div>
								<select name="item_id" id="item_id" class="form-control single-select itemOptions req">
									<option value="">Select Product Name</option>
									<?php
									foreach ($itemData as $row) :
										echo "<option value='" . $row->id . "' data-row='" . json_encode($row) . "'>[" . $row->item_code . "] " . $row->item_name . "</option>";
									endforeach;
									?>
								</select>

							</div>

							<div class="col-md-3 form-group">
								<!--<label for="unit_id">Unit</label>-->
								<input type="hidden" name="unit_name" id="unit_name" class="form-control" value="" readonly />
								<input type="hidden" name="unit_id" id="unit_id" value="">
								<label for="gst_per">GST Per.</label>
                                <select name="gst_per" id="gst_per" class="form-control single-select">
                                    <?php
                                    foreach ($gstPercentage as $row) :
                                        $selected = '';//(!empty($dataRow->gst_per) && $dataRow->gst_per == $row['rate']) ? "selected" : "";
                                        echo '<option value="' . $row['rate'] . '" ' . $selected . '>' . $row['val'] . '</option>';
                                    endforeach;
                                    ?>
                                </select>
							</div>
							<div class="col-md-3 form-group">
								<label for="qty">Quantity</label>
								<input type="number" name="qty" id="qty" class="form-control floatOnly req" value="0">

							</div>
							<div class="col-md-3 form-group">
								<label for="price">Price</label>
								<input type="number" name="price" id="price" class="form-control floatOnly req" value="" />

							</div>
							<div class="col-md-3 form-group">
								<label for="disc_per">Disc Per.</label>
								<input type="number" name="disc_per" id="disc_per" class="form-control floatOnly" value="0" />

							</div>
							<div class="col-md-12 form-group">
								<label for="item_remark">Remark</label>
								<input type="text" name="item_remark" id="item_remark" class="form-control" value="" />
							</div>
						</div>
						<hr>
						<div class="row form-group" id="batchDiv">
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

<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel1">Create Invoice</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<form id="party_so" method="post" action="">
				<div class="modal-body">
					<div class="col-md-12"><b>Party Name : <span id="partyName"></span></b></div>
					<input type="hidden" name="party_id" id="party_id_so" value="">
					<input type="hidden" name="party_name" id="party_name_so" value="">
                    <input type="hidden" name="inv_id" id="sales_id_so" value="" />
					<input type="hidden" name="from_entry_type" id="from_entry_type" value="4">
					<div class="col-md-12">
						<div class="error general"></div>
						<div class="table-responsive">
							<table id="orderTable" class="table table-bordered">
								<thead class="thead-info">
									<tr>
										<th class="text-center">#</th>
										<th class="text-center">SO. No.</th>
										<th class="text-center">SO. Date</th>
										<th class="text-center">Cust. PO.NO.</th>
										<th class="text-center">W.O. NO.</th>
										<th class="text-center">Part Code</th>
										<th class="text-center">Qty.</th>
									</tr>
								</thead>
								<tbody id="orderData">
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

<div class="modal fade" id="customInvModal" tabindex="-1" role="dialog" aria-labelledby="cumInvModalLabel" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title" id="cumInvModalLabel">Create Invoice</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<form id="party_cum_inv" method="post" action="">
				<div class="modal-body">
					<div class="col-md-12"><b>Party Name : <span id="partyNameCumInv"></span></b></div>
					<input type="hidden" name="party_id" id="party_id_cum" value="">
					<input type="hidden" name="party_name" id="party_name_cum" value="">
                    <input type="hidden" name="inv_id" id="sales_id_cum" value="" />
					<input type="hidden" name="from_entry_type" id="from_entry_type" value="11">
					<div class="col-md-12">
						<div class="error general"></div>
						<div class="table-responsive">
							<table id="cumInvTable" class="table table-bordered">
								<thead class="thead-info">
									<tr>
										<th class="text-center">#</th>
										<th class="text-center">Cum. Inv. No.</th>
										<th class="text-center">Packing No.</th>
										<th class="text-center">Part Code</th>
										<th class="text-center">Qty.</th>
									</tr>
								</thead>
								<tbody id="cumInvData">
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
					<button type="submit" class="btn waves-effect waves-light btn-outline-success" id="btn-create"><i class="fa fa-check"></i> Create Invoice</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/sales-invoice-form.js?v=<?= time() ?>"></script>
<script src="<?php echo base_url(); ?>assets/js/custom/master-form.js?v=<?= time() ?>"></script>
<?php
if (!empty($invoiceData->itemData)) :
	$i = 1;
	foreach ($invoiceData->itemData as $row) :
		if ($this->uri->segment(2) == "addSalesInvoiceOnSalesOrder") :
			$row->id = "";
		endif;
		$row->trans_id = $row->id;
		$row->cgst_amt = $row->cgst_amount;
		$row->cgst = $row->cgst_per;
		$row->sgst_amt = $row->sgst_amount;
		$row->sgst = $row->sgst_per;
		$row->igst_amt = $row->igst_amount;
		$row->igst = $row->igst_per;
		$row->gst_per = $row->igst_per;
		$row->disc_amt = $row->disc_amount;
		$row->amount = $row->taxable_amount;
		$row->location_id = explode(",",$row->location_id);
		$row->batch_no = explode(",",$row->batch_no);
		$row->batch_qty = explode(",",$row->batch_qty);
		$row->packing_trans_id = explode(",",$row->rev_no);
		$row->row_index = "";
		unset($row->entry_type);
		$row = json_encode($row);
		echo '<script>AddRow(' . $row . ');</script>';
		$i++;
	endforeach;
endif;

if (!empty($invItems)) {
	foreach ($invItems as $row) :
		$row->qty = $row->qty - $row->dispatch_qty;
		if (!empty($row->qty)) :
			$row->trans_id = "";
			$row->row_index = "";
			$row->from_entry_type = $row->entry_type;
			if($row->from_entry_type == 5){
				$row->so_ref_id = $row->ref_id;
			}else{
				$row->so_ref_id = $row->id;
			}
			$row->ref_id = $row->id;
			$row->hsn_code = (!empty($row->hsn_code)) ? $row->hsn_code : "";
			$row->gst_type = $gst_type;
			if($row->from_entry_type == 11):
				$row->org_price = $row->price;
				$row->price = round($row->org_price * $invMaster->inrrate, 2);
				$row->gst_per = $row->item_gst;
				unset($row->item_gst);
			endif;
			if (empty($row->disc_per)) :
				$row->disc_per = 0;
				$row->disc_amt = 0;
				$row->amount = round($row->qty * $row->price, 2);
			else :
				$row->disc_amt = round((($row->qty * $row->price) * $row->disc_per) / 100, 2);
				$row->amount = round(($row->qty * $row->price) - $row->disc_amt, 2);
			endif;
			$row->igst_per = $row->gst_per;
			$row->igst_amt = round(($row->amount * $row->igst_per) / 100, 2);

			$row->cgst_per = round(($row->igst_per / 2), 2);
			$row->cgst_amt = round(($row->igst_amt / 2), 2);
			$row->sgst_per = round(($row->igst_per / 2), 2);
			$row->sgst_amt = round(($row->igst_amt / 2), 2);

			$row->net_amount = round($row->amount + $row->igst_amt, 2);
			$row->stock_eff = ($row->stock_eff == 1) ? 0 : 1;
			unset($row->entry_type);
			$row = json_encode($row);
			echo '<script>AddRow(' . $row . ');</script>';
		endif;
	endforeach;
}
?>