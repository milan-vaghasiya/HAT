<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" class="form-control" value="<?=$dataRow->id?>" />
            <table class="table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th class="text-center" style="width: 35%;">Requirement</th>
                        <th class="text-center" style="width: 25%;">Status</th>
                        <th class="text-center" style="width: 60%;">Remark</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $html = "";
                        if(!empty($dataRow->required_test)):
                            foreach(json_decode($dataRow->required_test) as $row):
                                $selectedSatusOk = ($row->test_status == "OK")?"selected":"";
                                $selectedSatusNotOk = ($row->test_status == "NOT OK")?"selected":"";
                                $html .= '<tr>
                                            <td>
                                                <input type="text" name="test_name[]" class="form-control" value="'.$row->test_name.'" readonly />
                                            </td>
                                            <td>
                                                <select name="test_status[]" class="form-control">
                                                    <option value="">Select Status</option>
                                                    <option value="OK" '.$selectedSatusOk.'>OK</option>
                                                    <option value="NOT OK" '.$selectedSatusNotOk.'>NOT OK</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="test_remark[]"  placeholder="Enter Test Remark" class="form-control" value="'.$row->test_remark.'">
                                            </td>
                                        </tr>';
                            endforeach;
                        else:
                            foreach($requiredTestData as $row):
                                $html .= '<tr>
                                            <td>
                                                <input type="text" name="test_name[]" class="form-control" value="'.$row->requirement.'" readonly />
                                            </td>
                                            <td>
                                                <select name="test_status[]" class="form-control">
                                                    <option value="">Select Status</option>
                                                    <option value="OK">OK</option>
                                                    <option value="NOT OK">NOT OK</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="test_remark[]" placeholder="Enter Test Remark" class="form-control" value="">
                                            </td>
                                        </tr>';
                            endforeach;
                        endif;
                        echo $html;
                    ?>
                </tbody>
            </table>
            
        </div>
    </div>
</form>