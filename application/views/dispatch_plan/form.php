<div class="col-md-12">
    <form >
        <div class="row">
            <div class="error general_error"></div>
            <div class="table-responsive">
                <table id="inspection" class="table table-bordered align-items-center fhTable">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th>Delivery Date</th>
                            <th>SO NO</th>
                            <th>PO NO</th>
                            <th>Party Code</th>
                            <th>Part NO</th>
                            <th>Part Name</th>
                            <th>Part Size</th>
                            <th>W.O. NO</th>
                            <th>Order Qty</th>
                            <th>Plan Qty</th>
                        </tr>
                    </thead>
                    <tbody id="inspectionBody" class="scroll-tbody scrollable maxvh-60">
                        <?php
                            if(!empty($dataRow)):
                                $i=1;
                                foreach($dataRow as $row):
                                    $pendingQty = $row->qty - ($row->dispatch_qty + $row->plan_qty);
                                    ?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><?=formatDate($row->cod_date)?></td>
                                        <td><?= getPrefixNumber($row->trans_prefix,$row->trans_no)?></td>
                                        <td><?= $row->doc_no?></td>
                                        <td><?=$row->party_code?></td>
                                        <td><?=$row->item_code?></td>
                                        <td><?=$row->item_name?></td>
                                        <td><?=$row->item_size?></td>
                                        <td><?=$row->grn_data?></td>
                                        <td><?= floatVal($row->qty)?></td>
                                        <td>
                                            <input type="text" name="plan_qty[]" id="plan_qty<?=$i?>" class="form-control" value="<?=floatVal($pendingQty)?>">
                                            <input type="hidden" name="so_trans_id[]" id="so_trans_id<?=$i?>" value="<?=$row->id?>">
                                        </td>
                                    </tr>
                                    <?php
                                    $i++;
                                endforeach;
                            else:
                                echo '<tr><td colspan="11" style="text-align:center;">No Data Found</td></tr>';
                            endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>

</div>

