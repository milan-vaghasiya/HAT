<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="card-title">Tool & Die</h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-lg" data-function="addItem" data-form_title="Add Item"><i class="fa fa-plus"></i> Add Item</button>
                            </div>
                        </div>                             
                    </div>                                         
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='toolTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/item-stock-update.js?v=<?=time()?>"></script>
<script>
$(document).ready(function(){
    initToolTable();
	$(document).on('change','#category_id_filture',function(){ initToolTable(); }); 
});

function initToolTable() {
    var category_id = $('#category_id_filture').val();
    $('.ssTable').DataTable().clear().destroy();
    var tableOptions = {
        pageLength: 25,
        'stateSave': false
    };
    var tableHeaders = {
        'theads': '',
        'textAlign': textAlign,
        'srnoPosition': 1
    };
    var dataSet = {
        category_id: category_id
    }
    ssDatatable($('.ssTable'), tableHeaders, tableOptions, dataSet);
}
</script>