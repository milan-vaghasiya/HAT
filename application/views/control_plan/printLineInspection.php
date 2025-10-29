
<div class="row">
	<div class="col-12">
		<table class="table top-table-border" style="margin-top:2px;">
			<!-- <tr>
				<td style="width:60%;vertical-align:top;">
					<b>Date : </b> <?=(!empty($lineInspectData->insp_date)) ? formatDate($lineInspectData->insp_date) : ""?> <br><br>
					<b>Job Card : </b> <?=(!empty($lineInspectData->job_number)) ? $lineInspectData->job_number:""?><br><br>
					<b>Part Code : </b> <?=(!empty($lineInspectData->item_code)) ?$lineInspectData->item_code:""?>
				</td>
				<td style="width:40%;vertical-align:top;">
					<b>Process : </b> <?=(!empty($lineInspectData->process_name)) ?$lineInspectData->process_name:""?> <br><br>
					<b>Inspector : </b> <?=(!empty($lineInspectData->emp_name)) ?$lineInspectData->emp_name:""?><br><br>
					<b>Drawing No : </b> <?=(!empty($lineInspectData->drawing_no)) ?$lineInspectData->drawing_no:""?><br><br>
				</td>
			</tr> -->
			<tr>
				<td><b>M/C No. :</b></td>
				<td><b>Date : </b><?=(!empty($lineInspectData->insp_date)) ? formatDate($lineInspectData->insp_date) : ""?></td>
				<td><b>Shift :</b></td>
			</tr>
			<tr>
				<td><b>Item : </b><?=(!empty($lineInspectData->item_name)) ? $lineInspectData->item_name : ""?></td>
				<td><b>Operator : </b><?=(!empty($lineInspectData->emp_name)) ? $lineInspectData->emp_name : ""?></td>
				<td><b>Setup:- </b><?=(!empty($lineInspectData->process_name)) ?$lineInspectData->process_name:""?></td>
			</tr>
			<tr>
				<td colspan="2"><b>Customer Name:- </b><?=(!empty($lineInspectData->party_name)) ? $lineInspectData->party_name : ""?></td>
				<td><b>Drawing No:- </b><?=(!empty($lineInspectData->drawing_no)) ? $lineInspectData->drawing_no : ""?></td>
			</tr>
		</table>
		
		<table class="table item-list-bb" style="margin-top:2px;">
			<tr style="text-align:center;">
				<th rowspan="2" style="width:5%;">#</th>
				<th rowspan="2">Parameter</th>
				<th rowspan="2">Specification</th>
				<th rowspan="2">Min. Value</th>
				<th rowspan="2">Max. Value</th>
				<th rowspan="2">Instrument</th>
				<th colspan="<?= $rcount ?>">Observation Samples</th>
			</tr>
			<tr>
				<?php echo $theadData; ?>
			</tr>
			<?php echo $tbodyData; ?>
		</table>
		<table class="table item-list-bb">
		<tr class="text-center">
				<td><b>SM</b></td>
				<td><b>ST</b></td>
			</tr>
			<tr class="text-center">
				<td><b>SW</b></td>
				<td><b>SB</b></td>
			</tr>
			<tr class="text-center">
				<td><b>NS</b></td>
				<td><b>SV</b></td>
			</tr>
			<tr class="text-center">
				<td><b>SR</b></td>
				<td><b>SJF</b></td>
			</tr>
			<tr class="text-center">
				<td><b>SN</b></td>
				<td><b>SEP</b></td>
			</tr>
			<tr class="text-center">
				<td><b>SRW</b></td>
				<td><b>SPM</b></td>
			</tr>
			<tr>
				<td colspan="2"><b>Checked By:-</b></td>
			</tr>
		</table>
	</div>
</div>