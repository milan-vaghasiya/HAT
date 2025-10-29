<div class="row">
	<div class="col-md-12">
		<table class="table item-list-bb" style="margin-top:2px;">
			<tr>
				<td class="text-left"><b>Discription</b></td><td><?=$insData->item_name?></td>
				<th class="text-left">Identification No.</th><td><?=$insData->item_code?></td>
			</tr>
			<tr>
				<th class="text-left">Make</th>
				<td class="text-left" colspan="3"><?=$insData->make_brand?></td>
			</tr>
			<tr>
				<td class="text-left"><b>Cali. Frequency</b></td><td><?=$insData->cal_freq?>(Month)</td>
				<td class="text-left"><b>Permissable Error</b></td><td><?=$insData->permissible_error?></td>
			</tr>
		</table>

		<table class="table item-list-bb" style="margin-top:10px;">
			<tr>
				<th style="width:40px;">Cali. Date</th>
				<th style="width:100px;">Cali. Agency</th>
				<th style="width:40px;">Cali. No. & Date</th>
				<th style="width:40px;">Cali. Result</th>
				<th style="width:40px;">Cali. Due Date</th>
				<th style="width:40px;">Sign</th>
			</tr>
			<?php
                if(!empty($calData)):
					foreach($calData as $row):
						echo '<tr class="text-center" height="32">';
							echo '<td>'.formatDate($row->cal_date).'</td>';
							echo '<td>'.$row->cal_agency.'</td>';
							echo '<td>'.$row->cal_certi_no.'</td>';
							echo '<td>'.$row->cal_result.'</td>';
							echo '<td>'.date('d-m-Y',strtotime($row->cal_date.' +'.$insData->cal_freq.' Months')).'</td>';
							echo '<td>'.$row->emp_name.'</td>';
						echo '</tr>';
					endforeach;
				endif;
			?>
		</table>
	</div>
</div>