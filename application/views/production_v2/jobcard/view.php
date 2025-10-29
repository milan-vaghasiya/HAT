<div class="row">
	<div class="col-12">
		<table class="table" style="border-bottom:1px solid #000000;">
			<tr>
				<td class="text-uppercase text-center" style="font-size:1.3rem;font-weight:bold;width:40%;"><?=SITENAME?><br><span style="font-weight:normal;">[ PROCESS ROUTE CARD ]</span></td>
			</tr>
		</table>
		<table class="table top-table">
			<tr>
				<th style="width:12%;">Job Card No.</th><td style="width:48%"> : <?=$jobData->job_prefix.$jobData->job_no?></td>
				<th style="width:12%;">Job Date</th><td style="width:28%"> : <?=formatDate($jobData->job_date)?></td>
			</tr>
			<tr>
				<th>SO No.</th><td> : <?=$jobData->trans_prefix.$jobData->trans_no?></td>
				<th>Job Quantity</th><td> : <?=$jobData->qty?></td>
			</tr>
			<tr>
				<th>Product Code </th><td> : <?=$jobData->item_code?></td>
				<th>Drg. No.</th><td> : <?=$jobData->drawing_no?>, <?=$jobData->rev_no?></td>
			</tr>
			<tr>
				<th>Product Name</th><td> : <?=$jobData->item_name?></td>
				<th>Created By</th><td> : <?=$userDetail->emp_name?></td>
			</tr>
		</table>
		<h4 class="row-title">Material Detail:</h4>
		<table class="table itemList pad5 tbl-fs-11">
			<tr class="thead-gray">
				<th>Item Description</th>
				<th class="text-center" style="width:15%;">Issued Qty</th>
				<th class="text-center" style="width:12%;">UOM</th>
			</tr>
			<?php
				if(!empty($materialDetail)):
					foreach($materialDetail as $row):
						echo '<tr>';
							echo '<td>'.$row->item_name.'</td>';
							echo '<td class="text-center">'.floatVal($row->issue_qty).'</td>';
							echo '<td class="text-center">'.$row->unit_name.'</td>';
						echo '</tr>';
					endforeach;
				else:
					echo '<tr><th class="text-center" colspan="3">Record Not Found !</th></tr>';
				endif;
			?>
		</table>
		<h4 class="row-title">Inspection Detail:</h4>
		<table class="table itemList pad5 tbl-fs-11">
			<tr class="text-center thead-gray">
				<th style="width:5%;">No.</th>
				<th class="text-left">Process Detail</th>
				<th style="width:12%;">Issued Qty</th>
				<th style="width:12%;">OK Qty</th>
				<th style="width:12%;">R/W Qty</th>
				<th style="width:12%;">Rej. Qty</th>
				<th style="width:12%;">Pending Qty</th>
			</tr>
			<?php
				if(!empty($processDetail)):
					$i=1;
					foreach($processDetail as $row):
						echo '<tr>';
							echo '<td class="text-center">'.$i++.'</td>';
							echo '<td>'.$row->process_name.'</td>';
							echo '<td class="text-right">'.floatVal($row->in_qty).'</td>';
							echo '<td class="text-right">'.floatVal($row->total_ok_qty).'</td>';
							echo '<td class="text-right">'.floatVal($row->rework_qty).'</td>';
							echo '<td class="text-right">'.floatVal($row->rejection_qty).'</td>';
							echo '<td class="text-right">'.floatVal($row->in_qty-$row->total_ok_qty-$row->rework_qty-$row->rejection_qty).'</td>';
						echo '</tr>';
					endforeach;
				else:
					echo '<tr><th class="text-center" colspan="6">Record Not Found !</th></tr>';
				endif;
			?>
			
		</table>
		
		<!-- Inhouse Production Data -->
		<?php if(!empty($inhouseProduction['printTable'])): ?>
		<h4 class="row-title">Inhouse Production Detail :</h4>
		<table class="table itemList pad5 tbl-fs-11">
			<tr class="text-center thead-gray">
				<th style="width:5%;">No.</th>
				<th>Process</th>
				<th style="width:15%;">Date</th>
				<th>Operator</th>
				<!-- <th>Shift</th> -->
				<th>Machine</th>
				<th>OK Qty.</th>
				<th>Rej. Qty.</th>
				<th>R/W Qty.</th>
				<!-- <th>Hours</th>
				<th>Remark</th> -->
			</tr>
			<?=$inhouseProduction['printTable']?>
		</table>
		<?php endif; ?>
		
		<!-- Vendor Production Data -->
		<?php if(!empty($vendorProduction['printTable'])): ?>
		<h4 class="row-title">Vendor Production Detail :</h4>
		<table class="table itemList pad5 tbl-fs-11">
			<tr class="text-center thead-gray">
				<th style="width:5%;">No.</th>
				<th>Process</th>
				<th style="width:15%;">Date</th>
				<th>Vendor</th>
				<th>OK Qty.</th>
				<th>Rej. Qty.</th>
				<th>R/W Qty.</th>
				<!-- <th>Remark</th> -->
			</tr>
			<?=$vendorProduction['printTable']?>
		</table>
		<?php endif; ?>
	</div>
</div>