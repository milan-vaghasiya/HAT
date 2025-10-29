<?php $this->load->view('includes/header'); ?>
<style>
	.titleText {
		color: #000000;
		font-size: 1.2rem;
		text-align: center;
		padding: 5px;
		background: #45729f;
		color: #ffffff;
		font-weight: 600;
		letter-spacing: 1px;
	}

	.card-body {
		padding: 20px 10px;
	}

	.jpFWTab nav>div a.nav-item.nav-link.active:after {
		left: -18% !important;
	}

	.ui-sortable-handle {
		cursor: move;
	}

	.ui-sortable-handle:hover {
		background-color: #daeafa;
		border-color: #9fc9f3;
		cursor: move;
	}
</style>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-6">
								<h4 class="card-title">Job Card View [ Status : <?= $dataRow->order_status ?> ]</h4>
							</div>
							<div class="col-md-6">
								<a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-dark float-right"><i class="fa fa-arrow-left"></i> Back</a>
								<a href="<?= base_url($headData->controller) ?>/printDetailedRouteCard/<?= $dataRow->id ?>" class="btn waves-effect waves-light btn-outline-primary float-right mr-2" target="_blank"><i class="fa fa-print"></i> Print</a>
								<?php
									if ($dataRow->job_order_status == 4 && $dataRow->scrap_status == 0):
								?>								
									<!-- <button type="button" class="btn waves-effect waves-light btn-outline-warning float-right addScrap mr-2" data-button="both" data-modal_id="modal-lg" data-job_card_id="<?=$dataRow->id?>" data-scrap_qty="<?=$totalScrapQty?>" data-function="generateScrap" data-form_title="Scrap Management" data-fnsave="saveProductionScrape"><i class="fa fa-plus"></i> Generate Scrap</button>	 -->							
								<?php
									endif;
								?>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="col-md-12">
							<div class="row">
								<!-- Column -->
								<div class="col-lg-12 col-xlg-12 col-md-12">
									<div class="card">
										<div class="titleText">JOB DETAIL</div>
										<div class="card-body scrollable" style="height:30vh;border-bottom: 5px solid #45729f">
											<table class="table">
												<tr>
													<th>Job Card No.</th>
													<td>: <?= getPrefixNumber($dataRow->job_prefix, $dataRow->job_no) ?></td>
													<th>Job Date </th>
													<td>: <?= date("d-m-Y", strtotime($dataRow->job_date)) ?></td>
													<th>Customer </th>
													<td>: <?= $dataRow->party_code ?></td>
												</tr>
												<tr>
													<th>Product </th>
													<td>: <?= $dataRow->product_code ?></td>
													<th>Order Quatity </th>
													<td>: <?= $dataRow->qty ?> <small><?= $dataRow->unit_name ?></small></td>
													<th>Delivery Date </th>
													<td>: <?= date("d-m-Y", strtotime($dataRow->delivery_date)) ?></td>
												</tr>
												<tr>
													<th colspan="1">Remark </th>
													<td colspan="5">: <?= $dataRow->remark ?></td>
												</tr>
												<!--<tr>-->
												<!--	<th class="text-center h4" colspan="6"><u>Material Detail</u></th>-->
												<!--</tr>-->
												<?php
												    $supplier_name = (!empty($reqMaterials['supplier_name'])) ? '<br><small>('.$reqMaterials['supplier_name'].')</small>' : '';
												?>
												<tr>
													<th>Material Name</th>
													<td>: <?= (!empty($reqMaterials['material_name']))?$reqMaterials['material_name'].$supplier_name:''; ?></td>
													<th>Heat No. </th>
													<td>: <?= (!empty($reqMaterials['heat_no']))?$reqMaterials['heat_no']:''; ?></td>
													<th>Issue Qty. </th>
													<td>: <?= (!empty($reqMaterials['issue_qty']))?$reqMaterials['issue_qty']:''; ?></td>
												</tr>
											</table>
										</div>
									</div>
								</div>
								<div class="col-lg-12 col-xlg-12 col-md-12">
									<div class="card jpFWTab">
										<nav>
											<div class="nav nav-tabs nav-fill tabLinks" id="nav-tab" role="tablist">
												<a class="nav-item nav-link active productionTab" data-toggle="tab" href="#production_detail" role="tab" aria-controls="nav-home" aria-selected="true"> Production </a>

												<!-- <a class="nav-item nav-link" data-toggle="tab" href="#rework_detail" role="tab" aria-controls="nav-profile" aria-selected="false"> Rework </a> -->

												<a class="nav-item nav-link" data-toggle="tab" href="#req_material" role="tab" aria-controls="nav-profile" aria-selected="false"> Material Detail</a>

												<a class="nav-item nav-link" data-toggle="tab" href="#production_stages" role="tab" aria-controls="nav-profile" aria-selected="false"> Production Stages</a>

												<!-- <a class="nav-item nav-link" data-toggle="tab" href="#scrap_weight" role="tab" aria-controls="nav-profile" aria-selected="false"> Scrap Weight</a> -->
											</div>
										</nav>
										<div class="tab-content py-3 px-3 px-sm-0" id="pills-tabContent">
											<!-- Process Approval Start -->
											<div class="tab-pane fade show active scrollable" style="height:60vh;" id="production_detail" role="tabpanel" aria-labelledby="pills-production_detail-tab">
												<div class="card-body">
													<div class="table-responsive">
														<table class="table table-striped table-bordered ">
															<thead class="thead-info">
																<tr class="text-center">
																	<th>Action</th>
																	<th>#</th>
																	<th class="text-left">Process Name</th>
																	<th class="text-left">Vendor</th>
																	<th>Inward <br> Qty</th>
																	<th>Pend. For <br> Move</th>
																	<th>Moved to <br>Next</th>
																	<th>Status</th>
																	<th>Prod. Ok <br> Qty</th>					
																	<th>Pend. Prod. <br> Qty</th>
																	<th>Rework <br> Qty</th>
																	<th>Reject <br> Found</th>
																	<th>Reject <br> Belongs To</th>
																	<th>Scrap Qty</th>
																</tr>
															</thead>
															<tbody>
																<?php
																if (!empty($dataRow->processData)) :
																	$i = 1;
																	foreach ($dataRow->processData as $row) :

																?>
																		<tr class="text-center">
																			<td>
																				<?php
																				if (!empty($row->process_approvel_data)) :

																					$approvalData = $row->process_approvel_data;
																					$outParam = "{'id' : " . $approvalData->id . ", 'modal_id' : 'modal-xl', 'form_id' : 'outWard', 'title' : 'Process Moved','button' : 'close'}";

																					$storeLocationParam = "{'id' : " . $approvalData->job_card_id . ",'transid' : " . $approvalData->id . ", 'modal_id' : 'modal-lg', 'form_id' : 'storeLocation', 'title' : 'Store Location','button' : 'close'}";

																					$button = "";
																					$button = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Move" flow="up" onclick="outward(' . $outParam . ');"><i class="fas fa-paper-plane"></i></a>';
																					if ($approvalData->out_process_id == 0 && $row->total_ok_qty > 0) :
																						$button .= '<a class="btn btn-warning btn-edit" href="javascript:void(0)" datatip="Store Location" flow="up" onclick="storeLocation(' . $storeLocationParam . ');"><i class="fas fa-paper-plane"></i></a>';
																					endif;

																					echo '<div class="actionWrapper" style="position:relative;">
																					<div class="actionButtons actionButtonsRight">
																						<a class="mainButton btn-instagram" href="javascript:void(0)"><i class="fa fa-cog"></i></a>
																						<div class="btnDiv" style="left:85%;">
																							' . $button . '
																						</div>
																					</div>
																				</div>';
																				endif;
																				?>
																			</td>

																			<td><?= $i++ ?></td>
																			<td class="text-left"><?= $row->process_name ?></td>
																			<td><?= $row->vendor ?></td>
																			<td><?= $row->inward_qty ?></td>
																			<td><?= ($row->in_qty - $row->out_qty-$row->total_rejection_qty) ?></td>
																			<td><?= $row->out_qty ?></td>
																			<td><?= $row->status ?></td>
																			<td><?= $row->total_ok_qty ?></td>		
																			<!-- <td><?= ($row->in_qty - $row->total_ok_qty) ?></td> -->
																			<td><?= ($row->in_qty - $row->out_qty-$row->total_rejection_qty) ?></td>
																			<td><?= $row->total_rework_qty ?></td>
																			<td><?= $row->total_rejection_qty ?> </td>
																			<td><?= $row->total_rej_belongs ?> </td>
																			<td><?= $row->scrap_qty ?></td>
																		</tr>
																	<?php endforeach;
																else : ?>
																	<tr>
																		<td colspan="11" class="text-center">No data available in table </td>
																	</tr>
																<?php endif; ?>
															</tbody>
														</table>
													</div>
												</div>
											</div>
											<!-- Process Approval End -->

											<!-- Rework Process Approival Start -->
											<div class="tab-pane fade show scrollable" style="height:60vh;" id="rework_detail" role="tabpanel" aria-labelledby="pills-rework_detail-tab">
												<div class="card-body">
													<div class="table-responsive">
														<table class="table table-striped table-bordered ">
															<thead class="thead-info">
																<tr class="text-center">
																	<th>Action</th>
																	<th>#</th>
																	<th class="text-left">Process Name</th>
																	<th>Order <br> Qty</th>
																	<th>Pen. Ord. <br> Qty</th>
																	<th>Status</th>
																	<th>In/Ok <br> Qty</th>
																	<th>Out <br> Qty</th>
																	<th>Pending <br> Qty</th>
																	<th>Rework <br> Qty</th>
																	<th>Reject <br> Qty</th>
																</tr>
															</thead>
															<tbody>
																<?php
																if (!empty($dataRow->reworkData)) :
																	$i = 1;
																	foreach ($dataRow->reworkData as $row) :
																?>
																		<tr class="text-center">
																			<td>
																				<?php
																				//print_r($row->process_approvel_data);
																				if (!empty($row->process_approvel_data)) :
																					$approvalData = $row->process_approvel_data;
																					$outParam = "{'id' : " . $approvalData->id . ", 'modal_id' : 'modal-xl', 'form_id' : 'outWard', 'title' : 'Process Approval','button' : 'close'}";

																					$storeLocationParam = "{'id' : " . $approvalData->job_card_id . ",'transid' : " . $approvalData->id . ", 'modal_id' : 'modal-xl', 'form_id' : 'storeLocation', 'title' : 'Store Location','button' : 'close'}";

																					$button = "";
																					if ($approvalData->out_process_id != 0) :
																						$button = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Approve" flow="up" onclick="outward(' . $outParam . ');"><i class="fas fa-paper-plane"></i></a>';
																					else :
																						if (empty($dataRow->pre_disp_inspection)) :
																							$button = '<a class="btn btn-warning btn-edit" href="javascript:void(0)" datatip="Store Location" flow="up" onclick="storeLocation(' . $storeLocationParam . ');"><i class="fas fa-paper-plane"></i></a>';
																						endif;
																					endif;

																					echo '<div class="actionWrapper" style="position:relative;">
																					<div class="actionButtons actionButtonsRight">
																						<a class="mainButton btn-instagram" href="javascript:void(0)"><i class="fa fa-cog"></i></a>
																						<div class="btnDiv" style="left:85%;">
																							' . $button . '
																						</div>
																					</div>
																				</div>';
																				endif;
																				?>
																			</td>

																			<td><?= $i++ ?></td>
																			<td class="text-left"><?= $row->process_name ?></td>
																			<td><?= $row->inward_qty ?></td>
																			<td><?= $row->pending_qty ?></td>
																			<td><?= $row->status ?></td>
																			<td><?= $row->in_qty ?></td>
																			<td><?= $row->out_qty ?></td>
																			<td><?= ($row->in_qty - $row->out_qty) ?></td>
																			<td><?= $row->rework_qty ?></td>
																			<td><?= $row->rejection_qty ?> </td>
																		</tr>
																	<?php endforeach;
																else : ?>
																	<tr>
																		<td colspan="11" class="text-center">No data available in table </td>
																	</tr>
																<?php endif; ?>
															</tbody>
														</table>
													</div>
												</div>
											</div>
											<!-- Rework Process Approval End -->

											<!-- Material Detail Start -->
											<div class="tab-pane fade scrollable" style="height:60vh;" id="req_material" role="tabpanel" aria-labelledby="pills-req_material-tab">
												<div class="card-body">
													<?php if ($dataRow->job_order_status == 0) : ?>
														<div class="col-md-12 form-group">
															<form id="job_bom_data">
																<div class="row">
																	<input type="hidden" name="bom_job_card_id" id="bom_job_card_id" value="<?= $dataRow->id ?>">
																	<input type="hidden" name="bom_product_id" id="bom_product_id" value="<?= $dataRow->product_id ?>">
																	<input type="hidden" name="bom_process_id" id="bom_process_id" value="0">
																	<div class="col-md-6 form-group">
																		<label for="bom_item_id">Item Name</label>
																		<select name="bom_item_id" id="bom_item_id" class="form-control single-select req">
																			<option value="">Select Item Name</option>
																			<?php
																			foreach ($rawMaterial as $row) :
																				echo '<option value="' . $row->id . '">' . $row->item_name . '</option>';
																			endforeach;
																			?>
																		</select>
																	</div>
																	<div class="col-md-3 form-group">
																		<label for="bom_qty">Weight/Pcs</label>
																		<input type="number" name="bom_qty" id="bom_qty" class="form-control floatOnly req" min="0" value="" />
																	</div>
																	<div class="col-md-3 form-group">
																		<label for="">&nbsp;</label>
																		<button type="button" id="addJobBom" class="btn btn-outline-success waves-effect btn-block"><i class="fa fa-plus"></i> Add</button>
																	</div>
																</div>
															</form>
														</div>
													<?php endif; ?>
													<div class="table-responsive">
														<table class="table table-bordered">
															<thead class="thead-info">
																<tr class="text-center">
																	<th>#</th>
																	<th class="text-left">Item Name</th>
																	<th>Weight/Pcs</th>
																	<th>Required Qty.</th>
																	<th>Issue Qty.</th>
																	<th>Used Qty.</th>
																	<th>Stock Qty.</th>
																	<th>Action</th>
																</tr>
															</thead>
															<tbody id="requiredItems">

																<?php
																if (!empty($reqMaterial['result'])) :
																	echo $reqMaterial['result'];
																else :
																	echo '<tr><td colspan="8" class="text-center">No result found.</td></tr>';
																endif;
																?>
															</tbody>
														</table>
													</div>
												</div>
											</div>
											<!-- Material Detail End -->

											<!-- Production Stage Start -->
											<div class="tab-pane fade scrollable" style="height:60vh;" id="production_stages" role="tabpanel" aria-labelledby="pills-production_stages-tab">
												<div class="card-body">
													<div class="col-md-12">
														<div class="row">
															<div class="col-md-9 form-group">
																<label for="stage_id">Production Stages</label>
																<select name="stage_id" id="stage_id" data-input_id="process_id1" class="form-control single-select">
																	<option value="">Select Stage</option>
																	<?php
																	$productProcess = explode(",", $dataRow->process);
																	foreach ($processDataList as $row) :
																		if (!empty($productProcess) && (!in_array($row->id, $productProcess))) :
																			echo '<option value="' . $row->id . '">' . $row->process_name . '</option>';
																		endif;
																	endforeach;
																	?>
																</select>
																<input type="hidden" name="jobID" id="jobID" value="<?= $dataRow->id ?>">
																<input type="hidden" id="rnstages" value="<?= implode(',', $stageData['rnStages']) ?>">
																<input type="hidden" name="item_id" id="item_id" value="<?= $dataRow->product_id ?>" />
															</div>									
															<div class="col-md-3 form-group">
																<label>&nbsp;</label>
																<button type="button" class="btn btn-success waves-effect add-process btn-block addJobStage" data-jobid="<?= $dataRow->id ?>">+ Add</a>
															</div>
														</div>
													</div>
													<div class="table-responsive">
														<!--<table id="<?= $dataRow->tblId ?>" class="table excel_table table-bordered">-->
														<table id="jobStages" class="table excel_table table-bordered">
															<thead class="thead-info">
																<tr>
																	<th style="width:10%;text-align:center;">#</th>
																	<th style="width:65%;">Process Name</th>
																	<th style="width:15%;">Preference</th>
																	<th style="width:10%;">Remove</th>
																</tr>
															</thead>
															<tbody id="stageRows">
																<?php
																if (!empty($stageData)) :
																	$i = 1;
																	foreach ($stageData['stages'] as $row) :
																		echo '<tr id="' . $row['process_id'] . '">
																				<td class="text-center">' . $i++ . '</td>
																				<td>' . $row['process_name'] . '</td>
																				<td class="text-center">' . ($row['sequence'] + 1) . '</td>
																				<td class="text-center">
																					<button type="button" data-pid="' . $row['process_id'] . '" class="btn btn-outline-danger waves-effect waves-light permission-remove removeJobStage"><i class="ti-trash"></i></button>
																				</td>
																			  </tr>';
																	endforeach;
																else :
																	echo '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
																endif;
																?>
															</tbody>
														</table>
													</div>
												</div>
											</div>
											<!-- Production Stage End -->

											<!-- Scrap Weight Start -->
											<div class="tab-pane fade scrollable" style="height:60vh;" id="scrap_weight" role="tabpanel" aria-labelledby="pills-scrap_weight-tab">
												<div class="card-body">
													<div class="table-responsive">
														<table class="table table-striped table-bordered ">
															<thead class="thead-info">
																<tr class="text-center">
																	<th>#</th>
																	<th class="text-left">Process Name</th>
																	<th style="width: 30%;">Weight Per Pcs.</th>
																</tr>
															</thead>
															<tbody>
																<form id="scrapWeight">
																	<?php
																	if (!empty($dataRow->processData)) :
																		$i = 1;
																		foreach ($dataRow->processData as $row) :
																			$scrapeWeightData = (!empty($dataRow->scrap_weight)) ? json_decode($dataRow->scrap_weight) : array();
																			$scrapeWeight = "";
																			if (!empty($scrapeWeightData)) :
																				$processKey = array_search($row->process_id, array_column($scrapeWeightData, 'process_id'));
																				$scrapeWeight = $scrapeWeightData[$processKey]->out_w_pcs;
																			endif;
																	?>
																			<tr class="text-center">
																				<td><?= $i ?></td>
																				<td class="text-left"><?= $row->process_name ?></td>
																				<td><input name="out_w_pcs[]" id="out_w_pcs<?= $i ?>" class="floatOnly outWP form-control" value="<?= (!empty($row->process_approvel_data->finished_weight) ? $row->process_approvel_data->finished_weight : 0) ?>" /><input type="hidden" name="process_id[]" id="process_id<?= $i++ ?>" /><input type="hidden" name="job_id" id="job_id" value="<?= $dataRow->id ?>"></td>
																			</tr>
																		<?php endforeach;
																	else : ?>
																		<tr>
																			<td colspan="3" class="text-center">No data available in table </td>
																		</tr>
																	<?php endif; ?>
																</form>
															</tbody>
														</table>
													</div>
												</div>
											</div>
											<!-- Scrap Weight End -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="returnMaterial" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document" style="max-width:70%;">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel1" style="width:100%;">Production Management</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body jpFWTab">
				<table class="table" style="border-radius:15px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);">
					<tr class="">
						<th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;border-top-left-radius:15px;border-bottom-left-radius:15px;border:0px;">Product</th>
						<th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;" id="ProductItemName"></th>
						<th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;">Process</th>
						<th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;" id="ProductProcessName"></th>
						<th class="text-center text-white" style="background:#aeaeae;width:15%;padding:0.25rem 0.5rem;">Pending Qty.</th>
						<th class="text-left" style="background:#f3f2f2;width:15%;padding:0.25rem 0.5rem;border-top-right-radius:15px; border-bottom-right-radius:15px;border:0px;" id="ProductPendingQty"></th>
					</tr>
				</table>
				<div class="col-md-12 mt-3">
					<input type="hidden" id="ref_id" value="" />
					<input type="hidden" id="rproduct_id" value="" />
					<input type="hidden" id="in_process_id" value="" />
					<input type="hidden" id="job_card_id" value="" />
					<input type="hidden" id="PendingQty" value="" />
				</div>
				<div class="col-md-12">
					<div class="row">

						<input type="hidden" name="trans_type" id="trans_type_r" value="1">

						<div class="col-md-4 form-group">
							<label for="item_id">Item Name</label>
							<input type="text" id="item_name_r" class="form-control" value="" readonly />
							<input type="hidden" name="item_id" id="item_id_r" value="" />
						</div>
						<div class="col-md-3 form-group">
							<label for="location_id">Store Location</label>
							<select id="location_id_r" class="form-control single-select req">
								<option value="">Select Location</option>
							</select>
						</div>
						<div class="col-md-3 form-group">
							<label for="batch_no">Batch No.</label>
							<select id="batch_no_r" class="form-control single-select req">
								<option value="">Select Batch No.</option>
							</select>
						</div>
						<div class="col-md-2 form-group">
							<label for="qty">Qty.</label>
							<input type="number" name="qty" id="qty_r" class="form-control floatOnly req" placeholder="Enter Quantity" value="0" min="0" />
						</div>
						<div class="col-md-10 form-group">
							<label for="remark">Remark</label>
							<input type="text" name="remark" id="remark_r" class="form-control" placeholder="Enter Remark" value="">
						</div>

						<div class="col-md-2 form-group">
							<label>&nbsp;</label>
							<button type="button" class="btn btn-primary waves-effect waves-light btn-block" onclick="returnSave();"><i class="fa fa-check"></i> Save</button>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 mt-10">
							<div class="error item_stock mb-3"></div>
							<div class="table-responsive">
								<table id="returnTable" class="table table-bordered align-items-center" style="width:100%;">
									<thead class="thead-info">
										<tr>
											<th style="width:5%;">#</th>
											<th>Item Name</th>
											<th>Qty</th>
											<th>Remark</th>
											<th class="operatorCol">Operator</th>
											<th class="text-center" style="width:10%;">Action</th>
										</tr>
									</thead>
									<tbody id="returnData">

									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/production_v2/job-card-view.js?v=<?= time() ?>"></script>
<script src="<?php echo base_url(); ?>assets/js/custom/production_v2/process-approval.js?v=<?= time() ?>"></script>