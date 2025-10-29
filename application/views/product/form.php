<form enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="item_type" value="<?= (!empty($dataRow->item_type)) ? $dataRow->item_type : 1; ?>" />
			<input type="hidden" name="opening_qty" class="form-control floatOnly" min="0" value="<?= (!empty($dataRow->opening_qty)) ? $dataRow->opening_qty : "0" ?>" />
            
            <div class="col-md-6 form-group">
                <label for="item_name">Item Name</label>
                <textarea name="item_name" id="item_name" class="form-control req"><?=(!empty($dataRow->item_name))?$dataRow->item_name:""?></textarea>

            </div>
            <div class="col-md-6 form-group">
                <label for="party_id">Customer</label>
				<div for="party_id1" class="float-right">	
					<a class="text-primary font-bold waves-effect waves-dark addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-xl" data-function="addParty/1" data-controller="parties" data-class_name="partyOptions" data-form_title="Add Customer">+ Add New</a>
				</div>
                <select name="party_id" id="party_id" class="form-control single-select partyOptions">
                    <option value="0">Select</option>
                    <?php
                    $lastpart='';
                    foreach ($customerList as $row) :
                        if($row->party_type == 1):
                            $selected = '';
                            if(!empty($dataRow->party_id) && $dataRow->party_id == $row->id)
                            {
                                $selected = 'selected';
                                $lastpart='(Last Code : '.$row->last_part.')';
                                $lastpartcode = $row->last_part;
                                $pcode = substr($lastpart,0,5);
                            }
                            echo '<option value="' . $row->id . '" data-party_code="'.$row->party_code.'" ' . '" data-last_part="'.$row->last_part.'" ' . $selected . '>[' . $row->party_code . '] ' . $row->party_name . '</option>';
                        endif;
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="size">Part Size</label>
                <input type="text" name="size" class="form-control" value="<?=htmlentities((!empty($dataRow->size)) ? $dataRow->size : "")?>" />
            </div>
            <!--<div class="col-md-6 form-group">
                <label for="item_alias">Part Alias</label>
                <input type="text" name="item_alias" class="form-control" value="<?=htmlentities((!empty($dataRow->item_alias)) ? $dataRow->item_alias : "")?>" />
            </div>-->
            <div class="col-md-3 form-group">
                <label for="item_code">Product No.</label>
                <input type="text" name="item_code" id="item_code" class="form-control req" value="<?= (!empty($dataRow->item_code)) ? $dataRow->item_code : ""; ?>" />
            </div>
			<div class="col-md-3 form-group">
                <label for="part_no">Cust. Item Code</label>
                <input type="text" name="part_no" class="form-control" value="<?= (!empty($dataRow->part_no)) ? $dataRow->part_no : "" ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="hsn_code">HSN Code</label>
                <select name="hsn_code" id="hsn_code" class="form-control single-select req">
                    <option value="">Select HSN Code</option>
                    <?php
                        foreach ($hsnData as $row) :
                            $selected = (!empty($dataRow->hsn_code) && $dataRow->hsn_code == $row->hsn_code) ? "selected" : "";
                            //echo '<option value="' . floatVal($row->hsn_code) . '" ' . $selected . '>' . floatVal($row->hsn_code) . '</option>';
							echo '<option value="' . floatVal($row->hsn_code) . '" ' . $selected . '>' . floatVal($row->hsn_code) . ' [' . $row->description . ']</option>';
						endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="unit_id">Unit</label>
                <select name="unit_id" id="unit_id" class="form-control single-select req">
                    <option value="0">--</option>
                    <?php
                    foreach ($unitData as $row) :
                        $selected = (!empty($dataRow->unit_id) && $dataRow->unit_id == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>[' . $row->unit_name . '] ' . $row->description . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="item_name">Finish Diamention</label>
					<?php
                        $finishDime = (!empty($dataRow->make_brand))?explode(' X ',$dataRow->make_brand):"";
                    ?>
                <div class="input-group">
                    <input type="text" name="finish_od" id="finish_od" class="form-control" placeholder="OD" value="<?=(!empty($finishDime[0]))?$finishDime[0]:""?>" style="width:33%;" />
                    <input type="text" name="finish_id" id="finish_id" class="form-control" placeholder="ID" value="<?=(!empty($finishDime[1]))?$finishDime[1]:""?>" style="width:34%;" />
                    <input type="text" name="finish_length" id="finish_length" class="form-control" placeholder="Length" value="<?=(!empty($finishDime[2]))?$finishDime[2]:""?>" style="width:33%;" />
                </div>
            </div>
            <div class="col-md-3 form-group">
                <label for="gst_per">GST Per.</label>
                <select name="gst_per" id="gst_per" class="form-control single-select">
                    <?php
                    foreach ($gstPercentage as $row) :
                        $selected = (!empty($dataRow->gst_per) && $dataRow->gst_per == $row['rate']) ? "selected" : "";
                        echo '<option value="' . $row['rate'] . '" ' . $selected . '>' . $row['val'] . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <!--<div class="col-md-3 form-group">
                <label for="price">Default Price</label>
                <input type="number" name="price" id="price" min="0" class="form-control floatOnly" value="<?= (!empty($dataRow->price)) ? $dataRow->price : "" ?>" />
            </div>-->
			<div class="col-md-3 form-group">
                <label for="wt_pcs">FG Weight</label>
                <input type="text" name="wt_pcs" class="form-control" value="<?= (!empty($dataRow->wt_pcs)) ? $dataRow->wt_pcs : "" ?>" />
            </div>
			<div class="col-md-3 form-group">
                <label for="material_grade_id">Material Grade</label>
                <select name="material_grade_id" id="material_grade_id" class="form-control single-select">
                    <option value="">Select Grade</option>
                    <?php
                    foreach ($materialGrades as $row) :
                        $selected = (!empty($dataRow->material_grade_id) && $dataRow->material_grade_id == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->material_grade . ' - '.$row->standard.'</option>';
                    endforeach;
                    ?>
                </select>
                <input type="hidden" id="material_grade" name="material_grade" value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="drawing_file">Drawing File</label>
                <input type="file" name="drawing_file" class="form-control-file" />
            </div>
            <div class="col-md-3 form-group">
                <label for="item_image">Item Image</label>
                <input type="file" name="item_image" class="form-control-file" />
            </div>
            <!--<div class="col-md-4 form-group">
                <label for="min_qty">Minimum Qty.</label>
                <input type="text" name="min_qty" class="form-control floatOnly" value="<?= (!empty($dataRow->min_qty)) ? $dataRow->min_qty : "" ?>" />
            </div>-->
            
            <div class="col-md-3 form-group">
                <label for="drawing_no">Drawing No</label>
                <input type="text" name="drawing_no" class="form-control" value="<?= (!empty($dataRow->drawing_no)) ? $dataRow->drawing_no : "" ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="rev_no">Revision No</label>
                <input type="text" name="rev_no" class="form-control" value="<?= (!empty($dataRow->rev_no)) ? $dataRow->rev_no : "" ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="rev_date">Revision Date</label>
                <input type="date" name="rev_date" class="form-control" value="<?= (!empty($dataRow->rev_date)) ? $dataRow->rev_date : date("Y-m-d") ?>" />
            </div>
            
            
            <div class="col-md-12 form-group">
                <label for="description">Remarks</label>
                <input type="text" name="description" class="form-control" value="<?=(!empty($dataRow->description)) ? $dataRow->description:"" ?>" />
            </div>
        </div>
    </div>
</form>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>
<script>
$(document).ready(function(){
    $(document).on('change','#material_grade_id',function(){
        $('#material_grade').val($("#material_grade_idc").val());
    });
});
