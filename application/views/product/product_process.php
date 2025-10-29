<form>
    <div class="col-md-12">
        <label for="">Product Name : <span id="productNameP"></span></label>
        <input type="hidden" name="item_id" id="item_id_p" value="">
    </div>
    <div class="col-md-12">
        <div class="row">
            <table id="productProcess" class="table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th style="width:20%;text-align:center;">#</th>
                        <th style="width:70%;">Process Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if(!empty($processData)):
                            $i=1;$html = "";
                            foreach($processData as $row):				
                                $checked = (in_array($row->id,array_column($productProcess,'process_id')))?"checked":"";
                                echo '<tr><td class="text-center"><input type="checkbox" id="md_checkbox_'.$i.'" name="process[]" class="filled-in chk-col-success" value="'.$row->id.'" '.$checked.' ><label for="md_checkbox_'.$i.'" class="mr-3"></label></td><td>'.$row->process_name.'</td></tr>';
                                $i++;
                            endforeach;
                        else:
                            echo '<tr><td colspan="2" class="text-center">No data available in table</td></tr>';
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</form>