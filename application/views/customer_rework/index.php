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
                                    <li class="nav-item"> <button onclick="statusTab('customerRework',0);" class=" btn waves-effect waves-light btn-outline-info active  mr-1" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('customerRework',1);" class=" btn waves-effect waves-light btn-outline-info  mr-1" style="outline:0px" data-toggle="tab" aria-expanded="false">Inprocess</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('customerRework',2);" class=" btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-3">
                                <h4 class="card-title text-left">Customer Rework</h4>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="addRework" data-form_title="Add Customer Complaints"><i class="fa fa-plus"></i> Add  Rework</button>
                            </div>      
                        </div>
                        
                    </div>
                    <div class="card-body">
                            <div class="table-responsive">
                                <table id='customerRework' class="table table-bordered ssTable"
                                    data-url='/getDTRows'></table>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){

	$(document).on('change', '#party_id', function () {
        var party_id = $(this).val();
        if (party_id) {
            $.ajax({
                url: base_url + 'customerRework' + '/getInvbyParty',
                data: { party_id: party_id },
                type: "POST",
                dataType: 'json',
                success: function (data) {
					console.log(data);
                    $("#inv_id").html(data.options);
                    $("#inv_id").comboSelect();
                }
            });
        }
    });

    $(document).on('change', '#inv_id', function () {
		var inv_id = $(this).val();
        $.ajax({
            url: base_url + 'customerRework' + '/getInvItemList',
            data: { inv_id: inv_id},
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $("#item_id").html(data.options);
                $("#item_id").comboSelect();
            }
        });
    });

    $(document).on('change', '#item_id', function () {
        var  row = $("#item_id :selected").data('row');
        console.log(row);
        $("#so_ref_id").val(row.so_ref_id);
        $("#inv_child_id").val(row.id);
        $("#ref_id").val(row.ref_id);
		var inv_id = $("#inv_id").val();
        $("#batchData").html("");
        $.ajax({
            url: base_url + 'customerRework' + '/getItemWiseBatchDetail',
            data: { inv_id: inv_id,inv_child_id:row.id},
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $("#batchData").html(data.htmlData);
               
            }
        });
    });
});
</script> 
             
