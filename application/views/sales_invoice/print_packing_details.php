<div class="row">
	<div class="col-12">
		<table class="table">
		    <tr>
		        <td style="width:30%"></td>
		        <td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">PACKING DETAILS</td>
		        <td class="fs-15 text-right" style="letter-spacing: 1px;font-weight:bold;padding:0px !important;width:30%;">GSTIN : <?= $companyData->company_gst_no;?></td>
		    </tr>
        </table>
		
		<table class="table top-table-border" style="margin-top:2px;">
			<tr>
				<td rowspan="4" style="width:67%;vertical-align:top;">
					<b>Invoice To:</b><br>
					<b><?= $salesData->party_name;?></b><br>
					<b><?= $salesData->billing_address;?></b><br>
					<b>GSTIN :</b> <?= $party_gstin;?>
				</td>
				<th style="font-size:12px;">Packing Date</th>
				<td><?= formatDate($salesData->doc_date);?></td>
			</tr>
			<tr>
				<th style="width:15%;vertical-align:top;">Invoice No.</th>
				<td style="width:18%;vertical-align:top;"><?= $salesData->trans_prefix.$salesData->trans_no;?></td>
			</tr>
		</table>
		
		<table class="table item-list-bb" style="margin-top:10px;">
		    <thead>
			<tr>
				<th style="width:6%;">Wooden Box No.</th>
				<th class="text-left">H.A.T. Product No.</th>
				<th style="width:10%;">Invoice No. </th>
				<th style="width:10%;">Invoice Date </th>
				<th style="width:10%;">P.O NO.</th>
				<th style="width:10%;">P.O Date</th>

				<th style="width:25%;">Item </th>
				<th style="width:10%;">Drawing No.</th>
				<th style="width:15%;">Description</th>
				<th style="width:10%;">Grade</th>
				<th style="width:10%;">Qty</th>
			</tr>
			</thead>
			<tbody>
				<?php
					if(!empty($tempData))
					{
						$item_records = [];
						foreach ($tempData as $row)   
						{	
							$item_records[$row->wooden_box_no][] = $row;
						}
						if (!empty($item_records)) {
							foreach ($item_records as $key => $main_row) {
								$rowspan = count($main_row); 
								$firstRow = true; 

								foreach ($main_row as $row) {
									echo '<tr>';

									if ($firstRow) {
										echo '<td class="text-center" height="37" rowspan="'.$rowspan.'">'.$key.'</td>';
										$firstRow = false; 
									}

									echo '<td class="text-left">'.$row->item_code.'</td>';
									echo '<td class="text-left">'.$salesData->trans_number.'</td>';
									echo '<td class="text-center">'.formatDate($salesData->trans_date).'</td>';
									echo '<td class="text-center">'.$salesData->doc_no.'</td>';
									echo '<td class="text-center">'.formatDate($salesData->doc_date).'</td>';
									echo '<td class="text-left">'.$row->item_name.'</td>';
									echo '<td class="text-left">'.$row->drawing_no.'</td>';
									echo '<td class="text-center">'.$row->description.'</td>';
									echo '<td class="text-center">'.$row->material_grade.'</td>';
									echo '<td class="text-center">'.sprintf('%0.2f', $row->qty).'</td>';

									echo '</tr>';
								}
							}
						}
					}
				?>
			</tbody>
		</table>
	</div>
</div>