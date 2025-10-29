<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">  
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="statusTab('extraHoursTable',0);" class=" btn waves-effect waves-light btn-outline-danger btn-pending active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('extraHoursTable',1);" class=" btn waves-effect waves-light btn-outline-success btn-approve" style="outline:0px" data-toggle="tab" aria-expanded="false">Approved</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-7">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew" data-button="both" data-modal_id="modal-md" data-function="addExtraHours" data-form_title="Add Extra Hours"><i class="fa fa-plus"></i> Add Extra Hours</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='extraHoursTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<div class="modal fade" id="xhrsApprovalModal" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Extra Hours Approval</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-approveXHRS"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(document).on('click','.approveXHRS',function(){
        var id = $(this).data('id');
        $.ajax({
            url: base_url + controller + '/getXHRSDetail',
            data: {id:id},
            type: "POST",
            dataType:'json',
            success:function(data){
                $("#xhrsApprovalModal").modal({show:true});
                $('#xhrsApprovalModal .modal-body').html(data.approvalData);
            }
        }); 
    });
    $(document).on('click','.btn-approveXHRS',function(){
        var id = $('#id').val();
        if(id)
    	{
    		$("#xhrsApprovalModal").modal('hide');
    		$.ajax({
    			url: base_url + controller + '/approveXHRS',
    			data:{id:id},
    			type: "POST",
    			dataType:"json",
    		}).done(function(data){
    			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });			
    			$('.btn-pending').trigger('click');
    		});
    		
    	}
    	else
    	{
    		toastr.error("ID NOT FOUND", 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
    	}
    });
});
</script>