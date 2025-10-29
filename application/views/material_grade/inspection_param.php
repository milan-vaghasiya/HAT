<form>
    <div class="col-md-12">
        <div class="error generalError"></div>
        <div class="row">
            <input type="hidden" name="grade_id" id="grade_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            
            <hr style="width:100%"><div class="col-md-12"><h6>Chemical Composition :</h6></div>
            <div class="col-md-12 form-group">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <?php
                                foreach($specificationData as $row):
                                    if($row->spec_type == 1):
                                        echo '<th>'.$row->param_name.'</th>';
                                    endif;
                                endforeach;
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                                foreach($specificationData as $row):
                                    if($row->spec_type == 1):
                            ?>
                                <td>
                                    <div class="input-group">
                                        <input type="hidden" name="id[]" value="<?=(!empty($row->id))?$row->id:""; ?>" />
                                        <input type="text" name="min_value[]" class="form-control floatOnly" value="<?=(!empty($row->min_value))?$row->min_value:""; ?>" placeholder="Min" />
                                        <input type="text" name="max_value[]" class="form-control floatOnly" value="<?=(!empty($row->max_value))?$row->max_value:""; ?>" placeholder="Max" />
                                    </div>
                                </td>
                            <?php
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
                                        echo '<th>'.$row->param_name.'</th>';
                                    endif;
                                endforeach;
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                                foreach($specificationData as $row):
                                    if($row->spec_type == 2):
                            ?>
                                <td>
                                    <div class="input-group">
                                        <input type="hidden" name="id[]" value="<?=(!empty($row->id))?$row->id:""; ?>" />
                                        <input type="text" name="min_value[]" class="form-control floatOnly" value="<?=(!empty($row->min_value))?$row->min_value:""; ?>" placeholder="Min" />
                                        <input type="text" name="max_value[]" class="form-control floatOnly" value="<?=(!empty($row->max_value))?$row->max_value:""; ?>" placeholder="Max" />
                                    </div>
                                </td>
                            <?php
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
                                        echo '<th>'.$row->param_name.'</th>';
                                    endif;
                                endforeach;
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                                foreach($specificationData as $row):
                                    if($row->spec_type == 6):
                            ?>
                                <td>
                                    <div class="input-group">
                                        <input type="hidden" name="id[]" value="<?=(!empty($row->id))?$row->id:""; ?>" />
                                        <input type="text" name="min_value[]" class="form-control floatOnly" value="<?=(!empty($row->min_value))?$row->min_value:""; ?>" placeholder="Min" />
                                        <input type="text" name="max_value[]" class="form-control floatOnly" value="<?=(!empty($row->max_value))?$row->max_value:""; ?>" placeholder="Max" />
                                    </div>
                                </td>
                            <?php
                                    endif;
                                endforeach;
                            ?>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" class="form-control " value="<?=(!empty($dataRow->remark))?$dataRow->remark:""; ?>" />
            </div>
        </div>
    </div>
</form>
