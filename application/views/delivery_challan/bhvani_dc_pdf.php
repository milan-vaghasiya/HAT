<div class="row">
	<div class="col-12">
		<table class="table item-list-bb" >
			<tbody>
                <tr>
					<td colspan="4" class="text-center" style="font-size:2rem;"><b>Delivery Challan</b></td>
					<td colspan="4" class="text-right"  style="vertical-align:bottom !important;font-size:0rem;padding-top: 20px;"><b>ORIGINAL FOR CONSIGNEE/VENDOR</b></td>
				</tr>
				<tr>
					<td colspan="4" class="text-center"><b>See Rule 10 under Tax Invoice,Credit And Debit Note Rules</b></td>
					<td colspan="2" class="text-right"><b>Date:</b></td>
					<td colspan="2" class="text-left"><?=date('d/m/Y', strtotime($salesData->trans_date))?></td>
				</tr>
                <tr>
                    <td colspan="2" class="text-right"><b>JobWorker's Serial Number:</b></td>
					<td colspan="2" class="text-left"><?=$salesData->trans_no ?></td>
                    <td colspan="2" class="text-right"><b>Material Supplier's Sr.No.:</b></td>
					<td colspan="2" class="text-left"><?=$salesData->challan_no ?> </td>
				</tr>
                <tr>
					<td colspan="4" class="text-center"><b>Details of Job Work Service Provider</b></td>
					<td colspan="4" class="text-center"><b>Details Of Material Supplier</b></td>
				</tr>
				<tr>
                    <td class="text-right">
                        <b style="padding-top: 1px;vertical-align:top;">Name</b><br>
                        <b>Address</b><br><br><br>
                        <b>State & Code</b><br>
                        <b>GSTIN</b><br>
                    </td> 
					 <td colspan="3" class="text-left">
                        <u><b style="padding-top: 10px;"> <?= $companyData->company_name?></b></u><br>
                        <?= $companyData->company_address?><br>
                        <?= $companyData->company_state?>(<?= $companyData->company_state_code?>)<br>
                        <?= $companyData->company_gst_no?><br>
                    </td>
                    <td class="text-right">
                        <b style="padding-top: 1px;vertical-align:top;">Name</b><br>
                        <b>Address</b><br><br><br>
                        <b>State & Code</b><br>
                        <b>GSTIN</b><br>
                    </td>
					<td colspan="3" class="text-left">
					    <u><b> <?= $partyData->party_name?></b></u><br>
                        <?= $partyData->party_address?><br>
                        <?= $stateData->name?>(<?= $stateData->gst_statecode?>)<br>
                        <?= $partyData->gstin?><br>
                    </td>
				</tr>
                <tr>
					<td colspan="8" class="text-left"><b>Place Of Supply:</b> <?= $partyData->delivery_address?> <?= $partyData->delivery_pincode?></td>
				</tr>
                <tr>
					<td class="text-center"><b>Sr. No.</b></td>
					<td colspan="2" class="text-center"><b>Description Of Goods</b></td>
					<td class="text-center"><b>HSN Code</b></td>
					<td class="text-center"><b>Qty</b></td>
					<td class="text-center"><b>Unit</b></td>
					<td class="text-center"><b>Rate</b></td>
					<td class="text-center"><b>Taxable Value</b></td>
				</tr>
                <?php echo $tableBody; ?>
                <tr>
					<td colspan="8" class="text-left"><b>Completed Process:</b> <?=$salesData->vou_name_s ?></td>
				</tr>
                <tr>
					<td colspan="8" class="text-left"><b>Heat No:</b><?=$salesData->vou_name_l ?></td>
				</tr>
                <tr>
                    <td colspan="6" class="text-left"><b>Total Taxable Value</b></td>
					<td colspan="2" class="text-left"><b><?= $TaxAmt ?></b></td>
				</tr>
                <tr>
                    <td colspan="6" class="text-left"><b>Add CGST @6%</b></td>
					<td colspan="2" class="text-left"><b><?= $cgst ?></b></td>
				</tr>
                <tr>
                    <td colspan="6" class="text-left"><b>Add SGST @6%</b></td>
					<td colspan="2" class="text-left"><b><?=  $cgst ?></b></td>
				</tr>
                <tr>
                    <td colspan="6" class="text-left"><b>Total Value</b></td>
					<td colspan="2" class="text-left"><b><?= $totalTaxAmt ?></b></td>
					
				</tr>
                <tr>
                    <td colspan="3" class="text-left"><b>Declaration:</b>I declared that details mentioned above are true and correct to the best of my knowledge.</td>
					<td colspan="3" class="text-left">
                        <b>Name of Signatory :</b><br>
                        <b>Designation / status:</b><br>
                        <b>Signature in the panel--------------></b><br>
                         
                    </td>
					<td colspan="2" class="text-left"><b></b></td>
					
				</tr>
			</tbody>
		</table><br><br>
		<table class="table item-list-bb">
			<tbody>
                <tr>
					<td colspan="4" class="text-center" style="font-size:2rem;"><b>Delivery Challan</b></td>
					<td colspan="4" class="text-right"  style="vertical-align:bottom !important;font-size:0rem;padding-top: 20px;"><b>DUPLICATE FOR CONSIGNEE/VENDOR</b></td>
				</tr>
				<tr>
					<td colspan="4" class="text-center"><b>See Rule 10 under Tax Invoice,Credit And Debit Note Rules</b></td>
					<td colspan="2" class="text-right"><b>Date:</b></td>
					<td colspan="2" class="text-left"><?=date('d/m/Y', strtotime($salesData->trans_date))?></td>
				</tr>
                <tr>
                    <td colspan="2" class="text-right"><b>JobWorker's Serial Number:</b></td>
					<td colspan="2" class="text-left"><?=$salesData->trans_no ?></td>
                    <td colspan="2" class="text-right"><b>Material Supplier's Sr.No.:</b></td>
					<td colspan="2" class="text-left"></td>
				</tr>
                <tr>
					<td colspan="4" class="text-center"><b>Details of Job Work Service Provider</b></td>
					<td colspan="4" class="text-center"><b>Details Of Material Supplier</b></td>
				</tr>
				<tr>
                    <td  class="text-right">
                        <b style="padding-top: 1px;vertical-align:top;">Name</b><br>
                        <b>Address</b><br><br><br>
                        <b>State & Code</b><br>
                        <b>GSTIN</b><br>
                    </td> 
					 <td colspan="3" class="text-left">
                        <u><b style="padding-top: 10px;"> <?= $companyData->company_name?></b></u><br>
                        <?= $companyData->company_address?><br>
                        <?= $companyData->company_state?>(<?= $companyData->company_state_code?>)<br>
                        <?= $companyData->company_gst_no?><br>
                    </td>
                    <td  class="text-right">
                        <b style="padding-top: 1px;vertical-align:top;">Name</b><br>
                        <b>Address</b><br><br>
                        <b>State & Code</b><br><br>
                        <b>GSTIN</b><br>
                    </td>
					<td colspan="3" class="text-left">
                        <u><b style="padding-top: 10px;"> <?= $partyData->party_name?></b></u><br>
                        <?=$partyData->party_address?><br>
                        <?= $stateData->name?><br><br>
						<?=$partyData->gstin?> 
                    </td>
				</tr>
                <tr>
					<td colspan="8" class="text-left"><b>Place Of Supply:</b> <?= $partyData->delivery_address?> <?= $partyData->delivery_pincode?></td>
				</tr>
                <tr>
					<td class="text-center"><b>Sr. No.</b></td>
					<td colspan="2" class="text-center"><b>Description Of Goods</b></td>
					<td class="text-center"><b>HSN Code</b></td>
					<td class="text-center"><b>Qty</b></td>
					<td class="text-center"><b>Unit</b></td>
					<td class="text-center"><b>Rate</b></td>
					<td class="text-center"><b>Taxable Value</b></td>
				</tr>
                <?php echo $tableBody; ?>
                <tr>
					<td colspan="8" class="text-left"><b>Completed Process:</b> <?=$salesData->vou_name_s ?></td>
				</tr>
                <tr>
					<td colspan="8" class="text-left"><b>Heat No:</b><?=$salesData->vou_name_l ?></td>
				</tr>
                <tr>
                    <td colspan="6" class="text-left"><b>Total Taxable Value</b></td>
					<td colspan="2" class="text-left"><b><?= $TaxAmt ?></b></td>
				</tr>
                <tr>
                    <td colspan="6" class="text-left"><b>Add CGST @6%</b></td>
					<td colspan="2" class="text-left"><b><?= $cgst ?></b></td>
				</tr>
                <tr>
                    <td colspan="6" class="text-left"><b>Add SGST @6%</b></td>
					<td colspan="2" class="text-left"><b><?=  $cgst ?></b></td>
				</tr>
                <tr>
                    <td colspan="6" class="text-left"><b>Total Value</b></td>
					<td colspan="2" class="text-left"><b><?= $totalTaxAmt ?></b></td>
				</tr>
                <tr>
                    <td colspan="3" class="text-left"><b>Declaration:</b>I declared that details mentioned above are true and correct to the best of my knowledge.</td>
					<td colspan="3" class="text-left">
                        <b>Name of Signatory :</b><br>
                        <b>Designation / status:</b><br>
                        <b>Signature in the panel--------------></b><br>
                    </td>
					<td colspan="2" class="text-left"><b></b></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>