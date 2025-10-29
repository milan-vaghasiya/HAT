<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
                
            <div class="col-md-6 form-group">
                <label for="material_grade">Material Grade</label>
                <select name="material_grade" id="material_grade" class="form-control single-select req" tabindex="-1">
                    <option value="">Select Material Grade</option>
                    <?php $i=1; foreach($materialGrade as $row):
                        $selected = (!empty($dataRow->material_grade) && $dataRow->material_grade == $row->material_grade)?"selected":"";
                    ?>
                        <option value="<?=$row->material_grade?>" <?=$selected?>><?=$row->material_grade?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" id="materialGrade" name="materialGrade" value="" />
            </div>

            <div class="col-md-6 form-group">
                <label for="standard">Standard</label>
                <select name="standard" id="standard" class="form-control single-select req" tabindex="-1">
                    <option value="">Select Standard</option>
                    <?php $i=1; 
                    foreach($standard as $row):
                        $selected = (!empty($dataRow->standard) && $dataRow->standard == $row->standard)?"selected":"";
                    ?>
                        <option value="<?=$row->standard?>" <?=$selected?>><?=$row->standard?></option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" id="standardName" name="standardName" value="" />
            </div>

            <div class="col-md-12"><h6>Chemical Composition :</h6></div>
            <table class="table excel_table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th style="width:40%;text-align:center;">Chemical Composition</th>
                        <th style="width:30%;">Min </th>
                        <th style="width:30%;">Max</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center"><b>C%</b></td>
                        <td class="text-center">
                            <input type="text" name="c_min" class="form-control inputmask-his" value="<?=(!empty($dataRow->c_min))?$dataRow->c_min:""; ?>" />
                        </td>
                        <td class="text-center">
                            <input type="text" name="c_max" class="form-control inputmask-his" value="<?=(!empty($dataRow->c_max))?$dataRow->c_max:""; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">Mn%</td>
                        <td class="text-center">
                            <input type="text" name="mn_min" class="form-control inputmask-his" value="<?=(!empty($dataRow->mn_min))?$dataRow->mn_min:""; ?>" />
                        </td>
                        <td class="text-center">
                            <input type="text" name="mn_max" class="form-control inputmask-his" value="<?=(!empty($dataRow->mn_max))?$dataRow->mn_max:""; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">P%</td>
                        <td class="text-center">
                            <input type="text" name="p_min" class="form-control inputmask-his" value="<?=(!empty($dataRow->p_min))?$dataRow->p_min:""; ?>" />
                        </td>
                        <td class="text-center">
                            <input type="text" name="p_max" class="form-control inputmask-his" value="<?=(!empty($dataRow->p_max))?$dataRow->p_max:""; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">S%</td>
                        <td class="text-center">
                            <input type="text" name="s_min" class="form-control inputmask-his" value="<?=(!empty($dataRow->s_min))?$dataRow->s_min:""; ?>" />
                        </td>
                        <td class="text-center">
                            <input type="text" name="s_max" class="form-control inputmask-his" value="<?=(!empty($dataRow->s_max))?$dataRow->s_max:""; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">Si%</td>
                        <td class="text-center">
                            <input type="text" name="si_min" class="form-control inputmask-his" value="<?=(!empty($dataRow->si_min))?$dataRow->si_min:""; ?>" />
                        </td>
                        <td class="text-center">
                            <input type="text" name="si_max" class="form-control inputmask-his" value="<?=(!empty($dataRow->si_max))?$dataRow->si_max:""; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">Cr%</td>
                        <td class="text-center">
                            <input type="text" name="cr_min" class="form-control inputmask-his" value="<?=(!empty($dataRow->cr_min))?$dataRow->cr_min:""; ?>" />
                        </td>
                        <td class="text-center">
                            <input type="text" name="cr_max" class="form-control inputmask-his" value="<?=(!empty($dataRow->cr_max))?$dataRow->cr_max:""; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">Ni%</td>
                        <td class="text-center">
                            <input type="text" name="ni_min" class="form-control inputmask-his" value="<?=(!empty($dataRow->ni_min))?$dataRow->ni_min:""; ?>" />
                        </td>
                        <td class="text-center">
                            <input type="text" name="ni_max" class="form-control inputmask-his" value="<?=(!empty($dataRow->ni_max))?$dataRow->ni_max:""; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">Mo%</td>
                        <td class="text-center">
                            <input type="text" name="mo_min" class="form-control inputmask-his" value="<?=(!empty($dataRow->mo_min))?$dataRow->mo_min:""; ?>" />
                        </td>
                        <td class="text-center">
                            <input type="text" name="mo_max" class="form-control inputmask-his" value="<?=(!empty($dataRow->mo_max))?$dataRow->mo_max:""; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">N%</td>
                        <td class="text-center">
                            <input type="text" name="n_min" class="form-control inputmask-his" value="<?=(!empty($dataRow->n_min))?$dataRow->n_min:""; ?>" />
                        </td>
                        <td class="text-center">
                            <input type="text" name="n_max" class="form-control inputmask-his" value="<?=(!empty($dataRow->n_max))?$dataRow->n_max:""; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">Other</td>
                        <td class="text-center" colspan="2">
                            <input type="text" name="chemical_other" class="form-control inputmask-his" value="<?=(!empty($dataRow->chemical_other))?$dataRow->chemical_other:""; ?>" />
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="col-md-12"><h5>Mechanical Properties :</h5></div>
            <table class="table excel_table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th style="width:40%;text-align:center;">Mechanical Properties</th>
                        <th style="width:30%;">Min </th>
                        <th style="width:30%;">Max</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center"><b>TS</b></td>
                        <td class="text-center">
                            <input type="text" name="ts_min" class="form-control inputmask-his" value="<?=(!empty($dataRow->ts_min))?$dataRow->ts_min:""; ?>" />
                        </td>
                        <td class="text-center">
                            <input type="text" name="ts_max" class="form-control inputmask-his" value="<?=(!empty($dataRow->ts_max))?$dataRow->ts_max:""; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">YS</td>
                        <td class="text-center">
                            <input type="text" name="ys_min" class="form-control inputmask-his" value="<?=(!empty($dataRow->ys_min))?$dataRow->ys_min:""; ?>" />
                        </td>
                        <td class="text-center">
                            <input type="text" name="ys_max" class="form-control inputmask-his" value="<?=(!empty($dataRow->ys_max))?$dataRow->ys_max:""; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">Elong</td>
                        <td class="text-center">
                            <input type="text" name="elong_min" class="form-control inputmask-his" value="<?=(!empty($dataRow->elong_min))?$dataRow->elong_min:""; ?>" />
                        </td>
                        <td class="text-center">
                            <input type="text" name="elong_max" class="form-control inputmask-his" value="<?=(!empty($dataRow->elong_max))?$dataRow->elong_max:""; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">RA</td>
                        <td class="text-center">
                            <input type="text" name="ra_min" class="form-control inputmask-his" value="<?=(!empty($dataRow->ra_min))?$dataRow->ra_min:""; ?>" />
                        </td>
                        <td class="text-center">
                            <input type="text" name="ra_max" class="form-control inputmask-his" value="<?=(!empty($dataRow->ra_max))?$dataRow->ra_max:""; ?>" />
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="col-md-3 form-group">
                <label for="hardness">Hardness (BHN)</label>
                <input type="text" name="hardness" class="form-control " value="<?=(!empty($dataRow->hardness))?$dataRow->hardness:""; ?>" />
            </div>
            <div class="col-md-9 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" class="form-control " value="<?=(!empty($dataRow->remark))?$dataRow->remark:""; ?>" />
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('keyup','#material_gradec',function(){
        $('#materialGrade').val($(this).val());
    });
    $(document).on('keyup','#standardc',function(){
        $('#standardName').val($(this).val());
    });
});
</script>