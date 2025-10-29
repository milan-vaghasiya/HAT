<div class="row">
	<div class="col-12">
		<table class="table">
			<tr><td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">PURCHASE ENQUIRIE</td></tr>
		</table>
		
		<table class="table top-table-border" style="margin-top:10px;">
			<tr>
				<td rowspan="4" style="width:65%;vertical-align:top;">
					<b>M/S. <?=$enqData->supplier_name?></b> <br>
					<small><?=$enqData->party_address?></small><br><br>
					<b>Kind. Attn. : <?=$enqData->contact_person?></b><br>
					Contact No. : <?=$enqData->party_mobile?><br>
					Email : <?=$enqData->contact_email?>
				</td>
				<th>Enq. No.</th>
				<td><?=$enqData->enq_prefix,$enqData->enq_no?></td>
			</tr>
			<tr>
				<th>Enq. Date</th><td><?=formatDate($enqData->enq_date)?></td>
			</tr>
			
		</table>
		
		<table class="table item-list-bb" style="margin-top:25px;">
			<tr>
				<th style="width:40px;">No.</th>
				<th class="text-left">Item Description</th>
				<th style="width:100px;">Qty</th>
				<th style="width:50px;">UOM</th>
				<th style="width:60px;">Rate<br><small></small></th>
				<th style="width:110px;">Amount<br><small></small></th>
			</tr>
			<?php
				$i=1;$totalQty = 0;$totalAmt=0;
				if(!empty($enqData->itemData)):
					foreach($enqData->itemData as $row):
						$item_name = str_replace(["\r\n", "\r", "\n"], "<br/>", $row->item_name);
						$amount = $row->confirm_rate * $row->confirm_qty;
						echo '<tr>';
							echo '<td class="text-center">'.$i++.'</td>';
							echo '<td>'.$item_name.'</td>';
							echo '<td class="text-right" style="padding-right:8px;">'.$row->qty.'</td>';
							echo '<td class="text-center" style="padding-right:8px;">'.$row->unit_name.'</td>';
							echo '<td class="text-right" style="padding-right:8px;">'.sprintf('%.3f', $row->confirm_rate).'</td>';
							echo '<td class="text-right" style="padding-right:8px;">'.sprintf('%.3f', $amount).'</td>';
						echo '</tr>';
						$totalQty += $row->confirm_qty;$totalAmt += $amount;
					endforeach;
				endif;
			?>
			<tr>
				<th colspan="5" class="text-right">Total Amount</th>
				<th class="text-right"><?=sprintf('%.3f', $totalAmt)?></th>
			</tr>
		</table>
		<p><b>Amount In Words  : <i><?=numToWordEnglish($totalAmt)?></i></b></p>
		
	</div>
</div>