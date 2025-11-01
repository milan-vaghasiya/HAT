<div class="row">
	<div class="col-12">
		<table class="table">
		    <tr>
		        <td style="width:30%"></td>
		        <td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">DISPATCH ADVISE</td>
		        <td class="fs-15 text-right" style="letter-spacing: 1px;font-weight:bold;padding:0px !important;width:30%;">GSTIN : <?= $companyData->company_gst_no;?></td>
		    </tr>
        </table>
		
		<table class="table top-table-border" style="margin-top:2px;">
			<tr>
				<td rowspan="4" style="width:67%;vertical-align:top;">
					<b>M/S. <?= $salesData->party_name;?></b><br>
					<b>GSTIN :</b> <?= $party_gstin;?>
				</td>
				<th style="font-size:12px;">Date</th>
				<td><?= formatDate($salesData->trans_date);?></td>
			</tr>
			<tr>
				<th style="width:15%;vertical-align:top;">Challan No.</th>
				<td style="width:18%;vertical-align:top;"><?= getPrefixNumber($salesData->trans_prefix,$salesData->trans_no);?></td>
			</tr>
			<tr>
				<th>PO. No.</th><td><?= $salesData->doc_no;?></td>
			</tr>
		</table>
		
		<table class="table item-list-bb" style="margin-top:10px;">
		    <thead>
			<tr>
				<th style="width:6%;">Sr.No.</th>
 				<th class="text-left">Product No.</th>
 				<th style="width:25%;">Act. Ball Size</th>
 				<th style="width:10%;">Grade</th>
 				<th style="width:10%;">Heat No.</th>
 				<th style="width:10%;">Qty</th>
 				<th style="width:10%;">P.O NO.</th>
 				<th style="width:10%;">P.O Dt.</th>
 				<th style="width:15%;">Mat. Recd. Dt.</th>
 				<th style="width:10%;">Sign. (Disp. Dept.)</th>
 				<th style="width:10%;">Sign. (Sales Dept.)</th>
			</tr>
			</thead>
			<tbody>
				<?php
					if(!empty($tempData)){
						foreach($tempData as $key => $row){
							echo '<tr>';
								echo '<td class="text-center">'.($key+1).'</td>';
								echo '<td class="text-left">'.$row->item_code.'</td>';
								echo '<td class="text-left">'.$row->item_name.'</td>';
								echo '<td class="text-center">'.$row->material_grade.'</td>';
								echo '<td class="text-center">'.$row->batch_no.'</td>';
								echo '<td class="text-center">'.sprintf('%0.2f', $row->qty).'</td>';
								echo '<td class="text-center">'.$row->doc_no.'</td>';
								echo '<td class="text-center">'.formatDate($row->doc_date).'</td>';
								echo '<td class="text-center"></td>';
								echo '<td class="text-center"></td>';
								echo '<td class="text-center"></td>';
							echo '</tr>';
						}
					}
				?>
			</tbody>
		</table>
	</div>
</div>