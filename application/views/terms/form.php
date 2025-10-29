<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <div class="col-md-12 form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control req" value="<?= (!empty($dataRow->title)) ? $dataRow->title : "" ?>">
            </div>
            <div class="col-md-12 form-group">
                <label for="conditions">Conditions</label>
                <textarea name="conditions" id="conditions" class="form-control req" rows="2"><?= (!empty($dataRow->conditions)) ? $dataRow->conditions : "" ?></textarea>
            </div>
            <div class="col-md-6 form-group">
                <label for="type">Type</label>
                <select name="typeSelect" id="typeSelect" data-input_id="type" class="form-control jp_multiselect req" multiple="multiple">
                    <?php
                    foreach ($typeArray as $row) :
                        $selected = '';
                        if (!empty($dataRow->type)) {
                            if (in_array($row, explode(',', $dataRow->type))) {
                                $selected = "selected";
                            }
                        }
                        echo '<option value="' . $row . '" ' . $selected . '>' . $row . '</option>';
                    endforeach;
                    ?>
                </select>
                <input type="hidden" name="type" id="type" value="<?= (!empty($dataRow->type)) ? $dataRow->type : "" ?>" />
                <div class="error type"></div>
            </div>
            
            <div class="col-md-6 form-group">
                <label for="default_terms">Category</label>
                <select name="default_terms" id="default_terms" class="form-control single-select">
                    <option value="1" <?= ((!empty($dataRow) && $dataRow->default_terms == 1)? 'selected':'') ?>>General</option>
                    <option value="0" <?= ((!empty($dataRow) && $dataRow->default_terms == 0)? 'selected':'') ?>>Commercial</option>
                </select>
            </div>
        </div>
    </div>
</form>