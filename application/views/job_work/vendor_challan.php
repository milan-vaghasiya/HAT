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
							            <?php if($shortYear == "22-23"): ?>
                                            <!--<button type="button" class="btn waves-effect waves-light btn-success float-right createVendorChallan" title="Create Challan">-->
    									       <!-- <i class="fa fa-plus"></i> Create Challan-->
    								        <!--</button>-->
    								    <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row"> 
                            <div class="col-md-12"> 
                                <div class="table-responsive">
                                    <table id='vendorChallanTable' class="table table-bordered ssTable" data-url='/getChallanDTRows'></table>
                                </div>
                            </div>
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
                <input type="hidden" name="vendor_id" id="vendor_id" value="0" />

                <div class="modal-header">
                    <h4 class="modal-title">Create Challan For : <span id="vendorName"></span></h4> 
                    <input type="date" name="challan_date" id="challan_date" class="form-control float-right req" value="<?=date('Y-m-d')?>" style="width: 20%;">
                </div>
                <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for="job_inward_id">Job No.</label>
                        <select name="processSelect" id="processSelect" data-input_id="job_inward_id" class="form-control jp_multiselect req" multiple="multiple">
                        </select>
                        <input type="hidden" name="job_inward_id" id="job_inward_id" value="" />
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <h4> Material Details : </h4>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="item_id"> Material</label>
                                <select name="item_id" id="item_id" class="form-control single-select req">
                                    <option value=""> Material</option>
                                    <?php
                                    foreach ($materialData as $row) :
                                        echo '<option value="' . $row->id . '">' . $row->item_name . '</option>';
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="out_qty">Qty</label>
                                <input type="text" name="out_qty" id="out_qty" class="form-control floatOnly req" value="" />
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-success waves-effect waves-light float-right mt-30 save-form" onclick="AddRow();" ><i class="fa fa-plus"></i> Add</button>
                            </div>
                        </div>    
                    </div>
                    <hr>
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="packingBom" class="table table-bordered align-items-center">
                                <thead class="thead-info">
                                    <tr>
                                        <th style="width:5%;">#</th>
                                        <th>Packing Material</th>
                                        <th>Qty</th>
                                        <th class="text-center" style="width:10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="bomData">
                                    
                                    <!-- <?php
                                    if(!empty($packingData)): $i=1;
                                        foreach($packingData as $row):
                                    ?>
                                    <tr>
                                        <td style="width:5%;">
                                            <?=$i?>
                                        </td>
                                        <td>
                                            <?=$row->item_name?>
                                            <input type="hidden" name="item_id[]" value="<?=$row->item_id?>">  
                                        </td>
                                    
                                        <td>
                                            <?=$row->out_qty?>
                                            <input type="hidden" name="out_qty[]" value="<?=$row->out_qty?>">
                                            <div class="error out_qty<?=$i++?>"></div>
                                        </td>
                                        <td class="text-center" style="width:10%;">
                                            <button type="button" onclick="Remove(this);" class="btn btn-sm btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
                                        </td>
                                    </tr>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?> -->
                                </tbody>
                            </table>
                        </div>
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
		var item_id = $('#item_id').val();
		var party_id = $('#party_id').val();
		var party_name = $('#party_idc').val();
		if($("#party_id").val() == "" || $("#party_id").val() == 0){$(".party_id").html("Party is required.");valid=0;}
		if(valid)
		{
            $.ajax({
				url : base_url + '/jobWork/getCreateVendorChallan',
				type: 'post',
				data:{item_id:item_id,party_id:party_id},
				dataType:'json',
				success:function(data){
					$("#vendorChallanModal").modal();
					$("#exampleModalLabel1").html('Create Challan For : '+party_name);
                    $("#vendor_id").val(party_id);
					$("#vendorName").html(party_name);
					$("#processSelect").html("");
					$("#processSelect").html(data.htmlData);
                    $("#item_id").html("");
                    $("#item_id").html(data.materialData);
                    $("#item_id").comboSelect();
					reInitMultiSelect();
				}
			});
        }
    });    
});

function AddRow() {
    var valid = 1;
	$(".error").html("");
    
    if($("#item_id").val() == ""){$(".item_id").html("Packing Material is required.");valid = 0;}
	if($("#out_qty").val() == "" || $("#out_qty").val() == 0){$(".out_qty").html("qty is required.");valid = 0;}
	
	if(valid)
	{
        $(".item_id").html("");
        $(".out_qty").html("");
        //Get the reference of the Table's TBODY element.
        $("#packingBom").dataTable().fnDestroy();
        var tblName = "packingBom";
        
        var tBody = $("#"+tblName+" > TBODY")[0];
        
        //Add Row.
        row = tBody.insertRow(-1);
        
        //Add index cell
        var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
        var cell = $(row.insertCell(-1));
        cell.html(countRow);
        
        cell = $(row.insertCell(-1));
        cell.html($("#item_idc").val() + '<input type="hidden" name="item_id[]" value="'+$("#item_id").val()+'">');

       
        var out_qtyErrorDiv = $("<div></div>",{class:"error out_qty"+countRow});
        cell = $(row.insertCell(-1));
        cell.html($("#out_qty").val() + '<input type="hidden" name="out_qty[]" value="'+$("#out_qty").val()+'">');
	    cell.append(out_qtyErrorDiv);

        //Add Button cell.
        cell = $(row.insertCell(-1));
        var btnRemove = $('<button><i class="ti-trash"></i></button>');
        btnRemove.attr("type", "button");
        btnRemove.attr("onclick", "Remove(this);");
        btnRemove.attr("class", "btn btn-sm btn-outline-danger waves-effect waves-light");
        cell.append(btnRemove);
        cell.attr("class","text-center");
        $("#item_id").val("");
        $("#item_idc").val("");
        $("#out_qty").val("");
	}
};

function Remove(button) {
    //Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#packingBom")[0];
	table.deleteRow(row[0].rowIndex);
	$('#packingBom tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
};
</script>