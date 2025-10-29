<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <div class="col-md-12 form-group">
                <label for="head_name">Head Name</label>
                <input type="text" name="head_name" id="head_name" class="form-control req" value="<?=(!empty($dataRow->head_name))?$dataRow->head_name:""?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="type">Type</label> 
                <select name="type" id="type" class="form-control single-select req">
                    <?php 
                        if(!empty($typeArray)):
                            foreach($typeArray as $key=>$value):                                
                                $selected = (!empty($dataRow->type) && $dataRow->type == $key)?"selected":((!empty($dataRow->is_system))?"disabled":"");
                                echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                            endforeach;
                        endif;
                    ?>
                </select>
            </div>  
            <div class="col-md-6 form-group">
                <label for="effect_in">Effect In</label>
                <select name="effect_in" id="effect_in" class="form-control single-select req">
                    <option value="">Select</option>
                    <option value="1" <?=(!empty($dataRow->effect_in) && $dataRow->effect_in == 1)?"selected":""?> >ACTUAL</option>
                    <option value="2" <?=(!empty($dataRow->effect_in) && $dataRow->effect_in == 2)?"selected":""?> >ON PAPER</option>
                    <option value="3" <?=(!empty($dataRow->effect_in) && $dataRow->effect_in == 3)?"selected":""?> >BOTH</option>
                </select>
            </div>          
        </div>
    </div>
</form>