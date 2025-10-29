<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=$id?>" />

            <div class="col-md-4 form-group">
                <label for="trans_date">Date</label>
                <input type="date" id="trans_date" class="form-control req" value="<?=$maxDate?>" min="<?=$startYearDate?>" max="<?=$maxDate?>" />
            </div>
            <div class="col-md-5 form-group">
                <label for="sales_executive">Sales Executive</label>
                <select id="sales_executive" class="form-control single-select req" >
                    <option value="">Sales Executive</option>
                    <?php
                        foreach($salesExecutives as $row):
                            echo '<option value="'.$row->id.'">'.$row->emp_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="trans_status">Status</label>
                <select name="trans_status" id="trans_status" class="form-control single-select req">
                    <option value="0">Open</option>
                    <!--<option value="1">Approve</option>-->
                    <option value="2">Close</option>
                </select>
            </div>
            <div class="col-md-10 form-group">
                <label for="f_note">Note</label>
                <textarea id="f_note" class="form-control req" rows="1"></textarea>
            </div>
            <div class="col-md-2 form-group">
                <label for="f_note">&nbsp;</label>
                <button type="button" class="btn btn-block waves-effect waves-light btn-outline-success float-right btn-save" onclick="addFRow();"><i class="fa fa-plus"></i> Add</button>
             </div>
        </div>
        <hr>
        <div class="row">
            <div class="error generalError"></div>
            <div class="table-responsive">
                <table id="followup" class="table table-bordered align-items-center">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th>Date</th>
                            <th>Sales Executive</th>
                            <th>Note</th>
                            <th class="text-center" style="width:10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="followupBody">
                    <?php 
                        if(!empty($dataRow)): $i=1;
                            $followData = (!empty($dataRow->extra_fields))?json_decode($dataRow->extra_fields):array();
                            foreach($followData as $row):
                    ?>
                            <tr>
                                <td><?=$i++?></td>
                                <td>
                                    <?=formatDate($row->trans_date)?>
                                    <input type="hidden" name="trans_date[]" value="<?=$row->trans_date?>">
                                </td>
                                <td>
                                    <?=$row->sales_executiveName?>
                                    <input type="hidden" name="sales_executive[]" value="<?=$row->sales_executive?>">
                                    <input type="hidden" name="sales_executiveName[]" value="<?=$row->sales_executiveName?>">
                                </td>
                                <td>
                                    <?=$row->f_note?>
                                    <input type="hidden" name="f_note[]" value="<?=$row->f_note?>">
                                </td>
                                <td class="text-center"><button type="button" onclick="Remove(this);" class="btn btn-sm btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button></td>
                            </tr>
                    <?php
                            endforeach;
                        endif;
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>