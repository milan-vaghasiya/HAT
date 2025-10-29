<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Tool Consumption</h4>
                            </div>
                            <div class="col-md-6">
                                <!-- <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew" data-button="both" data-modal_id="modal-md" data-function="addCycleTime" data-form_title="Add Cycle Time"><i class="fa fa-plus"></i> Add Cycle Time</button> -->
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='cycleTimeTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/tool_consumption.js?v=<?=time()?>"></script>

<script>
    $(document).ready(function() {
        initMultiSelect();
        
        $(document).on('click', ".addToolConsumption", function() {
            var id = $(this).data('id');
            var productname = $(this).data('product_name');
            var functionName = $(this).data("function");
            var modalId = $(this).data('modal_id');
            var button = $(this).data('button');
            var title = $(this).data('form_title');
            var formId = functionName;

            $.ajax({
                type: "POST",
                url: base_url + 'toolConsumption/' + functionName,
                data: {
                    id: id
                }
            }).done(function(response) {
                $("#" + modalId).modal();
				$("#" + modalId + " .modal-dialog").css('max-width','50%');
                $("#" + modalId + ' .modal-title').html(title);
                $("#" + modalId + ' .modal-body').html(response);
                $("#" + modalId + " .modal-body form").attr('id', formId);
                $("#" + modalId + " .modal-footer .btn-save").attr('onclick', "store('" + formId + "');");
                if (button == "close") {
                    $("#" + modalId + " .modal-footer .btn-close").show();
                    $("#" + modalId + " .modal-footer .btn-save").hide();
                } else if (button == "save") {
                    $("#" + modalId + " .modal-footer .btn-close").hide();
                    $("#" + modalId + " .modal-footer .btn-save").show();
                } else {
                    $("#" + modalId + " .modal-footer .btn-close").show();
                    $("#" + modalId + " .modal-footer .btn-save").show();
                }
                $(".single-select").comboSelect();setPlaceHolder();
            });
        });
    });
</script>