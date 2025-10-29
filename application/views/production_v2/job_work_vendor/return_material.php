<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />

            <div class="col-md-12 form-group">
                <div class="table-responsive">
                    <table id="packingBom" class="table table-bordered align-items-center">
                        <thead class="thead-info">
                            <tr>
                                <th style="width:5%;">#</th>
                                <th>Material</th>
                                <th>Out Qty.</th>
                                <th>In Qty.</th>
                                <th>Pending Qty.</th>
                            </tr>
                        </thead>
                        <tbody id="bomData">
                            <?= $dataRow->tbody ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>