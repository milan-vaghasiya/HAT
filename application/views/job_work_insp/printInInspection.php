
<div class="row">
	<div class="col-12">
		<!-- <table class="table"><tr><td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">INCOMING INSPECTION REPORT</td></tr></table> -->
		<table style="margin-bottom:5px;">
									<tr>
										<th style="width:35%;letter-spacing:2px;" class="text-left fs-17" ></th>
										<th style="width:30%;letter-spacing:2px;" class="text-center fs-17">INCOMING INSPECTION REPORT</th>
										<th style="width:35%;" class="text-right fs-15">F QA 02 01(01/08/2021)</th>
									</tr>
								</table>
		<table class="table top-table-border" style="margin-top:2px;">
			<tr>
				<td style="width:60%;vertical-align:top;">
					<b>Vendor Name : <?=(!empty($inInspectData->party_name)) ?$inInspectData->party_name:""?></b> <br><br>
					<b>Part No. :</b> <?=(!empty($inInspectData->item_code)) ?$inInspectData->item_code:""?> <br><br>
					<b>Challan No./J.W.O. No. :</b> <?=(!empty($inInspectData->challan_no)) ?$inInspectData->challan_no:""?><?php (!empty($inInspectData->charge_no)) ?'/'.$inInspectData->charge_no:""?> <br><br>
					<b>Batch Code/Heat Code :</b> <?=(!empty($inInspectData->batch_no)) ?$inInspectData->batch_no:""?><br>
				</td>
				<td style="width:40%;vertical-align:top;">
					<b>Date :</b> <?=(!empty($inInspectData->entry_date)) ? formatDate($inInspectData->entry_date) : ""?> <br><br>
					<b>Process :</b> <?=(!empty($inInspectData->process_name)) ?$inInspectData->process_name:""?> <br><br>
					<b>Recive Qty.:</b> <?=(!empty($inInspectData->in_qty)) ?$inInspectData->in_qty:""?> <br><br>
					<b>Sampling Qty.:</b> <?=(!empty($inInspectData->sampling_qty))?$inInspectData->sampling_qty:""?><br>
				</td>
			</tr>
		</table>
		
		<table class="table item-list-bb" style="margin-top:10px;">
			<tr style="text-align:center;">
				<th rowspan="2" style="width:5%;">#</th>
				<th rowspan="2">Para <br> meter</th>
				<th rowspan="2">Specifi <br> cation</th>
				<th rowspan="2">Tolerance</th>
				<th rowspan="2">Psc/Sp. <br> Characteristics</th>
				<th rowspan="2">Instrument <br> Used</th>
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
							$obj = json_decode($inInspectData->observation_sample);
						endif;
						$paramItems = '';$flag=false;
							$paramItems.= '<tr>
										<td style="text-align:center;">'.$i.'</td>
										<td style="text-align:center;">'.$row->parameter.'</td>
										<td style="text-align:center;">'.$row->specification.'</td>
										<td style="text-align:center;">'.$row->lower_limit.'</td>
										<td style="text-align:center;">'.$row->upper_limit.'</td>
										<td style="text-align:center;">'.$row->measure_tech.'</td>';
							for($c=0;$c<10;$c++):
								if(!empty($obj->{$row->id})):
									$paramItems .= '<td style="text-align:center;">'.$obj->{$row->id}[$c].'</td>';
								endif;
								if(!empty($obj->{$row->id}[$c])){$flag=true;}
							endfor;
							if(!empty($obj->{$row->id})):
								$paramItems .= '<td style="text-align:center;">'.$obj->{$row->id}[10].'</td></tr>';
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