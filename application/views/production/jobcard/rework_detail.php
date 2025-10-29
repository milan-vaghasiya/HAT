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
								<h4 class="card-title">Rework Detail</h4>
							</div>
							<div class="col-md-6">
								<a href="<?= base_url($headData->controller . '/rework') ?>" class="btn waves-effect waves-light btn-outline-dark float-right"><i class="fa fa-arrow-left"></i> Back</a>

							</div>
						</div>
					</div>
					<div class="card-body">
						<div class="col-md-12">
							<div class="row">
								<!-- Column -->
								<div class="col-lg-12 col-xlg-12 col-md-12">
									<table class="table table-bordered-dark">
										<tr>
											<th>Tag No</th>
											<td><?= $dataRow->tag_prefix . sprintf("%04d", $dataRow->tag_no) ?></td>
											<th>Date </th>
											<td><?= formatDate($dataRow->entry_date) ?></td>
											<th>Rework Quatity </th>
											<td><?= floatval($dataRow->qty) ?></td>
										</tr>
										<tr>
											<th>Job Card No.</th>
											<td><?= ($dataRow->job_prefix . sprintf("%04d", $dataRow->job_no)) ?></td>
											<th>Product </th>
											<td colspan="2"><?= $dataRow->full_name ?></td>
										</tr>


									</table>
								</div>
								<div class="col-lg-12 col-xlg-12 col-md-12">
									<div class="card jpFWTab">
										<nav>
											<div class="nav nav-tabs nav-fill tabLinks" id="nav-tab" role="tablist">
												<a class="nav-item nav-link active productionTab" data-toggle="tab" href="#production_detail" role="tab" aria-controls="nav-home" aria-selected="true"> Production </a>

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
																	<th>Unaccepted <br> Qty</th>
																	<th>Accepted <br> Qty</th>
																	<th>Prod. Ok <br> Qty</th>
																	<th>Pend. Prod. <br> Qty</th>
																	<th>Status</th>
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

																					$button = "";

																					/* Movement Button */
																					$moveParam = "{'id' : ".$approvalData->id.", 'modal_id' : 'modal-xl', 'form_id' : 'movement', 'title' : 'Move To Next Process','button':'close','fnsave' : 'saveProcessMovement', 'fnedit' : 'processMovement','btnSave':'other'}";
																					if($approvalData->ok_qty  > 0 && !empty($approvalData->out_process_id)):		
																						$button .= '<a class="btn btn-warning btn-edit" datatip="Move to Next Process" flow="up" onclick="processMovement('.$moveParam.');"><i class="fa fa-step-forward"></i></a>';
																					endif;

																					/* Accept Button */
																					$unaccpetedQty = (($approvalData->inward_qty) - $approvalData->in_qty);
																					$unaccpetedQty = ($unaccpetedQty > 0)?$unaccpetedQty:0;
																					$acceptParam = "{'id' : ".$approvalData->id.", 'modal_id' : 'acceptInward','pending_qty':".$unaccpetedQty."}";
																					if($unaccpetedQty > 0):	
																						$button .= '<a class="btn btn-success btn-edit" datatip="Accept Inward" flow="up" onclick="acceptInward('.$acceptParam.')"><i class="fa fa-check"></i></a>';
																					endif;	

																					/* Production Log Button */
																					$outParam = "{'id' : " . $approvalData->id . ", 'modal_id' : 'modal-xl', 'form_id' : 'outWard', 'title' : 'Process Moved','button' : 'close'}";
																					if(!empty($approvalData->in_process_id)):
																						$button .= '<a class="btn btn-info btn-edit" href="javascript:void(0)" datatip="Production Log" flow="up" onclick="outward(' . $outParam . ');"><i class="fas fa-file"></i></a>';
																					endif;
																					
																					/* $outParam = "{'id' : " . $approvalData->id . ", 'modal_id' : 'modal-xl', 'form_id' : 'outWard', 'title' : 'Process Moved','button' : 'close'}";
																					$button = '<a class="btn btn-success btn-edit" href="javascript:void(0)" datatip="Move" flow="up" onclick="outward(' . $outParam . ');"><i class="fas fa-paper-plane"></i></a>'; */


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
																			<td class="text-left"><?= $row->vendor ?></td>
																			<td><?=$unaccpetedQty?></td>
																			<td><?=$row->in_qty?></td>
																			<td><?= $row->out_qty ?></td>
																			<td><?= ($row->in_qty - $row->out_qty - $row->total_rejection_qty - $row->total_rework_qty - $row->total_hold_qty) ?></td>

																			<td><?= $row->status ?></td>
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

<div class="modal fade" id="acceptInward" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Accept Inward</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
				<form id="acceptedInwatd">
					<div class="col-md-12"> 
						<div class="row">
							<input type="hidden" name="job_approval_id" id="job_approval_id" value="">
							<input type="hidden" name="trans_type" value="2">
							<div class="col-md-12 form-group">
								<label for="in_qty">Quantity</label>
								<span class="float-right">Unaccepted Qty. : <span id="pending_act_qty">0</span></span>
								<input type="text" name="in_qty" id="in_qty" class="form-control floatOnly" value="">
							</div>
						</div>
					</div>
					
				</form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close save-form" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" onclick="saveAcceptedQty('acceptedInwatd');" class="btn waves-effect waves-light btn-outline-success btn-save save-form"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/production/job-card-view.js?v=<?= time() ?>"></script>