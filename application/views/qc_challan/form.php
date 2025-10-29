<?php $this->load->view('includes/header'); ?>

<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>QC Challan</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveQcChallan">
                            <div class="col-md-12">
								<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />

								<div class="row form-group">
									<div class="col-md-2 form-group">
                                        <label for="challan_no">Challan No.</label>
                                        <div class="input-group">
                                            <input type="text" name="trans_prefix" class="form-control" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$trans_prefix?>" readOnly />
                                            <input type="text" name="trans_no" class="form-control" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$trans_no?>" readOnly />
                                        </div>
									</div>
									<div class="col-md-2 form-group">
										<label for="trans_date">Challan Date</label>
                                        <input type="date" id="trans_date" name="trans_date" class="form-control req" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date("Y-m-d")?>" />	
									</div>
									<div class="col-md-3 form-group">
										<label for="challan_type">Challan Type</label>
										<select id="challan_type" name="challan_type" class="form-control single-select req">
										    <option value="1" <?=((!empty($dataRow->challan_type) && $dataRow->challan_type == 1)?'selected':'')?>>IN-House Issue</option>
										    <option value="2" <?=((!empty($dataRow->challan_type) && $dataRow->challan_type == 2)?'selected':'')?>>Vendor Issue</option>
										    <option value="3" <?=((!empty($dataRow->challan_type) && $dataRow->challan_type == 3)?'selected':'')?>>Calibration</option>
										</select>
									</div>
									<div class="col-md-5 form-group">
										<label for="party_id">Issue From</label>
										<select name="party_id" id="party_id" class="form-control single-select req">
											<option value="0">IN-HOUSE</option>
											<?php
											    if(!empty($dataRow->id) && !empty($dataRow->challan_type) && $dataRow->challan_type != 1):
    												foreach($partyData as $row):
    													$selected = "";
    													if(!empty($dataRow->party_id) && $dataRow->party_id == $row->id){$selected = "selected";}
    													echo '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
    												endforeach;
												else:
													foreach($deptData as $row):
    													$selected = "";
    													if(!empty($dataRow->party_id) && $dataRow->party_id == $row->id){$selected = "selected";}
    													echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
    												endforeach;
												endif;
											?>
										</select>
									</div>	
									<div class="col-md-3 form-group">
										<label for="machine_id">Machine</label>
										<select name="machine_id" id="machine_id" class="form-control single-select">
											<option value="">Select Machine</option>
											<?php
											    if(!empty($dataRow->id)):
    											    if(!empty($dataRow->challan_type) && $dataRow->challan_type == 1):
    													foreach($machineData as $row):
        													$selected = "";
        													if(!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id){$selected = "selected";}
        													echo '<option value="'.$row->id.'" '.$selected.'>'.((!empty($row->item_code))?'['.$row->item_code.'] '.$row->item_name:$row->item_name).'</option>';
        												endforeach;
        											endif;
    											else:
    											    foreach($machineData as $row):
    													$selected = "";
    													if(!empty($dataRow->machine_id) && $dataRow->machine_id == $row->id){$selected = "selected";}
    													echo '<option value="'.$row->id.'" '.$selected.'>'.((!empty($row->item_code))?'['.$row->item_code.'] '.$row->item_name:$row->item_name).'</option>';
    												endforeach;
												endif;
											?>
										</select>
									</div>	
									<div class="col-md-9 form-group">
										<label for="remark">Remark</label>
										<input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
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
										<table id="qcChallanItems" class="table table-striped table-borderless">
											<thead class="thead-info">
												<tr>
													<th style="width:5%;">#</th>
													<th>Item Name</th>
													<th>Item Code</th>
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
														</td>
														<td>
															<?=$row->batch_no?>
															<input type="hidden" name="batch_no[]" value="<?=$row->batch_no?>">
														</td>
														<td>
															<?=$row->item_remark?>
															<input type="hidden" name="item_remark[]" value="<?=$row->item_remark?>">
														</td>
														
														<td class="text-center" style="width:10%;">
															<?php 
															    if(empty($row->trans_status)): 
                                                                    $row->trans_id = $row->id;
                                                                    $row = json_encode($row);
                                                            ?>
                                                                <!--<button type="button" onclick='Edit(<?=$row?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>-->
															    <button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
															<?php endif; ?>
														</td>
													</tr>
												<?php $i++; endforeach;  endif; ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveQcChallan('saveQcChallan');" ><i class="fa fa-check"></i> Save</button>
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
                            <input type="hidden" name="item_name" id="item_name" value="" />
                            <input type="hidden" name="batch_no" id="batch_no" value="" />
                            <input type="hidden" name="row_index" id="row_index" value="" />
                            
							<div class="col-md-12 form-group">
                                <label for="item_id">Item</label>
                                <select name="item_id" id="item_id" class="form-control single-select itemOptions req">
                                    <option value="">Select Item</option>
                                    <?php
                                        foreach($itemData as $row):
                                            echo "<option value='".$row->id."' data-code='".$row->item_code."' data-name='".$row->item_name."'>[".$row->item_code."] ".$row->item_name."</option>";
                                        endforeach;                                        
                                    ?>
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
<script src="<?php echo base_url();?>assets/js/custom/qc-challan-form.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>

<?php 
    if(!empty($challanItem)):
        foreach($challanItem as $row):
            $rowData = new stdClass();
            $rowData->trans_id = "";
            $rowData->item_id = $row->id;
            $rowData->item_name = $row->item_name;
            $rowData->batch_no = $row->item_code;
            $rowData->row_index = "";
            $rowData->item_remark = "";
            $rowData = json_encode($rowData);
            echo '<script>AddRow('.$rowData.');</script>';
        endforeach;
    endif;
?>
