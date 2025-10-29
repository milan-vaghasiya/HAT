
<div class="row">
	<div class="col-12">
		<table class="table top-table-border">
			<tr>
				<td><b>Customer:</b></td>
                <td><?=(!empty($dataRow->party_name)) ? '['.$dataRow->party_code.'] '.$dataRow->party_name : ""?></td>

                <td><b>Inspection Report No.</b></td>
                <td><?=(!empty($dataRow->report_number)) ? $dataRow->report_number : ""?></td>
			</tr>
            <tr>
				<td><b>Part :</b></td>
                <td><?=(!empty($dataRow->item_name)) ? '['.$dataRow->item_code.'] '.$dataRow->item_name : ""?></td>

                <td><b>Date:</b></td>
                <td><?=(!empty($dataRow->date)) ? formatDate($dataRow->date) : ""?></td>
			</tr>
            <tr>
				<td><b>Drg. No.:</b></td>
                <td><?=(!empty($dataRow->drg_no)) ? $dataRow->drg_no : ""?></td>

                <td><b>Material:</b></td>
                <td><?=(!empty($dataRow->material_grade)) ? $dataRow->material_grade : ""?></td>
			</tr>
            <tr>
				<td><b>P.O. No.:</b></td>
                <td><?=(!empty($dataRow->doc_no)) ? $dataRow->doc_no : ""?></td>

                <td><b>Qty.:</b></td>
                <td><?=(!empty($dataRow->dispatch_qty)) ? $dataRow->dispatch_qty : ""?></td>
			</tr>
            <tr>
				<td><b>Batch No.</b></td>
                <td colspan="3"><?=(!empty($dataRow->job_number)) ? $dataRow->job_number : ""?></td>
			</tr>
		</table>
		
		<table class="table top-table-border" style="margin-top:10px;">
			<tr style="text-align:center;">
				<th rowspan="2" style="width:7%;">Sr. No.</th>
				<th style="width:15%;">Description</th>
				<th rowspan="2">Tol.</th>
				<th rowspan="2">Min. Value</th>
				<th rowspan="2">Max. Value</th>
				<th rowspan="2">Instrument</th>
				<th colspan="5">Sample</th>
				<th rowspan="2">Remarks</th>
			</tr>
			<tr style="text-align:center;">
				<th style="width: 8%;">Drg. Dimension</th>
				<th style="width: 8%;">1</th>
				<th style="width: 8%;">2</th>
				<th style="width: 8%;">3</th>
				<th style="width: 8%;">4</th>
				<th style="width: 8%;">5</th>
			</tr>
			<?php
				$tbodyData=""; $i=1; $blankRows = '';
				if(!empty($paramData)):
					foreach($paramData as $row):
						$obj = New StdClass;
						if(!empty($dataRow)):
							$obj = json_decode($dataRow->observe_samples);
						endif;
						$paramItems = '';$flag=false;
							$paramItems.= '<tr>
										<td style="text-align:center;">'.$i.'</td>
										<td style="text-align:center;">'.$row->drg_diameter.'</td>
										<td style="text-align:center;">'.$row->specification.'</td>
										<td style="text-align:center;">'.$row->min_value.'</td>
										<td style="text-align:center;">'.$row->max_value.'</td>
										<td style="text-align:center;">'.$row->inst_used.'</td>';
							for($c=0;$c<5;$c++):
								if(!empty($obj->{$row->id})):
									$paramItems .= '<td style="text-align:center;">'.((!empty($obj->{$row->id}[$c])) ? $obj->{$row->id}[$c] : '').'</td>';
								endif;
								if(!empty($obj->{$row->id}[$c])){$flag=true;}
							endfor;
							if(!empty($obj->{$row->id})):
								$paramItems .= '<td style="text-align:center;">'.((!empty($obj->{$row->id}[5])) ? $obj->{$row->id}[5] : '').'</td></tr>';
							endif;
							if($flag):
								$tbodyData .= $paramItems;$i++;
							endif;	
							
					endforeach;						
					for($j=10; $i<=$j ; $i++):
						$blankRows.= '<tr>
							<td>&nbsp;</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>';			
					endfor;		
					$tbodyData .= $blankRows;		
				endif;
				echo $tbodyData;
			?>
		</table>
	</div>
</div>