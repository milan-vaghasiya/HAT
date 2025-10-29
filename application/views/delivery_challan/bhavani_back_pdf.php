<div class="row">
	<div class="col-12">
		<table class="table item-list-bb" >
			<tbody>
                <tr>
					<td class="text-center"><b>DETAILS OF MATERIAL SENT BACK</b></td>
					<td class="text-center"><b>Date</b></td>
					<td class="text-center"><b>Ch No.</b></td>
					<td class="text-center"><b>Ok Qty.</b></td>
					<td class="text-center"><b>Rej. Qty.</b></td>
					<td class="text-center"><b>Pcs In Stock</b></td>
				</tr>
                <?php echo $tableBody; ?>
                <tr>
					<td colspan="8" class="text-left"><b>SCRAP RETURNED KGS:</b> <?= $totalRejQty  ?> </td>
				</tr>
                <tr>
					<td colspan="8" class="text-left"><b>SCRAP NOT RETURNED KGS</b> <?=  $totalQty ?></td>
				</tr>
                <tr>
					<td colspan="8" class="text-left">
                        <b>Designation / status:</b><br>
                        <b>Signature in the panel--------------></b><br>
                    </td>
				</tr>
			</tbody>
		</table>	
	</div>
</div>

