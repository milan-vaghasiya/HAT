<style>table, .table{width:100% !important;border-collapse:collapse !important;}td,th{border-collapse:collapse !important;}</style>
<div class="row">
	<div class="col-12">
		<table class="table" style="border-bottom:1px solid #000000;">
			<tr>
			    <td style="width:20%;"></td>
				<td class="text-uppercase text-center" style="font-size:1.3rem;font-weight:bold;width:40%;">PROCESS ROUTE CARD</td>
				<td style="width:20%;"></td>
			</tr>
		</table>
		<table class="table itemList DD tbl-fs-11">
		    <tr>
				<th style="width:12%">Cust.Code:</th>
				<td> <?= $jobData->party_code ?></td>
				<th style="width:12%">P.O. No.</th>
				<td> <?= $jobData->doc_no ?></td>
				<th style="width:12%">P.O. Date</th>
				<td> <?= (!empty($jobData->doc_date))?formatDate($jobData->doc_date):''; ?></td>
			</tr>
			<tr>
				<th>Product No.</th>
				<td> <?= $jobData->product_code ?></td>
				<th>Part Name</th>
				<td> <?= $jobData->full_name ?></td>
				<th>Cust.Part No.</th>
				<td> <?= $jobData->part_no ?></td>
			</tr>
			<tr>
				<th>Drg.No:</th>
				<td> <?= $jobData->drawing_no ?></td>
				<th>Drg.Rev.No:</th>
				<td> <?= $jobData->rev_no ?></td>
				<th>Del.Date:</th>
				<td> <?= (!empty($jobData->del_date))?formatDate($jobData->del_date):''; ?></td>
			</tr>
			<tr>
				<th>F.M.Size:</th>
				<td> <?= $jobData->part_size ?></td>
				<th>O.A.No.</th>
				<td> <?= (!empty($jobData->trans_prefix))?getPrefixNumber($jobData->trans_prefix, $jobData->trans_no):''; ?></td>
				<th>O.A.Qty.</th>
				<td> <?= $jobData->so_qty ?></td>
			</tr>
			<tr>
				<th style="width:12%">Job Card No.</th>
				<td> <?= $jobData->job_number ?></td>
				<th style="width:12%">Job Quantity</th>
				<td> <?= floatval($jobData->qty) ?></td>
				<th style="width:12%">Job Date</th>
				<td> <?= formatDate($jobData->job_date) ?></td>
			</tr>
            <tr>
				<th style="width:12%">Remark</th>
				<td colspan="5">: <?= $jobData->remark ?></td>
			</tr>
		</table>
		<h4 class="row-title">Material Detail:</h4>
		<table class="table itemList DD tbl-fs-11">
			<tr class="thead-gray">
				<th>Item Description</th>
				<th class="text-center">Batch No</th>
				<th class="text-center" style="width:15%;">Issued Qty</th>
			</tr>
			<?php
			if (!empty($materialDetail)) :
				$i=0;
				foreach ($materialDetail as $row) :
					
					echo '<tr>';
					echo '<td>['.$row->rm_item_code.'] ' . $row->item_full_name . '</td>';
					echo '<td class="text-center">' . $row->batch_no . '</td>';
					echo '<td class="text-center">' . floatVal($row->issue_qty) . '</td>';
					echo '</tr>';
					$i++;
				endforeach;
			else :
				echo '<tr><th class="text-center" colspan="3">Record Not Found !</th></tr>';
			endif;
			?>
		</table>
		<h4 class="row-title">Process Detail:</h4>
		<table class="table itemList pad5 tbl-fs-11">
			<tr class="text-center thead-gray">
				<th style="width:5%;">No.</th>
				<th class="text-left">Process Detail</th>
				<th style="width:12%;">Issued Qty</th>
				<th style="width:12%;">OK Qty</th>
				<th style="width:12%;">Rej. Qty</th>
				<th style="width:12%;">Pending Qty</th>
			</tr>
			<?php
			if (!empty($processDetail)) :
				$i = 1;
				foreach ($processDetail as $row) :
					echo '<tr>';
					echo '<td class="text-center">' . $i++ . '</td>';
					echo '<td class="text-left">' . $row->process_name . '</td>';
					echo '<td class="text-center">' . floatVal($row->in_qty) . '</td>';
					echo '<td class="text-center">' . floatVal($row->total_ok_qty) . '</td>';
					echo '<td class="text-center">' . floatVal($row->rejection_qty) . '</td>';
					echo '<td class="text-center">' . floatVal($row->in_qty - $row->total_ok_qty - $row->rejection_qty) . '</td>';
					echo '</tr>';
				endforeach;
			else :
				echo '<tr><th class="text-center" colspan="6">Record Not Found !</th></tr>';
			endif;
			?>

		</table>

		<!-- Inhouse Production Data -->
		<?php if (!empty($inhouseProduction)) { ?>
			<h4 class="row-title">Inhouse Production Detail :</h4>
			<table class="table itemList pad5 tbl-fs-11">
				<tr class="text-center thead-gray">
					<th style="width:5%;">#</th>
					<th>Date</th>
					<th>Process</th>
					<th>Send To</th>
					<th>Production Time</th>
					<th>Machine</th>
					<th>Operator</th>
					<th>Shift</th>
					<th>Out Qty.</th>
					<th>Rejection Qty.</th>
					<th>Rework Qty.</th>
					<th>Hold Qty.</th>
					<th>Remark</th>
				</tr>
				<?php
				$i = 1;
				foreach ($inhouseProduction as $row) { ?>
					<tr>
						<td style="widtd:5%;"><?=$i++?></td>
						<td><?=formatDate($row->entry_date)?></td>
						<td><?=(!empty($row->process_name))?$row->process_name:'Raw Material';?></td>
						<td><?=$row->vendor_name?></td>
						<td><?=$row->production_time?></td>
						<td><?=(!empty($row->machine_code) ? '[' . $row->machine_code . '] ' : "") . $row->machine_name?></td>
						<td><?=$row->emp_name?></td>
						<td><?=$row->shift_name?></td>
						<td><?=$row->qty?></td>
						<td><?=$row->rej_qty?></td>
						<td><?=$row->rw_qty?></td>
						<td><?=$row->hold_qty?></td>
						<td><?=$row->remark?></td>
					</tr>
				<?php } ?>
			</table>
		<?php } ?>

		<!-- Vendor Production Data -->
		<?php if (!empty($vendorProduction)) { ?>
			<h4 class="row-title">Vendor Production Detail :</h4>
			<table class="table itemList pad5 tbl-fs-11">
				<tr class="text-center thead-gray">
					<th style="width:5%;">#</th>
					<th>Date</th>
					<th>Process</th>
					<th>Vendor</th>
					<th>Send To</th>
					<th>Production Time</th>
					<th>Machine</th>
					<th>Operator</th>
					<th>Shift</th>
					<th>Out Qty.</th>
					<th>Rejection Qty.</th>
					<th>Rework Qty.</th>
					<th>Hold Qty.</th>
					<th>Remark</th>
				</tr>
				<?php
				if (!empty($vendorProduction)) {
					foreach ($vendorProduction as $row) { ?>
						<tr>
							<td style="widtd:5%;"><?=$i++?></td>
							<td><?=formatDate($row->entry_date)?></td>
							<td><?=$row->process_name?></td>
							<td><?=$row->party_name?></td>
							<td><?=$row->vendor_name?></td>
							<td><?=$row->production_time?></td>
							<td><?=(!empty($row->machine_code) ? '[' . $row->machine_code . '] ' : "") . $row->machine_name?></td>
							<td><?=$row->emp_name?></td>
							<td><?=$row->shift_name?></td>
							<td><?=$row->qty?></td>
							<td><?=$row->rej_qty?></td>
							<td><?=$row->rw_qty?></td>
							<td><?=$row->hold_qty?></td>
							<td><?=$row->remark?></td>
						</tr>
					<?php } }?>
			
			</table>
		<?php } ?>
	</div>
</div>