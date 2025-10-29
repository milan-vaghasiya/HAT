<?php $this->load->view('includes/header'); ?>
<style> 
	.typeahead.dropdown-menu{width:95.5% !important;padding:0px;border: 1px solid #999999;box-shadow: 0 2px 5px 0 rgb(0 0 0 / 26%);}
	.typeahead.dropdown-menu li{border-bottom: 1px solid #999999;}
	.typeahead.dropdown-menu li .dropdown-item{padding: 8px 1em;margin:0;}
</style>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Sales Enquiry</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveSalesEnquiry">
                            <div class="col-md-12">

								<input type="hidden" name="enq_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
								<input type="hidden" name="form_entry_type" value="1" />

								<div class="row form-group">

									<div class="col-md-3">
                                        <label for="enq_no">Enquiry No.</label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="trans_prefix" class="form-control req" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$trans_prefix?>" readonly />
                                            <input type="text" name="trans_no" class="form-control" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$nextTransNo?>" readonly />
                                        </div>
									</div>

									<div class="col-md-2">
										<label for="trans_date">Enquiry Date</label>
                                        <input type="date" id="trans_date" name="trans_date" class=" form-control req" placeholder="dd-mm-yyyy" aria-describedby="basic-addon2" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />	
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
													echo "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".$row->party_name."</option>";
												endforeach;
												if(!empty($dataRow) && $dataRow->party_id == 0):
													echo '<option value="0" data-row="" selected>'.$dataRow->party_name.'</option>';
												endif;
											?>
										</select>
										<input type="hidden" name="party_name" id="party_name" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>" />
									</div>
									<div class="col-md-3 form-group">
										<label for="contact_person">Contact Person</label>
										<input type="text" name="contact_person" id="contact_person" class="form-control req" value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>" />
									</div>
									<div class="col-md-3 form-group">
										<label for="contact_no">Contact Number</label>
										<input type="text" name="contact_no" id="contact_no" class="form-control req" value="<?=(!empty($dataRow->party_mobile))?$dataRow->party_mobile:""?>" />
									</div>
									<div class="col-md-3 form-group">
										<label for="contact_email">Contact Email</label>
										<input type="text" name="contact_email" id="contact_email" class="form-control req" value="<?=(!empty($dataRow->contact_email))?$dataRow->contact_email:""?>" />
									</div>
									<div class="col-md-3 form-group">
										<label for="party_phone">Party Phone</label>
										<input type="text" name="party_phone" id="party_phone" class="form-control" value="<?=(!empty($dataRow->party_phone))?$dataRow->party_phone:""?>" />
									</div>
									<div class="col-md-3 form-group">
										<label for="party_email">Party Email</label>
										<input type="text" name="party_email" id="party_email" class="form-control" value="<?=(!empty($dataRow->party_email))?$dataRow->party_email:""?>" />
									</div>
									<div class="col-md-3 form-group">
										<label for="ref_by">Referance By</label>
                                        <input type="text" id="ref_by" name="ref_by" class=" form-control" value="<?=(!empty($dataRow->ref_by))?$dataRow->ref_by:""?>" />	
									</div>
									<div class="col-md-3 form-group">
										<label for="sales_executive">Sales Executive</label>
										<select name="sales_executive" id="sales_executive" class="form-control single-select" >
											<option value="">Sales Executive</option>
											<?php
												foreach($salesExecutives as $row):
													$selected = (!empty($dataRow->sales_executive) && $dataRow->sales_executive == $row->id)?"selected":"";
													echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
												endforeach;
											?>
										</select>
									</div>
									<div class="col-md-6 form-group">
										<label for="remark">Remark</label>
										<input type="text" name="remark" id="remark" class="form-control" placeholder="Enter Remark" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
									</div>
									<div class="col-md-9 form-group">
										<label for="party_address">Address</label>
										<input type="text" id="party_address" name="party_address" class=" form-control" value="<?=(!empty($dataRow->party_address))?$dataRow->party_address:""?>" />	
									</div>
									<div class="col-md-3 form-group">
										<label for="party_pincode">Pincode</label>
										<input type="text" id="party_pincode" name="party_pincode" class=" form-control" value="<?=(!empty($dataRow->party_pincode))?$dataRow->party_pincode:""?>" />	
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
													<th>Feasibility</th>
													<th style="width:15%;">Reason</th>
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
															<input type="hidden" name="automotive[]" value="<?=$row->automotive?>">
															<input type="hidden" name="from_entry_type[]" value="<?=$row->from_entry_type?>" />

															<input type="hidden" name="item_type[]" value="<?=$row->item_type?>" />
															<input type="hidden" name="item_code[]" value="<?=$row->item_code?>" />
															<input type="hidden" name="item_desc[]" value="<?=$row->item_desc?>" />
															<input type="hidden" name="hsn_code[]" value="<?=$row->hsn_code?>" />
															<input type="hidden" name="gst_per[]" value="<?=$row->gst_per?>" />
															<input type="hidden" name="price[]" value="<?=$row->price?>" />
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
														    <?=$row->feasible?>
															<input type="hidden" name="feasible[]" value="<?=$row->feasible?>">
														</td>
														<td>
															<?=$row->feasibleReason?>
															<input type="hidden" name="item_remark[]" value="<?=$row->item_remark?>">
															<input type="hidden" name="grn_data[]" value="<?=$row->grn_data?>">
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
												<!--<tr id="noData">-->
												<!--	<td colspan="10" class="text-center">No data available in table</td>-->
												<!--</tr>-->
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
                <form id="enquiryItemForm">
                    <div class="col-md-12">

                        <div class="row form-group">
                            <input type="hidden" name="trans_id" id="trans_id" value="" />		
							<input type="hidden" name="from_entry_type" value="0" />
							<input type="hidden" name="item_type" id="item_type" value="1" />
							<input type="hidden" name="item_code" id="item_code" value="" />
							<input type="hidden" name="item_name" id="item_name" value="" />
							<input type="hidden" name="item_desc" id="item_desc" value="" />
							<input type="hidden" name="hsn_code" id="hsn_code" value="" />
							<input type="hidden" name="gst_per" id="gst_per" value="" />
							<input type="hidden" name="price" id="price" value="" />
							<input type="hidden" name="unit_name" id="unit_name" value="" />
							<input type="hidden" name="row_index" id="row_index" value="">

                           <!--  <div class="col-md-12 form-group">
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
                                            echo "<option value='".$row->id."' data-row='".json_encode($row)."'>[".$row->item_code."] ".$row->item_name."</option>";
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
								<label for="automotive">Automotive</label>
								<select name="automotive" id="automotive" class="form-control">
									<?php
									foreach ($automotiveArray as $key => $value) :
										$selected = (!empty($dataRow->automotive) && $dataRow->automotive == $key) ? "selected" : "";
										echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
									endforeach;
									?>
								</select>
							</div>
							<div class="col-md-6 form-group">
								<label for="drg_rev_no">Drg. No.</label>
								<input type="text" name="drg_rev_no" id="drg_rev_no" class="form-control" value="" />
							</div>
							<div class="col-md-6 form-group">
								<label for="rev_no">Rev. No.</label>
								<input type="text" name="rev_no" id="rev_no" class="form-control" value="" />
							</div>
							<div class="col-md-6 form-group">
								<label for="batch_no">Part No.</label>
								<input type="text" name="batch_no" id="batch_no" class="form-control" value="" />
							</div>
							<div class="col-md-6 form-group">
								<label for="feasible">Feasible</label>
								<select name="feasible" id="feasible" class="form-control">
									<option value="Yes">Yes</option>
									<option value="No">No</option>
								</select>
							</div>
                            <div class="col-md-12 form-group">
                                <!--<label for="item_remark">Reason</label>-->
                                <!--<input type="text" name="item_remark" id="item_remark" class="form-control" value="">-->
                                <label for="item_remark">Reason</label>
								<select name="item_remark" id="item_remark" class="form-control single-select req">
									<option value="">Select Reason</option>
									<?php
										foreach ($itemRemark as $row) :
											$selected = (!empty($dataRow->item_remark) && $dataRow->item_remark == $row->id) ? "selected" : "";
											echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->remark . '</option>';
										endforeach;
									?>
								</select>
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
<script src="<?php echo base_url();?>assets/js/custom/sales-enquiry-form.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>