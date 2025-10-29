<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                <h4 class="card-title">Process Approval</h4>
                            </div>
                            <div class="col-md-4">
                                <select id="process_id" class="single-select">
                                    <option value="">All Process</option>
                                    <?php
                                        $jobCardProcess = explode(",",$job_card_process);
                                        foreach($processList as $row):
                                            if(in_array($row->id,$jobCardProcess)):
                                                echo '<option value="'.$row->id.'">'.$row->process_name.'</option>';
                                            endif;
                                        endforeach;
                                    ?>
                                </select>
                                <input type="hidden" id="job_id" value="<?=$job_id?>">
                            </div>   
                            <div class="col-md-3">
                                <a href="<?=base_url('jobcard')?>" class="btn waves-effect waves-light btn-outline-dark float-right " style="width:30%"><i class="fa fa-arrow-left"></i> Back</a>
                                <a href="<?=base_url('productions/index/'.$job_id)?>" class="btn btn-outline-success waves-effect waves-light float-right" style="width:68%;margin-right:3px;">Production</a>
                            </div>                          
                        </div>                                         
                    </div>
                    <div class="card-body">
						<table class="table" style="border-radius:15px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);">
							<tr class="">
								<th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;border-top-left-radius:15px;border-bottom-left-radius:15px;border:0px;">Job No.</th>
								<th class="text-left" style="background:#f3f2f2;width:20%;padding:0.25rem 0.5rem;"><?=getPrefixNumber($jobData->job_prefix,$jobData->job_no)?></th>
								<th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;">Product</th>
								<th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;"><?=$jobData->item_code?></th>
								<th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;">Order Qty.</th>
								<th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;border-top-right-radius:15px; border-bottom-right-radius:15px;border:0px;"><?=floatVal($jobData->qty)?></th>
							</tr>
						</table>
                        <div class="table-responsive">
                            <table id='productionTable' class="table table-bordered table-striped">
                                <thead class="thead-info">
                                    <th style="width:5%;" class="text-center">Action</th>
                                    <th style="width:5%;" class="text-center">#</th>
                                    <!--<th class="text-center">Job No.</th>
									<th class="text-center">Delivery Date</th>-->
                                    <!-- <th class="text-center">Vendor Name</th> -->
                                    <!--<th class="text-center">Product</th>-->
                                    <th class="text-center">Production Type</th>
                                    <th class="text-center">In From Process</th>
                                    <th class="text-center">In Qty</th>
                                    <th class="text-center">Out To Process</th>
                                    <th class="text-center">Out Qty</th>
                                    <th class="text-center">Pending Qty.</th>
                                    <th>Remark</th>
                                </thead>
                                <tbody id="productionData">
                                    <?php echo $productionData; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/process-approval.js?v=<?=time()?>"></script>