<form>
    <div class="col-md-12">
        <div class="row">
            <h6 style="color:#ff0000;font-size:1rem;"><i>Note : Cycle Time Per Piece</i></h6>
            <table class="table excel_table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th style="width:10%;text-align:center;">#</th>
                        <th style="width:70%;">Process Name</th>
                        <th style="width:20%;">Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($processData)) :
                        $i = 1;
                        $html = "";
                        foreach ($processData as $row) :
                            $pid = (!empty($row->id)) ? $row->id : "";
                            $ct = (!empty($row->cycle_time)) ? $row->cycle_time : "";
                            echo '<tr id="' . $row->id . '">
                                <td class="text-center">' . $i++ . '</td>
                                <td>' . $row->process_name . '</td>
                                <td class="text-center">
                                    <input type="text" name="cycle_time[]" class="form-control inputmask-his" value="' . $ct . '" />
                                    <input type="hidden" name="id[]" value="' . $pid . '" />
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
