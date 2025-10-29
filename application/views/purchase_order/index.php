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
                                    <li class="nav-item"> <button onclick="statusTab('purchaseOrderTable',0);" class="nav-link btn waves-effect waves-light btn-outline-info active" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('purchaseOrderTable',1);" class="nav-link btn waves-effect waves-light btn-outline-success" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('purchaseOrderTable',2);" class="nav-link btn waves-effect waves-light btn-outline-primary" data-toggle="tab" aria-expanded="false">Short Close</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center">Purchase Order</h4>
                            </div>
                            <div class="col-md-4">
                                <?php if($shortYear == CURRENT_FYEAR): ?>
                                    <a href="<?=base_url($headData->controller."/addPurchaseOrder")?>" class="btn waves-effect waves-light btn-outline-primary float-right permission-write"><i class="fa fa-plus"></i> Add Order</a>
                                <?php endif; ?>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='purchaseOrderTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Create GRN</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_so" method="post" action="<?=base_url("grn/createGrn");?>">
                <div class="modal-body">
                    <div class="col-md-12"><b>Party Name : <span id="partyName"></span></b></div>
                    <input type="hidden" name="party_id" id="party_id" value="">
                    <input type="hidden" name="party_name" id="party_name" value="">
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">PO. No.</th>
                                        <th class="text-center">PO. Date</th>
                                    </tr>
                                </thead>
                                <tbody id="orderData">
                                    <tr>
                                        <td class="text-center" colspan="3">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                    <button type="submit" class="btn waves-effect waves-light btn-outline-success" id="btn-create"><i class="fa fa-check"></i> Create GRN</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Item List</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_so" method="post" action="">
                <div class="modal-body">
                    <div class="col-md-12"><b>Party Name : <span id="partyName"></span></b></div>
                    <input type="hidden" name="party_id" id="party_id" value="">
                    <input type="hidden" name="party_name" id="party_name" value="">
                    <input type="hidden" name="from_entry_type" id="from_entry_type" value="4">
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Item Description</th>
                                        <th class="text-center">HSN/SAC</th>
                                        <th class="text-center">GST <small>%</small></th>
                                        <th class="text-center">Qty.</th>
                                        <th class="text-center">UOM</th>
                                        <th class="text-center">Rate<br><small></small></th>
                                        <th class="text-center">Amount<br><small></small></th>
                                    </tr>
                                </thead>
                                <tbody id="itemData">
                                    <tr>
                                        <td class="text-center" colspan="5">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
  
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewPOModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content animated slideDown">
            <!-- <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">SalesOrder</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div> -->
            <form id="party_so" method="post" action="">
            <input type="hidden" id="id">
                <div class="modal-body"  >
                    <div class="col-md-12" id="poView"></div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form" onclick="saveApprove()"><i class="fa fa-check"></i> Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(document).on('click','.createGrn',function(){
        var party_id = $(this).data('party_id');
		var party_name = $(this).data('party_name');

		$.ajax({
			url : base_url + controller + '/getPartyOrders',
			type: 'post',
			data:{party_id:party_id},
			dataType:'json',
			success:function(data){
				$("#orderModal").modal();				
				$("#partyName").html(party_name);
				$("#party_name").val(party_name);
				$("#party_id").val(party_id);
				$("#orderData").html("");
				$("#orderData").html(data.htmlData);
			}
		});
    });

    $(document).on('click',".approvePOrder",function(){
		var id = $(this).data('id');
		var val = $(this).data('val');
        var msg= $(this).data('msg');
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to '+ msg +' this Purchase Order?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/approvePOrder',
							data: {id:id,val:val,msg:msg},
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
								    initTable(); 
									toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
									//window.location.reload();
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
	});
	
	$(document).on('click',".closePOrder",function(){
		var id = $(this).data('id');
		var val = $(this).data('val');
        var msg= $(this).data('msg');
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to '+ msg +' this Purchase Order?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/closePOrder',
							data: {id:id,val:val,msg:msg},
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
	});

	$(document).on('click','.createItemList',function(){		
		var order_id = $(this).data('id');
		var party_name = $(this).data('party_name');

		$.ajax({
			url : base_url + controller + '/getItemList',
			type: 'post',
			data:{order_id:order_id},
			dataType:'json',
			success:function(data){
				$("#itemModal").modal();
				$("#partyName").html(party_name);
				$("#party_name").val(party_name);
				$("#party_id").val(party_id);
				$("#itemData").html("");
				$("#itemData").html(data.htmlData);
			}
		});
	});
});

function openView(id)
{
    $('#viewPOModal').modal();
    $('#id').val(id);
    $.ajax({
			url:base_url + controller + '/purchaseOrderView',
			type:'post',
			data:{id:id},
			dataType:'json',
			global:false,
			success:function(data)
			{
				$('#poView').html(data.pdfData)
			}
		});
}

function saveApprove() {

	var id = $("#id").val();
	$.ajax({
		url: base_url + controller + '/approvePOrder',
		data: {
			id: id,
			val: '1',
			msg: 'Approve'
		},
		type: "POST",
		dataType: "json",
		success: function(data) {
			if (data.status == 0) {
				toastr.error(data.message, 'Sorry...!', {
					"showMethod": "slideDown",
					"hideMethod": "slideUp",
					"closeButton": true,
					positionClass: 'toastr toast-bottom-center',
					containerId: 'toast-bottom-center',
					"progressBar": true
				});
			} else {
				initTable();
				toastr.success(data.message, 'Success', {
					"showMethod": "slideDown",
					"hideMethod": "slideUp",
					"closeButton": true,
					positionClass: 'toastr toast-bottom-center',
					containerId: 'toast-bottom-center',
					"progressBar": true
				});
				window.location.reload();
			}
		}
	});
}

</script>