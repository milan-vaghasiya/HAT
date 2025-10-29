<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Deviation Approval Report</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="deviationReport">
                            <div class="col-md-12">
                                <input type="hidden" name="id" value="<?=(!empty($dData->id))?$dData->id:""?>" />
                                <input type="hidden" name="grn_id" value="<?=(!empty($dataRow->grn_id))?$dataRow->grn_id:""?>" />
                                <input type="hidden" name="grn_trans_id" value="<?=(!empty($dataRow->grn_trans_id))?$dataRow->grn_trans_id:""?>" />
                                <input type="hidden" name="party_id" value="<?=(!empty($dataRow->party_id))?$dataRow->party_id:""?>" />
                                <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:""?>" />
                                <input type="hidden" name="trans_type" value="2" /> 
                                <div class="row">
									<div class="col-md-8 form-group">
                                        <label for="grn_id">
                                            Customer Name : <?=(!empty($dataRow->party_name))? $dataRow->party_name:"";?><br>
                                            Part No : <?=(!empty($product_code))? $product_code:"";?><br>
                                            Date : <?=(!empty($dataRow->grn_date))? formatDate($dataRow->grn_date):"";?><br>
                                            Stage : QC
                                        </label>
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
										<table id="preDispatchtbl" class="table table-bordered generalTable" style="margin-bottom:60px">
											<thead class="thead-info">
												<tr style="text-align:center;">
													<th style="width:5%;">#</th>
													<th >Parameter</th>
													<th>Specification</th>
													<th>Observation</th>
													<th>Qty</th>
													<th>Deviation</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyData">
                                                <?php
                                                    $tbodyData="";$i=1; 
                                                    if(!empty($paramData)):
                                                        foreach($paramData as $row):
                                                            $obj = New StdClass;
                                                            if(!empty($inInspectData)):
                                                                $obj = json_decode($inInspectData->observation_sample);
                                                            endif;
                                                            if(!empty($obj->{$row->id})):
                                                                if($obj->{$row->id}[10] == 'Not Ok'):
                                                                    $objDev = New StdClass;
                                                                    if(!empty($dData)):
                                                                        $objDev = json_decode($dData->observation_sample);
                                                                    endif;
                                                                    $tbodyData.= '<tr>
                                                                                <td style="text-align:center;">'.$i++.'</td>
                                                                                <td>'.$row->parameter.'</td>
                                                                                <td>'.$row->specification.'</td>';
                                                                    
                                                                        if(!empty($obj->{$row->id})):
                                                                            $tbodyData .= '<td><input type="text" name="observation_'.$row->id.'" class="xl_input maxw-150 text-center" value="'.((!empty($objDev->{$row->id}[0]))?$objDev->{$row->id}[0]:'').'"></td>';
                                                                            $tbodyData .= '<td><input type="text" name="qty_'.$row->id.'" class="xl_input maxw-150 text-center" value="'.((!empty($objDev->{$row->id}[1]))?$objDev->{$row->id}[1]:'').'"></td>';
                                                                            $tbodyData .= '<td><input type="text" name="deviation_'.$row->id.'" class="xl_input maxw-150 text-center" value="'.((!empty($objDev->{$row->id}[2]))?$objDev->{$row->id}[2]:'').'"></td></tr>';
                                                                        else:
                                                                            $tbodyData .= '<td><input type="text" name="observation_'.$row->id.'" class="xl_input maxw-150 text-center" value=""></td>';
                                                                            $tbodyData .= '<td><input type="text" name="qty_'.$row->id.'" class="xl_input maxw-150 text-center" value=""></td>';
                                                                            $tbodyData .= '<td><input type="text" name="deviation_'.$row->id.'" class="xl_input maxw-150 text-center" value=""></td></tr>';
                                                                        endif;
                                                                endif;
                                                            endif;
                                                        endforeach;
                                                    else:
                                                        $tbodyData.= '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
                                                    endif;
                                                    echo $tbodyData;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>          
                                    <div class="col-md-10 form-group">
                                        <label for="production_incharge">Comments of Production Incharge :</label>
                                        <input type="text" name="production_incharge" class="form-control" value="<?=(!empty($dData->production_incharge))?$dData->production_incharge:""?>"/>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="production_date">Date</label> 
                                        <input type="date" id="production_date" name="production_date" class=" form-control" value="<?=(!empty($dData->production_date))?$dData->production_date:date('Y-m-d')?>" >
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <label for="qa_incharge">Comments of  QA Incharge :</label>
                                        <input type="text" name="qa_incharge" class="form-control" value="<?=(!empty($dData->qa_incharge))?$dData->qa_incharge:""?>"/>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="qa_date">Date</label> 
                                        <input type="date" id="qa_date" name="qa_date" class=" form-control" value="<?=(!empty($dData->qa_date))?$dData->qa_date:date('Y-m-d')?>" >
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <label for="sales_incharge">Comments of  Sales Incharge :</label>
                                        <input type="text" name="sales_incharge" class="form-control" value="<?=(!empty($dData->sales_incharge))?$dData->sales_incharge:""?>"/>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="sales_date">Date</label> 
                                        <input type="date" id="sales_date" name="sales_date" class=" form-control" value="<?=(!empty($dData->sales_date))?$dData->sales_date:date('Y-m-d')?>" >
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <label for="approval_md">Approval of MD :</label>
                                        <input type="text" name="approval_md" class="form-control" value="<?=(!empty($dData->approval_md))?$dData->approval_md:""?>"/>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="approval_date">Date</label> 
                                        <input type="date" id="approval_date" name="approval_date" class=" form-control" value="<?=(!empty($dData->approval_date))?$dData->approval_date:date('Y-m-d')?>" >
                                    </div>
                                    <div class="col-md-10 form-group">
                                        <label for="approval_customer">Approval of Customer (If Required ) :</label>
                                        <input type="text" name="approval_customer" class="form-control" value="<?=(!empty($dData->approval_customer))?$dData->approval_customer:""?>"/>
                                    </div>
                                    <div class="col-md-2 form-group">
                                        <label for="approval_cust_date">Date</label> 
                                        <input type="date" id="approval_cust_date" name="approval_cust_date" class=" form-control" value="<?=(!empty($dData->approval_cust_date))?$dData->approval_cust_date:date('Y-m-d')?>" >
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveDeviationReport('deviationReport','saveDeviationReport');" ><i class="fa fa-check"></i> Save</button>
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
    
});
function saveDeviationReport(formId,fnsave){
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