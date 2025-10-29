<div class="row">
	<div class="col-12">
		<table class="table">
		    <tr>
		        <td style="width:30%"></td>
		        <td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">SALES ORDER</td>
		        <td class="fs-15 text-right" style="letter-spacing: 1px;font-weight:bold;padding:0px !important;width:30%;">GSTIN : <?=$companyData->company_gst_no?></td>
		    </tr>
        </table>
		
		<table class="table top-table-border" style="margin-top:2px;">
			<tr>
				<td rowspan="4" style="width:67%;vertical-align:top;">
					<b>M/S. <?=$partyData->party_code?></b>
				</td>
				<th style="width:15%;vertical-align:top;">SO No.</th>
				<td style="width:18%;vertical-align:top;"><?=getPrefixNumber($soData->trans_prefix,$soData->trans_no)?></td>
			</tr>
			<tr>
				<th style="font-size:12px;">SO Date</th><td><?=formatDate($soData->trans_date)?></td>
			</tr>
			<tr>
				<th>Cust. PO. No.</th><td><?=$soData->doc_no?></td>
			</tr>
			<tr>
				<th>Cust. PO. Date</th><td><?=(!empty($soData->doc_date)) ? formatDate($soData->doc_date) : ""?></td>
			</tr>
		</table>
		<?php
			$totalCols=8;if($soData->gst_applicable == 1){$totalCols=9;}else{$totalCols=8;} 
		?>
		<table class="table item-list-bb" style="margin-top:10px;">
		    <thead>
			<tr>
				<th style="width:5%;">No.</th>
				<th class="text-left" style="width:50%;">Item Description</th>
				<th style="width:15%;">Grade</th>
				<th style="width:10%;">Qty</th>
				<th style="width:10%;">req. Del. Date</th>
				<th style="width:10%;">HAT Del. Date</th>
			</tr>
			</thead>
			<tbody>
			<?php
				$i=1;$totalQty = 0;
				if(!empty($soData->items)):
					foreach($soData->items as $row):					
						$drgNo = (!empty($row->itemDrgNo)) ? '<br><b>Drg. No.</b> '.$row->itemDrgNo : '';	
						$revNo = (!empty($row->itemRevNO)) ? $row->itemRevNO : '';	
						$itmSize = (!empty($row->size)) ? '<br><b>Size</b> '.$row->size : '';
						if(!empty($revNo) AND !empty($drgNo)){$drgNo = $drgNo.', <b>Rev. No.</b> '.$row->itemRevNO;}
						if(!empty($revNo) AND empty($drgNo)){$drgNo = '<br> <b>Rev. No.</b> '.$row->itemRevNO;}
						if(!empty($row->partNo) AND !empty($drgNo)){$drgNo = $drgNo.', <b>Item Code</b> '.$row->partNo;}
						if(!empty($row->partNo) AND empty($drgNo)){$drgNo = '<br> <b>Item Code</b> '.$row->partNo;}	
						$item_code = (!empty($row->item_code))?'['.$row->item_code.']':'';
						
						echo '<tr>';
							echo '<td class="text-center">'.$i++.'</td>';
							echo '<td>'.$item_code.' '.nl2br($row->item_name).$itmSize.$drgNo .'</td>';
							echo '<td class="text-center">'.$row->material_grade.'</td>';
							echo '<td class="text-right">'.$row->qty.'</td>';
							echo '<td class="text-center">'.formatDate($row->cod_date).'</td>';
							echo '<td class="text-center">'.formatDate($row->target_date).'</td>';
						echo '</tr>';
					endforeach;
				endif;
			?>
			</tbody>
		</table>
	</div>
</div>