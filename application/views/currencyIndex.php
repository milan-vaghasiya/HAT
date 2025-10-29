<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Currency</h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right save-form permission-write" onclick="store('addCurrency','saveCurrency');"><i class="fa fa-check"></i> Save Currency</button>
                            </div>    
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <form id="addCurrency">
                        <div class="table-responsive">
                            <table id='currencyTable' class="table table-bordered ssTable" data-url='/getCurrencyRows'></table>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>