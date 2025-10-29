<form>

    <div class="col-md-12">

        <div class="row">

            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />

            <input type="hidden" name="ref_type" id="ref_type" value="29" />

            <div class="col-md-6">

                <label for="ref_date"> Date</label>

                <input type="date" id="ref_date" name="ref_date" class=" form-control req" value="<?=(!empty($dataRow->ref_date))?$dataRow->ref_date:date("Y-m-d")?>" />	



            </div>

            <div class="col-md-6 form-group">

                <label for="item_id">Item Name</label>

                    <select name="item_id" id="item_id" class="form-control single-select req">

                        <option value="">Select Item Name</option>

                        <?php

                            foreach ($itemList as $row) :

                                echo '<option value="' . $row->id . '" >' . $row->item_name . '</option>';

                            endforeach;

                        ?>

                    </select>

            </div>

            <div class="col-md-6 form-group">

                <label for="vendor_id">Vendor</label>

                    <select name="vendor_id" id="vendor_id" class="form-control single-select req">

                        <option value="0">IN House</option>

                        <?php

                            foreach ($vendorList as $row) :

                                echo '<option value="' . $row->id . '" >' . $row->party_name . '</option>';

                            endforeach;

                        ?>

                    </select>

            </div>

            <div class="col-md-6 form-group">

                <label for="job_order_id">Job Work Order</label>

                    <select name="job_order_id" id="job_order_id" class="form-control single-select ">

                        <option value="">Select</option>
                        <?php

                            foreach ($jobNo as $row) :
                                $selected = (!empty($dataRow->job_order_id) && $dataRow->job_order_id == $row->id)?"selected":"";
                              
                                echo "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected." >".getPrefixNumber($row->jwo_prefix,$row->jwo_no)."</option>";
                                
                            endforeach;
                        ?>
                    </select>
            </div>

            <div class="col-md-12 form-group">

                <div class="error location_id"></div>

                <div class="error qty"></div>

                <div class="table-responsive">

                    <table id='reportTable' class="table table-bordered">

                        <thead class="thead-info" id="theadData">

                            <tr>

                                <th>#</th>

                                <th>Location</th>	

                                <th>Batch</th>

                                <th>Current Stock</th>

                                <th>Qty.</th>

                            </tr>

                        </thead>

                        <tbody id="batchData">

                            <tr><td class="text-center" colspan="5">No Data Found.</td></tr>

                        </tbody>

                        <!-- <tfoot>

                            <tr>

                                <th class="text-right" colspan="4">

                                    Total Qty

                                    <input type="hidden" name="qty" id="qty" value="0">

                                </th>

                                <th id="totalQty">0.000</th>

                            </tr>

                        </tfoot> -->

                    </table>

                </div>				

            </div>

            

        </div>

    </div>

</form>

