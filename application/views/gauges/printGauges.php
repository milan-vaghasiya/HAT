<div class="row">
	<div class="col-md-12">
		<table class="table item-list-bb" style="margin-top:2px;">
			<tr>
				<th style="width:12%; text-align:left;">Location : </th><td style="width:30%;text-align:left;"><?=$insData->location?></td>
				<th style="width:12%; text-align:left;">Master Inst. : </th><td style="width:28%;text-align:left;"><?= (!empty($itmData->item_code) ? '['.$itmData->item_code.']'.$itmData->item_name : '') ?></td>
			</tr>
			<tr>
				<th style="height:10% text-align:left;">Gauge Description:</th><td style="height:10%;text-align:left;"><?=$insData->category_name?></td>
				<th style="text-align:left;">Master Inst.(Least Count) : </th><td style="text-align:left;"><?= (!empty($itmData->least_count) ? $itmData->least_count : '')?></td>
			</tr>
			<tr>
				<th style="text-align:left;">Ident Code:</th><td style="text-align:left;"><?=$insData->item_code?></td>
				<th style="text-align:left;">Master Inst. Cal. Date:</th><td style="text-align:left;"><?= (!empty($itmData->last_cal_date) ? formatDate($itmData->last_cal_date) : '') ?></td>
			</tr>
			<tr>
				<th style="text-align:left;">Frequency:</th><td style="text-align:left;"><?=$insData->cal_freq?></td>
				<th style="text-align:left;">Master Inst. Cal. Due Date:</th><td style="text-align:left;"><?= (!empty($itmData->next_cal_date) ? formatDate($itmData->next_cal_date) : '')?></td>
			</tr>
			<tr>
				<th style="text-align:left;">Make:</th><td colspan="3" style="text-align:left;"><?=$insData->make_brand?></td>
			</tr>
		</table>
		<table class="table item-list-bb" style="margin-top:2px;">
			<tr>
				<th colspan="4">Specification</th>
			</tr>
			<tr>
				<th style="width:25%; text-align:left;">Size(Go): </th><td style="width:25%; text-align:left;"><?=$insData->drawing_no?></td>
				<th style="width:25%; text-align:left;">Acceptance Criteria(Go): </th><td style="width:25%; text-align:left;"><?=$insData->drawing_file?></td>
			</tr>
			<tr>
				<th style="width:25%; text-align:left;">Size(No Go) : </th><td style="width:25%; text-align:left;"><?=$insData->part_no?> </td>
				<th style="width:25%; text-align:left;">Acceptance Criteria(No Go): </th><td style="width:25%; text-align:left;"><?=$insData->rev_no?></td>
			</tr>	
		</table>
		
		
		<table class="table item-list-bb" style="margin-top:10px;">
			<tr>
				<th style="width:100px;" rowspan="2">Cali. Date</th>
				<th style="width:40px;" rowspan="2"></th>
				<th colspan="2">Actual Size</th>
				<th rowspan="2">Remark</th>
				<th style="width:100px;" rowspan="2">Cali. Due Date</th>
				<th style="width:90px;" rowspan="2">Name & Sign</th>
			</tr>
			<tr>
				<th>GO</th>
				<th>NOGO</th>
			</tr>
			<?php
				$i=1;
                if(!empty($calData)):
					foreach($calData as $row): $i++;
						echo '<tr class="text-center">';
							echo '<td style="width:100px;" rowspan="2">'.formatDate($row->cal_date).'</td>';
							echo '<td style="width:40px;">DURING CAL.</td>';
							echo '<td>'.$row->during_go.'</td>';
							echo '<td>'.$row->during_nogo.'</td>';
							echo '<td rowspan="2">'.$row->during_remark.'</td>';
							echo '<td style="width:100px;" rowspan="2">'.date('d-m-Y',strtotime($row->cal_date.' +'.$insData->cal_freq.' Months')).'</td>';
							echo '<td rowspan="2">'.$row->emp_name.'</td>';
						echo '</tr>';
						echo '<tr class="text-center">
						<td style="width:40px;">AFTER CAL./REPAIR</td>
						<td>'.$row->after_go.'</td>
						<td>'.$row->after_nogo.'</td>
						</tr>';
					endforeach;
				endif;
			?>
		</table>
	</div>
</div>