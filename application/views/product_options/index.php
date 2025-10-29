<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Product Options</h4>
                            </div>
                            <div class="col-md-6">
                                <select name="is_child" id="is_child" class="form-control float-right" style="width:40%">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='productOptionTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/product.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/item-stock-update.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/tool_consumption.js?v=<?=time()?>"></script>

<script>
    $(document).ready(function() {
	
        $(document).on('click', ".addProductOption", function() {
            var id = $(this).data('id');
            var productName = $(this).data('product_name');
            var functionName = $(this).data("function");
            var modalId = $(this).data('modal_id');
            var button = $(this).data('button');
            var title = $(this).data('form_title');
            var formId = functionName;
			var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
			var srposition = 0;
			if ($(this).is('[data-srposition]')){srposition = $(this).data("srposition");}
            var printbtn='';
            if($(this).hasClass('printbtn')){
                printbtn = '<a class="btn btn-outline-success btn-edit" href="'+base_url+'productOption/printToolConsumption/'+id+'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
            }

            $.ajax({
                type: "POST",
                url: base_url + 'productOption/' + functionName,
                data: {id: id}
            }).done(function(response) {
                $("#" + modalId).modal();
				$("#" + modalId + " .modal-dialog").css('max-width','50%');
                $("#" + modalId + ' .modal-title').html(title + " [ Product : "+productName+" ]");
                $("#" + modalId + ' .modal-body').html(response);
                $("#" + modalId + " .modal-body form").attr('id', formId);
				// $("#" + modalId + " .modal-footer .btn-save").attr('onclick', "store('" + formId + "', '"+fnsave+"');");
				$("#" + modalId + " .modal-footer .btn-save").attr('onclick', "store('" + formId + "', '"+fnsave + "', '"+srposition+"');");
                if (button == "close") {
                    $("#" + modalId + " .modal-footer .btn-close").show();
                    $("#" + modalId + " .modal-footer .btn-save").hide();
                } else if (button == "save") {
                    $("#" + modalId + " .modal-footer .btn-close").hide();
                    $("#" + modalId + " .modal-footer .btn-save").show();
                } else {
                    $("#" + modalId + " .modal-footer .btn-close").show();
                    $("#" + modalId + " .modal-footer .btn-save").show();
                    $("#" + modalId + " .modal-footer .btn-edit").hide();
                }
                $("#" + modalId + " .modal-footer").append(printbtn);
				$(".inputmask-his").inputmask("99:99:99");
				$(".single-select").comboSelect();setPlaceHolder();
            });
        });
		
		$(document).on('click', ".addInspectionOption", function() {
            var id = $(this).data('id');
            var productName = $(this).data('product_name');
            var param_type = $(this).data('param_type');
            var functionName = $(this).data("function");
            var modalId = $(this).data('modal_id');
            var button = $(this).data('button');
            var title = $(this).data('form_title');
            var formId = functionName;
            var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
			var srposition = 1;
			if ($(this).is('[data-srposition]')){srposition = $(this).data("srposition");}

            $.ajax({
                    type: "POST",
                    url: base_url + 'productOption/' + functionName,
                    data: {item_id: id,param_type:param_type}
            }).done(function(response) {
                $("#" + modalId).modal();
                $("#" + modalId + " .modal-dialog").css('max-width','50%');
                $("#" + modalId + ' .modal-title').html(title + " [Product : "+productName+"]");
                $("#" + modalId + ' .modal-body').html(response);
                $("#" + modalId + " .modal-body form").attr('id', formId);
                // $("#" + modalId + " .modal-footer .btn-save").attr('onclick', "store('" + formId + "', '"+fnsave+"');");
				$("#" + modalId + " .modal-footer .btn-save").attr('onclick', "store('" + formId + "', '"+fnsave + "', '"+srposition+"');");
                if (button == "close") {
                    $("#" + modalId + " .modal-footer .btn-close").show();
                    $("#" + modalId + " .modal-footer .btn-save").hide();
                } else if (button == "save") {
                    $("#" + modalId + " .modal-footer .btn-close").hide();
                    $("#" + modalId + " .modal-footer .btn-save").hide();
                } else {
                    $("#" + modalId + " .modal-footer .btn-close").show();
                    $("#" + modalId + " .modal-footer .btn-save").hide();
                }
                $(".single-select").comboSelect();setPlaceHolder();
            });
        });
        
        $(document).on("change","#is_child",function(){
            $("#productOptionTable").attr("data-url",'/getDTRows/'+$(this).val());
            ssTable.state.clear();initTable(0);
        });
    });
</script>