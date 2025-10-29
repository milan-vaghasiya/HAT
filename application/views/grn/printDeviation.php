
<div class="row">
	<div class="col-12">
		<table class="table top-table-border" style="margin-top:2px;">
			<tr>
				<td style="width:60%;vertical-align:top;">
					<b>Suplier Name : <?=(!empty($deviationData->party_name)) ? $deviationData->party_name:""?></b> <br><br>
					<b>Part No : </b> <?=(!empty($deviationData->fgCode)) ?$deviationData->fgCode:""?>
				</td>
				<td style="width:40%;vertical-align:top;">
					<b>Date : </b> <?=(!empty($deviationData->grn_date)) ? formatDate($deviationData->grn_date) : ""?> <br><br>
					<b>Stage : </b>	QC
				</td>
			</tr>
		</table>
		
		<table class="table item-list-bb" style="margin-top:4px;">
			<tr style="text-align:center;">
				<th style="width:5%;">#</th>
				<th >Parameter</th>
				<th>Specification</th>
				<th>Observation</th>
				<th>Qty</th>
				<th>Deviation</th>
			</tr>
			<?php
			if(!empty($devArray)){
				$i=1;
				foreach($devArray as $row){
					?>
					<tr>
						<td style="text-align:center;" height="30"><?=$i++?></td>
						<td style="text-align:center;"><?=$row->parameter?></td>
						<td style="text-align:center;"><?=$row->specification?></td>
						<td style="text-align:center;"><?=$row->observation_?></td>
						<td style="text-align:center;"><?=$row->qty_?></td>
						<td style="text-align:center;"><?=$row->deviation_?></td>
					</tr>
					<?php
				}
			}
			?>
		</table>
		<table class="table top-table-border" style="margin-top:5px;">
			<tr>
				<td height="50" style="text-align:left"><b>Comments of Production Incharge :</b><br><?=(!empty($deviationData->production_incharge)) ? $deviationData->production_incharge:""?></td>
				<td><b> Date :</b><?=(!empty($deviationData->production_date)) ? formatdate($deviationData->production_date):""?></td>
			</tr>
			<tr>
				<td height="50" style="text-align:left"><b>Comments of  QA Incharge:</b><br><?=(!empty($deviationData->qa_incharge)) ? $deviationData->qa_incharge:""?></td>
				<td><b> Date :</b><?=(!empty($deviationData->qa_date)) ? formatdate($deviationData->qa_date):""?></td>
			</tr>
			<tr>
				<td height="50" style="text-align:left"><b>Comments of  Sales Incharge:</b><br><?=(!empty($deviationData->sales_incharge)) ? $deviationData->sales_incharge:""?></td>
				<td><b> Date :</b><?=(!empty($deviationData->sales_date)) ? formatdate($deviationData->sales_date):""?></td>
			</tr>
			<tr>
				<td height="50" style="text-align:left"><b>Approval of MD:</b><br><?=(!empty($deviationData->approval_md)) ? $deviationData->approval_md:""?></td>
				<td><b> Date :</b><?=(!empty($deviationData->approval_date)) ? formatdate($deviationData->approval_date):""?></td>
			</tr>
			<tr>
				<td height="50" style="text-align:left"><b>Approval of Customer (If Required ):</b><br><?=(!empty($deviationData->approval_customer)) ? $deviationData->approval_customer:""?></td>
				<td><b> Date :</b><?=(!empty($deviationData->approval_cust_date)) ? formatdate($deviationData->approval_cust_date):""?></td>
			</tr>
			
		</table>
	</div>
</div>