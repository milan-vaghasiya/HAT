<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header text-center">
						<div class="col-md-12 row text-center">
						    <div class="col-md-4"></div>
						    <div class="col-md-4"><h4><u>Packing</u></h4></div>
						    <div class="col-md-4"><h4 class="float-right"><?=(!empty($dataRow->id))?$dataRow->trans_number:$trans_number?></h4></div>
						</div>
					</div>
					<div class="card-body">
                        <form id="savePacking">
                            <div class="col-md-12">
                                <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                                <input type="hidden" name="trans_no" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:""?>">
                                <input type="hidden" name="trans_prefix" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:""?>">
                                <input type="hidden" name="trans_number" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:""?>">
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label for="entry_date">Packing Date</label>
                                        <input type="date" name="entry_date" id="entry_date" class="form-control req" value="<?=(!empty($dataRow->entry_date))?$dataRow->entry_date:date("Y-m-d")?>">
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label for="entry_type">Packing Type</label>
                                        <select name="entry_type" id="entry_type" class="form-control req">
                                            <option value="Regular" <?=(!empty($dataRow->entry_type) && $dataRow->entry_type == "Regular")?"selected":""?>>Regular</option>
                                            <option value="Export" <?=(!empty($dataRow->entry_type) && $dataRow->entry_type == "Export")?"selected":""?>>Export</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 form-group expField" style="<?=(!empty($dataRow->entry_type))?(($dataRow->entry_type != "Export")?"display:none;":""):"display:none;"?>">
                                        <label for="is_final">Packing Status</label>
                                        <select name="is_final" id="is_final" class="form-control req">
                                            <option value="0" <?=(!empty($dataRow->id) && $dataRow->is_final == 0)?"selected":""?>>Tentative</option>
                                            <option value="1" <?=(!empty($dataRow->id))?(( $dataRow->is_final == 1)?"selected":""):"selected"?>>Final</option>
                                        </select>
                                    </div>                                   
                                </div>                                   

                                <hr>

                                <div class="col-md-12 row">
                                    <div class="col-md-6">
                                        <h4>Packing Details : </h4>					
                                    </div>								
                                    <div class="col-md-6 text-right"><button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button></div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div class="error item_name_error"></div>
                                    <div class="row form-group">
                                        <div class="table-responsive ">
                                            <table id="packingItems" class="table table-striped table-borderless">
                                                <thead class="table-info">
                                                    <tr>
                                                        <th style="width:5%;">#</th>
                                                        <th>Package No.</th>
                                                        <th>Box Size (cm)</th>
                                                        <th>Item Name</th>
                                                        <th>Qty Per <br> Box (Nos)</th>
                                                        <th>Total Box <br> (Nos)</th>
                                                        <th>Total Qty. <br> (Nos)</th>
                                                        <th>Net Weight <br> Per Pcs (KG)</th>
                                                        <th>Total Net <br> Weight (KG)</th>
                                                        <th>Packing <br> Weight (KG)</th>
                                                        <th>Wooden Box <br> Weight (KG)</th>
                                                        <th>Total Gross <br> Weight (KG)</th>
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
                                </div>
                            </div>
                        </form>
                    </div>
					<div class="card-footer">
						<div class="col-md-12">
							<button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="savePacking('savePacking');"><i class="fa fa-check"></i> Save</button>
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
				<form id="packingItemForm">
					<div class="col-md-12">

						<div class="row form-group">

							<div id="itemInputs">
								<input type="hidden" name="id" id="id" value="" />
								<!-- <input type="hidden" name="box_size" id="box_size" value=""> -->
                                <input type="hidden" name="trans_child_id" id="trans_child_id" value="0">
                                <input type="hidden" name="item_code" id="item_code" value="">
								<input type="hidden" name="row_index" id="row_index" value="">
                                <input type="hidden" name="ref_id" id="ref_id" value="">
                                <input type="hidden" name="packing_type" id="packing_type" value="1">

							</div>

							<div class="col-md-7 form-group expField" style="<?=(!empty($dataRow->entry_type))?(($dataRow->entry_type != "Export")?"display:none;":""):"display:none;"?>">
                                <label for="trans_main_id">Sales Order No.</label>
                                <select name="trans_main_id" id="trans_main_id" class="form-control single-select req">
                                    <option value="">Select Order No.</option>
                                </select>
                            </div>                            
                            
                            <div class="col-md-5 form-group itemDiv">
                                <label for="item_id">Product </label>
                                <select name="item_id" id="item_id" class="form-control single-select itemList req">
                                    <option value="" data-trans_child_id="0">Selec Product</option>
                                    <?php
                                        if(!empty($itemData)):
                                            foreach($itemData as $row):
                                                echo '<option value="'.$row->id.'" data-trans_child_id="0">'.$row->item_code.'</option>';
                                            endforeach;
                                        endif
                                    ?>
                                </select>
                            </div>
                            <!-- <div class="col-md-4 form-group expField" style="<?=(!empty($dataRow->entry_type))?(($dataRow->entry_type != "Export")?"display:none;":""):"display:none;"?>">
                                <label for="packing_type">Packing Type</label>
                                <select name="packing_type" id="packing_type" class="form-control single-select req">
                                    <option value="">Select Packing Type</option>
                                    <option value="1">Regular Packing</option>
                                    <option value="2">Wooden Box</option>
                                </select>
                            </div> -->
                            <div class="col-md-2 form-group">
                                <label for="package_no">Package No.</label>
                                <input type="text" name="package_no" id="package_no" class="form-control" value="">
                            </div>                            

                            <div class="col-md-7 form-group">
                                <label for="box_id">Packing Material</label>
                                <select name="box_id" id="box_id" class="form-control single-select req">
                                    <option value="" data-qty_per_box="" data-ref_id="" data-wt_pcs="" data-size="">Packing Material</option>
                                    <?php
                                        /* foreach ($boxData as $row) :
                                            echo '<option value="'.$row->id.'" data-ref_id="" data-wt_pcs="" data-qty_per_box="" data-size="'.$row->size.'">' . $row->item_name . '</option>';
                                        endforeach; */
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-3 form-group regular">
                                <label for="qty_per_box">Qty Per Box (Nos)</label>
                                <input type="number" name="qty_per_box" id="qty_per_box" class="form-control numericOnly req totalQtyNos" value="">
                            </div>
                          

                            <div class="col-md-4 form-group">
                                <label for="total_box">Total Box (Nos)</label>
                                <input type="number" name="total_box" id="total_box" class="form-control numericOnly req totalQtyNos" value="">
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="total_qty">Total Qty (Nos)</label>
                                <input type="number" name="total_qty" id="total_qty" class="form-control numericOnly req" value="" readonly>
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="wt_pcs">Net Weight Per Pcs (Kg)</label>
                                <input type="number" name="wt_pcs" id="wt_pcs" class="form-control floatOnly" value="">
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="packing_wt">Packing Weight (Kg)</label>
                                <input type="text" name="packing_wt" id="packing_wt" class="form-control floatOnly" value="">
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="wooden_wt">Wooden Box Weight (Kg)</label>
                                <input type="text" name="wooden_wt" id="wooden_wt" class="form-control floatOnly" value="">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="box_size">Wooden Box Size</label>
                                <input type="text" name="box_size" id="box_size" class="form-control" value="">
                            </div>

                            <div class="col-md-12 finalPacking">
                                <div class="error location_id"></div>
                                <div class="error qty"></div>
                                <div class="error packing_qty"></div>
                                <div class="table-responsive">
                                    <table id='reportTable' class="table table-bordered">
                                        <thead class="thead-info" id="theadData">
                                            <tr>
                                                <th>#</th>
                                                <th>Location</th>	
                                                <th>Batch</th>
                                                <th>Current Stock</th>
                                                <th>Qty.</th>
                                            </tr>
                                        </thead>
                                        <tbody id="batchData">
                                            <tr><td class="text-center" colspan="5">No Data Found.</td></tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th class="text-right" colspan="4">
                                                    Total Qty
                                                    <input type="hidden" name="packing_qty" id="packing_qty" value="">
                                                </th>
                                                <th id="totalQty">0.000</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>				
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
<script src="<?php echo base_url(); ?>assets/js/custom/packing-form.js?v=<?= time() ?>"></script>
<?php
if(!empty($dataRow->itemData)):
    foreach($dataRow->itemData as $row):
        $row->batch_data = $row->json_data;
        $row->row_index = "";
        unset($row->json_data);
        $row = json_encode($row);
        echo '<script>AddRow(' . $row . ');</script>';
    endforeach;
endif;
?>