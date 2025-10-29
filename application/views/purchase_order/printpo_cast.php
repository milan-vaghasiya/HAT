<div class="row">
	<div class="col-12">
	    
		<table class="table">
		    <tr>
		        <td style="width:30%"></td>
		        <td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;width:40%;">PURCHASE ORDER</td>
		        <td class="fs-15 text-right" style="letter-spacing: 1px;font-weight:bold;padding:0px !important;width:30%;">GSTIN : <?=$companyData->company_gst_no?></td>
		    </tr>
		</table>
		
		<table class="table top-table-border">
			<tr>
				<td rowspan="4" style="width:70%;vertical-align:top;">
					<b>M/S. <?=$poData->party_name?></b> <br>
					<small><?=$poData->party_address?></small><br><br>
					Kind. Attn. : <?=$poData->contact_person?><br>
					Contact No. : <?=$poData->party_mobile?><br>
					Email : <?=$poData->contact_email?><br>
					Reference : <?=$poData->reference_by?>
				</td>
				<th style="width:12%;vertical-align:top;">PO No.</th>
				<td style="width:18%;vertical-align:top;"><?=getPrefixNumber($poData->po_prefix,$poData->po_no)?></td>
			</tr>
			<tr>
				<th>PO Date</th><td><?=formatDate($poData->po_date)?></td>
			</tr>
			<tr>
				<th>Qtn. No.</th><td><?=$poData->quotation_no?></td>
			</tr>
			<tr>
				<th>Qtn. Date</th><td><?=(!empty($poData->quotation_date)) ? formatDate($poData->quotation_date) : ""?></td>
			</tr>
		</table>
		
		<table class="table item-list-bb" style="margin-top:10px;">
			<tr>
				<th style="width:40px;">No.</th>
				<th class="text-left">Item Code</th>
				<th class="text-left">Item Description</th>
				<th style="width:75px;">Die No.</th>
				<th style="width:75px;">Req. Cast. Dim.</th>
				<th style="width:75px;">Delivery Date</th>
				<th style="width:80px;">Qty (Kg)</th>
				<th style="width:80px;">Qty (Nos)</th>
				<th style="width:75px;">Rate</th>
				<th style="width:110px;">Amount</th>
			</tr>
			<?php
				$i=1;$totalQtyKg = 0;$totalQty = 0;
				if(!empty($poData->itemData)):
					foreach($poData->itemData as $row):
						$indent = (!empty($poData->enq_id)) ? '<br>Reference No:'.$poData->enq_prefix.$poData->enq_no : '';
						$delivery_date = (!empty($row->delivery_date)) ? '<br>Delivery Date :'.formatDate($row->delivery_date) : '';
						$drg_no = (!empty($row->drg_no)) ? '<br>Drg. No. :'.$row->drg_no : '';
						
						$castDime = (!empty($row->material_grade))?explode(' X ',$row->material_grade):"";
						$dimention = '<b>ID:-</b> '.(!empty($castDime[0])?$castDime[0]:'').'<br>';
						$dimention .= '<b>OD:-</b> '.(!empty($castDime[1])?$castDime[1]:'').'<br>';
						$dimention .= '<b>Length:-</b> '.(!empty($castDime[2])?$castDime[2]:'');
						
						echo '<tr>';
							echo '<td rowspan="2" class="text-center">'.$i++.'</td>';
							echo '<td>'.$row->item_code.'</td>';
							echo '<td>'.$row->item_name.$indent.$delivery_date.$drg_no.'</td>';
							echo '<td class="text-center">'.$row->die_no.'<br>Pin.'.$row->pin_dia.'</td>';
							echo '<td class="text-left">'.$dimention.'</td>';
							echo '<td class="text-center">'.formatDate($row->delivery_date).'</td>';
							echo '<td class="text-right">'.sprintf('%0.2f',$row->qty_kg).'</td>';
							echo '<td class="text-right">'.sprintf('%0.2f',$row->qty).'</td>';
							echo '<td class="text-center">'.$row->price.'</td>';
							echo '<td rowspan="2" class="text-right">'.$row->amount.'</td>';
						echo '</tr>';
						echo '<tr><td colspan="5"><i>Notes:</i> '.$row->item_remark.'</td></tr>';
						$totalQty += $row->qty;$totalQtyKg += $row->qty_kg;
					endforeach;
				endif;
			?>
			<tr>
				<th colspan="6" class="text-right">Total Qty.</th>
				<th class="text-right"><?=sprintf('%.3f',$totalQtyKg)?></th>
				<th class="text-right"><?=sprintf('%.3f',$totalQty)?></th>
				<th colspan="1" class="text-right">Sub Total</th>
				<th class="text-right"><?=sprintf('%.2f',$poData->amount)?></th>
			</tr>
			
			<?php
			    $rspan = 3;
			    if($poData->gst_type == 2){$rspan = 3;}else{$rspan = 4;} ?>
    			<tr>
    			    <td colspan="7" rowspan="<?=$rspan?>" style="vertical-align:top;"><b>Notes :</b><br><?=str_replace("\n", '<br />',  $poData->remark)?></td>
    				
    			</tr>
    			<tr>
    				<th colspan="2" class="text-right">Taxable Amount</th>
    				<th class="text-right"><?=sprintf('%.2f',($poData->amount + $poData->packing_charge))?></th>
    			</tr>
			
			<?php if($poData->gst_type == 2){ ?>
    			<tr>
    				<th colspan="2" class="text-right">IGST</th>
    				<td class="text-right"><?=sprintf('%.2f',($poData->igst_amt + $poData->packing_gst))?></td>
    			</tr>
    		<?php }else{ ?>
    			<tr>
    				<th colspan="2" class="text-right">CGST</th>
    				<td class="text-right"><?=sprintf('%.2f',($poData->cgst_amt + $poData->packing_gst))?></td>
    			</tr>
    			<tr>
    				<th colspan="2" class="text-right">SGST</th>
    				<td class="text-right"><?=sprintf('%.2f',($poData->sgst_amt + $poData->packing_gst))?></td>
    			</tr>
			<?php }?>

			<tr>
				<td colspan="7" rowspan="2" style="vertical-align:top;"><b>Amount In Words: </b> <?=numToWordEnglish($poData->net_amount)?></td>
				<th colspan="2" class="text-right">Round Off</th>
				<td class="text-right"><?=sprintf('%.2f',$poData->round_off)?></td>
			</tr>
			<tr>
				<th colspan="2" class="text-right">Grand Total</th>
				<th class="text-right"><?=sprintf('%.2f',$poData->net_amount)?></th>
			</tr>
		</table>
		
		<table class="table top-table" style="margin-top:10px;">
			<tr>
			    <td><h4>Terms & Conditions :-</h4></td>
			</tr>
			<?php
				if(!empty($poData->terms_conditions)):
					$terms = json_decode($poData->terms_conditions);
					foreach($terms as $row):
						echo '<tr>';
							echo '<th class="text-left fs-11" style="width:100px;vertical-align:top;">'.$row->term_title.'</th>';
							echo '<td class=" fs-11" style="vertical-align:top;">: </td>';
							echo '<td class=" fs-11" style="text-align:justify;">'.nl2br($row->condition).'</td>';
						echo '</tr>';
					endforeach;
				endif;
			?>
		</table>
	</div>
</div>