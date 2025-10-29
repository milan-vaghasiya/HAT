<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Products</h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-lg" data-function="addProduct" data-form_title="Add Product"><i class="fa fa-plus"></i> Add Product</button>
                                <!--<select name="stock" id="stock" class="form-control float-right mr-2" style="width:20%;">
									<option value="0">ALL</option>
									<option value="1">With Stock</option>
									<option value="2">With Out Stock</option>
								</select>
								
								<input type="file" name="item_excel" id="item_excel" class="form-control-file float-left col-md-3" />
                                <a href="javascript:void(0);" class="btn btn-labeled btn-success bg-success-dark ml-2 importExcel  " type="button">
                                    <i class="fa fa-upload"></i>&nbsp;
                                    <span class="btn-label">Upload Excel &nbsp;<i class="fa fa-file-excel"></i></span>
                                </a>
                                <a href="<?= base_url($headData->controller . '/createExcel/') ?>" class="btn btn-labeled btn-info bg-info-dark mr-2" target="_blank">
                                    <i class="fa fa-download"></i>&nbsp;&nbsp;
                                    <span class="btn-label">Download Excel&nbsp;&nbsp;<i class="fa fa-file-excel"></i></span>
                                </a>
                                <h6 class="col-md-12 msg text-primary text-left mt-1"></h6>-->
                            </div>                 
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='productTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
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
<script>
$(document).ready(function() {
    $('body').on('click', '.importExcel', function() {
        $(".msg").html("");
        $(this).attr("disabled", "disabled");
        var fd = new FormData();
        fd.append("item_excel", $("#item_excel")[0].files[0]);
        $.ajax({
            url: base_url + controller + '/importExcel',
            data: fd,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
        }).done(function(data) {
            if (data.status === 0) {
                $(".msg").html("");
                var error='';
                $.each(data.message, function(key, value) {
                    error+=' '+value;
                });
                $(".msg").html(error);
            } else if (data.status == 1) {
                toastr.success(data.message, 'Success', {
                    "showMethod": "slideDown",
                    "hideMethod": "slideUp",
                    "closeButton": true,
                    positionClass: 'toastr toast-bottom-center',
                    containerId: 'toast-bottom-center',
                    "progressBar": true
                });
                initTable();
            }
         
            $(this).removeAttr("disabled");
            $("#item_excel").val(null);
        });
    });
});
</script>