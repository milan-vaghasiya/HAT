<?php $this->load->view('includes/header'); ?>

<div class="page-wrapper">

    <div class="container-fluid bg-container">

        <div class="row">

            <div class="col-12">

                <div class="card">

                    <div class="card-header">

                        <div class="row">

                            

                            <div class="col-md-6">

                                <h4 class="card-title">RM Process</h4>

                            </div>

                            <div class="col-md-6">

                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew" data-button="both" data-modal_id="modal-lg" data-function="addRmProcess" data-form_title="Add RM Process"><i class="fa fa-plus"></i> Add RM Process</button>

                            </div>    

                            

                        </div>                                         

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table id='rmProcessTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>

                        </div>

                    </div>

                </div>

            </div>

        </div>        

    </div>

</div>



<?php $this->load->view('includes/footer'); ?>

<script>

$(document).ready(function(){

    $(document).on('change', '#item_id', function () {

		var item_id = $(this).val();


		if (item_id == "") {

			$("#batchData").html('<tr><td class="text-center" colspan="5">No Data Found.</td></tr>');

		} else {


			$.ajax({

				url: base_url + controller + '/batchWiseItemStock',

				data: { item_id: item_id, batch_no: "", location_id: "", batch_qty: "" },

				type: "POST",

				dataType: 'json',

				success: function (data) {

					$("#batchData").html(data.batchData);

				}

			});

		}

	});
	
	 $(document).on('change','#vendor_id',function(){
		var vendor_id = $(this).val();		
		var item_id = $("#item_id").val();		

		if(vendor_id){
			$.ajax({
				url:base_url + controller + '/getJobOrderList',
				type:'post',
				data:{vendor_id:vendor_id,item_id:item_id},
				dataType:'json',
				success:function(data){
					$("#job_order_id").html("");
					$("#job_order_id").html(data.options);
					$("#job_order_id").comboSelect();
				}
			});
		} else {
			$("#job_order_id").html("<option value=''>Select Job Work Order</option>");
			$("#job_order_id").comboSelect();
		}
    });

});

</script>



