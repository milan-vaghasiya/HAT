<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header text-center">
						<h4>
							<u>GST Expense</u>
							<!--<span style="float:right">-->
							<!--	<a href="javascript:void(0)" class="clickMic" ><i class="fas fa-microphone fs-15"></i></a>-->
							<!--</span>-->
						</h4>
					</div>
					<div class="card-body">
						<form autocomplete="off" id="saveGstExpense">
							<div class="col-md-12">
								<input type="hidden" name="id" value="<?= (!empty($invoiceData->id)) ? $invoiceData->id : "" ?>" />

								<input type="hidden" name="entry_type" id="entry_type" value="18">

								<input type="hidden" name="reference_entry_type" id="reference_entry_type" value="<?= (!empty($invoiceData->from_entry_type)) ? $invoiceData->from_entry_type : "" ?>">

								<input type="hidden" name="reference_id" value="<?= (!empty($invoiceData->ref_id)) ? $invoiceData->ref_id : $ref_id ?>">

								<input type="hidden" name="gst_type" id="gst_type" value="<?= (!empty($invoiceData->gst_type)) ? $invoiceData->gst_type : "" ?>">

								<input type="hidden" name="inv_prefix" id="inv_prefix" class="form-control req" value="<?= (!empty($invoiceData->trans_prefix)) ? $invoiceData->trans_prefix : $trans_prefix ?>" />

								<input type="hidden" name="inv_no" id="inv_no" class="form-control" placeholder="Enter Invoice No." value="<?= (!empty($invoiceData->trans_no)) ? $invoiceData->trans_no : $nextTransNo ?>" />

								<div class="row form-group">
									<!-- <div class="col-md-3">
										<label for="inv_no">Invoice No.</label>
                                        <div class="input-group">
                                        </div>
									</div> -->
									<div class="col-md-2">
										<label for="doc_no">Invoice No.</label>
										<input type="text" name="doc_no" id="doc_no" class="form-control" value="<?= (!empty($invoiceData->doc_no)) ? $invoiceData->doc_no : "" ?>" />
									</div>
									<div class="col-md-2">
										<label for="inv_date">Invoice Date</label>
										<input type="date" id="inv_date" name="inv_date" class=" form-control req inv_date" placeholder="dd-mm-yyyy" value="<?= (!empty($invoiceData->trans_date)) ? $invoiceData->trans_date : $maxDate ?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />
									</div>
									<!-- <div class="col-md-3">
										<label for="sp_acc_id">Purchase A/c.</label>
										<select name="sp_acc_id" id="sp_acc_id" class="form-control single-select req">
											<option value="">Select Account</option>
											<?php
												foreach($spAccounts as $row):
													if($row->system_code != "PURACC"):
														$selected = (!empty($invoiceData->sp_acc_id))?"selected":"";
														echo "<option value='".$row->id."' ".$selected.">".$row->party_name."</option>";
													endif;
												endforeach;
											?>
										</select>
									</div> -->
									<div class="col-md-4">
										<label for="party_id">Party Name</label>
										<!-- <div for="party_id1" class="float-right">
											<a href="javascript:void(0)" class="text-primary font-bold createPurchaseInvoice permission-write1" datatip="Grn" flow="down">+ GRN</a>
										</div> -->
										<select name="party_id" id="party_id" class="form-control single-select partyOptions req">
											<option value="">Select Party</option>
											<?php
											foreach ($partyData as $row) :
												$selected = (!empty($party_id) && $party_id == $row->id) ? "selected" : ((!empty($invoiceData->party_id) && $invoiceData->party_id == $row->id) ? "selected" : "");
												echo "<option data-row='" . json_encode($row) . "' value='" . $row->id . "' " . $selected . ">" . $row->party_name . "</option>";
											endforeach;
											?>
										</select>

										<input type="hidden" name="party_name" id="party_name" value="<?= (!empty($invoiceData->party_name)) ? $invoiceData->party_name : ((!empty($party_name)) ? $party_name : "") ?>">

										<input type="hidden" name="party_state_code" id="party_state_code" value="<?= (!empty($invoiceData->party_state_code)) ? $invoiceData->party_state_code : ((!empty($invMaster->gstin)) ? substr($invMaster->gstin, 0, 2) : "") ?>">

										<input type="hidden" name="gstin" id="gstin" value="<?=(!empty($invoiceData->gstin))?$invoiceData->gstin:((!empty($invMaster->gstin))?$invMaster->gstin:"")?>">
									</div>

									<div class="col-md-2 form-group">
										<label for="gst_applicable">GST Applicable</label>
										<select name="gst_applicable" id="gst_applicable" class="form-control req">
											<option value="1" <?= (!empty($invoiceData) && $invoiceData->gst_applicable == 1) ? "selected" : "" ?>>Yes</option>
											<option value="0" <?= (!empty($invoiceData) && $invoiceData->gst_applicable == 0) ? "selected" : "" ?>>No</option>
										</select>
									</div>
									<div class="col-md-2">
										<label for="challan_no">PO. NO.</label>
										<input type="text" name="challan_no" class="form-control" value="<?= (!empty($invoiceData->challan_no)) ? $invoiceData->challan_no : (isset($orderData) && !empty(($orderData->challan_no)) ? $orderData->challan_no : "") ?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="apply_round">Apply Round Off</label>
										<select name="apply_round" id="apply_round" class="form-control single-select">
											<option value="0" <?= (!empty($invoiceData) && $invoiceData->apply_round == 0) ? "selected" : "" ?>>Yes</option>
											<option value="1" <?= (!empty($invoiceData) && $invoiceData->apply_round == 1) ? "selected" : "" ?>>No</option>
										</select>
									</div>
								</div>
							</div>
							<hr>
							<div class="col-md-12 row">
								<div class="col-md-6">
									<h4>Ledger Details : </h4>
								</div>
								<div class="col-md-6"><button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Ledger</button></div>
							</div>
							<div class="col-md-12 mt-3">
								<div class="error item_name_error"></div>
								<div class="row form-group">
									<div class="table-responsive ">
										<table id="invoiceItems" class="table table-striped table-borderless">
											<thead class="table-info">
												<tr>
													<th style="width:5%;">#</th>
													<th>Ledger Name</th>
													<th>HSN Code</th>
													<th hidden>Qty.</th>
													<th hidden>Unit</th>
													<th>Price</th>
													<th class="igstCol">IGST</th>
													<th class="cgstCol">CGST</th>
													<th class="sgstCol">SGST</th>
													<th hidden>Disc.</th>
													<th class="amountCol">Amount</th>
													<th class="netAmtCol">Amount</th>
													<th>Remark</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
												<?php
												if (!empty($invoiceData->itemData)) :
													$i = 1;
													foreach ($invoiceData->itemData as $row) :
														if ($this->uri->segment(2) == "addSalesInvoiceOnSalesOrder") :
															$row->id = "";
														endif;
												?>
														<tr>
															<td style="width:5%;">
																<?= $i ?>
															</td>
															<td>
																<?= $row->item_name ?>
																<input type="hidden" name="item_id[]" value="<?= $row->item_id ?>">
																<input type="hidden" name="item_name[]" value="<?= htmlentities($row->item_name) ?>">
																<input type="hidden" name="trans_id[]" value="<?= $row->id ?>">
																<input type="hidden" name="from_entry_type[]" value="<?= $row->from_entry_type ?>">
																<input type="hidden" name="ref_id[]" value="<?= $row->ref_id ?>">
																<input type="hidden" name="stock_eff[]" value="<?= $row->stock_eff ?>">
																<input type="hidden" name="location_id[]" value="<?= $row->location_id ?>">
																<input type="hidden" name="batch_no[]" value="<?= $row->batch_no ?>">
																<input type="hidden" name="batch_qty[]" value="<?= $row->batch_qty ?>">
																<input type="hidden" name="cessPer[]" value="<?= $row->cess_per ?>">

																<input type="hidden" name="item_type[]" value="<?= $row->item_type ?>" /><input type="hidden" name="item_code[]" value="<?= $row->item_code ?>" /><input type="hidden" name="item_desc[]" value="<?= $row->item_desc ?>" /><input type="hidden" name="gst_per[]" value="<?= $row->gst_per ?>" />
																<div class="error batch_no<?= $i ?>"></div>
															</td>
															<td>
																<?= $row->hsn_code ?>
																<input type="hidden" name="hsn_code[]" value="<?= $row->hsn_code ?>">
															</td>
															<td hidden>
																<?= $row->qty ?>
																<input type="hidden" name="qty[]" value="<?= $row->qty ?>">
																<div class="error qty<?= $i ?>"></div>
															</td>
															<td hidden>
																<?= $row->unit_name ?>
																<input type="hidden" name="unit_id[]" value="<?= $row->unit_id ?>">
																<input type="hidden" name="unit_name[]" value="<?= $row->unit_name ?>">
															</td>
															<td>
																<?= $row->price ?>
																<input type="hidden" name="price[]" value="<?= $row->price ?>">
																<div class="error price<?= $i ?>"></div>
															</td>
															<td class="cgstCol">
																<?= $row->cgst_amount ?>(<?= $row->cgst_per ?>%)
																<input type="hidden" name="cgst_amt[]" value="<?= $row->cgst_amount ?>">
																<input type="hidden" name="cgst[]" value="<?= $row->cgst_per ?>">
															</td>
															<td class="sgstCol">
																<?= $row->sgst_amount ?>(<?= $row->sgst_per ?>%)
																<input type="hidden" name="sgst_amt[]" value="<?= $row->sgst_amount ?>">
																<input type="hidden" name="sgst[]" value="<?= $row->sgst_per ?>">
															</td>
															<td class="igstCol">
																<?= $row->igst_amount ?>(<?= $row->igst_per ?>%)
																<input type="hidden" name="igst_amt[]" value="<?= $row->igst_amount ?>">
																<input type="hidden" name="igst[]" value="<?= $row->igst_per ?>">
															</td>
															<td hidden>
																<?= $row->disc_amount ?>(<?= $row->disc_per ?>%)
																<input type="hidden" name="disc_per[]" value="<?= $row->disc_per ?>">
																<input type="hidden" name="disc_amt[]" value="<?= $row->disc_amount ?>">
															</td>
															<td class="amountCol">
																<?= $row->taxable_amount ?>
																<input type="hidden" name="amount[]" value="<?= $row->taxable_amount ?>">
															</td>
															<td class="netAmtCol">
																<?= $row->net_amount ?>
																<input type="hidden" name="net_amount[]" value="<?= $row->net_amount ?>">
															</td>
															<td>
																<?= $row->item_remark ?>
																<input type="hidden" name="item_remark[]" value="<?= $row->item_remark ?>">
															</td>
															<td class="text-center" style="width:10%;">
																<?php
																$row->trans_id = $row->id;
																unset($row->entry_type);
																$row->cessPer=$row->cess_per;
																$row->gst_per=floatval($row->gst_per);
																$row = json_encode($row);
																?>
																<button type="button" onclick='Edit(<?= $row ?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>

																<button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
															</td>
														</tr>
													<?php $i++;
													endforeach;
												else : ?>
													<tr id="noData">
														<td colspan="13" class="text-center">No data available in table</td>
													</tr>
												<?php endif; ?>
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
																if(!empty($invExpenseData)):
																	$filedName = $row->map_code."_acc_id";
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
															if(!empty($invExpenseData)):
																$filedName = $taxRow->map_code."_acc_id";
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
							<button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveGstExpense('saveGstExpense');"><i class="fa fa-check"></i> Save</button>
							<a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
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
				<h4 class="modal-title">Add or Update Ledger</h4>
				<!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
			</div>
			<div class="modal-body">
				<form id="invoiceItemForm">
					<div class="col-md-12">
						<div class="row form-group">
							<input type="hidden" name="trans_id" id="trans_id" value="" />
							<input type="hidden" name="unit_name" id="unit_name" class="form-control" value="" />
							<input type="hidden" name="unit_id" id="unit_id" value="">
							<input type="hidden" name="item_type" id="item_type" value="">
							<input type="hidden" name="disc_per" id="disc_per" value="0">
							<input type="hidden" name="row_index" id="row_index" value="">
							<input type="hidden" name="cess_per" id="cessPer" class="form-control">
							<input type="hidden" name="po_trans_id" id="po_trans_id" value="" />
							<input type="hidden" name="item_name" id="item_name" value="" />
							<input type="hidden" name="fgitem_id" id="fgitem_id" value="" />
							<input type="hidden" name="color_code" id="color_code" value="" />
							<input type="hidden" name="location_id" id="location_id" value="" />
							<input type="hidden" name="qty" id="qty" value="1" />
							<input type="hidden" name="qty_kg" id="qty_kg" value="" />
							<div class="col-md-12 form-group">
								<label for="item_id">Ledger Name</label>
								<select name="item_id" id="item_id" class="form-control single-select itemOptions req">
									<option value="">Select Ledger Name</option>
									<?php
									foreach ($itemData as $row) :
										echo "<option data-row='" . json_encode($row) . "' value='" . $row->id . "'>" . $row->party_name . "</option>";
									endforeach;
									?>
								</select>								
							</div>
							<div class="col-md-6 form-group">
								<label for="price">Price</label>
								<input type="text" name="price" id="price" class="form-control floatOnly" value="0">
								
							</div>
							<div class="col-md-6 form-group">
								<label for="hsn_code">HSN Code</label>
								<select name="hsn_code" id="hsn_code" class="form-control single-select">
									<option value="">Select HSN Code</option>
									<?php 
										foreach ($hsnData as $row) :
											echo '<option value="' . floatVal($row->hsn_code) . '" data-gst_per="'.floatVal($row->gst_per).'">' . floatVal($row->hsn_code) . '</option>';
										endforeach;
									?>
								</select>
							</div>
							<div class="col-md-6 form-group">
								<label for="gst_per">GST(%)</label>
								<!-- <input type="text" name="gst_per" id="gst_per" value="" class="form-control floatOnly"> -->
								<select name="gst_per" id="gst_per" class="form-control single-select">
									<?php
									foreach ($gstPercentage as $row) :
										echo '<option value="' . $row['rate'] . '">' . $row['val'] . '</option>';
									endforeach;
									?>
								</select>
							</div>
							<!-- <div class="col-md-4 form-group">
								<label for="cess_per">Cess Per</label>
								<input type="text" name="cess_per" id="cessPer" class="form-control">
							</div> -->
							<div class="col-md-12 form-group">
								<label for="item_remark">Remark</label>
								<input type="text" name="item_remark" id="item_remark" class="form-control" value="">
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
<script src="<?php echo base_url(); ?>assets/js/custom/gst-expense.js?v=<?= time() ?>"></script>
<script src="<?php echo base_url(); ?>assets/js/custom/master-form.js?v=<?= time() ?>"></script>
<script>
	gstType();	
</script>
<?php
if (!empty($orderItems)) :
	$i = 1;
	foreach ($orderItems as $row) :
		$row->row_index = "";
		$row->trans_id = "";
		$row->item_name = "[" . $row->item_code . "] " . $row->item_name;
		$row->ref_id = $row->id;
		$row->qty = round(($row->qty), 2);
		$row->qty_kg = "0.000";
		$row->amount = $row->price * $row->qty;
		$row->disc_per = 0;
		$row->disc_amt = 0;
		$row->cgst_per = ($row->gst_per / 2);
		$row->sgst_per = ($row->gst_per / 2);
		$row->igst_per = $row->gst_per;
		$row->cgst_amt = ($row->amount * $row->cgst_per) / 100;
		$row->sgst_amt = ($row->amount * $row->sgst_per) / 100;
		$row->igst_amt = ($row->amount * $row->igst_per) / 100;
		$row->stateCode = "";
		$row->net_amount = ($row->cgst_amt + $row->sgst_amt + $row->amount - $row->disc_amt);
		echo '<script>AddRow(' . json_encode($row) . ');</script>';
	endforeach;
endif;
?>

<script>
	$(document).ready(function(){
		//$(".calculateSummary").trigger('keyup');
		$("#party_id").trigger('change');
	});
</script>