<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title"><?=($party_category == 1 ? "Customer": ($party_category == 2 ? "Vendor":"Supplier"))?></h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="addParty/<?=$party_category?>" data-form_title="Add <?=($party_category == 1 ? "Customer": ($party_category == 2 ? "Vendor":"Supplier"))?>"><i class="fa fa-plus"></i> Add <?=($party_category == 1 ? "Customer": ($party_category == 2 ? "Vendor":"Supplier"))?></button>
                                <?php if($party_category == 2): ?>
                                    <select name="process_id" id="process_id_search" class="form-control float-right" style="width:50%"> 
                                        <option value="">Select All</option>
                                        <?php
                                            if(!empty($processData)):
                                                foreach($processData as $row):  
                                                    echo '<option value="' . $row->id . '">' . $row->process_name . '</option>';
                                                endforeach;
                                            endif;
                                        ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='partyTable' class="table table-bordered ssTable" data-url='/getDTRows/<?=$party_category?>'></table>
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
    initPartyTable();
	$(document).on('change','#process_id_search',function(){ initPartyTable(); }); 
});

function initPartyTable() {
    var process_id = $('#process_id_search').val();
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
        process_id: process_id
    }
    ssDatatable($('.ssTable'), tableHeaders, tableOptions, dataSet);
}
</script>