<style>table, .table{width:100% !important;border-collapse:collapse !important;}td,th{border-collapse:collapse !important;}</style>
<div class="row">
	<div class="col-12">
		<table class="table" style="border-bottom:1px solid #000000;">
			<tr>
			    <td style="width:20%;">
				<td class="text-uppercase text-center" style="font-size:1.3rem;font-weight:bold;width:40%;">PROCESS ROUTE CARD</td>
				<td style="width:20%;"></td>
			</tr>
		</table>
		<table class="table itemList DD tbl-fs-11">
		    <tr>
				<th style="width:12%">Cust.Code</th>
				<td><?= $jobData->party_code ?></td>
				<th style="width:12%">P.O. No.</th>
				<td><?= $jobData->doc_no ?></td>
				<th style="width:12%">P.O. Date</th>
				<td><?= (!empty($jobData->doc_date))?formatDate($jobData->doc_date):''; ?></td>
			</tr>
			<tr>
				<th>FG. Wtg./Pc.</th>
				<td><?= $jobData->wt_pcs ?> Kg.</td>
				<th>Cust. Part No.</th>
				<td><?= $jobData->part_no ?></td>
				<th>Del.Date</th>
				<td><?= (!empty($jobData->del_date))?formatDate($jobData->del_date):''; ?>||<?= (!empty($jobData->target_date))?formatDate($jobData->target_date):''; ?></td>
			</tr>
			
			<tr>
				<th>Drg.No</th>
				<td><?= $jobData->drawing_no ?></td>
				<th>Drg.Rev.No</th>
				<td><?= $jobData->rev_no ?></td>
				<th>RM. Wtg.</th>
				<td><?= ($jobData->wt_pcs * $jobData->qty) ?> Kg.</td>
			</tr>
			<tr>
				<th>Size</th>
				<td><?= $jobData->part_size ?></td>
				<th>O.A.No.</th>
				<td><?= (!empty($jobData->trans_prefix))?getPrefixNumber($jobData->trans_prefix, $jobData->trans_no):''; ?></td>
				<th>O.A.Qty.</th>
				<td><?= floatval($jobData->so_qty) ?></td>
			</tr>
			<tr>
				<th style="width:12%">Job Date</th>
				<td><?= formatDate($jobData->job_date) ?></td>
				<th style="width:12%">Job Card No.</th>
				<td><?= $jobData->job_number ?></td>
				<th style="width:12%">Job Quantity</th>
				<td><?= floatval($jobData->qty) ?></td>
			</tr>
			<tr>
				<th style="width:12%">Grade</th>
				<td> <?= $jobData->material_grade.(!empty($jobData->standard)?' - '.$jobData->standard : '') ?></td>
				<th style="width:12%">Heat No.</th>
				<td colspan="3"><?= $batch_no ?></td>
			</tr>
			<tr>
				<th style="width:12%">Remark</th>
				<td colspan="5"> <?= $jobData->remark ?></td>
			</tr>
		</table>
		<h4 class="row-title">Process Detail</h4>
		<table class="table itemList pad3 DD tbl-fs-11">
			<tr class="text-center thead-gray">
				<th rowspan="2" style="width:100px;">Operation</th>
				<th rowspan="2" style="width:50px;">Machine<br>No.</th>
				<th rowspan="2" style="width:50px;">Cycle<br>Time</th>
				<th rowspan="2" style="width:80px;">Start<br>Date</th>
				<th colspan="4">Quantity</th>
				<th rowspan="2" style="width:50px;">Total<br>Time</th>
				<th rowspan="2" style="width:80px;">End<br>Date</th>
				<th rowspan="2" style="width:50px;">Weight<br>After</th>
				<th rowspan="2" style="width:80px;">Operator Name</th>
			</tr>
			<tr class="text-center thead-gray">
				<th style="width:50px;">Rec'd. Qty</th>
				<th style="width:50px;">Rewok Qty</th>
				<th style="width:50px;">Rej. Qty</th>
				<th style="width:50px;">Total Qty</th>
			</tr>
			<?php
			if (!empty($processDetail)) :
				$i = 1;
				foreach ($processDetail as $row) :
					echo '<tr>';
    					echo '<td class="text-left" height="38">' . $row->process_name . '</td>';
    					echo '<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
					echo '</tr>';
				endforeach;
			else :
				echo '<tr><td class="text-left" height="38"></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
			endif;
			?>

		</table>		
	</div>
</div>