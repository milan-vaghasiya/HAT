<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Cft Authorization</h4>
                            </div>
                        </div>
                    </div>
                            
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-9 form-group">
                                    <label for="emp_id">Employee </label>
                                    <select name="empSelect" id="empSelect" data-input_id="emp_id" class="form-control jp_multiselect" multiple="multiple">
                                        <?php
                                            foreach ($empDataList as $row) :
                                                $selected = (!empty($empData) && (in_array($row->id, explode(',',$empData)))) ? "selected" : "";
                                                echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->emp_name . '</option>';
                                            endforeach;
                                        ?>
                                    </select>
                                        <input type="hidden" name="emp_id" id="emp_id" value="<?=(!empty($empData) ? $empData:"")?>" />
                                </div>
                                    <div class="col-md-3 form-group">
                                        <label>&nbsp;</label>
                                        <button type="button" class="btn btn-success waves-effect add-process btn-block save-form" onclick="saveCftAuth();">Update</a>
                                    </div>
                            </div>
                        </div>
                    </div>
                                            
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <h6 style="color:#ff0000;font-size:1rem;"><i>Note : Drag & Drop Row to Change Employee Sequance</i></h6>
                                    <table id="itemProcess" class="table excel_table table-bordered">
                                        <thead class="thead-info">
                                            <tr>
                                                <th style="width:10%;text-align:center;">#</th>
                                                <th style="width:50%;">Employee Name</th>
                                                <th style="width:10%;">Department</th>
                                                <th style="width:30%;">Designation</th>
                                                <th style="width:30%;">Sequance</th>
                                            </tr>
                                        </thead>
                                        <tbody id="empData">
                                            <?php
                                            if (!empty($cftData)) :
                                                $i = 1; $html = "";
                                                foreach ($cftData as $row) :
                                                    echo '<tr id="' . $row->id . '">
                                                            <td class="text-center">' . $i++ . '</td>
                                                            <td>' . $row->emp_name . '</td>
                                                            <td>' . $row->name . '</td>
                                                            <td>' . $row->title . '</td>
                                                            <td class="text-center">' . $row->sequence . '</td>
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
                    </div>
                </div>
            </div>
        </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function() {

            $("#itemProcess tbody").sortable({
                items: 'tr',
                cursor: 'pointer',
                axis: 'y',
                dropOnEmpty: false,
                //helper: fixWidthHelper,
                start: function (e, ui) {
                    ui.item.addClass("selected");
                },
                stop: function (e, ui) {
                    ui.item.removeClass("selected");
                    $(this).find("tr").each(function (index) {
                        $(this).find("td").eq(4).html(index+1);
                    });
                },
                update: function () 
                {
                    var ids='';
                    $(this).find("tr").each(function (index) {ids += $(this).attr("id")+",";});
                    var lastChar = ids.slice(-1);
                    if (lastChar == ',') {ids = ids.slice(0, -1);}
                    
                    $.ajax({
                        url: base_url + controller + '/updateEmpSequance',
                        type:'post',
                        data:{id:ids},
                        dataType:'json',
                        global:false,
                        success:function(data){}
                    });
                }
            });	
    	   
});

function saveCftAuth(){ 
        var emp_id = $('#emp_id').val();
        //var i_id = $('#item_id').val();
        $.ajax({ 
            type: "post",   
            url: base_url + "cftAuthorization/saveCftAuth",   
            data: {emp_id:emp_id},
			dataType:'json',
			success:function(data){
				if(data.status==0)
				{
					swal("Sorry...!", data.message, "error");
				}
				else
				{
					$("#empData").html(data.cftHtml);
                    initMultiSelect();
				}
			}
		});
    };

    

</script>