<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <ul class="nav nav-pills">
                                <li class="nav-item">  <a href="<?=base_url($headData->controller."/index/")?>"  class="nav-link btn waves-effect waves-light btn-outline-info mr-1" >Orders</a> </li>

                                <li class="nav-item">  <a href="<?=base_url($headData->controller."/plannedSo")?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1 active">Planned S.O.</a> </li>
                            </ul>                
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='expenseMasterTable' class="table table-bordered ssTable" data-url='/getPlanDTRows'></table>
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
        $(document).on("change","#entry_type",function(){
            var entry_name = $("#entry_type :selected").text();
            $("#entry_name").val(entry_name);
        });
    });
</script>