<table class="table top-table-border">
    <tr>
        <th class="text-center" colspan="3" style="font-size:1rem;width:50%">MATERIAL TEST CERTIFICATE - EN 10204 - 3.1</th>
    </tr>
    <tr>
        <td style="width: 60%;">
            <b><?= $grnData->party_name ?></b><br>
            <?= $grnData->party_address . ' ' . $grnData->party_pincode ?><br><br>

            <b> Type Of Material : </b><?= $inInspectData->group_name ?><br><br>
            <b> Material Grade : </b><?= $inInspectData->material_grade ?>
        </td>
       
        <td style="width: 40%;"><b>T.C.NO. :</b><?= $inInspectData->tc_no ?><br><br>
            <b>DT.: </b><?= date("d-m-Y", strtotime($inInspectData->tc_date)) ?><br><br>
            <b>Colour Code : </b><?= $inInspectData->color_code?>
        </td>
    </tr>
</table>

<table class="table  top-table-border">
    <thead class="thead-info">
        <tr>
            <th colspan="10" class="text-center">CHEMICAL ANALYSIS</th>
        </tr>
        <tr>
            <td colspan="10">( Ref. <?= ((!empty($inInspectData->ch_ref)) ? $inInspectData->ch_ref : "") ?> T.C.No: <?= ((!empty($inInspectData->ch_tc_no)) ? $inInspectData->ch_tc_no : "") ?> Dated: <?= ((!empty($inInspectData->ch_tc_date)) ? formatDate($inInspectData->ch_tc_date) : "") ?></td>
        </tr>
        <tr style="text-align:center;">
            <th rowspan="2" style="width:5%;">#</th>
            <th rowspan="2" style="width:50%;">Parameter</th>
            <th colspan="3">Chemical Parameters</th>
        </tr>
        <tr style="text-align:center;">
            <th style="width:15%;">Min</th>
            <th style="width:15%;">Max</th>
            <th style="width:15%;">Our Observation</th>
        </tr>
    </thead>
    <tbody id="tbodyData">
        <?php		
            $i=1;  
            if(!empty($paramData)):
                foreach($paramData as $row):
                    $inInspectData = $this->grnModel->getInInspection($grnTrans->id,$row->id);
                    echo '<tr >';
                        echo '<td style="text-align:center;">'.$i++.'</td>';
                        echo '<td>'.$row->parameter.'</td>';
                        echo '<td style="text-align:center;">'.((!empty($inInspectData->obs_1))?$inInspectData->obs_1:"").'</td>';
                        echo '<td style="text-align:center;">'.((!empty($inInspectData->obs_2))?$inInspectData->obs_2:"").'</td>';
                        echo '<td style="text-align:center;">'.((!empty($inInspectData->obs_3))?$inInspectData->obs_3:"").'</td>';
                    echo '</tr>';
                endforeach;
            endif;
        ?>	
    </tbody>
</table>

<table class="table top-table-border">
    <thead class="thead-info">
        <tr>
            <th colspan="6" class="text-center">MECHANICAL PROPERTIES</th>
        </tr>
        <tr style="text-align:center;">
            <td colspan="6">( Ref. <?= ((!empty($inInspectData->mech_ref)) ? $inInspectData->mech_ref : "") ?> T.C.No: <?= ((!empty($inInspectData->mech_tc_no)) ? $inInspectData->mech_tc_no : "") ?> Dated: <?= ((!empty($inInspectData->mech_tc_date)) ? formatDate($inInspectData->mech_tc_date) : "") ?></td>
        </tr>
        <tr style="text-align:center;">
            <th rowspan="2" style="width:5%;">#</th>
            <th rowspan="2" style="width:50%;">Parameter</th>
            <th colspan="3">Mechanical Parameters</th>
        </tr>
        <tr style="text-align:center;">
            <th style="width:15%;">Min	</th>
            <th style="width:15%;">Max</th>
            <th style="width:15%;">Our Observation	</th>
        </tr>
    </thead>
    <tbody id="mechData">
        <?php		
            $i=1;  
            if(!empty($mData)):
                foreach($mData as $row):
                    $inspectData = $this->grnModel->getInInspection($grnTrans->id,$row->id);
                    echo '<tr >';
                        echo '<td style="text-align:center;">1</td>';
                        echo '<td>'.$row->parameter.'</td>';
                        echo '<td style="text-align:center;">'.((!empty($inspectData->obs_1))?$inspectData->obs_1:"").'</td>';
                        echo '<td style="text-align:center;">'.((!empty($inspectData->obs_2))?$inspectData->obs_2:"").'</td>';
                        echo '<td style="text-align:center;">'.((!empty($inspectData->obs_3))?$inspectData->obs_3:"").'</td>';
                    echo '</tr>';
                endforeach;
            endif;
        ?>	
    </tbody>
</table>

<h4 class="text-left">Description Of Goods:</h4>
<table class="table item-list-bb" style="margin-top:10px;">
    <tr>
        <th style="width:40px;">No.</th>
        <th style="width:90px;">Invoice No.</th>
        <th class="text-left">Item </th>
        <th style="width:60px;">Drawing No. </th>
        <th style="width:100px;">Qty</th>
    </tr>
    <?php
        $i=1;
        if(!empty($grnData->itemData)):
            foreach($grnData->itemData as $row):
                echo '<tr>';
                    echo '<td class="text-center">'.$i++.'</td>';
                    echo '<td>'.$grnData->challan_no.'</td>';
                    echo '<td>'.$row->item_name.'</td>';
                    echo '<td>'.$row->drawing_no.'</td>';
                    echo '<td class="text-right">'.$row->qty.'</td>';
                echo '</tr>';
            endforeach;
        endif;
    ?>
</table>
