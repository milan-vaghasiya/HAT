<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title">Import Row Material</h4>
                            </div>                            
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <form id="importRM">
                            <div class="row  justify-content-center">
                                <input type="file" name="insp_excel" id="insp_excel" class="form-control-file float-left col-md-3" />
                                <a href="javascript:void(0);" class="btn btn-labeled btn-success bg-success-dark ml-2 importExcel  " type="button">
                                    <i class="fa fa-upload"></i>&nbsp;
                                    <span class="btn-label">Upload Excel &nbsp;<i class="fa fa-file-excel"></i></span>
                                </a>
                                <h6 class="col-md-12 msg text-primary text-center mt-1">
                                </h6>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function() {
    $('body').on('click', '.importExcel', function() {
        $(this).attr("disabled", "disabled");
        var fd = new FormData();
        fd.append("insp_excel", $("#insp_excel")[0].files[0]);
        $.ajax({
            url: base_url + controller + '/importRMExcel',
            data: fd,
            type: "POST",
            processData: false,
            contentType: false,
            dataType: "json",
        }).done(function(data) {
            $(".msg").html(data.message);
        });
    });
});
</script>