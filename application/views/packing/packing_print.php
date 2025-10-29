<div class="row">
    <div class="col-12">
        <table class="table item-list-bb" repeat-header="1">
            <thead>
                <tr>
                    <th colspan="10">
                        Annexure A
                    </th>
                    <th colspan="2">
                        <?php 
                            if($pdf_type == 0):
                                echo 'Internal Copy';
                            elseif($pdf_type == 1):
                                echo 'Customer Copy';
                            elseif($pdf_type == 2):
                                echo 'Custom Copy';
                            else:
                                echo '';
                            endif;
                        ?>
                    </th>
                </tr>
                <tr>
                    <th>Package No.</th>
                    <th>Box Size (cm)</th>
                    <th>Item Name</th>
                    <th>Qty Per Box (No.s)</th>
                    <th>Total Box (No.s)</th>
                    <th>Total Qty. (No.s)</th>
                    <th>Net Weight Per Piece (kg)</th>
                    <th>Total Net Weight (kg)</th>
                    <th>Packing Weight (kg)</th>
                    <th>Wooden Box Weight (kg)</th>
                    <th>Item Gross Weight (kg)</th>
                    <th>Packing Gross Weight (kg)</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $totalNetWt = 0; $totalPackWt = 0; $totalWoodenWt = 0; $totalGrossWt = 0;
            if (!empty($packingData)) {
                $itemIds = array();
                foreach ($packingData as $pack) {
                    $itemIds = array();
                    $transData = $pack->itemData;
            ?>
                    <tr>
                        <td rowspan="<?=count($pack->itemData)?>" class="text-center"><?= $pack->package_no ?></td>
                        <td rowspan="<?=count($pack->itemData)?>" class="text-center"><?= $pack->box_size ?></td>
                        <?php 
                            if(!in_array($transData[0]->item_id,$itemIds)):
                                $itemRowspan = 0;
                                $itemRowspan = count(array_keys(array_column($transData,'item_id'), $transData[0]->item_id));
                                $itemIds[] = $transData[0]->item_id;
                                
                                if($pdf_type == 0):
                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->item_code.'</td>';
                                elseif($pdf_type == 1):
                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->part_no.' - '.$transData[0]->item_name.'</td>';
                                elseif($pdf_type == 2):
                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->alias_name.'</td>';
                                else:
                                    echo '<td class="text-left" rowspan="'.$itemRowspan.'">'.$transData[0]->item_name.'</td>';
                                endif;
                            endif;
                        ?>
                        <td class="text-right"><?= round($transData[0]->qty_per_box,0) ?></td>
                        <td class="text-right"><?= round($transData[0]->total_box,0) ?></td>
                        <td class="text-right"><?= round($transData[0]->total_qty,0) ?></td>
                        <td class="text-right"><?= $transData[0]->wt_pcs?></td>
                        <td class="text-right"><?= $transData[0]->net_wt ?></td>
                        <td class="text-right"><?= $transData[0]->packing_wt ?></td>
                        <td class="text-right"><?= $transData[0]->wooden_wt ?></td>
                        <td class="text-right"><?= $transData[0]->gross_wt ?></td>
                        <td class="text-right" rowspan="<?= count($pack->itemData) ?>"><?=sprintf("%.3f",array_sum(array_column($pack->itemData,'gross_wt')))?></td>
                    </tr>

                    <?php
                    $i = 1;
                    foreach ($transData as $row) {
                        if ($i > 1) {
                    ?>
                            <tr>
                                <?php 
                                    if(!in_array($row->item_id,$itemIds)):
                                        $itemRowspan = 0;
                                        $itemRowspan = count(array_keys(array_column($transData,'item_id'), $row->item_id));
                                        $itemIds[] = $row->item_id;
                                        
                                        if($pdf_type == 0):
                                            echo '<td class="text-center" rowspan="'.$itemRowspan.'">'.$row->item_code.'</td>';
                                        elseif($pdf_type == 1):
                                            echo '<td class="text-center" rowspan="'.$itemRowspan.'">'.$row->part_no.' - '.$row->item_name.'</td>';
                                        elseif($pdf_type == 2):
                                            echo '<td class="text-center" rowspan="'.$itemRowspan.'">'.$row->alias_name.'</td>';
                                        else:
                                            echo '<td class="text-center" rowspan="'.$itemRowspan.'">'.$row->item_name.'</td>';
                                        endif;
                                    endif;
                                ?>
                                <td class="text-right"><?= round($row->qty_per_box,0) ?></td>
                                <td class="text-right"><?= round($row->total_box,0) ?></td>
                                <td class="text-right"><?= round($row->total_qty,0) ?></td>
                                <td class="text-right"><?= $row->wt_pcs ?></td>
                                <td class="text-right"><?= $row->net_wt ?></td>
                                <td class="text-right"><?= $row->packing_wt ?></td>
                                <td class="text-right"><?= $row->wooden_wt ?></td>
                                <td class="text-right"><?= $row->gross_wt ?></td>
                            </tr>
            <?php
                        }
                        $i++;
                        $totalNetWt += $row->net_wt;
                        $totalPackWt += $row->packing_wt;
                        $totalWoodenWt += $row->wooden_wt;
                        $totalGrossWt += $row->gross_wt;
                    }
                }
            }
            ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" class="text-right">Total</th>
                    <th class="text-right"><?=sprintf("%.3f",$totalNetWt)?></th>
                    <th class="text-right"><?=sprintf("%.3f",$totalPackWt)?></th>
                    <th class="text-right"><?=sprintf("%.3f",$totalWoodenWt)?></th>
                    <th class="text-right"><?=sprintf("%.3f",$totalGrossWt)?></th>
                    <th class="text-right"><?=sprintf("%.3f",$totalGrossWt)?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>