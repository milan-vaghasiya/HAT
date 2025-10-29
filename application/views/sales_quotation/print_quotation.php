<div class="row">
	<div class="col-12">
		<table class="table">
			<tr>
				<td><?=$companyData->company_gst_no?></td>
				<td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">SALES QUOTATION</td>
				<td class="text-right">F/MKT/02 (00/01.01.16)</td>
			</tr>
		</table>
		
		<table class="table top-table-border" style="margin-top:10px;">
			<tr>
				<td rowspan="4" style="width:55%;vertical-align:top;">
					<b>M/S. <?=$sqData->party_name?></b> <br>
					<small><?=$sqData->party_address?></small><br><br>
					<b>Kind. Attn. : <?=$sqData->contact_person?></b><br>
					Contact No. : <?=$sqData->party_mobile?><br>
					Email : <?=$sqData->contact_email?>
				</td>
				<th style="width:12%;vertical-align:top;">Qtn. No.</th>
				<td style="width:12%;vertical-align:top;"><?=$sqData->trans_no?></td>
				<td style="width:21%;vertical-align:top;"><?=$qrn?></td>
			</tr>
			<tr>
				<th>Qtn. Date</th><td colspan="2"><?=formatDate($sqData->trans_date)?></td>
			</tr>
			<tr>
				<th>Reference</th><td colspan="2"><?=$sqData->ref_by?> (<?=$sqData->refNo?>)</td>
			</tr>
			<tr>
				<th>Ref. Date</th><td colspan="2"><?=(!empty($sqData->ref_date)) ? formatDate($sqData->ref_date) : ""?></td>
			</tr>
		</table>
		
		<table class="table item-list-bb" style="margin-top:25px;">
			<tr>
				<th style="width:40px;">No.</th>
				<th class="text-left">Item Description</th>
				<th style="width:100px;">Qty</th>
				<th style="width:50px;">UOM</th>
				<th style="width:60px;">Rate<br><small>(<?=$sqData->lr_no?>)</small></th>
				<th style="width:110px;">Amount<br><small>(<?=$sqData->lr_no?>)</small></th>
			</tr>
			<?php
				$i=1;$totalQty = 0;$totalAmt=0;
				if(!empty($sqData->itemData)):
					foreach($sqData->itemData as $row):
						$drg_rev_no = (!empty($row->drg_rev_no)) ? '<b>Drg. No.</b>:'.$row->drg_rev_no : '';
						$rev_no = (!empty($row->rev_no)) ? '<b>Rev. No</b>:'.$row->rev_no : '';
						$rev_no = (!empty($drg_rev_no)) ? ', '.$rev_no : $rev_no;
						$part_no = (!empty($row->batch_no)) ? '<b>Part No:</b>'.$row->batch_no : '';
						$part_no = (!empty($rev_no)) ? ', '.$part_no : $part_no;
						$item_name = $row->item_name.'<br>'.$drg_rev_no.$rev_no.$part_no;
						$item_name = (!empty($row->grn_data)) ? $item_name.'<br>'.$row->grn_data : $item_name;
						$item_name = str_replace(["\r\n", "\r", "\n"], "<br/>", $item_name);
						$amount = $row->price * $row->qty;
						echo '<tr>';
							echo '<td class="text-center">'.$i++.'</td>';
							echo '<td>'.$item_name.'</td>';
							echo '<td class="text-right" style="padding-right:8px;">'.floatVal($row->qty).'</td>';
							echo '<td class="text-center" style="padding-right:8px;">'.$row->unit_name.'</td>';
							echo '<td class="text-right" style="padding-right:8px;">'.sprintf('%.3f',$row->price).'</td>';
							echo '<td class="text-right" style="padding-right:8px;">'.sprintf('%.3f',$amount).'</td>';
						echo '</tr>';
						$totalQty += $row->qty;$totalAmt += $amount;
					endforeach;
					if($sqData->challan_no == 2):
						echo '<tr>';
							echo '<td class="text-center">'.$i.'</td>';
							echo '<td>Development Charge</td>';
							echo '<td class="text-right" style="padding-right:8px;">-</td>';
							echo '<td class="text-center" style="padding-right:8px;">-</td>';
							echo '<td class="text-right" style="padding-right:8px;">-</td>';
							echo '<td class="text-right" style="padding-right:8px;">'.sprintf('%.3f',$sqData->net_weight).'</td>';
						echo '</tr>';
						$totalAmt += $sqData->net_weight;
					endif;
				endif;
			?>
			<tr>
				<th colspan="5" class="text-right">Total Amount</th>
				<th class="text-right"><?=sprintf('%.3f',$totalAmt)?></th>
			</tr>
		</table>
		<p><b>Amount In Words (<?=$sqData->lr_no?>) : <i><?=numToWordEnglish($totalAmt)?></i></b></p>
		<h4>Terms & Conditions :-</h4>
		<table class="table top-table" style="margin-top:10px;">
			<?php
				if(!empty($sqData->terms_conditions)):
					$terms = json_decode($sqData->terms_conditions);
					foreach($terms as $row):
						echo '<tr>';
							echo '<th class="text-left fs-11" style="width:140px;">'.$row->term_title.'</th>';
							echo '<td class=" fs-11"> : '.$row->condition.'</td>';
						echo '</tr>';
					endforeach;
				endif;
			?>
		</table>		
	</div>
</div>