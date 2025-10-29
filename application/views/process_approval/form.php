<form>
    <div class="col-md-12">
        <div class="row">

            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="ref_id" id="ref_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <input type="hidden" name="trans_type" id="trans_type" value="<?=$dataRow->trans_type?>">
            <input type="hidden" name="w_pcs" id="w_pcs" value="<?=$dataRow->in_w_pcs?>">
            <input type="hidden" name="total_weight" id="total_weight" value="<?=$dataRow->in_total_weight?>">
			
			<input type="hidden" name="batch_no" id="batch_no" value="">
			<input type="hidden" name="issue_qty" id="issue_qty" value="0">
			<input type="hidden" name="used_qty" id="used_qty" value="0">
			<input type="hidden" name="req_qty" id="req_qty" value="0">
			<input type="hidden" name="wp_qty" id="wp_qty" value="0">
			
            <div class="col-md-3 form-group">
                <label for="entry_date">Date</label>
                <!--<input type="date" name="entry_date" id="entry_date" class="form-control" max="<?=date("Y-m-d")?>" value="<?=date("Y-m-d")?>" min="<?=$dataRow->minDate?>">-->
                <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?=$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="job_card_id">Job Card No.</label>
                <input type="text" id="job_card_no" class="form-control" value="<?=(!empty($dataRow->job_card_id))?$dataRow->job_prefix.$dataRow->job_no:""?>" readonly />
                <input type="hidden" name="job_card_id" id="job_card_id" value="<?=(!empty($dataRow->job_card_id))?$dataRow->job_card_id:""?>" />
                <div class="error job_card_id"></div>
            </div>
            <div class="col-md-3 form-group">
                <label for="delivery_date">Delivery Date</label>
                <input type="text" id="delivery_date" class="form-control" value="<?=(!empty($dataRow->delivery_date))?date("d-m-Y",strtotime($dataRow->delivery_date)):""?>" readonly />
            </div>
            <div class="col-md-3 form-group">
                <label for="product_id">Product</label>
                <input type="text" id="product_name" class="form-control" value="<?=(!empty($dataRow->product_code))?$dataRow->product_code:""?>" readonly />
                <input type="hidden" name="product_id" id="product_id" value="<?=(!empty($dataRow->product_id))?$dataRow->product_id:""?>" />
                <div class="error product_id"></div>
            </div>
            <div class="col-md-4 form-group">
                <label for="in_process_id">In From Process</label>
                <input type="text" id="in_process_name" class="form-control" value="<?=(!empty($dataRow->in_process_name))?$dataRow->in_process_name:""?>" readonly />
            </div>
            <div class="col-md-4 form-group">
                <label for="in_qty">In Qty.</label>
                <input type="text" name="in_qty" id="in_qty" class="form-control" value="<?=(!empty($dataRow->in_qty))?$dataRow->in_qty:""?>" readonly />
            </div>
            <div class="col-md-4 form-group">
                <label for="out_process_id">Out To Process</label>
                <input type="text" id="out_process_name" class="form-control" value="<?=(!empty($dataRow->out_process_name))?$dataRow->out_process_name:""?>" readonly />
                <input type="hidden" name="out_process_id" id="out_process_id" value="<?=(!empty($dataRow->out_process_id))?$dataRow->out_process_id:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="in_qty">Pending Qty.</label>
                <input type="text" id="pqty" class="form-control" value="<?=(!empty($dataRow->pqty))?$dataRow->pqty:""?>" readonly />
            </div>
            <div class="col-md-4 form-group">
                <label for="out_qty">Out Qty. (Pcs)</label>
                <input type="number" name="out_qty" id="out_qty" class="form-control numericOnly req" value="">
            </div>
            <div class="col-md-4 form-group">
                <label for="in_qty_kg">Out Qty. (Kg)</label>
                <input type="number" name="in_qty_kg" id="in_qty_kg" class="form-control floatOnly req1" value="">
            </div>            
            <div class="col-md-3 form-group">
                <label for="setup_status">Setup Required</label>
                <select name="setup_status" id="setup_status" class="form-control single-select">
                    <option value="1">No</option>
                    <option value="0">Yes</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="setter_id">Setter Name</label>
                <select name="setter_id" id="setter_id" class="form-control single-select">
                    <option value="">Select Setter</option>
                    <?php
                        foreach($employeeData as $row):
                            echo '<option value="'.$row->id.'">['.$row->emp_code.'] '.$row->emp_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group" id="machineNoDiv">
                <label for="machine_id">Machine No.</label>
                <select id="machine_id" name="machine_id" class="form-control single-select">
                    <option value="">Select Machine</option>
                    <?php
                        foreach($machineData as $row):
                            echo '<option value="'.$row->id.'">[ '.$row->item_code.' ] '.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>                
            </div>
            <div class="col-md-3 form-group">
                <label for="material_used_id">Material Batch</label>
                <select name="material_used_id" id="material_used_id" class="form-control">
                    <option value="">Material Batch</option>
                    <?php
                        foreach($materialBatch as $row):
							$pending_qty = $row->issue_qty - $row->used_qty;
							
							if( $pending_qty > 0):
								echo '<option value="'.$row->id.'" data-batch_no="'.$row->batch_no.'" data-issue_qty="'.$row->issue_qty.'" data-used_qty="'.$row->used_qty.'" data-wp_qty="'.$row->wp_qty.'">'.$row->batch_no.'</option>';
							endif;
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="vendor_id">Vendor Name</label>
                <select name="vendor_id" id="vendor_id" class="form-control single-select">
                    <option value="0">In House</option>
                    <?php
                        foreach($vendorData as $row):
                            echo '<option value="'.$row->id.'">'.$row->party_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="job_order_id">Job Order No.</label>
                <select name="job_order_id" id="job_order_id" class="form-control single-select">
                    <option value="">Select Job Order No.</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="job_process_ids">Job Order Process</label>
                <select id="jobProcessSelect" data-input_id="job_process_ids" class="form-control jp_multiselect req" multiple="multiple">
                    
                </select>
                <input type="hidden" name="job_process_ids" id="job_process_ids" value="" />
                <div class="error job_process_ids"></div>
            </div>
            
			<div class="batchMaterial bg-info text-white col-md-6 form-group text-left"></div>
			<div class="reqMaterial bg-info text-white col-md-6 form-group text-right"></div>
            
            <div class="col-md-10 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>
            <input type="hidden" name="material_request" value="1">
            <div class="col-md-2 form-group">
                <label for="">&nbsp;</label>
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-block float-right save-form" onclick="saveOutward('outWard');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>        
    </div>    
</form>
<hr>
<div class="col-md-12">
    <div class="row">
        <label for="">Process Transaction : </label>
        <div class="table-responsive">
            <table id='outwardTransTable' class="table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Batch No.</th>
                        <th>Out Qty.</th>
                        <th style="width:10%;">Action</th>
                    </tr>                            
                </thead>
                <tbody id="outwardTransData">
                    <?php
                        $html = "";$i=1;
                        if(!empty($outwardTrans)):                                
                            foreach($outwardTrans as $row):
                                /* $machineName = array();
                                if((!empty($row->machine_id))):
                                    $machineIds = explode(',',$row->machine_id);
                                    foreach($machineIds as $key=>$value):
                                        $machineData = $this->machine->getMachine($value);
                                        $machineName[] = '[ '.$machineData->machine_no.' ] '.$machineData->machine_description;
                                    endforeach;
                                endif;
                                $machineName = implode(",",$machineName); */
                                $transDate = date("d-m-Y",strtotime($row->entry_date));
                                $transType = ($row->trans_type == 0)?"Regular":"Rework";
                                $deleteBtn = '';
                                if(empty($row->accepted_by)):
                                    $deleteBtn = '<button type="button" onclick="trashOutward('.$row->id.');" class="btn btn-outline-danger waves-effect waves-light permission-remove"><i class="ti-trash"></i></button>';
                                endif;
                                $html .= '<tr>
                                            <td>'.$i++.'</td>
                                            <td>'.$transDate.'</td>
                                            <td>'.$transType.'</td>
                                            <td>'.$row->issue_batch_no.'</td>
                                            <td>'.$row->in_qty.'</td>
                                            <td class="text-center" style="width:10%;">
                                                '.$deleteBtn.'
                                            </td>
                                        </tr>';
                            endforeach;
                        else:
                            $html = '<td colspan="6" class="text-center">No Data Found.</td>';
                        endif;
                        echo $html;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
    
