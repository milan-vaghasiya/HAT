<?php $this->load->view('includes/header'); ?>

<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>In Challan</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveInChallan">
                            <div class="col-md-12">

								<input type="hidden" name="challan_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />

								<div class="row form-group">

									<div class="col-md-3 form-group">
                                        <label for="challan_no">Challan No.</label>
                                        <input type="text" name="doc_no" id="doc_no" class="form-control req" value="<?=(!empty($dataRow->doc_no))?$dataRow->doc_no:""?>" />

                                        <input type="hidden" name="challan_prefix" value="<?=(!empty($dataRow->challan_prefix))?$dataRow->challan_prefix:$challan_prefix?>" />

                                        <input type="hidden" name="challan_no" value="<?=(!empty($dataRow->challan_no))?$dataRow->challan_no:$challan_no?>" />
									</div>

									<div class="col-md-3 form-group">
										<label for="challan_date">Challan Date</label>
                                        <input type="date" id="challan_date" name="challan_date" class="form-control req" placeholder="dd-mm-yyyy" aria-describedby="basic-addon2" value="<?=(!empty($dataRow->challan_date))?$dataRow->challan_date:date("Y-m-d")?>" />	
									</div>

									<div class="col-md-6 form-group">
										<label for="party_id">Customer Name</label>
										<select name="party_id" id="party_id" class="form-control single-select req">
											<option value="">Select Customer Name</option>
											<?php
												foreach($partyData as $row):
													$selected = "";
													if(!empty($dataRow->party_id) && $dataRow->party_id == $row->id){$selected = "selected";}
													echo '<option value="'.$row->id.'" '.$selected.' data-party_name="'.$row->party_name.'" >'.$row->party_name.'</option>';
												endforeach;
											?>
										</select>
										<input type="hidden" name="party_name" id="party_name" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>" />
									</div>	
									<div class="col-md-12 form-group">
										<label for="remark">Remark</label>
										<input type="text" name="remark" id="remark" class="form-control" placeholder="Enter Remark" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
									</div>									
								</div>
							</div>
							<hr>
                            <div class="col-md-12 row">
                                <div class="col-md-6"><h4>Item Details : </h4></div>
                                <div class="col-md-6"><button type="button" class="btn btn-outline-success waves-effect float-right add-item" data-toggle="modal" data-target="#itemModel"><i class="fa fa-plus"></i> Add Item</button></div>
                            </div>														
							<div class="col-md-12 mt-3">
								<div class="error item_name_error"></div>
								<div class="row form-group">
									<div class="table-responsive ">
										<table id="inChallanItems" class="table table-striped table-borderless">
											<thead class="thead-info">
												<tr>
													<th style="width:5%;">#</th>
													<th>Item Name</th>
													<th>Qty.</th>
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
															<?=$row->item_name?>
															<input type="hidden" name="item_name[]" value="<?=htmlentities($row->item_name)?>">
															<input type="hidden" name="item_id[]" value="<?=$row->item_id?>">
															<input type="hidden" name="trans_id[]" value="<?=$row->id?>">
															<input type="hidden" name="is_returnable[]" value="<?=$row->is_returnable?>">
															<input type="hidden" name="location_id[]" value="<?=$row->location_id?>">
                                                            <input type="hidden" name="batch_no[]" value="<?=$row->batch_no?>">
														</td>
														<td>
															<?=$row->qty?>
															<input type="hidden" name="qty[]" value="<?=$row->qty?>">
															<input type="hidden" name="unit_id[]" value="<?=$row->unit_id?>">
															<input type="hidden" name="unit_name[]" value="<?=$row->unit_name?>">
														</td>
														<td>
															<?=$row->item_remark?>
															<input type="hidden" name="item_remark[]" value="<?=$row->item_remark?>">
														</td>
														
														<td class="text-center" style="width:10%;">
															<?php if(empty($row->trans_status)): ?>
															    <?php 
                                                                    $row->trans_id = $row->id;
                                                                    $row = json_encode($row);
                                                                ?>
                                                                <button type="button" onclick='Edit(<?=$row?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>
                                                                
															    <button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
															<?php endif; ?>
														</td>
													</tr>
												<?php $i++; endforeach; else: ?>
												<tr id="noData">
													<td colspan="6" class="text-center">No data available in table</td>
												</tr>
												<?php endif; ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveInChallan('saveInChallan');" ><i class="fa fa-check"></i> Save</button>
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
            </div>
            <div class="modal-body">
                <form id="challanItemForm">
                    <div class="col-md-12">

                        <div class="row form-group">
                            <input type="hidden" name="trans_id" id="trans_id" value="" />
							<input type="hidden" name="unit_id" id="unit_id" value="" />
							<input type="hidden" name="location_id" id="location_id" value="137" />

							<div class="col-md-12 form-group">
                                <label for="item_name">Item Name</label>
                                <input type="text" name="item_name" id="item_name" class="form-control req" />
                            </div>
                            <!-- <div class="col-md-6 form-group">
								<label for="location_id">Location</label>
                                <select name="location_id" id="location_id" class="form-control model-select2 req">
									<option value="">Select Location</option>
                                    <?php
										foreach($locationData as $lData):
											echo '<optgroup label="'.$lData['store_name'].'">';
											foreach($lData['location'] as $row):
												echo '<option value="'.$row->id.'">'.$row->location.' </option>';
											endforeach;
											echo '</optgroup>';
                                        endforeach;
									?>
                                </select>
                                <input type="hidden" name="location_name" id="location_name" value="" />
							</div> -->
							<!-- <div class="col-md-6 form-group">
								<label for="batch_no">Heat/Batch No.</label>
								<input type="text" name="batch_no" id="batch_no" class="form-control" value="" />
							</div> -->
                            <div class="col-md-6 form-group">
                                <label for="qty">Quantity</label>
                                <input type="number" name="qty" id="qty" class="form-control floatOnly req" value="0">
                            </div>

                            <!-- <div class="col-md-4 form-group">
                                <label for="unit_id">Unit</label>
								<input type="text" name="unit_name" id="unit_name" class="form-control" value="" readonly/>							
                            </div> -->

                            <div class="col-md-6 form-group">
								<label for="is_returnable">Returnable</label>
								<select name="is_returnable" id="is_returnable" class="form-control">
									<option value="0">No</option>
									<option value="1">Yes</option>
								</select>
							</div>
                            <div class="col-md-12 form-group">
                                <label for="item_remark">Item Remark</label>
                                <input type="text" name="item_remark" id="item_remark" class="form-control" value="">
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
<script src="<?php echo base_url();?>assets/js/custom/in-challan-form.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>