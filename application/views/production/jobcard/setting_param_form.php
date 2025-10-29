<form>
    <div class="col-md-12"> 
        <div class="row">
            <input type="hidden" name="job_approval_id" id="job_approval_id" value="<?=$job_approval_id?>">
            <input type="hidden" name="id" id="id" value="">
          
            <div class="col-md-2 form-group">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control req" max="<?=date("Y-m-d")?>" value="<?=date("Y-m-d")?>"  >
            </div>
            <div class="col-md-3 form-group">
                <label for="tool_no">Tool No.</label>
                <input type="text" name="tool_no" id="tool_no" class="form-control numericOnly req" value="" />
            </div>
            <div class="col-md-4 form-group">
                <label for="insert_id">Insert</label>
                <select name="insert_id" id="insert_id" class="form-control single-select req">
                    <option value="">Select Insert</option>
                    <?php
                        if(!empty($insertData)){
                            foreach($insertData as $row){
                                ?><option value="<?=$row->id?>" data-insert_name="<?=$row->item_name?>"><?=$row->item_name?></option><?php
                            }
                        }
                    ?>
                </select>
                <input type="hidden" name="insert_name" id="insert_name">
            </div>
            <div class="col-md-3 form-group">
                <label for="corner_radius">Corner Redius</label>
                <input type="text" name="corner_radius" id="corner_radius" class="form-control floatOnly " value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="grade">Grade</label>
                <input type="text" name="grade" id="grade" class="form-control " value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="make">Make</label>
                <input type="text" name="make" id="make" class="form-control " value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="cutting_speed">Cutting Speed / RPM </label>
                <input type="text" name="cutting_speed" id="cutting_speed" class="form-control numericOnly " value="" />
            </div>
            <div class="col-md-3 form-group">
                <label for="feed">Feed</label>
                <input type="text" name="feed" id="feed" class="form-control floatOnly " value="" />
            </div>
            <div class="col-md-10 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="" />
            </div>
            <div class="col-md-2 form-group">
                <label for="">&nbsp;</label>
                <button type="button" class="btn waves-effect waves-light btn-success float-right btn-block save-form" onclick="saveSttingParameter('sttingParameter');"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</form>
<hr>
<div class="col-md-12">
    <div class="row">
        <label for="">Transactions : </label>
        <div class="table-responsive">
            <table id='storeLocationTransTable' class="table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th class="text-center" style="width: 5%;">#</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Tool No.</th>
                        <th class="text-center">Insert</th>
                        <th class="text-center">Corner Redius</th>
                        <th class="text-center">Grade</th>
                        <th class="text-center">Make</th>
                        <th class="text-center">Cutting Speed / RPM</th>
                        <th class="text-center">Feed</th>
                        <th class="text-center">Remark</th>
                        <th class="text-center" style="width: 8%;">Action</th>
                    </tr>
                </thead>
                <tbody id="settingParamData">
                    <?=$htmlData?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $(document).on('change','#insert_id',function(){
            var insert_name = $("#insert_id :selected").data("insert_name");
            $("#insert_name").val(insert_name);
        });

    });

    function editSettingParam(data,button){ 
        $.each(data,function(key, value) {
            $("#"+key).val(value);
        }); 
        $("#insert_id").comboSelect();
    }
</script>