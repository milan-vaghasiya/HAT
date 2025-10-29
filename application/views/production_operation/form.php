<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-12 form-group">
                <label for="operation_name">Opretion Name</label>
                <input name="operation_name" class="form-control req" placeholder="Operation Name" value="<?=(!empty($dataRow->operation_name))?$dataRow->operation_name:"";?>" />
            </div>
        </div>
    </div>
</form>