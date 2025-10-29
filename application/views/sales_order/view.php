<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>View Sales Order</u></h4>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="form-row">
								<div class="col-md-3">
									<label for="party_id">Party Name : </label>
									<label><?=$this->party->getParty($dataRow->party_id)->party_name?>
								</div>
								<div class="col-md-3">
									<label for="so_no">SO. No. : </label>
									<label>#<?=$dataRow->so_no?></label>
								</div>
								<div class="col-md-3">
									<label for="po_date">SO. Date : </label>
									<label><?=date("d-m-Y",strtotime($dataRow->so_date))?></label>
								</div>
								<div class="col-md-3">
									<label for="delivery_date">Delivery Date : </label>
									<label for=""><?=date("d-m-Y",strtotime($dataRow->delivery_date))?></label>
								</div>
								<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
									<div class="form-group">
										<label for="remark">Remark : </label>
										<label><?=$dataRow->remark?></label>
									</div>
								</div>
								<div class="col-md-4">
									<label for="order_image">Attachment : </label>
									<?php
										if(!empty($dataRow->order_image)):
											echo '<a href="'.base_url('assets/uploads/sales_order/'.$dataRow->order_image).'" class="btn btn-outline-primary" download><i class="fa fa-arrow-down"></i> Download</a>';
										endif;
									?>
								</div>
							</div>
							<h5 style="border-bottom:1px solid #767676;">Ordered Item</h5>
							<div class="form-row mt-20">
								<div class="table-responsive">
									<table id="itemData" class="table table-bordered">
										<thead class="thead-info">
											<tr>
												<th>#</th>
												<th>Item Name</th>
												<th>Required Qty</th>
												<th>Stock Qty</th>
												<th>Remark</th>
												<th class="text-center">Production Status</th>
												<th class="text-center">Required Material</th>
												<th class="text-center">Action</th>
											</tr>
										</thead>
										<tbody id="OrderItemDataRow">
											<?php
												if(!empty($dataRow->items)):
												$i=1;
												foreach($dataRow->items as $row):
											?>
											<tr>
												<td><?=$i++?></td>
												<td>
													<?php
														echo "[".$row->item_code."] ".$row->item_name;
													?>
												</td>
												<td>
													<?=$row->qty." (".$row->unit_name.")"?>
												</td>
												<td>
													<?=$row->stock_qty?>
												</td>
												<td style="width:20%"><?=$row->item_remark?></td>
												<td class="text-center" id="orderStatus<?=$row->id?>">
													<?php if($row->production_status == 0): ?>
														<span class="badge badge-danger m-1">Pending</span>
													<?php elseif($row->production_status == 1): ?>
														<span class="badge badge-warning m-1">In-Process</span>
													<?php else: ?>
														<span class="badge badge-success m-1">Complete</span>
													<?php endif; ?>
												</td>
												<td class="text-center">
													<a href="javascript:void(0)" class="btn btn-outline-info waves-effect waves-light requiredMaterial" title="Required Material" data-id="<?=$row->item_id?>" data-product="<?=$row->item_name?>" data-qty="<?=$row->qty?>" ><i class="fa fa-eye"></i></a>
												</td>
												<td class="text-center" id="orderItem<?=$row->id?>">
													<?php if(!empty($dataRow->created_by)):?>
                                                        <?php if($row->production_status == 1): ?>
                                                            <a href="javascript:void(0)" class="btn btn-outline-success waves-effect waves-light completeOrderItem" title="Complete Order Item" data-id="<?=$row->id?>" data-val="2" data-msg="Completed"><i class="fa fa-check"></i></a>
                                                        <?php elseif($row->production_status == 0): ?>
                                                            <a href="javascript:void(0)" class="btn btn-outline-warning waves-effect waves-light completeOrderItem" title="In-Process Order Item" data-id="<?=$row->id?>" data-val="1" data-msg="In-Process"><i class="fa fa-spinner"></i></a>
                                                        <?php else: ?>
                                                            <a href="javascript:void(0)" class="btn btn-outline-danger waves-effect waves-light completeOrderItem" title="Pending Order Item" data-id="<?=$row->id?>" data-val="0" data-msg="UnCompeleted"><i class="fa fa-window-close"></i></a>
                                                        <?php endif; ?>
													<?php endif; ?>
												</td>
											</tr>
											<?php 
												endforeach;
												endif;
											?>
										</tbody>
									</table>
								</div>
							</div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-secondary float-right" style="margin-right:10px;"><i class="fa fa-arrow-left"></i> Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="requiredMaterialModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Required Material</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Product Name : <span id="productName"></span></h5>
                        </div>
                        <div class="col-md-6">
                            <h5>Order Qty. : <span id="orderQty"></span></h5>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-info">
                                <th class="text-center">#</th>
                                <th>Item Name</th>
                                <th>Qty./Nos</th>
                                <th>Required Qty.</th>
                                <th>Stock Qty.</th>
                            </thead>
                            <tbody id="requiredItems">
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/sales-order-view.js?v=<?=time()?>"></script>