<form>
    <div class="col-md-12">
        <div class="row">
            <h6 style="color:#ff0000;font-size:1rem;"><i></i></h6>
            <table class="table excel_table table-bordered">
                <thead class="thead-info">
                <tr>
                    <th style="width:10%;text-align:center;">#</th>
                    <th style="width:40%;">Process Name</th>
                    <th style="width:12%;">Costing</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (!empty($processData)) :
                    $i = 1; $html = "";
                    foreach ($processData as $row) :
                        echo '<tr id="' . $row->id . '">
                                <td class="text-center">' . $i++ . '</td>
                                <td>' . $row->process_name . '</td>
                                <td>
                                    <input type="hidden" name="id[]" id="id'.$row->id.'" value="'.$row->id.'" ">
                                    <input type="text" class="form-control floatOnly" name="costing[]" id="costing'.$row->id.'" value="'.$row->costing.'" ">
                                </td>
                               
                            </tr>';
                    endforeach;
                else :
                    echo '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
                endif;
                ?>
                </tbody>
            </table>
        </div>
    </div>
</form>
