
<div class="row">
	<div class="col-12">
		<table style="margin-bottom:5px;">
			<tr>
				<th style="width:30%;letter-spacing:2px;" class="text-left fs-17" ></th>
				<th style="width:40%;letter-spacing:2px;" class="text-center fs-17">PREDISPATCH INSPECTION REPORT</th>
				<th style="width:30%;" class="text-right fs-15">F QA 05 01(01/06/2020)</th>
			</tr>
		</table>
		<table class="table top-table-border" style="margin-top:2px;">
			<tr>
				<td style="width:60%;vertical-align:top;">
					<b>Customer Name : <?=(!empty($inInspectData->party_name)) ?$inInspectData->party_name:""?></b> <br>
					<b>Cust. Part No.:</b> <?=(!empty($inInspectData->part_no)) ?$inInspectData->part_no:""?> <br>
					<b>Part No.:</b> <?=(!empty($inInspectData->item_code)) ?$inInspectData->item_code:""?><?php (!empty($inInspectData->charge_no)) ?'/'.$inInspectData->charge_no:""?> <br>
					<b>Item Description:</b> <?=(!empty($inInspectData->item_name)) ?$inInspectData->item_name:""?> <br>
					<b>Batch Code :</b> <?=(!empty($inInspectData->batch_no)) ?$inInspectData->batch_no:""?>
				</td>
				<td style="width:40%;vertical-align:top;">
					<b>Date :</b> <?=(!empty($inInspectData->date)) ? formatDate($inInspectData->date) : ""?> <br>
					<b>Invoice No:</b> <?=(!empty($inInspectData->inv_no)) ?$inInspectData->inv_no:""?> <br>
					<b>Invoice Date.:</b> <?=(!empty($inInspectData->inv_date)) ? formatDate($inInspectData->inv_date):""?><br>
					<b>Dispatch Qty.:</b> <?=(!empty($inInspectData->dispatch_qty)) ?$inInspectData->dispatch_qty:""?>
				</td>
			</tr>
		</table>
		
		<table class="table item-list-bb" style="margin-top:10px;">
			<tr style="text-align:center;">
				<th rowspan="2" style="width:5%;">#</th>
				<th rowspan="2">Parameter</th>
				<th rowspan="2">Specification</th>
				<th rowspan="2">Lower <br> Limit</th>
				<th rowspan="2">Upper <br> Limit</th>
				<th rowspan="2">Instrument Used</th>
				<th colspan="10">Observation on Samples</th>
				<th rowspan="2">Result</th>
			</tr>
			<tr style="text-align:center;">
				<th style="width: 8%;">1</th>
				<th style="width: 8%;">2</th>
				<th style="width: 8%;">3</th>
				<th style="width: 8%;">4</th>
				<th style="width: 8%;">5</th>
				<th style="width: 8%;">6</th>
				<th style="width: 8%;">7</th>
				<th style="width: 8%;">8</th>
				<th style="width: 8%;">9</th>
				<th style="width: 8%;">10</th>
			</tr>
			<?php
				$tbodyData="";$i=1; 
				if(!empty($paramData)):
					foreach($paramData as $row):
						$obj = New StdClass;
						if(!empty($inInspectData)):
							$obj = json_decode($inInspectData->observe_samples);
						endif;
						$paramItems = '';$flag=false;
							$row->measure_tech = (!empty($row->item_code)) ? '['.$row->item_code.'] '.$row->item_name : $row->item_name;                     
							$paramItems.= '<tr>
										<td style="text-align:center;">'.$i.'</td>
										<td style="text-align:center;">'.$row->parameter.'</td>
										<td style="text-align:center;">'.$row->specification.'</td>
										<td style="text-align:center;">'.$row->lower_limit.'</td>
										<td style="text-align:center;">'.$row->upper_limit.'</td>
										<td style="text-align:center;">'.$row->measure_tech.'</td>';
							for($c=0;$c<10;$c++):
								if(!empty($obj->{$row->id})):
									$paramItems .= '<td style="text-align:center;">'.((!empty($obj->{$row->id}[$c])) ? $obj->{$row->id}[$c] : '').'</td>';
								endif;
								if(!empty($obj->{$row->id}[$c])){$flag=true;}
							endfor;
							if(!empty($obj->{$row->id})):
								$paramItems .= '<td style="text-align:center;">'.((!empty($obj->{$row->id}[10])) ? $obj->{$row->id}[10] : '').'</td></tr>';
							endif;
							
							if($flag):
								$tbodyData .= $paramItems;$i++;
							endif;
					endforeach;
				else:
					$tbodyData.= '<tr><td colspan="17" style="text-align:center;">No Data Found</td></tr>';
				endif;
				echo $tbodyData;
			?>
		</table>
	</div>
</div>