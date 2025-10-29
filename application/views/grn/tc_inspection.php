<form>
    <div class="col-md-12">
        <div class="error generalError"></div>
        <div class="row">
            <input type="hidden" name="grn_trans_id" id="grn_trans_id" value="<?=(!empty($gateReceipt->id))?$gateReceipt->id:""; ?>" />
            <input type="hidden" name="grn_id" id="grn_id" value="<?=(!empty($gateReceipt->grn_id))?$gateReceipt->grn_id:""; ?>" />
            <input type="hidden" id="grade_id" value="<?=(!empty($gateReceipt->material_grade_id))?$gateReceipt->material_grade_id:""; ?>" />
            <input type="hidden" id="old_trans_id" value="" />
			<input type="hidden" name="type" value="<?=$type?>" />

            <?php if($type == 1): ?>
				<div class="col-md-2 form-group">
					<label for="ref_tc_no">TC No.</label>
					<select name="reftc" id="reftc" class="form-control single-select">
						<option value="0">Select TC No.</option>
						<option value="-1">New Certificate</option>
						<?php
							if(!empty($tclist)):
								foreach($tclist as $row):
									if(!empty($row->ref_tc_no)):
										echo '<option value="'.$row->ref_tc_no.'" data-old_trans_id="'.$row->grn_trans_id.'">'.$row->ref_tc_no.' ('.$row->batch_no.')</option>';
									endif;
								endforeach;
							endif;
						?>
					</select>
				</div>  
            <?php endif; ?>
            <div class="col-md-2 form-group">
                <label for="ref_tc_no">New TC No.</label>
                <input type="text" id="ref_tc_no" name="ref_tc_no" class="form-control req" value="<?=(!empty($gateReceipt->tc_no))?$gateReceipt->tc_no:""; ?>" readonly />
            </div>
            <div class="col-md-2 form-group">
                <label for="batch_no">Batch/Heat No.</label>
                <input type="text" id="batch_no" name="batch_no" class="form-control" value="<?=(!empty($gateReceipt->batch_no))?$gateReceipt->batch_no:""; ?>" readonly />
            </div>
            <div class="col-md-3 form-group">
                <label for="material_grade">Material Grade</label>
                <input type="text" id="material_grade" class="form-control" value="<?=(!empty($dataRow->material_grade))?$dataRow->material_grade:""; ?>" readonly />
            </div>
            <div class="col-md-3 form-group">
                <label for="standard">Standard </label>
                <input type="text" id="standard" class="form-control" value="<?=(!empty($dataRow->standard))?$dataRow->standard:""; ?>" readonly />
            </div>

            <hr style="width:100%"><div class="col-md-12"><h6>Chemical Composition :</h6></div>
            <div class="col-md-12 form-group">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <?php
                                foreach($specificationData as $row):
                                    if($row->spec_type == 1):
                                        echo '<th>'.$row->param_name.'<br>'.$row->min_value.' - '.$row->max_value.'</th>';
                                    endif;
                                endforeach;
                            ?>
                        </tr>
                    </thead>
                    <tbody id="chemBody">
                        <tr>
                            <?php $i=1;
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
                            ?>
                        </tr>
                    </tbody>
                </table>
            </div>
            <hr style="width:100%"><div class="col-md-12"><h5>Mechanical Properties :</h5></div>
            <div class="col-md-8 form-group">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <?php
                                foreach($specificationData as $row):
                                    if($row->spec_type == 2):
                                        echo '<th>'.$row->param_name.'<br>'.$row->min_value.' - '.$row->max_value.'</th>';
                                    endif;
                                endforeach;
                            ?>
                        </tr>
                    </thead>
                    <tbody id="mechBody">
                        <tr>
                            <?php $i=1;
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
                            ?>

                           
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 form-group">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <?php
                                foreach($specificationData as $row):
                                    if($row->spec_type == 6):
                                        echo '<th>'.$row->param_name.'<br>'.$row->min_value.' - '.$row->max_value.'</th>';
                                    endif;
                                endforeach;
                            ?>
                        </tr>
                    </thead>
                    <tbody id="hardBody">
                        <tr>
                            <?php $i=1;
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
    $(document).on('change keyup',"#reftc",function(){
        var reftc = $(this).val();
        var grade_id = $('#grade_id').val();
        
        var old_trans_id = $('#reftc :selected').data('old_trans_id');
        $('#old_trans_id').val(old_trans_id);

        $('#ref_tc_no').val('');
        if(reftc == -1){
            $("#ref_tc_no").removeAttr("readonly");
            $('#ref_tc_no').val();
        }else{
            $("#ref_tc_no").attr("readonly", "readonly");
            $('#ref_tc_no').val(reftc);
            if(reftc != 0){
                $.ajax({
                    url:base_url + controller + '/getTcParamByTcNo',
                    type:'post',
                    data:{grade_id:grade_id,tc_no:reftc},
                    dataType:'json',
                    success:function(data){
                        $("#chemBody").html("");
                        $("#chemBody").html(data.chemBody);
                        
                        $("#mechBody").html("");
                        $("#mechBody").html(data.mechBody);
                        
                        $("#hardBody").html("");
                        $("#hardBody").html(data.hardBody);
                    }
                });
            }
        }
    });
});
</script>
