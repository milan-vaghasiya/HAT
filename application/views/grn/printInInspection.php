
<div class="row">
	<div class="col-12">
		<table class="table top-table-border" style="margin-top:2px;">
			<tr>
				<td style="width:60%;vertical-align:top;">
					<b>Suplier Name : <?=(!empty($inInspectData->party_name)) ? $inInspectData->party_name:""?></b> <br><br>
					<b>Part Name :</b> <?=(!empty($inInspectData->item_name)) ? $inInspectData->item_name:""?> <br><br>
					<b>Part No.:</b> <?=(!empty($inInspectData->fgCode)) ?$inInspectData->fgCode:""?><?php (!empty($inInspectData->charge_no)) ?'/'.$inInspectData->charge_no:""?> <br><br>
					<b>Material Grade :</b> <?=(!empty($inInspectData->material_grade)) ? $inInspectData->material_grade:""?><br>
				</td>
				<td style="width:40%;vertical-align:top;">
					<b>Receive Date :</b> <?=(!empty($inInspectData->grn_date)) ? formatDate($inInspectData->grn_date) : ""?> <br><br>
					<b>Lot Qty.:</b> <?=(!empty($inInspectData->qty)) ? $inInspectData->qty:""?> <br><br>
					<b>Batch No.:</b> <?=(!empty($inInspectData->batch_no)) ? $inInspectData->batch_no:""?> <br><br>
					<b>Color Code:</b> <?=(!empty($inInspectData->color_code)) ? $inInspectData->color_code:""?><br>
				</td>
			</tr>
		</table>

		<table class="table item-list-bb" style="margin-top:10px;">
			<tr style="text-align:center;">
				<th rowspan="2" style="width:5%;">#</th>
				<th rowspan="2">date</th>
				<th rowspan="2">Ch. No.</th>
				<th rowspan="2">Item Description</th>
				<th rowspan="2">Lot Qty.</th>
				<th rowspan="2">Parameter</th>
				<th rowspan="2">Specifiation & Tolerance</th>
				<th colspan="5">Observation on Samples</th>
				<th rowspan="2">Ok Qty.</th>
				<th rowspan="2">Accepted UD</th>
				<th rowspan="2">Rej Qty.</th>
				<th rowspan="2">Remarks</th>
				<th rowspan="2">Sign.</th>
			</tr>
			<tr style="text-align:center;">
				<th style="width: 8%;">1</th>
				<th style="width: 8%;">2</th>
				<th style="width: 8%;">3</th>
				<th style="width: 8%;">4</th>
				<th style="width: 8%;">5</th>
			</tr>
			<?php
				$tbodyData="";$i=1; 
				if(!empty($paramData)):
					foreach($paramData as $row):
							$obj = New StdClass;
							if(!empty($inInspectData)):
								$obj = json_decode($inInspectData->observation_sample);
							endif;
							$paramItems = '';$flag=false;
								$paramItems.= '<tr>
											<td style="text-align:center;">'.$i.'</td>
											<td style="text-align:center;">'.formatDate($inInspectData->grn_date).'</td>
											<td style="text-align:center;">'.$inInspectData->challan_no.'</td>
											<td style="text-align:center;">'.$row->item_name.'</td>
											<td style="text-align:center;">'.$inInspectData->qty.'</td>
											<td style="text-align:center;">'.$row->parameter.'</td>
											<td style="text-align:center;">'.$row->specification.'</td>';
								for($c=0;$c<5;$c++):
									if(!empty($obj->{$row->id})):
										$paramItems .= '<td style="text-align:center;">'.$obj->{$row->id}[$c].'</td>';
									endif;
									if(!empty($obj->{$row->id}[$c])){$flag=true;}
								endfor;
								if(!empty($obj->{$row->id})):
									$paramItems .= '<td style="text-align:center;">'.$inInspectData->qty.'</td>
													<td style="text-align:center;"></td>
													<td style="text-align:center;"></td>
													<td style="text-align:center;">'.$row->remark.'</td>
													<td style="text-align:center;"></td>
											</tr>';
								endif;
								
								if($flag):
									$tbodyData .= $paramItems;$i++;
								endif;
							
					endforeach;
				else:
					$tbodyData.= '<tr><td colspan="12" style="text-align:center;">No Data Found</td></tr>';
				endif;
				echo $tbodyData;
			?>
		</table>
		
		<table class="table top-table" style="margin-top: 50px;">
			<tr>
				<th style="width:8%">Note :</th>
				<td style="text-align:left">
				<?=(!empty($inInspectData->approval_remarks)) ? $inInspectData->approval_remarks:""?>
				</td>
			</tr>
		</table>
	</div>
</div>