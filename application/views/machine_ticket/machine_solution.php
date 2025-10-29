<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:"" ?>" />
            <div class="col-md-5 form-group">
                <label for="solution_by">Solution By</label>
                <input type="text" name="solution_by" id="solution_by" class="form-control req" value="<?=(!empty($dataRow->solution_by)) ? $dataRow->solution_by : "" ?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="solution_date">Solution Date</label>
                <input type="datetime-local" name="solution_date" id="solution_date" class="form-control req" min="<?=(!empty($dataRow->problem_date))?$dataRow->problem_date:date('Y-m-d')?>" max="<?=$maxDate?>" value="<?=(!empty($dataRow->solution_date))?$dataRow->solution_date:$maxDate?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="solution_charge">Solution Charge</label>
                <input type="text" name="solution_charge" id="solution_charge" class="form-control floatOnly" value="<?=(!empty($dataRow->solution_charge)) ? $dataRow->solution_charge : "" ?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="solution_detail">Solution Detail</label>
                <textarea name="solution_detail" id="solution_detail" class="form-control req" placeholder="Solution Detail"><?=(!empty($dataRow->solution_detail))?$dataRow->solution_detail:""?></textarea>
            </div>
        </div>
    </div>
</form>