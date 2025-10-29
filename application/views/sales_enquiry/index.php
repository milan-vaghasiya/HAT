<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="statusTab('salesEnquiryTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('salesEnquiryTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('salesEnquiryTable',2);" class=" btn waves-effect waves-light btn-outline-primary" style="outline:0px" data-toggle="tab" aria-expanded="false">Regreted</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">Sales Enquiry</h4>
                            </div>
                            <div class="col-md-4"> 
							    <?php if($shortYear == CURRENT_FYEAR): ?>
                                    <a href="<?=base_url($headData->controller."/addEnquiry")?>" class="btn waves-effect waves-light btn-outline-primary float-right permission-write"><i class="fa fa-plus"></i> Add Enquiry</a>
                                <?php endif; ?>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='salesEnquiryTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<div class="modal fade" id="lastActivityModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Feasibility</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="col-md-12"><b>Enq No : <span id="enqNo"></span></b></div>
                <div class="col-md-12">
                    <div class="error general"></div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-info">
                                <tr class="text-center">
                                    <th style="width:5%;">#</th>
                                    <th>Item Name</th>
                                    <th>Qty.</th>
                                    <th>Unit</th>
                                    <th>Drg. No.</th>
                                    <th>Rev. No.</th>
                                    <th>Part No.</th>
                                    <th>Feasibility</th>
                                    <th style="width:15%;">Reason</th>
                                    <th style="width:15%;">Action</th>
                                    
                                </tr>
                            </thead>
                            <tbody id="activityData">
                                <tr>
                                    <td class="text-center" colspan="5">No Data Found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary save-form" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changePartyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Change Party</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="changeParty">
                    <div class="col-md-12"><b>Enq No : <span id="salesEnqNo"></span></b></div>

                    <div class="col-md-12">
                        <label for="change_party_id">Customer Name</label>
                        <select name="party_id" id="change_party_id" class="form-control single-select">
                            <?php
                                foreach($customerData as $row):
                                    echo "<option value='".$row->id."'>".$row->party_name."</option>";
                                endforeach;
                            ?>
                        </select>
                        <div class="error general"></div>
                        <input type="hidden" id="trans_main_id" name="trans_main_id">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn waves-effect waves-light btn-outline-secondary save-form" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form" onclick="changeParty()"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/sales-quotation.js?v=<?=time()?>"></script>
<script>
$(document).ready(function(){
    $(document).on('click','.changeParty',function(){ 
        var trans_main_id = $(this).data('trans_main_id'); 
        var party_id = $(this).data('party_id'); 
        var enq_no = $(this).data('enq_no'); 
        
        $("#changePartyModal").modal();
		$("#enqNo").html(enq_no);
		$("#change_party_id").val(party_id);
		$("#change_party_id").comboSelect();
		$("#trans_main_id").val(trans_main_id);
    });
});
function changeParty(){
    setPlaceHolder();
	
	var form = $('#changeParty')[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/changeParty',
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
			initTable(1); $('#changeParty')[0].reset();$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initTable(1); $('#changeParty')[0].reset();$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}
</script>