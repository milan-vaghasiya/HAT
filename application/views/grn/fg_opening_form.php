<form>
    <div class="col-md-12">
        <div class="error generalError"></div>
        <div class="row">
           
            <input type="hidden" id="grade_id" value="" />
			<input type="hidden" name="type" value="1" />
			<input type="hidden" name="item_type" value="<?=$item_type?>" />



            <?php
            if($item_type == 1){
                ?>
                <div class="col-md-3 form-group">
                    <label for="fg_item_id">Finish Good</label>
                    <select name="fg_item_id" id="fg_item_id" class="form-control single-select">
                        <option value="">Select Item</option>
                        <?php
                            foreach($fgList as $row):
                                echo '<option value="'.$row->id.'"  data-item_type="'.$row->item_type.'"   data-material_grade="'.$row->material_grade.'">[' . $row->item_code . '] '.$row->item_name.'</option>';
                            endforeach;
                        ?>
                    </select>
                </div>
                <?php
            }
            ?>
            <div class="col-md-3 form-group">
                <label for="item_id">Item</label>
                <select name="item_id" id="item_id" class="form-control single-select">
                    <option value="">Select Item</option>
                    <?php
                        foreach($itemList as $row):
                            echo '<option value="'.$row->id.'"  data-item_type="'.$row->item_type.'"   data-material_grade="'.$row->mtr_grade.'">[' . $row->item_code . '] '.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="material_grade">Material Grade</label>
                <select name="material_grade" id="material_grade" class="form-control single-select1 select2">
                    <option value="">Select Material Grade</option>
                    <?php
                        foreach($materialGrade as $row):
                            $selected = (!empty($dataRow->material_grade) && $dataRow->material_grade == $row->material_grade)?"selected":"";
                           
                            ?>
                            <option value="<?=$row->material_grade?>" data-grade_id="<?=$row->id?>" data-standard='<?=$row->standard?>' <?=$selected?> ><?='[' . $row->metal_code . '] '.$row->material_grade?></option>
                            <?php
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="standard">Standard </label>
                <input type="text" id="standard" class="form-control" value="<?=(!empty($dataRow->standard))?$dataRow->standard:""; ?>" readonly />
            </div>
            <div class="col-md-3 form-group">
                <label for="ref_tc_no">New TC No.</label>
                <input type="text" id="ref_tc_no" name="ref_tc_no" class="form-control req" value="<?=(!empty($gateReceipt->tc_no))?$gateReceipt->tc_no:""; ?>"  />
            </div>
           
            <?php
            if($item_type == 1){
                ?>
                <div class="col-md-3 form-group">
                    <label for="batch_no">FG Batch</label>
                    <input type="text" id="batch_no" name="batch_no" class="form-control" value="<?=(!empty($gateReceipt->batch_no))?$gateReceipt->batch_no:""; ?>"  />
                </div>
            <?php
            }
            ?>
            <div class="<?=($item_type == 1)?'col-md-3':'col-md-2'?> form-group">
                <label for="heat_no">Heat No</label>
                <input type="text" id="heat_no" name="heat_no" class="form-control" value="<?=(!empty($gateReceipt->heat_no))?$gateReceipt->heat_no:""; ?>"  />
            </div>
            <div class="<?=($item_type == 1)?'col-md-3':'col-md-2'?> form-group">
                <label for="qty">Qty</label>
                <input type="text" id="qty" name="qty" class="form-control" value="<?=(!empty($gateReceipt->qty))?$gateReceipt->qty:""; ?>"  />
            </div>
            <div class="<?=($item_type == 1)?'col-md-12':'col-md-8'?> form-group">
                <label for="remark">Remark</label>
                <input type="text" id="remark" name="remark" class="form-control" value="<?=(!empty($gateReceipt->remark))?$gateReceipt->remark:""; ?>"  />
            </div>
            <hr style="width:100%"><div class="col-md-12"><h6>Chemical Composition :</h6></div>
            <div class="col-md-12 form-group">
                <table class="table table-bordered">
                    <thead class="thead-info" id="chemThead">
                        <tr class="text-center">
                            <?php
                            if(!empty($specificationData)):
                                foreach($specificationData as $row):
                                    if($row->spec_type == 1):
                                        echo '<th>'.$row->param_name.'<br>'.$row->min_value.' - '.$row->max_value.'</th>';
                                    endif;
                                endforeach;
                            endif;
                            ?>
                        </tr>
                    </thead>
                    <tbody id="chemBody">
                        <tr>
                            <?php $i=1;
                            if(!empty($specificationData)):
                                foreach($specificationData as $row):
                                    if($row->spec_type == 1):
                            ?>
                                <td>
                                    <div class="input-group">
                                        <input type="hidden" name="id[]" value="<?=(!empty($row->id))?$row->id:""; ?>" />
                                        <input type="hidden" name="grade_id[]" value="<?=(!empty($row->grade_id))?$row->grade_id:""; ?>" />
                                        <input type="hidden" name="spec_type[]" value="<?=(!empty($row->spec_type))?$row->spec_type:""; ?>" />
                                        <input type="hidden" name="param_name[]" value="<?=(!empty($row->param_name))?$row->param_name:""; ?>" />
                                        <input type="hidden" name="sub_param[]" value="<?=(!empty($row->sub_param))?$row->sub_param:""; ?>" />
                                        <input type="hidden" name="min_value[]" id="min_1<?= $i ?>" value="<?=(!empty($row->min_value))?$row->min_value:""; ?>" />
                                        <input type="hidden" name="max_value[]" id="max_1<?= $i ?>" value="<?=(!empty($row->max_value))?$row->max_value:""; ?>" />
                                        <input type="text" name="result[]" class="form-control floatOnly checkResult" data-rowid="1<?=$i?>" value="<?=(!empty($row->result))?$row->result:""; ?>" placeholder="Result" />
                                    </div><br>
                                    <div class="error 1<?=$i?>"></div>
                                </td>
                            <?php       $i++;
                                    endif;
                                endforeach;
                            endif;
                            ?>
                        </tr>
                    </tbody>
                </table>
            </div>
            <hr style="width:100%"><div class="col-md-12"><h5>Mechanical Properties :</h5></div>
            <div class="col-md-8 form-group">
                <table class="table table-bordered">
                    <thead class="thead-info" id="mechThead">
                        <tr class="text-center">
                            <?php
                            if(!empty($specificationData)):
                                foreach($specificationData as $row):
                                    if($row->spec_type == 2):
                                        echo '<th>'.$row->param_name.'<br>'.$row->min_value.' - '.$row->max_value.'</th>';
                                    endif;
                                endforeach;
                            endif;
                            ?>
                        </tr>
                    </thead>
                    <tbody id="mechBody">
                        <tr>
                            <?php $i=1; 
                             if(!empty($specificationData)):
                                foreach($specificationData as $row):
                                    if($row->spec_type == 2):
                            ?>
                                <td>
                                    <div class="input-group">
                                        <input type="hidden" name="id[]" value="<?=(!empty($row->id))?$row->id:""; ?>" />
                                        <input type="hidden" name="grade_id[]" value="<?=(!empty($row->grade_id))?$row->grade_id:""; ?>" />
                                        <input type="hidden" name="spec_type[]" value="<?=(!empty($row->spec_type))?$row->spec_type:""; ?>" />
                                        <input type="hidden" name="param_name[]" value="<?=(!empty($row->param_name))?$row->param_name:""; ?>" />
                                        <input type="hidden" name="sub_param[]" value="<?=(!empty($row->sub_param))?$row->sub_param:""; ?>" />
                                        <input type="hidden" name="min_value[]" id="min_2<?= $i ?>" value="<?=(!empty($row->min_value))?$row->min_value:""; ?>" />
                                        <input type="hidden" name="max_value[]" id="max_2<?= $i ?>" value="<?=(!empty($row->max_value))?$row->max_value:""; ?>" />
                                        <input type="text" name="result[]" class="form-control floatOnly checkResult" data-rowid="2<?=$i?>" value="<?=(!empty($row->result))?$row->result:""; ?>" placeholder="Result" />
                                    </div><br>
                                    <div class="error 2<?=$i?>"></div>
                                </td>
                            <?php       $i++;
                                    endif;
                                endforeach;
                            endif;
                            ?>

                           
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 form-group">
                <table class="table table-bordered">
                    <thead class="thead-info" id="hardThead">
                        <tr class="text-center">
                            <?php
                            if(!empty($specificationData)):
                                foreach($specificationData as $row):
                                    if($row->spec_type == 6):
                                        echo '<th>'.$row->param_name.'<br>'.$row->min_value.' - '.$row->max_value.'</th>';
                                    endif;
                                endforeach;
                            endif;
                            ?>
                        </tr>
                    </thead>
                    <tbody id="hardBody">
                        <tr>
                            <?php $i=1;
                            if(!empty($specificationData)):
                                foreach($specificationData as $row):
                                    if($row->spec_type == 6):
                            ?>
                                <td>
                                    <div class="input-group">
                                        <input type="hidden" name="id[]" value="<?=(!empty($row->id))?$row->id:""; ?>" />
                                        <input type="hidden" name="grade_id[]" value="<?=(!empty($row->grade_id))?$row->grade_id:""; ?>" />
                                        <input type="hidden" name="spec_type[]" value="<?=(!empty($row->spec_type))?$row->spec_type:""; ?>" />
                                        <input type="hidden" name="param_name[]" value="<?=(!empty($row->param_name))?$row->param_name:""; ?>" />
                                        <input type="hidden" name="sub_param[]" value="<?=(!empty($row->sub_param))?$row->sub_param:""; ?>" />
                                        <input type="hidden" name="min_value[]" id="min_3<?= $i ?>" value="<?=(!empty($row->min_value))?$row->min_value:""; ?>" />
                                        <input type="hidden" name="max_value[]" id="max_3<?= $i ?>"value="<?=(!empty($row->max_value))?$row->max_value:""; ?>" />
                                        <input type="text" name="result[]" class="form-control floatOnly checkResult" data-rowid="3<?=$i?>" value="<?=(!empty($row->result))?$row->result:""; ?>" placeholder="Result" />
                                    </div><br>
                                    <div class="error 3<?=$i?>"></div>
                                </td>
                            <?php       $i++;
                                    endif;
                                endforeach;
                            endif;
                            ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function(){
    $(document).on('change',"#material_grade",function(){
        var grade_id = $("#material_grade :selected").data('grade_id');
        var standard = $("#material_grade :selected").data('standard');
        $("#grade_id").val(grade_id);
        $("#standard").val(standard);
        $("#chemBody").html("");$("#chemThead").html("");
        $("#mechBody").html("");$("#mechThead").html("");
        $("#hardBody").html("");$("#hardThead").html("");

        if(grade_id){
            $.ajax({
                url:base_url + controller + '/getTcParamByMaterialGrade',
                type:'post',
                data:{grade_id:grade_id,tc_no:-1},
                dataType:'json',
                success:function(data){
                    $("#chemBody").html(data.chemBody);
                    $("#chemThead").html(data.chemThead);
                    
                    $("#mechBody").html(data.mechBody);
                    $("#mechThead").html(data.mechThead);
                    
                    $("#hardBody").html(data.hardBody);
                    $("#hardThead").html(data.hardThead);
                }
            });
        }
        
    });

    $(document).on('change',"#item_id",function(){
        var item_type = $("#item_id :selected").data('item_type');
        var material_grade = $("#item_id :selected").data('material_grade');
      

        if(item_type == 3){
            $("#material_grade").val(material_grade);
            $("#material_grade ").val(material_grade);
            //$("#material_grade").comboSelect();
            $("#material_grade option[value='"+material_grade+"']").removeAttr("disabled");
            $("#material_grade option[value!='"+material_grade+"']").attr("disabled",true);
			setTimeout(function(){  $("#material_grade").trigger('change'); }, 100);

        }
        
    });
});
</script>
