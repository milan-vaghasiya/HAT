<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title pageHeader">Stock Ledger</h4>
                            </div>
                            <div class="col-md-6">
                                <a href="<?= base_url($headData->controller.'/items') ?>" class="btn waves-effect waves-light btn-outline-dark float-right"><i class="fa fa-arrow-left"></i> Back</a>
                            </div>                            
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th>#</th>
										<th>Store</th>
										<th>Location</th>
										<th>Batch</th>
										<th>Current Stock</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
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
function loadItems(){
    var item_id = '<?=$itemId?>';
    if(item_id){
        $.ajax({
            url: base_url + controller + '/getstockTransferData',
            data: {item_id:item_id,fdate:'',tdate:''},
            type: "POST",
            dataType:'json',
            success:function(data){
                $("#reportTable").dataTable().fnDestroy();
                $("#theadData").html(data.thead);
                $("#tbodyData").html(data.tbody);
                reportTable();
            }
        });
    }
}
</script>
<script src="<?php echo base_url();?>assets/js/custom/stock-transfer.js?v=<?=time()?>"></script>