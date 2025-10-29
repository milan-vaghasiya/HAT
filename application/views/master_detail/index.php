<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-7">
                                <ul class="nav nav-pills">
									<h4 class="card-title">Sub Group</h4>
                                 </ul>
                            </div>
                            <div class="col-md-5">
                                <button type="button" class="btn btn-outline-primary float-right addNew" data-button="both" data-modal_id="modal-md" data-type=3 data-function="addMasterDetail/4" data-form_title="Add Sub Group" ><i class="fa fa-plus"></i> Add Sub Group</button>
                            </div>                          
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='masterDetailTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
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
       
}); 
function typeTab(tableId,type){
    $(".mType").hide();$(".bType").hide();$(".sType").hide();
    if(type == 1){ 
        $(".mType").show();$(".bType").hide();$(".sType").hide()
    }else if(type == 2){
        $(".mType").hide();$(".bType").show();$(".sType").hide()
    }else{
        $(".mType").hide();$(".bType").hide();$(".sType").show()
    }
    $("#"+tableId).attr("data-url",'/getDTRows/'+type);
    ssTable.state.clear();initTable();
}
</script>