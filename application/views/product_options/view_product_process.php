<style>
.ui-sortable-handle{cursor: move;}
.ui-sortable-handle:hover{background-color: #daeafa;border-color: #9fc9f3;cursor: move;}
</style>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-9 form-group">
            <label for="process_id">Production Process</label>
            <select name="processSelect" id="processSelect" data-input_id="process_id" class="form-control jp_multiselect" multiple="multiple">
                <?php
                foreach ($processDataList as $row) :
                    if($row->id != 53):
                        $selected = (!empty($productProcess) && (in_array($row->id, $productProcess))) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->process_name . '</option>';
                    endif;
                endforeach;
                ?>
            </select>
            <input type="hidden" name="process_id" id="process_id" value="<?=(!empty($productProcess) ? implode(',',$productProcess):"")?>" />
            <input type="hidden" name="item_id" id="item_id" value="<?=$item_id?>" />
        </div>
        <div class="col-md-3 form-group">
			<label>&nbsp;</label>
            <button type="button" class="btn btn-success waves-effect add-process btn-block save-form" onclick="addProcess()">Update</button>
        </div>
    </div>
</div>
<hr>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-8 form-group">
            <h6 style="color:#ff0000;font-size:1rem;"><i>Note : Drag & Drop Row to Change Process Sequance</i></h6>
        </div>
        <div class="col-md-4 form-group">
            <!-- <button type="button" class="btn btn-outline-warning waves-effect float-right" onclick="fwEffectOnJob(<?=$item_id?>);">FW Effect On Job</button> -->
        </div>       
        
        <table id="itemProcess" class="table excel_table table-bordered">
            <thead class="thead-info">
                <tr>
                    <th style="width:10%;text-align:center;">#</th>
                    <th style="width:40%;">Process Name</th>
                    <th style="width:10%;">Sequence</th>
                    <!--<th style="width:28%;">Operation</th>-->
                    <th style="width:12%;">Finished Weight</th>
                </tr>
            </thead>
            <tbody id="itemProcessData">
                <?php
                if (!empty($processData)) :
                    $i = 1; $html = "";
                    foreach ($processData as $row) :
                        echo '<tr id="' . $row->id . '">
                                <td class="text-center">' . $i++ . '</td>
                                <td>' . $row->process_name . '</td>
                                <td class="text-center">' . $row->sequence . '</td>
                                <!--<td><select name="operationSelect" id="operationSelect'.$row->id.'" data-input_id="operation_id'.$row->id.'" class="form-control jp_multiselect operation_id" multiple="multiple">'.
                                    $productOperation[$row->id]
                                .'</select><input type="hidden" name="operation_id" id="operation_id'.$row->id.'" data-id="'.$row->id.'" value="'.$row->operation.'" /></td>-->
                                <td>
                                    <input type="text" class="form-control floatOnly finished_weight" id="finished_weight'.$row->id.'" data-id="'.$row->id.'" value="'.$row->finished_weight.'" onKeyUp="updateFinishedWeight('.$row->id.')">
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
<!--  -->
<script>
    $(document).ready(function() {
        initMultiSelect();

        $(document).on('change','.operation_id',function(){
		    var operation = $("#operation_id"+$(this).find(":selected").data("id")).val();
            var id = $(this).find(":selected").data("id");

            var eleId = $(this).attr('id');
            var id= eleId.split('operationSelect')[1];
            if(!operation){operation = '';}
            if(id){
                $.ajax({ 
                    type: "post",   
                    url: base_url + "products/saveProductOperation",   
                    data: {operation:operation,id:id},
                    dataType:'json',
                    success:function(data){
                        if(data.status==0){swal("Sorry...!", data.message, "error");}
                        else{initMultiSelect();}
                    }
                });
            }
        });
    });

    function addProcess(){
        var p_id = $('#process_id').val();
        var i_id = $('#item_id').val();
        $.ajax({ 
            type: "post",   
            url: base_url + "products/saveProductProcess",   
            data: {process_id:p_id,item_id:i_id},
			dataType:'json',
			success:function(data){
				if(data.status==0)
				{
					swal("Sorry...!", data.message, "error");
				}
				else
				{
					$("#itemProcessData").html(data.processHtml);
                    initMultiSelect();
				}
			}
		});
    };

    function updateFinishedWeight(id){
        // var operation = $("#operation_id"+$(this).find(":selected").data("id")).val();
        var finished_weight = $('#finished_weight'+id).val(); 
        
        if(id){
            $.ajax({ 
                type: "post",   
                url: base_url + "productOption/saveProductFinishedWeight",   
                data: {finished_weight:finished_weight,id:id},
                dataType:'json',
                success:function(data){
                    if(data.status==0){swal("Sorry...!", data.message, "error");}
                    else{}
                }
            });
        }
    }

    function fwEffectOnJob(product_id){
        $.confirm({
            title: 'Confirm!',
            content: 'Are you sure want to update finished weight in all runing jobs?',
            type: 'red',
            buttons: {   
                ok: {
                    text: "ok!",
                    btnClass: 'btn waves-effect waves-light btn-outline-success',
                    keys: ['enter'],
                    action: function(){
                        $.ajax({
                            url: base_url + controller + '/finishedWeightEffectOnRuningJobs',
                            data: {product_id:product_id},
                            type: "POST",
                            dataType:"json",
                            success:function(data)
                            {
                                if(data.status==0){
                                    toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                }else{
                                    initTable();
                                    toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                }
                            }
                        });
                    }
                },
                cancel: {
                    btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                    action: function(){

                    }
                }
            }
        });
    }

</script>