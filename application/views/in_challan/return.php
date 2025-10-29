<div class="modal fade" id="returnItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Return Item</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table" style="border-radius:15px;box-shadow:1px 2px 2px 0 rgb(0 0 0/ 70%);">
                        <tr class="">
                            <th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;border-top-left-radius:15px;border-bottom-left-radius:15px;border:0px;">Item Name</th>
                            <th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;" id="ProductItemName"></th>
                            <th class="text-center text-white" style="background:#aeaeae;width:15%;padding:0.25rem 0.5rem;">Pending Qty.</th>
                            <th class="text-left" style="background:#f3f2f2;width:15%;padding:0.25rem 0.5rem;border-top-right-radius:15px; border-bottom-right-radius:15px;border:0px;" id="ProductPendingQty"></th>
                        </tr>
                    </table>
                </div>
                <hr>
                <form>
                    <div class="col-md-12 row">
                        <input type="hidden" name="id" value="" />
                        <input type="hidden" name="ref_id" id="ref_id" value="" />
                        <input type="hidden" name="ref_no" id="ref_no" value="" />
                        <input type="hidden" name="ref_type" id="ref_type" value="11" />
                        <input type="hidden" name="trans_type" id="trans_type" value="2" />
                        <input type="hidden" name="location_id" id="location_id" value="" />
                        <input type="hidden" name="batch_no" id="batch_no" value="" />
                        <input type="hidden" name="item_id" id="item_id" value="" />
                        <input type="hidden" name="challan_trans_id" id="challan_trans_id" value="" />

                        <div class="col-md-4 form-group">
                            <label for="ref_date">Date</label>
                            <input type="date" name="ref_date" id="ref_date" class="form-control req" value="<?=date("Y-m-d")?>">
                        </div>

                        <div class="col-md-4 form-group">
                            <label for="qty">Qty.</label>
                            <input type="number" name="qty" id="qty" class="form-control floatOnly req" value="" />
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="">&nbsp;</label>
                            <button type="button" class="btn waves-effect waves-light btn-outline-success btn-block save-form" onclick="saveReturnItem(this.form)"><i class="fa fa-check"></i> Save</button>
                        </div>
                    </div>
                </form>
                <hr>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="returnItemTable" class="table table-bordered align-items-center">
                            <thead class="thead-info">
                                <tr>
                                    <th style="width:5%;">#</th>
                                    <th>Date</th>
                                    <th>Qty</th>
                                    <th class="text-center" style="width:10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="returnItemTableData">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary save-form" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>