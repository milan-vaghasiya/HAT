<div class="col-md-12">
    <form>
        <div class="row"> 
            <input type="hidden" name="id" class="id" value="" />
            <input type="hidden" name="item_id" class="item_id" value="<?=$item_id?>" />

            <div class="col-md-3 form-group">
                <label for="process_id">Process</label>
                <select id="process_id" name="process_id" class="form-control single-select req">
                    <option value="">Select Process</option>
                    <?php
                        foreach($processList as $row):
                            echo '<option value="'.$row->process_id.'">'.$row->process_name.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error process_id"></div>
            </div>
			<div class="col-md-3 form-group">
                <label for="sub_group">Material Type</label>
                <select  id="sub_group" name="sub_group" class="form-control single-select req">
                    <option value="">Select Material Type</option>
                    <?php
                        foreach ($subGroupList as $row) :
                            echo '<option value="' . $row->id . '">' . $row->title . '</option>';
                        endforeach;
                    ?>
					<!-- <option value="0">Instrument & Gauges</option> -->
                </select>
                <div class="error ref_item_id"></div>
            </div>
            <div class="col-md-6 form-group">
                <label for="ref_item_id">Item</label>
                <select  id="ref_item_id" name="ref_item_id" class="form-control single-select toolingItem req">
                    <option value="">Select Item</option>
                </select>
                <div class="error ref_item_id"></div>
            </div>
			<div class="col-md-2 form-group">
                <label for="req_qty">Req. Qty</label>
                <input type="text" id="req_qty" name="req_qty" class="form-control req" value="" min="0" />
                <div class="error req_qty"></div>
            </div>
			<div class="col-md-2 form-group">
                <label for="used_for">Used For</label>
                 <select  id="used_for" name="used_for" class="form-control single-select req">
                    <option value="1">Per. Pcs</option>
					<option value="2">Per. Job</option>
				</select>
                <div class="error used_for"></div>
            </div>
            <div class="col-md-2 form-group">
                <label for="tool_unit">Tool Unit</label>
                <select  id="tool_unit" name="tool_unit" class="form-control single-select req">
                    <option value="">Select Unit</option>
                    <?php
                        foreach($unitData as $row):
                            echo '<option value="'.$row->unit_name.'">'.$row->unit_name.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error tool_unit"></div>
            </div>
            <div class="col-md-4 form-group">
                <label for="remark">Remark</label>
                <input type="text" id="remark" name="remark" class="form-control" value="" min="0" />
                <div class="error remark"></div>
            </div>
            <div class="col-md-2 form-group">
            <button type="button" class="btn btn-block btn-success waves-light mt-30 save-form" onclick="saveToolConsumption('addToolConsumption');" ><i class="fa fa-plus"></i> Save</button>
            </div>
        </div>
    </form>
    <hr>
    <div class="row">
        <div class="table-responsive">
            <table id="toolConsumption" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Process</th>
                        <th>Item Code</th>
                        <th>Description</th>
						<th>Material Type</th>
                        <th>Unit</th>
                        <th>Req. Qty</th>
                        <th>Used For</th>
                        <th>Remark</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="toolBody">
                    <?php
                        if(!empty($toolConsumptionData['tbody'])):
                            echo $toolConsumptionData['tbody'];
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>