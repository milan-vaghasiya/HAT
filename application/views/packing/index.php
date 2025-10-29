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
                                    <li class="nav-item"> 
                                       <button onclick="statusTab('packingTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Tentative Packing</button> 
                                    </li>
                                    <li class="nav-item"> 
                                       <button onclick="statusTab('packingTable',1);" class=" btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Final Export</button>
                                    </li>
                                    <li class="nav-item"> 
                                       <button onclick="statusTab('packingTable',2);" class=" btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Final Domestic</button>
                                    </li>
                                    <li class="nav-item"> 
                                        <button onclick="statusTab('packingTable',3);" class=" btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Invoiced</button>
                                    </li>
                                    <li class="nav-item"> 
                                        <button onclick="statusTab('packingTable',4);" class=" btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">New Requested</button>
                                    </li>
                               </ul>
                            </div>
                            <div class="col-md-2">
                                <h4 class="card-title">Packing</h4>
                            </div>
                            <div class="col-md-5">
                                <a href="<?=base_url('packing/addPacking')?>" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write"><i class="fa fa-plus"></i> New Packing</a>
                            </div>
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='packingTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="print_dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog" style="min-width:30%;">
		<div class="modal-content animated zoomIn border-light">
			<div class="modal-header bg-light">
				<h5 class="modal-title text-dark"><i class="fa fa-print"></i> Print Options</h5>
				<button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="printModel" method="post" action="<?=base_url($headData->controller.'/packing_pdf')?>" target="_blank">
				<div class="modal-body">
					<div class="col-md-12">
						<div class="row">
							<!-- <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12"> -->
                            <div class="col-md-12 form-group">
                                <label>Print For</label>
                                <select name="cust_id" id="cust_id" class="form-control single-select">
                                    <option value="1" <?= (!empty($dataRow->cust_id) && $dataRow->cust_id == "Custom")?"selected":"" ?>>Custom Print</option>
                                    <option value="2" <?= (!empty($dataRow->cust_id) && $dataRow->cust_id == "Customer")?"selected":"" ?>>Customer Print</option>
                                </select>
								<input type="hidden" name="printsid" id="printsid" value="0">

                            </div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<a href="#" data-dismiss="modal" class="btn btn-secondary"><i class="fa fa-times"></i> Close</a>
					<button type="submit" class="btn btn-success" onclick="closeModal('print_dialog');"><i class="fa fa-print"></i> Print</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="tagModel" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated zoomIn border-light">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark"><i class="fas fa-print" ></i> &nbsp;&nbsp; Packing Print</h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
			<form id="printPackingTag" action="<?=base_url($headData->controller.'/printPackingTag')?>" method="POST"  target="_blank">
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="dispatch_date">Dispatch Date</label>
                                <input type="date" name="dispatch_date" id="dispatch_date" class="form-control" value="<?=date('Y-m-d')?>" />
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Item</label>
                                <select name="item_id" id="item_id" class="form-control single-select">
                                    <option value="">Select Item</option>
                                </select>
                            </div>
                            <div class="col-md-5 form-group">
                                <label>Sales Order</label>
                                <select name="so_id_footer" id="so_id_footer" class="form-control single-select">
                                    <option value="">Select All</option>
                                    
                                </select>
                                <input type="hidden" name="packingid" id="packingid" value="0">
                                <input type="hidden" name="order_id" id="order_id" value="0">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="inv_no">Invoice No</label>
                                <input type="text" name="inv_no" id="inv_no" class="form-control" value="" />
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="lr_no">L.R. No</label>
                                <input type="text" name="lr_no" id="lr_no" class="form-control" value="" />
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="lot_qty">Lot Qty.</label>
                                <input type="text" name="lot_qty" id="lot_qty" class="form-control floatOnly" value="" />
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="heat_no">Heat No.</label>
                                <input type="text" name="heat_no" id="heat_no" class="form-control floatOnly" value="" />
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="trans_way">By</label>
                                <input type="text" name="trans_way" id="trans_way" class="form-control" value="" />
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="print_qty">No. Of Print</label>
                                <input type="text" name="print_qty" id="print_qty" class="form-control floatOnly" value="" />
                            </div>
                            <input type="hidden" name="print_id" id="print_id">
                            <!--<div class="col-md-4 form-group">-->
                            <!--    <label for="print_id">Print</label>-->
                            <!--    <select name="print_id" id="print_id" class="form-control single-select req">-->
                            <!--        <option value="1">Print 1</option>-->
                            <!--        <option value="2">Print 2</option>-->
                            <!--        <option value="3">Print 3</option>-->
                            <!--    </select>-->
                            <!--</div>-->
                        </div>
                        <div class="row">
                            <div class="col-sm-12 form-group">
                                <a href="javascript:void(0);" class="btn btn-labeled btn-success bg-success-dark printPackingTag float-right" type="button" target="_blank"><i class="fas fa-print" ></i>&nbsp;&nbsp;<span class="btn-label">Print &nbsp;&nbsp;</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Created By Meghavi @27/08/2022  -->
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Item List</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_so" method="post" action="">
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                        <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Item Name</th>
                                        <th class="text-center">Box Size(cm)</th>
                                        <th class="text-center">Qty Per Box(Nos)</th>
                                        <th class="text-center">Total Box(Nos)</th>
                                        <th class="text-center">Total Qty(Nos)</th>
                                        <th class="text-center">Net Weight Per Pcs(KG)</th>
                                        <th class="text-center">Total Net Weight(KG)</th>
                                        <th class="text-center">Packing Weight(KG)</th>
                                        <th class="text-center">Wooden Box Weight(KG)</th>
                                        <th class="text-center">Total Gross Weight(KG)</th>
                                    </tr>
                                </thead>
                                <tbody id="itemData">
                                    <tr>
                                        <td class="text-center" colspan="11">No Data Found</td>
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

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){

	<?php if(!empty($printID)): ?>
		$("#printModel").attr('action',base_url + controller + '/packing_pdf');
		$("#printsid").val(<?=$printID?>);
		$("#print_dialog").modal();
	<?php endif; ?>

	$(document).on("click",".printPacking",function(){
		$("#printModel").attr('action',base_url + controller + '/packing_pdf');
		$("#printsid").val($(this).data('id'));
		$("#print_dialog").modal();
	});		
	
	$(document).on("click",".packingTag",function(){
        $('#printPackingTag')[0].reset();
        var id = $(this).data('id');
        var order_id = $(this).data('soid');
        var packing_sticker = $(this).data('packing_sticker');
        $.ajax({
            url:base_url+'packing/getPackingItems',
            type:'post',
            data:{id:id,order_id:order_id},
            dataType:'json',
            success:function(data){
                $("#item_id").html("");
                $("#item_id").html(data.options);
                $("#item_id").comboSelect();
            }
        });
        $("#packingid").val(id); 
        $("#order_id").val(order_id); 
        $("#print_id").val(packing_sticker);
        $(".printTag1").data('id',$(this).data('id'));
        $("#tagModel").modal();
    });
    
    $(document).on("change","#item_id",function(){
        var id = $("#packingid").val();
        var item_id = $("#item_id").val();
        var order_id ='';
        $.ajax({
            url:base_url+'packing/getSalesOrderNoListForPacking',
            type:'post',
            data:{id:id,order_id:order_id,item_id:item_id},
            dataType:'json',
            success:function(data){
                
                $("#so_id_footer").html("");
                $("#so_id_footer").html(data.orderNoList);
                $("#so_id_footer").comboSelect();
            }
        });
        $("#packingid").val(id); 
        $(".printTag1").data('id',$(this).data('id'));
        $("#tagModel").modal();
    });

    $(document).on('click','.createItemList',function(){		
        var id = $(this).data('id');
        $.ajax({
            url : base_url + controller + '/getItm',
            type: 'post',
            data:{id:id},
            dataType:'json',
            success:function(data){
                $("#itemModal").modal();
                $("#itemData").html("");
                $("#itemData").html(data.htmlData);
            }
        });
    });

    $(document).on("click",".printPackingTag",function(){ $('#printPackingTag').submit(); });
});

function closeModal(modalId)
{
	$("#"+ modalId).modal('hide');
	
	<?php if(!empty($printID)): ?>
		window.location = base_url + controller;
	<?php endif; ?>
}	
</script>