<div class="col-md-12 form-group">
    <table class="table top-table-border"> 
        <tr>
            <th colspan="5">MATERIAL TEST CERTIFICATE <br> As Per EN 10204:2004 - Type 3.1</th>
			<th>F/QC/03(00/01.01.16)</th>
        </tr>
        <tr>
            <tr>
				<th class="text-left">Customer Name:</th> <td></td>
				<th class="text-left">Type Of Material:</th> <td><?=(!empty($gateReceipt->category_name))?$gateReceipt->category_name:"";?></td>
				<th class="text-left">Material Grade:</th> <td><?=(!empty($dataRow->material_grade))?$dataRow->material_grade:"";?></td>
			</tr>
			<tr>
				<th class="text-left">T.C. No.:</th> <td><?=(!empty($gateReceipt->tc_no))?$gateReceipt->tc_no:"";?></td>
				<th class="text-left">Date:</th> <td><?=(!empty($gateReceipt->grn_date))?formatDate($gateReceipt->grn_date):"";?></td>
				<th class="text-left">Colour Code:</th> <td><?=(!empty($dataRow->color_code))?$dataRow->color_code:"";?></td>
			</tr>
        </tr>
    </table>
    <table class="table item-list-bb">
        <thead class="thead-info"> 
            <tr>
                <th colspan="13">We hereby certify that material found results as per <?=(!empty($dataRow->standard))?$dataRow->standard:""; ?></th>
            </tr>
            <tr>
                <th colspan="13">Chemical Composition</th>
            </tr>
            <tr class="text-center">
                <th></th>
                <th>Ref. TC No.</th>
                <th>Size</th>
                <?php $minHtml1 = $maxHtml1 = '';
                    foreach($specificationData as $row):
                        if($row->spec_type == 1):
                            echo '<th>'.$row->param_name.'</th>';
                            $minHtml1 .= '<td>'.$row->min_value.'</td>';
                            $maxHtml1 .= '<td>'.$row->max_value.'</td>';
                        endif;
                    endforeach;
                ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Min.</td>
                <td></td>
                <td></td>
                <?= $minHtml1 ?>
            </tr>
            <tr>
                <td>Max.</td>
                <td></td>
                <td></td>
                <?= $maxHtml1 ?>
            </tr>
            <tr>
                <td><?= (!empty($gateReceipt->batch_no))?$gateReceipt->batch_no:""; ?></td>
                <td><?=(!empty($gateReceipt->tc_no))?$gateReceipt->tc_no:"N/A";?></td>
                <td><?=(!empty($gateReceipt->size))?$gateReceipt->size:"N/A";?></td>
                <?php $i=1;
                    foreach($specificationData as $row):
                        if($row->spec_type == 1):
                ?>
                    <td><?= (!empty($row->result))?$row->result:""; ?></td>
                <?php       $i++;
                        endif;
                    endforeach;
                ?>
            </tr>
        </tbody>
    </table>
</div>
<div class="col-md-12 form-group">
    <table class="table item-list-bb">
        <thead class="thead-info">
            <tr>
                <th colspan="7">Mechanical Properties</th>
            </tr>
            <tr class="text-center">
                <th></th>
                <th>Ref. TC No.</th>
                <?php $minHtml2 = $maxHtml2  = $minHtml3 = $maxHtml3 = ''; 
                    foreach($specificationData as $row):
                        if($row->spec_type == 2):
                            echo '<th>'.$row->param_name.'</th>';
                            $minHtml2 .= '<td>'.$row->min_value.'</td>';
                            $maxHtml2 .= '<td>'.$row->max_value.'</td>';
                        endif;
                    endforeach;
                    
                    foreach($specificationData as $row):
                        if($row->spec_type == 6):
                            echo '<th>'.$row->param_name.'</th>';
                            $minHtml3 .= '<td>'.$row->min_value.'</td>';
                            $maxHtml3 .= '<td>'.$row->max_value.'</td>';
                        endif;
                    endforeach;
                ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Min.</td>
                <td></td>
                <?= $minHtml2.$minHtml3 ?>
            </tr>
            <tr>
                <td>Max.</td>
                <td></td>
                <?= $maxHtml2.$maxHtml3 ?>
            </tr>
            <tr>
                <td><?= (!empty($gateReceipt->batch_no))?$gateReceipt->batch_no:""; ?></td>
                <td><?=(!empty($gateReceipt->tc_no))?$gateReceipt->tc_no:"N/A";?></td>
                <?php $i=1;
                    foreach($specificationData as $row):
                        if($row->spec_type == 2):
                            echo '<td>'.((!empty($row->result))?$row->result:"").'</td>'; $i++;
                        endif;
                    endforeach;
                ?>
                <?php $i=1;
                    foreach($specificationData as $row):
                        if($row->spec_type == 6):
                            echo '<td>'.((!empty($row->result))?$row->result:"").'</td>'; $i++;
                        endif;
                    endforeach;
                ?>
            </tr>
        </tbody>
    </table>
</div>