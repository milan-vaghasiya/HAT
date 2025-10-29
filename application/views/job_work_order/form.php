<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
            <div class="col-md-3 form-group">
                <label for="jwo_date">Order Date</label>
                <input type="date" name="jwo_date" id="jwo_date" class="form-control req" value="<?=(!empty($dataRow->id))?$dataRow->jwo_date:$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="jwo_no">Order No.</label>
                <input type="text" id="jwo_no" class="form-control req" value="<?=(!empty($dataRow))?getPrefixNumber($dataRow->jwo_prefix,$dataRow->jwo_no):getPrefixNumber($jobOrderPrefix,$jobOrderNo)?>" readonly>
            </div>
            <div class="col-md-6 form-group">
                <label for="vendor_id">Vendor Name</label>
                <select name="vendor_id" id="vendor_id" class="form-control single-select req">
                    <option value="">Select Vendor</option>
                    <?php
                        foreach($vendorList as $row):
                            $selected = (!empty($dataRow->vendor_id) && $dataRow->vendor_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
             <div class="col-md-3 form-group">
                <label for="item_type">Item Type</label>
                <select name="item_type" id="item_type" class="form-control single-select">
                    <option value="1" <?=(!empty($dataRow->item_type) && $dataRow->item_type == 1)?"selected":""?>>Finish Goods</option>
                    <option value="3" <?=(!empty($dataRow->item_type) && $dataRow->item_type == 3)?"selected":""?>>Raw Material</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="product_id">Product</label>
                <select name="product_id" id="product_id" class="form-control single-select req">
                    <option value="">Select Product</option>
                    <?php
                        foreach($productList as $row):
                            $selected = (!empty($dataRow->product_id) && $dataRow->product_id == $row->id)?"selected":"";
                            if($row->item_type == 1){
                                echo "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".$row->item_code."</option>";
                            }else{
                                echo "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".$row->item_name."</option>";
                            }
                        endforeach; 
                    ?>
                </select>
            </div>
            <div class="col-md-5 form-group">
                <label for="process_id">Process</label>
                <select id="processSelect" data-input_id="process_id" class="form-control jp_multiselect req" multiple="multiple">
                        <?php
                            /* foreach ($processList as $row):
                                $selected = '';
                                if(!empty($dataRow->process_id)){
                                    if (in_array($row->id,explode(',',$dataRow->process_id))) {
                                        $selected = "selected";
                                    }
                                }
                                echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->process_name . '</option>';
                            endforeach; */
                            echo (!empty($dataRow))?$dataRow->vendorProcess:"";
                        ?>
                    </select>
                    <input type="hidden" name="process_id" id="process_id" value="<?=(!empty($dataRow->process_id))?$dataRow->process_id:"" ?>" />
            </div>
            
            <div class="col-md-3 form-group">
                <label for="qty">Qty Pcs.</label>
                <input type="number" name="qty" id="qty" class="form-control floatOnly req" min="0" value="<?=(!empty($dataRow->qty))?$dataRow->qty:""?>" />
                <div class="error qty_pcs"></div>
            </div>
            <div class="col-md-3 form-group">
                <label for="qty_kg">Qty Kg.</label>
                <input type="number" name="qty_kg" id="qty_kg" class="form-control floatOnly req" min="0" value="<?=(!empty($dataRow->qty_kg))?$dataRow->qty_kg:""?>" />
                <div class="error qty_kg"></div>
            </div>
            <div class="col-md-3 form-group">
                <label for="production_days">Prod. Days</label>
                <input type="number" name="production_days" id="production_days" class="form-control req numericOnly" min="0" value="<?=(!empty($dataRow->production_days))?$dataRow->production_days:""?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="scrap">Scrap Returnable?</label>
                <select name="scrap" id="scrap" class="form-control single-select">
                    <option value="0" <?php (!empty($dataRow->scrap) && $dataRow->scrap == 0)?"selected":""?>>NO</option>
                    <option value="1" <?php (!empty($dataRow->scrap) && $dataRow->scrap == 1)?"selected":""?>>YES</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="rate">Rate</label>
                <input type="number" name="rate" id="rate" class="form-control floatOnly req" min="0" value="<?=(!empty($dataRow->rate))?$dataRow->rate:""?>">
            </div>

            <div class="col-md-4 form-group">
                <label for="rate_per">Rate Per</label>
                <select name="rate_per" id="rate_per" class="form-control single-select req">
                    <option value="">Select Rate Per</option> 
                    <option value="1"  <?=(!empty($dataRow->rate_per) && $dataRow->rate_per == "1")?"selected":""?>>Per Pcs.</option>
                    <option value="2"  <?=(!empty($dataRow->rate_per) && $dataRow->rate_per == "2")?"selected":""?>>Per Kg.</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="amount">Amount</label>
                <input type="number" name="amount" id="amount" class="form-control floatOnly" min="0" value="<?=(!empty($dataRow->amount))?$dataRow->amount:""?>" readonly>
            </div>
            <div class="col-md-8 form-group">
                <label for="reamrk">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label>Select Terms <span class="text-danger">*</span></label>
                <button type="button" class="btn btn-outline-success waves-effect btn-block" data-toggle="modal" data-target="#termModel">Terms & Conditions (<span class="termsCounter"><?=($termsCount)?></span>)</button>
                <div class="error term_id"></div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="termModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document" style="max-width:70%;">
            <div class="modal-content animated slideDown">
                <div class="modal-header">
                    <h4 class="modal-title">Terms & Conditions</h4>
                    <button type="button" class="close closeTerms" data-dismiss="" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12 mb-10">
                        <table id="terms_condition" class="table table-bordered dataTable no-footer">
                            <thead class="thead-info">
                                <tr>
                                    <th style="width:10%;">#</th>
                                    <th style="width:25%;">Title</th>
                                    <th style="width:65%;">Condition</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if(!empty($terms)):
                                        $termaData = (!empty($dataRow->terms_conditions))?json_decode($dataRow->terms_conditions):array();
                                        $i=1;$j=0;
                                        foreach($terms as $row):
                                            $checked = "";
                                            $disabled = "disabled";
                                            if(in_array($row->id,array_column($termaData,'term_id'))):
                                                $checked = "checked";
                                                $disabled = "";
                                                $row->conditions = $termaData[$j]->condition;
                                                $j++;
                                            endif;
                                ?>
                                    <tr>
                                        <td style="width:10%;">
                                            <input type="checkbox" id="md_checkbox<?=$i?>" class="filled-in chk-col-success termCheck" data-rowid="<?=$i?>" check="<?=$checked?>" <?=$checked?> />
                                            <label for="md_checkbox<?=$i?>"><?=$i?></label>
                                        </td>
                                        <td style="width:25%;">
                                            <?=$row->title?>
                                            <input type="hidden" name="term_id[]" id="term_id<?=$i?>" value="<?=$row->id?>" <?=$disabled?> />
                                            <input type="hidden" name="term_title[]" id="term_title<?=$i?>" value="<?=$row->title?>" <?=$disabled?> />
                                        </td>
                                        <td style="width:65%;">
                                            <input type="text" name="condition[]" id="condition<?=$i?>" class="form-control" value="<?=$row->conditions?>" <?=$disabled?> />
                                        </td>
                                    </tr>
                                <?php
                                            $i++;
                                        endforeach;
                                    else:
                                ?>
                                <tr>
                                    <td class="text-center" colspan="3">No data available in table</td>
                                </tr>
                                <?php
                                    endif;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">	
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary closeTerms" data-dismiss=""><i class="fa fa-times"></i> Close</button>
                    <button type="button" class="btn waves-effect waves-light btn-outline-success closeTerms" data-dismiss=""><i class="fa fa-check"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
	
    /*var numberOfChecked = $('.termCheck:checkbox:checked').length;
    $(".termsCounter").html(numberOfChecked);
    
	$(document).on("click",".termCheck",function(){
        var id = $(this).data('rowid');
		var numberOfChecked = $('.termCheck:checkbox:checked').length;
		$(".termsCounter").html(numberOfChecked);
        if($("#md_checkbox"+id).attr('check') == "checked"){
            $("#md_checkbox"+id).attr('check','');
            $("#md_checkbox"+id).removeAttr('checked');
            $("#term_id"+id).attr('disabled','disabled');
            $("#term_title"+id).attr('disabled','disabled');
            $("#condition"+id).attr('disabled','disabled');
        }else{
            $("#md_checkbox"+id).attr('check','checked');
            $("#term_id"+id).removeAttr('disabled');
            $("#term_title"+id).removeAttr('disabled');
            $("#condition"+id).removeAttr('disabled');
        }
    });*/
});
</script>