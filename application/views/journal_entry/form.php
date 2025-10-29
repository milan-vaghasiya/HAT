<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header text-center">
						<h4>
							<u>Journal Entry</u>
						</h4>
					</div>
					<div class="card-body">
						<form autocomplete="off" id="saveJournalEntry">
							<div class="col-md-12">
								<input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : "" ?>" />

								<input type="hidden" name="entry_type" id="entry_type" value="17">
								<div class="row form-group">
									<div class="col-md-3">
										<label for="trans_no">Journal No.</label>
										<div class="input-group">
											<input type="text" name="trans_prefix" id="trans_prefix" class="form-control req" readonly value="<?= (!empty($dataRow->trans_prefix)) ? $dataRow->trans_prefix : $trans_prefix ?>" />

											<input type="text" name="trans_no" id="trans_no" class="form-control" readonly placeholder="Enter Invoice No." value="<?= (!empty($dataRow->trans_no)) ? $dataRow->trans_no : $nextTransNo ?>" />

										</div>
									</div>
									<div class="col-md-3">
										<label for="trans_date">Journal Date</label>
										<input type="date" id="trans_date" name="trans_date" class=" form-control req trans_date" placeholder="dd-mm-yyyy" value="<?= (!empty($dataRow->trans_date)) ? $dataRow->trans_date : $maxDate ?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />
									</div>

								</div>

							</div>
							<hr>
							<div class="col-md-12 row">
								<div class="col-md-6">
									<h4>Journal Details : </h4>
								</div>
								<div class="col-md-6"><button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add New Entry</button></div>
							</div>
							<div class="col-md-12 mt-3">
								<div class="error item_name_error"></div>
								<div class="row form-group">
									<div class="table-responsive ">
										<table id="invoiceItems" class="table table-striped table-borderless" >
											<thead class="table-info">
												<tr>
													<th style="width:5%;">#</th>
													<th>Ledger</th>
													<th>CR</th>
													<th>DR</th>
													<th>Remark</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
												<?php
												if (!empty($dataRow->ledgerData)) :
													$i = 1;
													foreach ($dataRow->ledgerData as $row) :												
														$credit_amount = ($row->c_or_d=='CR') ? $row->amount : 0;
														$debit_amount = ($row->c_or_d=='DR') ? $row->amount : 0;
												?>
														<tr>
															<td style="width:5%;">
																<?= $i ?>
															</td>
															<td>
																<?= $row->party_name ?>
																<input type="hidden" name="item_id[]" value="<?= $row->vou_acc_id ?>">
																<input type="hidden" name="item_name[]" value="<?= htmlentities($row->party_name) ?>">
																<input type="hidden" name="price[]" value="<?=$row->amount?>">
																<input type="hidden" name="trans_id[]" value="<?= $row->id ?>">
																
															</td>															
															<td>
																<?= $credit_amount ?>
																
																<div class="error price<?= $i ?>"></div>
																
																<input type="hidden" name="cr_dr[]" value="<?=$row->c_or_d ?>">
																<input type="hidden" name="credit_amount[]" value="<?= $credit_amount ?>">
															</td>
															<td>
																<?= $debit_amount ?>
															
																<input type="hidden" name="debit_amount[]" value="<?= $debit_amount ?>">
															</td>
															<td>
																<?= $row->remark ?>
																<input type="hidden" name="item_remark[]" value="<?= $row->remark ?>">
															</td>
															<td class="text-center" style="width:10%;">
																<?php
																$row->trans_id = $row->id;	
																$row->item_id = $row->vou_acc_id;
																$row->item_name = $row->party_name;
																$row->cr_dr = $row->c_or_d;
																$row->price = $row->amount;
																$row->item_remark = $row->remark;
																$row->credit_amount = $credit_amount;
																$row->debit_amount = $debit_amount;

																unset($row->id,$row->entry_type);
																$row = json_encode($row);
																?>
																<button type="button" onclick='Edit(<?=$row?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>

																<button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
															</td>
														</tr>
													<?php $i++;
													endforeach;
												else : ?>
													<tr id="noData">
														<td colspan="6" class="text-center">No data available in table</td>
													</tr>
												<?php endif; ?>
											</tbody>
											<tfoot>
												<tr>
													<td colspan="2" class="font-bold">Total</td>
													<td id="total_cr_amount" class="font-bold">0.00</td>
													<td id="total_dr_amount" class="font-bold">0.00</td>
													<td class="error total_cr_dr_amt"></td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<hr>

								<div class="row form-group">
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-12 form-group">
												<label for="remark">Remark</label>
												<input type="text" name="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>" />
											</div>

										</div>
									</div>
								</div>
							</div>
						
						</form>
					</div>
					<div class="card-footer">
						<div class="col-md-12">
							<button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveJournalEntry('saveJournalEntry');"><i class="fa fa-check"></i> Save</button>
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
				<h4 class="modal-title">Add or Update Entry</h4>
				<!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
			</div>
			<div class="modal-body">
				<form id="invoiceItemForm">
					<div class="col-md-12">
						<div class="row form-group">
							<input type="hidden" name="trans_id" id="trans_id" value="" />
		
							<input type="hidden" name="row_index" id="row_index" value="">
							<div class="col-md-12 form-group">
								<label for="item_id">Ledger</label>
								<select name="item_id" id="item_id" class="form-control single-select itemOptions req">
									<option value="">Select Ledger</option>
									<?php
									foreach ($partyData as $row) :
										echo "<option data-row='" . json_encode($row) . "' value='" . $row->id . "'>" . $row->party_name . "</option>";
									endforeach;
									?>
								</select>
								
								<input type="hidden" name="item_name" id="item_name" value="" />
								
							</div>
							<div class="col-md-7 form-group">
								<label for="price">Amount</label>
								<input type="text" name="price" id="price" class="form-control floatOnly" value="0">

							</div>
							<div class="col-md-5 form-group">
								<label for="gst_per">CR./DR.</label>
								<select name="cr_dr" id="cr_dr" class="form-control">
									<option value="">Select Type</option>
									<option value="CR">Credit</option>
									<option value="DR">Debit</option>
								</select>

							</div>

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
<script src="<?php echo base_url(); ?>assets/js/custom/journal-entry.js?v=<?= time() ?>"></script>




