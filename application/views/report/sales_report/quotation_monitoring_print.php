<div class="row">
	<div class="col-12">
		<table class="table top-table-border">
			<tr>
                <td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important; width:505px;">SALES QUOTATION</td>
                <td class="text-center"><b>F/MKT/02 (00/01.01.16)</b></td>
            </tr>
            <tr>
                <td colspan="2">Address:- <?=$sqData->party_address?></td>
            </tr>
            <tr>
                <td colspan="2">Mobile No.:- <?=$sqData->party_mobile?> &nbsp;&nbsp; Email:- <?=$sqData->contact_email?></td>
            </tr>
		</table>
		
		<table class="table top-table-border" style="margin-top:0px;">
			<tr>
				<td rowspan="3" style="width:55%;vertical-align:top;">To,</td>
				<td style="border-right:none;border-bottom:none;">Quotation No.:</td>
                <td style="border-left:none;border-bottom:none;">Dt. : <?=formatDate($sqData->trans_date)?></td>
			</tr>
			<tr>
				<td style="border-right:none;border-top:none;border-bottom:none;">Customer's Ref. No. :</td>
                <td style="border-left:none;border-top:none;border-bottom:none;">Dt. :</td>
			</tr>
			<tr>
				<td colspan="2" style="border-top:none;">Kind. Attn. :</td>
			</tr>
            <tr>
                <td colspan="3">We are sending you quotation, subject to terms & conditions as under :</td>
            </tr>
		</table>
		
		<table class="table item-list-bb" style="margin-top:0px;">
			<tr>
				<th style="width:40px;">No.</th>
				<th class="text-center" style="width:250px;">Item Name</th>
				<th>Drg. No.</th>
				<th>Qty.</th>
				<th>MOC</th>
				<th>Rate</th>
			</tr>
			<?php
				$i=1;$totalQty = 0;$totalAmt=0;
				if(!empty($sqData->itemData)):
					foreach($sqData->itemData as $row):
						// $drg_rev_no = (!empty($row->drg_rev_no)) ? '<b>Drg. No.</b>:'.$row->drg_rev_no : '';
						// $rev_no = (!empty($row->rev_no)) ? '<b>Rev. No</b>:'.$row->rev_no : '';
						// $rev_no = (!empty($drg_rev_no)) ? ', '.$rev_no : $rev_no;
						// $part_no = (!empty($row->batch_no)) ? '<b>Part No:</b>'.$row->batch_no : '';
						// $part_no = (!empty($rev_no)) ? ', '.$part_no : $part_no;
						// $item_name = $row->item_name.'<br>'.$drg_rev_no.$rev_no.$part_no;
						// $item_name = (!empty($row->grn_data)) ? $item_name.'<br>'.$row->grn_data : $item_name;
						// $item_name = str_replace(["\r\n", "\r", "\n"], "<br/>", $item_name);
						$amount = $row->price * $row->qty;
						echo '<tr>';
							echo '<td class="text-center">'.$i++.'</td>';
							echo '<td>'.$row->item_name.'</td>';
							echo '<td class="text-center">'.$row->drg_rev_no.'</td>';
							echo '<td class="text-right">'.floatVal($row->qty).'</td>';
							echo '<td class="text-right"></td>';
							echo '<td class="text-right">'.sprintf('%.3f',$amount).'</td>';
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