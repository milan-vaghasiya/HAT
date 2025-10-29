<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">RM Inspection Parameter</h4>
                            </div>                            
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='inspectionParamTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
    $(document).ready(function() {
        $(document).on('click', ".addInspectionOption", function() {
            var id = $(this).data('id');
            var productName = $(this).data('product_name');
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
                    url: base_url + 'inspectionParam/' + functionName,
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
                    $("#" + modalId + " .modal-footer .btn-save").hide();
                } else {
                    $("#" + modalId + " .modal-footer .btn-close").show();
                    $("#" + modalId + " .modal-footer .btn-save").hide();
                }
                $(".single-select").comboSelect();setPlaceHolder();
            });
        });
    });
</script>