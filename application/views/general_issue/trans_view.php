<form>
    <?php
    /** Created By Mansee @ 19-02-22 */
    ?>
    <div class="col-md-12">
        <div class="row">
            <table class="table">
                <tr>
                    <th style="width: 20%;">Issue Date</th>
                    <td style="width: 30%;"><?= (!empty($dataRow->dispatch_date)) ?date("d-m-y",strtotime($dataRow->dispatch_date))  : date("Y-m-d") ?></td>
                    <th style="width: 25%;">Material Collected By</th>
                    <td style="width: 25%;"><?= (!empty($dataRow->collect_by))?$dataRow->collect_by:'' ?></td>
                </tr>
                <tr>
                    <th>Department</th>
                    <td><?= (!empty($dataRow->dept_name)?$dataRow->dept_name:'') ?></td>
                    <th>Remark</th>
                    <td><?= !empty($dataRow->remark)?$dataRow->remark:'' ?></td>
                </tr>
            </table>
            <div class="col-md-12 form-group">
                <div class="error general_batch_no"></div>
                <div class="table-responsive ">
                    <table  class="table  table-bordered">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Item</th>
                                <th>Location</th>
                                <th>Batch No.</th>
                                <th>Qty.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($dataRow->trans_data)) :

                                $i = 1;
                                foreach ($dataRow->trans_data as $row) :

                            ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= $row->item_name ?></td>
                                        <td><?= '[ ' . $row->store_name . ' ] ' . $row->location ?></td>
                                        <td><?= $row->batch_no ?></td>
                                        <td><?= abs($row->qty) ?></td>
                                    </tr>
                                <?php
                                endforeach;
                            else :
                                ?>
                                <tr>
                                    <td class="text-center" colspan="6">No Data Found</td>
                                </tr>
                            <?php
                            endif;
                            ?>

                        </tbody>
                    </table>

                </div>
            </div>


        </div>
    </div>
</form>