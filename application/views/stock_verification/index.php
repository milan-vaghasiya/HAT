<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6"><h4 class="card-title"><?=$pageHeader?></h4></div>       
							<div class="col-md-6">
                                <select id="item_type" class="form-control float-right" style="width: 40%;">
                                    <option value="1">Finish Good</option>
                                    <option value="2">Consumable</option>
                                    <option value="3">Raw Material</option>
                                    <!-- <option value="4">Capital Goods</option>
                                    <option value="5">Machineries</option>
                                    <option value="6">Instruments</option>
                                    <option value="7">Gauges</option> -->
                                </select>
                            </div>
                        </div>                                         
                    </div>  
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered ssTable" data-url='/<?=$dataUrl?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="storeModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-body">
                <form id="saveStock">
					<div class="row" style="border-bottom:1px solid #ccc;padding:5px;margin-bottom:10px;">
						<h4 class="modal-title col-md-9">Select Store Location</h4>
						<input type="date" name="entry_date" id="entry_date" class="form-control col-md-3" value="<?=date('Y-m-d')?>" max="<?=date('Y-m-d')?>" />
					</div>
                    <input type="hidden" name="id" id="id" value="" />
                    <input type="hidden" name="item_id" id="item_id" value="" />
                    
                    <div class="table-responsive">
                        <table id='reportTable' class="table table-bordered">
                            <thead class="thead-info" id="theadData">
                                <tr>
                                    <th>#</th>
                                    <th>Store</th>
                                    <th>Location</th>
                                    <th>Batch</th>
                                    <th>Current Stock</th>
                                    <th>Physical Qty.</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyData"></tbody>
                        </table>
                    </div>	
                </form>					
			</div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close save-form" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save-close save-form" data-fn="save_close" onclick="store('saveStock','save',0);"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(document).on('change',"#item_type",function(){
        var item_type = $(this).val();
        $("#reportTable").attr("data-url",'/getStockVerification/'+item_type);
        initTable(0);
    });
});

function editStock(data,button){
	$("#storeModel").modal();
    $("#item_id").val(data.id);
	$.ajax({
		url: base_url +'stockVerification/editStock',
		data: {item_id:data.id},
		type: "POST",
		dataType:'json',
		success:function(data){
			$("#reportTable").dataTable().fnDestroy();
			$("#theadData").html(data.thead);
			$("#tbodyData").html(data.tbody);
			initTable(0);
		}
	});
}
</script>