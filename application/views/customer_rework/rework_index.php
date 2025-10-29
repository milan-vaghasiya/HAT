<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="changeStatusTab('customerRework',0);" class=" btn waves-effect waves-light btn-outline-info active mr-1" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="changeStatusTab('customerRework',1);" class=" btn waves-effect waves-light btn-outline-info  mr-1" style="outline:0px" data-toggle="tab" aria-expanded="false">Inprocess</button> </li>
                                    <li class="nav-item"> <button onclick="changeStatusTab('customerRework',2);" class=" btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4 class="card-title text-left">Customer Rework</h4>
                            </div>
                                
                        </div>
                        
                    </div>
                    <div class="card-body">
                            <div class="table-responsive">
                                <table id='customerRework' class="table table-bordered ssTable"
                                    data-url='/getReworkDTRows'></table>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer'); ?>
<script>
function acceptRework(id,name='Record'){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to Accept this Rework?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/acceptRework',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								initTable(); initMultiSelect();
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

function changeStatusTab(tableId,status){
    $("#"+tableId).attr("data-url",'/getReworkDTRows/'+status);
    ssTable.state.clear();initTable();
}


</script> 
             
