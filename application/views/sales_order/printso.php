<div class="row">
	<div class="col-12">
		<table class="table">
		    <tr>
		        <td style="width:30%"></td>
		        <td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">ORDER ACCEPTANCE</td>
		        <td class="fs-15 text-right" style="letter-spacing: 1px;font-weight:bold;padding:0px !important;width:30%;">GSTIN : <?=$companyData->company_gst_no?></td>
		    </tr>
        </table>
		
		<table class="table top-table-border" style="margin-top:2px;">
			<tr>
				<td rowspan="4" style="width:67%;vertical-align:top;">
					<b>M/S. <?=$soData->party_name?></b> <br>
					<small><?=$partyData->party_address?></small><br><br>
					<b>Kind. Attn. : <?=$soData->trans_mode?></b> <br>
					Contact No. : <?=$partyData->party_mobile?><br>
					Email : <?=$partyData->contact_email?><br><br>
					GSTIN : <?=$partyData->gstin?>
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
			$totalCols=6;if($soData->gst_applicable == 1){$totalCols=7;}else{$totalCols=6;} 
		?>
		<table class="table item-list-bb" style="margin-top:10px;">
		    <thead>
			<tr>
				<th style="width:5%;">Sr. No.</th>
				<th class="text-left" style="width:8%;">Product No.</th>
				<th class="text-left" style="width:30%;">Item Description</th>
				<th class="text-left" style="width:8%;">Grade</th>
				<th style="width:10%;">Req. Del. Date</th>
				<th style="width:10%;">HAT Del. Date</th>
				<th style="width:8%;">Qty</th>
				<?php if($soData->gst_applicable == 1){ ?>
					<th style="width:5%;">GST <small>%</small></th>
				<?php } ?>
				<th style="width:8%;">Rate</th>
				<th style="width:10%;">Amount</th>
			</tr>
			</thead>
			<tbody>
    			<?php
    				$i=1;$totalQty = 0; $totalPackCh = 0;
    				if(!empty($soData->items)):
    					foreach($soData->items as $row):
    										
    						$drgNo = (!empty($row->itemDrgNo)) ? '<br><b>Drg. No.</b> '.$row->itemDrgNo : '';	
    						$revNo = (!empty($row->itemRevNO)) ? $row->itemRevNO : '';	
    						if(!empty($revNo) AND !empty($drgNo)){$drgNo = $drgNo.', <b>Rev. No.</b> '.$row->itemRevNO;}
    						if(!empty($revNo) AND empty($drgNo)){$drgNo = '<br> <b>Rev. No.</b> '.$row->itemRevNO;}
    						if(!empty($row->partNo) AND !empty($drgNo)){$drgNo = $drgNo.', <b>Item Code</b> '.$row->partNo;}
    						if(!empty($row->partNo) AND empty($drgNo)){$drgNo = '<br> <b>Item Code</b> '.$row->partNo;}	
    						
    						$item_code = (!empty($row->item_code))?$row->item_code:'';
    						
    						echo '<tr>';
    							echo '<td class="text-center">'.$i++.'</td>';
    							echo '<td>'.$item_code.'</td>';
    							echo '<td>'.nl2br($row->item_name).$drgNo.'</td>';
    							echo '<td>'.$row->material_grade.'</td>';
    							echo '<td>'.formatDate($row->cod_date).'</td>';
    							echo '<td>'.formatDate($row->target_date).'</td>';
    							echo '<td class="text-right">'.floatval($row->qty).'</td>';
    							if($soData->gst_applicable == 1){echo '<td class="text-center">'.floatVal($row->igst_per).'</td>';}
    							echo '<td class="text-right">'.sprintf('%.3f',$row->price).'</td>';
    							echo '<td class="text-right">'.sprintf('%.2f',$row->amount).'</td>';
    						echo '</tr>';
    						$totalQty += $row->qty;
    						$totalPackCh += $row->packing_charge;
    					endforeach;
    				endif;
    			?>
    			<?php
        			$gstBottomRow = '';$amtRowSpan=5;$gstAmount=0;
        			if($soData->gst_applicable == 1): 
        				if(empty($soData->party_state_code) || $soData->party_state_code == "24"):
        					$gstBottomRow = '<tr>
        										<th colspan="2" class="text-right">CGST</th>
        										<td class="text-right">'.sprintf('%.2f',$soData->cgst_amount).'</td>
        									</tr>
        									<tr>
        										<th colspan="2" class="text-right">SGST</th>
        										<td class="text-right">'.sprintf('%.2f',$soData->sgst_amount).'</td>
        									</tr>';
        					$amtRowSpan=6;$gstAmount+=$soData->cgst_amount+$soData->sgst_amount;
        				else:
        					$gstBottomRow = '<tr>
        										<th colspan="2" class="text-right">IGST</th>
        										<td class="text-right">'.sprintf('%.2f',$soData->igst_amount).'</td>
        									</tr>';
        					$amtRowSpan=5;$gstAmount+=$soData->cgst_amount+$soData->sgst_amount;
        				endif; 
        			endif;
    			?>
    			<tr>
    				<th colspan="<?=($totalCols - 1)?>" class="text-right">Total Qty.</th>
    				<th class="text-right"><?=floatval($totalQty)?></th>
    				<th colspan="2" class="text-right">P & F</th>
    				<td class="text-right"><?=sprintf('%.2f',$totalPackCh)?></td>
    			</tr>
    			<tr>
    			    <?php if($soData->currency == 'INR') : ?>
    			        <th colspan="<?=($totalCols)?>" rowspan="<?=($amtRowSpan+1)?>">Amount In Words (<?=$soData->currency?>) : <?=numToWordCurrency($soData->net_amount,"","en_IN")?></th>
    			    <?php else: ?>
    			            <th colspan="<?=($totalCols)?>" rowspan="<?=($amtRowSpan+1)?>">Amount In Words (<?=$soData->currency?>) : <?=numToWordCurrency($soData->net_amount)?></th>
    			    <?php endif; ?>
    				<th colspan="2" class="text-right">Sub Total</th>
    				<th class="text-right"><?=sprintf('%.2f',($soData->total_amount + $totalPackCh))?></th>
    			</tr>
    			<tr>
    				<th colspan="2" class="text-right">Taxable Amount</th>
    				<th class="text-right"><?=sprintf('%.2f',($soData->taxable_amount + $totalPackCh))?></th>
    			</tr>
    			<tr>
    			<?=$gstBottomRow?>
    			<tr>
                	<th colspan="2" class="text-right">Round Off</th>
                	<td class="text-right"><?=sprintf('%.2f',$soData->round_off_amount)?></td>
                </tr>
                <tr>
                	<th colspan="2" class="text-right">Grand Total</th>
                	<th class="text-right"><?=sprintf('%.2f',$soData->net_amount)?></th>
                </tr>
            </tbody>
		</table>
		<h4>Terms & Conditions :-</h4>
		<table class="table top-table" style="margin-top:10px;">
			<?php
				if(!empty($soData->terms_conditions)):
					$terms = json_decode($soData->terms_conditions);
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