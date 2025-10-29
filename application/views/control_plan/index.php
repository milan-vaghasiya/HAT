<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Product Inspection Parameter</h4>
                            </div>                            
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='controlPlanTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
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
		var param_type = $(this).data('param_type');
		var functionName = $(this).data("function");
		var modalId = $(this).data('modal_id');
		var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName;
		var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
		var srposition = 0;
		if ($(this).is('[data-srposition]')){srposition = $(this).data("srposition");}

		$.ajax({
				type: "POST",
				url: base_url + controller + '/' + functionName,
				data: {item_id: id,param_type:param_type}
		}).done(function(response) {
			$("#" + modalId).modal();
			$("#" + modalId + " .modal-dialog").css('max-width','50%');
			$("#" + modalId + ' .modal-title').html(title + " [Product : "+productName+"]");
			$("#" + modalId + ' .modal-body').html(response);
			$("#" + modalId + " .modal-body form").attr('id', formId);
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
			$(".modal-lg").attr("style","max-width: 70% !important;");
			$(".single-select").comboSelect();setPlaceHolder();
		});
	});

	$(document).on('click', '.importExcel', function() {
        $(this).attr("disabled", "disabled");
        var fd = new FormData();
        fd.append("insp_excel", $("#insp_excel")[0].files[0]);
        fd.append("item_id", $("#item_id").val());
        fd.append("param_type", $("#param_type").val());
        $.ajax({
            url: base_url + controller + '/importExcel',
            data: fd,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
        }).done(function(data) {
            $(".msg").html(data.message);
            $(this).removeAttr("disabled");
            $("#insp_excel").val(null);
            if (data.status == 1) {
                initTable(0);
                $("#inspectionBody").html(data.tbodyData);
            }
        });
    });
});
</script>