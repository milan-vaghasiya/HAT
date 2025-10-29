<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Vendor Challan</h4>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <select name="party_id" id="party_id" class="form-control single-select" style="width:70%;">
                                        <option value="">Select Vendor</option>
                                        <?php
                                        foreach ($vendorData as $row) :
                                            echo '<option value="' . $row->id . '">' . $row->party_name . '</option>';
                                        endforeach;
                                        ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right createVendorChallan" title="Create Challan">
									        <i class="fa fa-plus"></i> Create Challan
								        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='vendorChallanTable' class="table table-bordered ssTable" data-url='/getChallanDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="vendorChallanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <form id="vendorChallanForm">
                <div class="modal-header">
                    <h4 class="modal-title">Create Challan For : <span id="vendorName"></span></h4> 
                    <input type="date" name="challan_date" id="challan_date" class="form-control float-right req" value="<?=date('Y-m-d')?>" style="width: 20%;">
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                            <input type="hidden" name="vendor_id" id="vendor_id" value="0" />
                            <div class="error orderError"></div>
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr class="text-center">
                                        <th class="text-center">#</th>
                                        <th class="text-center">Challan No.</th>
                                        <th class="text-center">Product</th>
                                        <th class="text-center">Process</th>
                                        <th class="text-center">Out Qty.</th>
                                        <th class="text-center">In Qty.</th>
                                        <th class="text-center">Pending Qty.</th>
                                    </tr>
                                </thead>
                                <tbody id="vendorData">
                                    <tr class="text-center">
                                        <td class="text-center" colspan="7">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                    <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save save-form" onclick="store('vendorChallanForm','saveVendorChallan');"><i class="fa fa-check"></i> Create Challan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(document).on('click','.createVendorChallan',function(e){
		$(".error").html("");
		var valid = 1;
		var party_id = $('#party_id').val();
		var party_name = $('#party_idc').val();
		if($("#party_id").val() == "" || $("#party_id").val() == 0){$(".party_id").html("Party is required.");valid=0;}
		if(valid)
		{
            $.ajax({
				url : base_url + '/jobWork/getVendorInward',
				type: 'post',
				data:{party_id:party_id},
				dataType:'json',
				success:function(data){
					$("#vendorChallanModal").modal();
					$("#exampleModalLabel1").html('Create Challan For : '+party_name);
                    $("#vendor_id").val(party_id);
					$("#vendorName").html(party_name);
					$("#vendorData").html("");
					$("#vendorData").html(data.htmlData);
				}
			});
        }
    });    
});
</script>