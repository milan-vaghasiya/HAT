<div class="row">
	<div class="col-12">
		<table class="table">
		    <tr>
		        <td class="6 text-left" style="width:30%;font-weight:bold;padding:0px !important;"></td>
		        <td class="8 text-center" style="width:40%;letter-spacing: 2px;font-weight:bold;padding:0px !important;">GOODS RECEIPT NOTE</td>
		        <td class="6 text-right" style="width:30%;font-weight:bold;padding:0px !important;"></td>
		    </tr>
		</table>
		
		<table class="table top-table-border" style="margin-top:2px;">
			<tr>
                <td style="border-right:none; border-bottom:none; width:16%">GRN No.</td>
                <td style="border-left:none; border-right:none; border-bottom:none;" colspan="4">: <b><?=getPrefixNumber($grnData->grn_prefix,$grnData->grn_no)?></b></td>
                <td style="border-left:none; border-bottom:none">Date : <b><?=formatDate($grnData->grn_date)?></b></td>
			</tr>
			<tr>
                <td style="border-right:none; border-top:none">Supplier</td>
                <td style="border-left:none; border-right:none; border-top:none" colspan="3">: <b><?=$partyData->party_name?></b></td>
                <td style="border-left:none; border-top:none" colspan="2"></td>
			</tr>
        </table>

        <table class="table top-table-border">
			<tr>
                <td style="border-right:none; border-bottom:none">D.C. No.</td>
                <td style="border-left:none; border-right:none; border-bottom:none"> : <?=$grnData->challan_no?></td>
                <td style="border-left:none; border-bottom:none; border-right:none;" colspan="4">Date : <?=formatDate($grnData->grn_date)?></td>
                <td style="border-left:none; border-bottom:none; width:220px" colspan="4">Transport : <?= $grnData->transport?> </td>
			</tr>
			<tr>
                <td style="border-right:none; border-top:none">Challan No.</td>
                <td style="border-left:none; border-right:none; border-top:none"> : <?= $grnData->challan_no ?> </td>
                <td style="border-left:none; border-top:none; border-right:none" colspan="4">Date : <?=formatDate($grnData->challan_date)?> </td>
                <td style="border-left:none; border-top:none" colspan="4">Vehicle No. : <?= $grnData->vehicle_no?></td>
			</tr>
		</table>
		
        <table class="table item-list-bb" style="margin-top:10px;">
			<tr>
				<th style="width:5%;"> Sr. No.</th>
				<th style="width:25%;">Item Name / Description</th>
				<th style="width:10%;">Unit</th>
				<th style="width:15%;">Qty.(Opt. Unit)</th>
				<th style="width:15%;">Qty.(As/P.O.)</th>
				<th style="width:15%;">Rate</th>
				<th style="width:15%;">P.O. No.</th>
			</tr>
			<?php
				$i=1; $totalQty=0;
				if(!empty($grnData->itemData)):
					foreach($grnData->itemData as $row): 
						$item_name = str_replace(["\r\n", "\r", "\n"], "<br/>", $row->item_name);
                      
						echo '<tr>';
							echo '<td class="text-center">'.$i++.'</td>';
							echo '<td class="text-center">'.$item_name.'</td>';
							echo '<td class="text-center">'.$row->unit_name.'</td>';
							echo '<td class="text-right">'.$row->qty_kg.'</td>';
							echo '<td class="text-right">'.$row->qty.'</td>';
							echo '<td class="text-right">'.$row->price.'</td>';
							echo '<td class="text-center">'.$row->po_prefix.$row->po_no.'</td>';
						echo '</tr>';
                        $totalQty += $row->qty;
					endforeach;
				endif;
			?>
            <tr>
                <td colspan="4" class="text-right">Total : </td>
                <td class="text-right"><?=$totalQty?></td>
                <td colspan="2"></td>
            </tr>
		</table>
	</div>
</div>