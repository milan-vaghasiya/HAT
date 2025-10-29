<div class="col-md-12">
    <div class="row">
        <div class="col-md-3 form-group">
            <label for="qty">Add/Reduce</label>
            <select name="log_type" id="log_type" class="form-control" style="mix-width:10%;">
                <option value="1">(+) Add</option>
                <!--<option value="-1">(-) Reduce</option>-->
            </select>
            <input type="hidden" id="job_card_id" value="<?=$job_card_id?>" />
            <input type="hidden" name="log_date" id="log_date" value="<?=date("Y-m-d")?>" />        
        </div>
        <div class="col-md-6 form-group">
            <label for="qty">Quantity</label>
            <input type="text" id="qty" class="form-control numericOnly req" />
        </div>
        <div class="col-md-3 form-group">
            <button type="button" class="btn waves-effect waves-light btn-outline-success mt-30 save-form saveJobQty" ><i class="fa fa-plus"></i> Save</button>
        </div>
    </div>
    <hr>
    <div class="table-responsive">
        <table id="jobTable" class="table table-bordered align-items-center">
            <thead class="thead-info">
                <tr>
                    <th style="width:5%;">#</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th class="text-center" style="width:10%;">Action</th>
                </tr>
            </thead>
            <tbody id="joblogData">
                <?php
                    if(!empty($logData)): $i=1;
                        foreach($logData as $row):
                            $deleteParam = $row->id . ",'Jobcard Log'";
                            $logType = ($row->log_type == 1)?'(+) Add':'(-) Reduce';
                            echo '<tr>
                                <td>'.$i++.'</td>
                                <td>'.formatDate($row->log_date).'</td>
                                <td>'.$logType.'</td>
                                <td>'.$row->qty.'</td>
                                <td><a class="btn btn-sm btn-outline-danger permission-remove" href="javascript:void(0)" onclick="trashJobUpdateQty(' . $deleteParam . ');" datatip="Remove" flow="left"><i class="ti-trash"></i></a></td>
                            </tr>';
                        endforeach;
                    endif;
                ?>
            </tbody>
        </table>
    </div>
</div>