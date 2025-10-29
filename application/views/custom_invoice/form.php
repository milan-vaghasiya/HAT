<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Custom Invoice</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="customInvoiceForm">
                            <div class="col-md-12">
								<input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
								<input type="hidden" name="from_entry_type" id="from_entry_type" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>">
                                <input type="hidden" name="sales_type" id="sales_type" value="2">
                                <input type="hidden" name="gst_type" id="gst_type" value="<?=(!empty($dataRow->gst_type))?$dataRow->gst_type:"2"?>">
                                <input type="hidden" name="gst_applicable" id="gst_applicable" value="<?=(!empty($dataRow->gst_applicable))?$dataRow->gst_applicable:"1"?>">
                                <input type="hidden" name="extra_fields[packing_master_id]" id="packing_master_id" value="<?=(!empty($dataRow->packing_master_id))?$dataRow->packing_master_id:""?>">
                                <input type="hidden" name="extra_fields[no_of_wooden_box]" id="no_of_wooden_box" value="<?=(!empty($dataRow->no_of_wooden_box))?$dataRow->no_of_wooden_box:""?>">
                                <input type="hidden" name="extra_fields[so_id]" id="so_id" value="<?=(!empty($dataRow->so_id))?$dataRow->so_id:""?>">
                                <input type="hidden" name="extra_fields[cust_po_no]" id="cust_po_no" value="<?=(!empty($dataRow->cust_po_no))?$dataRow->cust_po_no:""?>">
                                <input type="hidden" name="extra_fields[cust_po_date]" id="cust_po_date" value="<?=(!empty($dataRow->cust_po_date))?$dataRow->cust_po_date:""?>">
                                <input type="hidden" name="extra_fields[total_net_weight]" id="total_net_weight" value="<?=(!empty($dataRow->total_net_weight))?$dataRow->total_net_weight:""?>">
                                <input type="hidden" name="extra_fields[total_gross_weight]" id="total_gross_weight" value="<?=(!empty($dataRow->total_gross_weight))?$dataRow->total_gross_weight:""?>">

                                <div class="row">
									<div class="col-md-3 form-group">
                                        <label for="ref_id">Cum. Pac. No.</label>
                                        <select name="ref_id" id="ref_id" class="form-control single-select req">
                                            <?php
                                                echo '<option value="">Select Packing No.</option>';
												foreach($cumPackingNoList as $row):
													$selected = (!empty($dataRow->ref_id) && $dataRow->ref_id==$row->id)?"selected":"";
                                                    if(empty($dataRow)):
                                                        if($row->trans_status == 0):
													        echo '<option value="'.$row->id.'" '.$selected.'>'.$row->trans_number.'</option>';
                                                        endif;
                                                    else:
                                                        echo '<option value="'.$row->id.'" '.$selected.'>'.$row->trans_number.'</option>';
                                                    endif;
												endforeach;
                                            ?>                                            
                                        </select>
                                    </div>

									<div class="col-md-2 form-group">
										<label for="doc_no">INV. No.</label>
										<input type="text" name="doc_no" id="doc_no" class="form-control req" placeholder="Enter INV No." value="<?=(!empty($dataRow->doc_no))?$dataRow->doc_no:""?>" readonly/>
									</div>

									<div class="col-md-2 form-group">
										<label for="doc_date">INV. Date</label>
										<input type="text" id="doc_date" name="doc_date" class=" form-control req" placeholder="dd-mm-yyyy" value="<?=(!empty($dataRow->doc_date))?$dataRow->doc_date:""?>" readonly/>	
									</div>

                                    <div class="col-md-2 form-group">
                                        <label for="export_type">Export Type</label>
                                        <input type="text" id="export_type_name" class="form-control" value="<?=(!empty($dataRow->export_type))?(($dataRow->export_type == "(Supply Meant For Export With Payment Of IGST)")?"IGST":"LUT"):""?>" readonly>

                                        <input type="hidden" name="extra_fields[export_type]" id="export_type" value="<?=(!empty($dataRow->export_type) && $dataRow->export_type == "(Supply Meant For Export With Payment Of IGST)")?$dataRow->export_type:""?>">

                                        <!-- <select name="extra_fields[export_type]" id="export_type" class="form-control req">
                                            <option value="">Select Export Type</option>
                                            
                                            <option value="(Supply Meant For Export With Payment Of IGST)" data-text="(Supply Meant For Export With Payment Of IGST)" <?=(!empty($dataRow->export_type) && $dataRow->export_type == "(Supply Meant For Export With Payment Of IGST)")?"selected":"disabled"?>>IGST</option>
                                            
                                            <option value="(Supply Meant For Export Under Bond Or Letter Of Undertaking Without Payment Of IGST)" data-text="(Supply Meant For Export Under Bond Or Letter Of Undertaking Without Payment Of IGST)" <?=(!empty($dataRow->export_type) && $dataRow->export_type == "(Supply Meant For Export Under Bond Or Letter Of Undertaking Without Payment Of IGST)")?"selected":"disabled"?>>LUT</option>
                                        </select> -->
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="lut_no">LUT No.</label>
                                        <input type="text" name="extra_fields[lut_no]" id="lut_no" class="form-control" value="<?=(!empty($dataRow->lut_no))?$dataRow->lut_no:""?>" readonly>
                                    </div>

                                    <div class="col-md-4 form-group">
										<label for="party_id">Buyer Name</label>
										
										<input type="text" name="party_name" id="party_name" class="form-control req" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""?>" readonly>	
										
										<input type="hidden" name="party_id" id="party_id" value="<?=(!empty($dataRow->party_id))?$dataRow->party_id:""?>">

										<input type="hidden" name="party_state_code" id="party_state_code" value="<?=(!empty($dataRow->party_state_code))?$dataRow->party_state_code:""?>">	
									</div>

                                    <div class="col-md-8 form-group">
                                        <label for="buyer_address">Buyer Address</label>
                                        <input type="text" name="extra_fields[buyer_address]" id="buyer_address" class="form-control req" value="<?=(!empty($dataRow->buyer_address))?$dataRow->buyer_address:""?>" readonly>
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label for="consignee_name">Consignee Name</label>
                                        <input type="text" name="extra_fields[consignee_name]" id="consignee_name" class="form-control req" value="<?=(!empty($dataRow->consignee_name))?$dataRow->consignee_name:""?>" readonly>
                                    </div>

                                    <div class="col-md-8 form-group">
                                        <label for="consignee_address">Consignee Address</label>
                                        <input type="text" name="extra_fields[consignee_address]" id="consignee_address" class="form-control req" value="<?=(!empty($dataRow->consignee_address))?$dataRow->consignee_address:""?>" readonly>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="ref_by">Packing No.</label>
                                        <input type="text" name="ref_by" id="ref_by" class="form-control req" value="<?=(!empty($dataRow->ref_by))?$dataRow->ref_by:""?>" readonly>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="pre_carriage_by">Pre-Carriage by</label>
                                        <input type="text" name="extra_fields[pre_carriage_by]" id="pre_carriage_by" class="form-control req" value="<?=(!empty($dataRow->pre_carriage_by))?$dataRow->pre_carriage_by:""?>" readonly>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="place_of_rec_by_pre_carrier">Place of receipt by Pre-Carrier</label>
                                        <input type="text" name="extra_fields[place_of_rec_by_pre_carrier]" id="place_of_rec_by_pre_carrier" class="form-control req" value="<?=(!empty($dataRow->place_of_rec_by_pre_carrier))?$dataRow->place_of_rec_by_pre_carrier:""?>" readonly>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="vessel_flight">Vessel / Flight</label>
                                        <input type="text" name="extra_fields[vessel_flight]" id="vessel_flight" class="form-control req" value="<?=(!empty($dataRow->vessel_flight))?$dataRow->vessel_flight:""?>" readonly>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="port_of_loading">Port Of Loading</label>
                                        <input type="text" name="extra_fields[port_of_loading]" id="port_of_loading" class="form-control req" value="<?=(!empty($dataRow->port_of_loading))?$dataRow->port_of_loading:""?>" readonly>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="port_of_discharge">Port Of Discharge</label>
                                        <input type="text" name="extra_fields[port_of_discharge]" id="port_of_discharge" class="form-control req" value="<?=(!empty($dataRow->port_of_discharge))?$dataRow->port_of_discharge:""?>" readonly>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="place_of_delivery">Place Of Delivery</label>
                                        <input type="text" name="extra_fields[place_of_delivery]" id="place_of_delivery" class="form-control req" value="<?=(!empty($dataRow->place_of_delivery))?$dataRow->place_of_delivery:""?>" readonly>
                                    </div>

                                    <div class="col-md-3 form-group">
                                        <label for="country_of_final_destonation">Country of Final Destination</label>
                                        <input type="text" name="extra_fields[country_of_final_destonation]" id="country_of_final_destonation" class="form-control req" value="<?=(!empty($dataRow->country_of_final_destonation))?$dataRow->country_of_final_destonation:""?>" readonly>
                                    </div>

                                    <div class="col-md-4 form-group">
                                        <label for="country_of_final_destonation">Applicable Prefrential Agreement</label>
                                        <input type="text" name="extra_fields[applicable_prefrential_agreement]" id="applicable_prefrential_agreement" class="form-control req" value="<?=(!empty($dataRow->applicable_prefrential_agreement))?(($dataRow->applicable_prefrential_agreement == "NCPTI")?"NCPTI":"GSTP"):""?>" readonly>
                                        <!-- <select name="extra_fields[applicable_prefrential_agreement]" id="applicable_prefrential_agreement" class="form-control req">
                                            <option value="">Select Agreement</option>

                                            <option value="NCPTI" data-text="NCPTI" <?=(!empty($dataRow->applicable_prefrential_agreement) && $dataRow->applicable_prefrential_agreement == "NCPTI")?"selected":"disabled"?>>NCPTI</option>

                                            <option value="GSTP" data-text="GSTP" <?=(!empty($dataRow->applicable_prefrential_agreement) && $dataRow->applicable_prefrential_agreement == "GSTP")?"selected":"disabled"?>>GSTP</option>
                                        </select> -->
                                    </div>
							</div>
							<hr>
							<div class="col-md-12 row">
                                <div class="col-md-6"><h4>Item Details : </h4></div>
                            </div>
							<div class="col-md-12 mt-3">
								<div class="error item_name_error"></div>
								<div class="row form-group">
									<div class="table-responsive">
										<table id="customInvoiceItems" class="table table-striped table-borderless">
											<thead class="thead-info">
												<tr>
													<th style="width:5%;">#</th>
													<th>Item Name</th>
													<th>HSN Code</th>
													<th>HSN Desc.</th>
													<th>Qty (Pcs.)</th>
													<th>Price</th>
													<th>Amount</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
												<tr id="noData">
													<td colspan="8" class="text-center">
														No data available in table
													</td>
												</tr>
											</tbody>   
										</table>
									</div>
								</div>
								<hr>
								<div class="row form-group">
									<div class="col-md-6">
										<div class="row">
											<div class="col-md-12 form-group">
												<label for="terms_conditions">Terms of  Delivery and Payment</label>  
												<textarea name="terms_conditions" id="terms_conditions" cols="30" rows="3" class="form-control req" readonly><?=(!empty($dataRow->terms_conditions))?$dataRow->terms_conditions:""?></textarea>  
											</div>   
											<div class="col-md-12 form-group">
												<label for="remark">Remarks</label>  
												<textarea name="remark" id="remark" cols="30" rows="3" class="form-control" readonly><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>  
											</div>  
										</div>
									</div>
									<div class="col-md-6 text-right">
										<table class="table table-borderless text-right">
											<tbody id="summery">
												<tr>
													<th class="text-right">Sub Total :</th>
													<td class="subTotal" style="width:40%;">
														<input type="text" name="total_amount" id="total_amount" class="form-control floatOnly" value="<?=(!empty($dataRow->total_amount))?$dataRow->total_amount:"0.00"?>" readonly>
													</td>
												</tr>
												<tr>
													<th class="text-right">Freight :</th>
													<td style="width:40%;">
														<input type="text" name="freight_amount" id="freight_amount" class="form-control floatOnly" value="<?=(!empty($dataRow->freight_amount))?$dataRow->freight_amount:"0.00"?>">
													</td>
												</tr>
												<tr>
													<th class="text-right">Insuracnce :</th>
													<td class="roundOff" style="width:40%;">
														<input type="text" name="other_amount" id="other_amount" class="form-control floatOnly" value="<?=(!empty($dataRow->other_amount))?$dataRow->other_amount:"0.00"?>">
													</td>
												</tr>
											</tbody>
											<tfoot>
												<tr>
													<th class="text-right">Net Amount :</th>
													<th class="netAmountTotal" style="width:40%;">
														<input type="text" name="net_amount" id="net_amount" class="form-control floatOnly" value="<?=(!empty($dataRow->net_amount))?$dataRow->net_amount:"0.00"?>" readonly>
													</th>
												</tr>
											</tfoot>
										</table>                                         
									</div>
								</div>
							</div>
                        </form>
                    </div>                    
                </div>
				<div class="card-footer">
					<div class="col-md-12">
						<button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveInvoice('customInvoiceForm');" ><i class="fa fa-check"></i> Save</button>
						<a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
					</div>
				</div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="itemModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Add or Update Item</h4>
            </div>
            <div class="modal-body">
                <form id="customInvoiceItemForm">
                    <div class="col-md-12">

                        <div class="row form-group">

							<div id="itemInputs">
								<input type="hidden" name="id" id="id" value="" />
								<input type="hidden" name="from_entry_type" id="from_entry_type" value="">
								<input type="hidden" name="ref_id" id="ref_id" value="">
								<input type="hidden" name="item_id" id="item_id" value="" />
								<input type="hidden" name="item_name" id="item_name" value="" />		
								<input type="hidden" name="item_desc" id="item_desc" value="" />
								<input type="hidden" name="item_type" id="item_type" value="" />
								<input type="hidden" name="item_code" id="item_code" value="" />
								<input type="hidden" name="row_index" id="row_index" value="">
                            </div> 

                            <div class="col-md-12 form-group">
                                <label for="item_id">Product Name</label>
								<input type="text"  name="item_alias" id="item_alias" class="form-control" value="" readonly/>
                            </div>

							<div class="col-md-4 form-group">
								<label for="hsn_code">HSN Code</label>
								<input type="text" name="hsn_code" id="hsn_code" class="form-control" value="" readonly/>
							</div>

							<div class="col-md-8 form-group">
								<label for="hsn_code">HSN Description</label>
								<input type="text" name="hsn_desc" id="hsn_desc" class="form-control" value="" readonly/>
							</div>

							<div class="col-md-6 form-group">
                                <label for="qty">Qty (Pcs.)</label>
                                <input type="number" name="qty" id="qty" class="form-control floatOnly req" value="0" readonly/>                                 
                            </div>
							
                            <div class="col-md-6 form-group">
                                <label for="price">Price</label>
                                <input type="number" name="price" id="price" class="form-control floatOnly req" value="" />                                 
                            </div> 
                        </div>
                    </div>          
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save" data-fn="save"><i class="fa fa-check"></i> Save</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-warning saveItem btn-save-close" data-fn="save_close"><i class="fa fa-check"></i> Save & Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/custom-invoice-form.js?v=<?=time()?>"></script>
<?php
    if(!empty($dataRow)):
        foreach($dataRow->itemData as $row):
            $row->row_index = "";
            echo '<script>AddRow('.json_encode($row).')</script>';
        endforeach;
    endif;
?>