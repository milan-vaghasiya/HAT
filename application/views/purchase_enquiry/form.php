<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Purchase Enquiry</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="savePurchaseEnquiry">
                            <div class="col-md-12">
								<input type="hidden" name="enq_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
								<input type="hidden" name="req_id" id="req_id" value="<?= (isset($req_id) and !empty($req_id)) ? $req_id : "" ?>" />
                                <div class="row form-group">
									<div class="col-md-3">
                                        <label for="enq_no">Enquiry No.</label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="enq_prefix" class="form-control" value="<?=(!empty($dataRow->enq_prefix))?$dataRow->enq_prefix:$enqPrefix?>" readonly />
                                            <input type="text" name="enq_no" class="form-control req" value="<?=(!empty($dataRow->enq_no))?$dataRow->enq_no:$nextEnqNo?>" readonly />
                                        </div>
									</div>
									<div class="col-md-3">
										<label for="enq_date">Enquiry Date</label>
                                        <input type="date" id="enq_date" name="enq_date" class=" form-control" aria-describedby="basic-addon2" value="<?=(!empty($dataRow->enq_date))?$dataRow->enq_date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />
									</div>
									<div class="col-md-6">
										<label for="supplier_name">Supplier Name</label>
                                        <div for="party_id1" class="float-right">	
                                            <span class="dropdown float-right">
                                                <a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>
                                                <div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
                                                    <div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
                                                    
                                                    <!--<a class="dropdown-item leadAction addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-xl" data-function="addParty/2" data-controller="parties" data-class_name="partyOptions" data-form_title="Add Vendor">+ Vendor</a>-->
                                                    
                                                    <a class="dropdown-item leadActionStatic addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-xl" data-function="addParty/3" data-controller="parties" data-class_name="partyOptions" data-form_title="Add Supplier" > + Supplier</a>
                                                    
                                                </div>
                                            </span>
                                        </div>
										<select name="supplier_id" id="supplier_id" class="form-control single-select partyOptions req">
											<option value="">Select or Enter Supplier Name</option>
											<?php
												foreach($partyData as $row):
                                                    if($row->party_category == 3):
													    $selected = (!empty($dataRow->supplier_id) && $dataRow->supplier_id == $row->id)?"selected":"";
                                                        echo "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".$row->party_name."</option>";
                                                    endif;
												endforeach;
                                                if(!empty($dataRow) && $dataRow->supplier_id == 0):
													echo '<option value="0" data-row="" selected>'.$row->supplier_name.'</option>';
												endif;
											?>
										</select>
                                        <input type="hidden" name="supplier_name" id="supplier_name" class="form-control" value="<?=(!empty($dataRow->supplier_name))?$dataRow->supplier_name:""?>" />
									</div>
                                    <div class="col-md-12">
                                        <label for="remark">Remark</label>
                                        <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
                                    </div>
                                </div>
							</div>
							<hr>
                            <div class="col-md-12 row">
                                <div class="col-md-6"><h4>Item Details : <small class="error item_name"></small></h4></div>
                                <div class="col-md-6"><button type="button" class="btn btn-outline-success waves-effect float-right add-item" data-toggle="modal" data-target="#itemModel"><i class="fa fa-plus"></i> Add Item</button></div>
                            </div>							
							<div class="col-md-12 mt-3">
								<div class="row form-group">
									<div class="table-responsive ">
										<table id="purchaseEnqItems" class="table table-striped table-borderless">
											<thead class="thead-info">
												<tr>
													<th style="width:5%;">#</th>
													<th>Item Name</th>
                                                    <th>Item Type</th>
													<th>Finish Goods</th>
													<th>Qty.</th>
													<th>Unit</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
                                                <?php
                                                $rflag = 0;$i = 1;
                                                if (!empty($reqItem)) :
                                                    $rflag = 1; 
                                                ?>
                                                    <tr>
														<td style="width:5%;">
															<?=$i++?>
														</td>
														<td>
                                                            <?= $reqItem->item_name ?>
															<input type="hidden" name="item_name[]" value="<?=htmlentities($reqItem->item_name)?>">
															<input type="hidden" name="trans_id[]" value="">
															<input type="hidden" name="item_remark[]" value="">
														</td>
                                                        <td>
                                                            <?=($reqItem->item_type == 0)?"Consumable":"Raw Material"?>
                                                            <input type="hidden" name="item_type[]" value="<?=$reqItem->item_type?>">
                                                        </td>
														<td>
														    <?=$reqItem->fgitem_name?>
															<input type="hidden" name="fgitem_id[]" value="<?=$reqItem->fgitem_id?>">
                                                            <input type="hidden" name="fgitem_name[]" value="<?=htmlentities($reqItem->fgitem_name)?>">
														</td>
														<td>
															<?=$reqItem->qty?>
															<input type="hidden" name="qty[]" value="<?=$reqItem->qty?>">
														</td>
														<td>
															<?="[".$reqItem->unit_name."] "?>
															<input type="hidden" name="unit_id[]" value="<?=$reqItem->unit_id?>">
														</td>
														
														<td class="text-center" style="width:10%;">
															<?php if(empty($reqItem->confirm_status)): ?>
                                                                <?php 
                                                                    $reqItem->trans_id = '';
                                                                    $reqItem = json_encode($reqItem);
                                                                ?>
                                                                <button type="button" onclick='Edit(<?=$reqItem?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>
                                                                
															    <button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
															<?php endif; ?>
														</td>
													</tr>
                                                <?php endif; ?>
												<?php 
													if(!empty($dataRow->itemData)): 
													$i=1;
													foreach($dataRow->itemData as $row):
												?>
													<tr>
														<td style="width:5%;">
															<?=$i++?>
														</td>
														<td>
															<?=$row->item_name?>
															<input type="hidden" name="item_name[]" value="<?=htmlentities($row->item_name)?>">
															<input type="hidden" name="trans_id[]" value="<?=$row->id?>">
															<input type="hidden" name="item_remark[]" value="<?=$row->item_remark?>">
														</td>
                                                        <td>
                                                            <?=($row->item_type == 0)?"Consumable":"Raw Material"?>
                                                            <input type="hidden" name="item_type[]" value="<?=$row->item_type?>">
                                                        </td>
														<td>
															<?=$row->fgitem_name?>
															<input type="hidden" name="fgitem_id[]" value="<?=$row->fgitem_id?>">
                                                            <input type="hidden" name="fgitem_name[]" value="<?=htmlentities($row->fgitem_name)?>">
                                                        </td>
														<td>
															<?=$row->qty?>
															<input type="hidden" name="qty[]" value="<?=$row->qty?>">
														</td>
														<td>
															<?="[".$row->unit_name."] ".$row->description?>
															<input type="hidden" name="unit_id[]" value="<?=$row->unit_id?>">
														</td>
														
														<td class="text-center" style="width:10%;">
															<?php if(empty($row->confirm_status)): ?>
                                                                <?php 
                                                                    $row->trans_id = $row->id;
                                                                    $row = json_encode($row);
                                                                ?>
                                                                <button type="button" onclick='Edit(<?=$row?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>
                                                                
															    <button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
															<?php endif; ?>
														</td>
													</tr>
												<?php endforeach; 
                                                else: if ($rflag == 0) : ?>
    												<!--<tr id="noData">-->
    												<!--	<td colspan="7" class="text-center">No data available in table</td>-->
    												<!--</tr>-->
												<?php endif; endif; ?>
											</tbody>
										</table>
									</div>
								</div>
								<hr>								
							</div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveEnquiry('savePurchaseEnquiry');" ><i class="fa fa-check"></i> Save</button>
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
                <form id="enquiryItemForm">
                    <div class="col-md-12">
                        <div class="row form-group">
                            <input type="hidden" name="trans_id" id="trans_id" value="" />
							<input type="hidden" name="row_index" id="row_index" value="">
                            <div class="col-md-12 form-group">
                                <label for="item_name">Item Name</label>
                                <input type="text" name="item_name" id="item_name" class="form-control req" />
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="rm_type">Item Type</label>
                                <select name="item_type" id="item_type" class="form-control">
                                    <?php 
										foreach($itemTypeData as $row):
											if($row->id != 1):
												echo '<option value="'.$row->id.'">'.$row->group_name.'</option>';
											endif;
										endforeach;
									?>
                                </select>
                                <input type="hidden" name="item_type_name" id="item_type_name" value="">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="fgitem_id">Finish Goods <small>(Used In)</small></label>
                                <select name="fgitem_id" id="fgitem_id" class="form-control single-select">
                                    <option value="">Select Finish Goods</option>
                                    <?php
                                        if(!empty($fgItemList) ):
                                            foreach($fgItemList as $row):		
                                               echo '<option value="'.$row->id	.'">'.$row->item_code.'</option>';
                                            endforeach;
                                        endif;                                        
                                    ?>
                                </select>
								<input type="hidden" name="fgitem_name" id="fgitem_name" value="">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="qty">Quantity</label>
                                <input type="number" name="qty" id="qty" class="form-control floatOnly" value="0">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="unit_id">Unit</label>
                                <select name="unit_id" id="unit_id" class="form-control single-select req">
                                    <option value="0">--</option>
                                    <?php
                                        foreach($unitData as $row):
                                            echo '<option value="'.$row->id.'">['.$row->unit_name.'] '.$row->description.'</option>';
                                        endforeach;
                                    ?>
                                </select>
                                <input type="hidden" name="unit_name" id="unit_name" value="" />
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
                <button type="button" class="btn waves-effect waves-light btn-outline-primary saveItem btn-save-close" data-fn="save_close"><i class="fa fa-check"></i> Save & Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/purchase-enquiry-form.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>