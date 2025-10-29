<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Incoming Inspection Report</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="InInspection">
                            <div class="col-md-12">
                                <input type="hidden" name="id" value="<?=(!empty($inInspectData->id))?$inInspectData->id:""?>" />
                                <input type="hidden" name="grn_id" value="<?=(!empty($dataRow->grn_id))?$dataRow->grn_id:""?>" />
                                <input type="hidden" name="grn_trans_id" id="grn_trans_id" value="<?=(!empty($dataRow->grn_trans_id))?$dataRow->grn_trans_id:""?>" />
                                <input type="hidden" name="party_id" value="<?=(!empty($dataRow->party_id))?$dataRow->party_id:""?>" />
                                <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:""?>" />
                                <input type="hidden" name="trans_type" value="0" />
                                <div class="row">
									<div class="col-md-8 form-group">
                                        <label for="grn_id">
                                            Grn No : <?=(!empty($dataRow->grn_no))? $dataRow->grn_no:"";?> <br>
                                            Item Name : <?=(!empty($dataRow->item_name))? $dataRow->item_name:"";?>
                                        </label>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <select name="inspection_route" id="inspection_route" class="form-control single-select req" >
                                        <option>Select Route</option>
                                            <option value="ROUTE-1" >ROUTE-1</option>
											<option value="ROUTE-2" >ROUTE-2</option>
											<option value="ROUTE-3" >ROUTE-3</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12">
                                <div class="error general"></div>
                            </div>
							<div class="col-md-12 mt-3">
								<div class="row form-group">
									<div class="table-responsive">
										<table id="preDispatchtbl" class="table table-bordered generalTable">
											<thead class="thead-info">
												<tr style="text-align:center;">
													<th rowspan="2" style="width:5%;">#</th>
													<th rowspan="2">Parameter</th>
													<th rowspan="2">Specification</th>
													<th rowspan="2">Tolerance</th>
													<th colspan="5">Observation on Samples</th>
													<th rowspan="2">Result</th>
                                                </tr>
                                                <tr style="text-align:center;">
													<th>1</th>
													<th>2</th>
													<th>3</th>
													<th>4</th>
													<th>5</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyData">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveInInspection('InInspection','saveInInspection');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url($headData->controller.'/materialInspection')?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    preDispatchtbl();
    setTimeout(function () {
        $('#inspection_route').val('ROUTE-1');$('#inspection_route').comboSelect();
        $('#inspection_route').trigger('change');
    }, 100);

    $(document).on('change','#inspection_route',function(e){
        var inspection_route = $(this).val();
        var item_id = $('#item_id').val();
        var grn_trans_id = $('#grn_trans_id').val();
        if(inspection_route)
        {
            $.ajax({
                url: base_url + controller + '/inInspectionData',
                data: {item_id:item_id,inspection_route:inspection_route,grn_trans_id:grn_trans_id},
                type: "POST",
                dataType:'json',
                success:function(data){
                    $("#preDispatchtbl").dataTable().fnDestroy();
                    $("#tbodyData").html(data.tbodyData);
                    preDispatchtbl();
                }
            });
        }
    });
    
    $(document).on('keyup change','.parameter_limit',function(){
        var specification = $(this).data('specification');
        var limit = $(this).data('lower_limit');
        var parameter_value = $(this).val();
        parameter_value = (parseFloat(parameter_value) > 0)?parseFloat(parameter_value).toFixed(3):0;
        var upper_limit = 0;
        var lower_limit = 0;       
        
        upper_limit = parseFloat(parseFloat(specification) + parseFloat(limit)).toFixed(3);
        lower_limit = parseFloat(parseFloat(specification) - parseFloat(limit)).toFixed(3);
        
        if(parseFloat(specification) > 0 && parseFloat(limit) > 0){
            if(parseFloat(upper_limit) >= parseFloat(parameter_value) && parseFloat(lower_limit) <= parseFloat(parameter_value)){
                $(this).removeClass('bg-danger');
            }else{
                if(parseFloat(parameter_value) > 0){
                    $(this).addClass('bg-danger');
                }else{
                    $(this).removeClass('bg-danger');
                }            
            }
        }
    });
});

function preDispatchtbl()
{
	var preDispatchtbl = $('#preDispatchtbl').DataTable( 
	{
		responsive: true,
		scrollY: '55vh',
        scrollCollapse: true,
		"scrollX": true,
		"scrollCollapse":true,
		//'stateSave':true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:25,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
	});
	preDispatchtbl.buttons().container().appendTo( '#preDispatchtbl_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return preDispatchtbl;
}

function saveInInspection(formId,fnsave){
    setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(); $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            location.href = base_url + 'grn/materialInspection';
        }else{
			initTable();  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            location.href = base_url + 'grn/materialInspection';
        }
	});
}
</script>