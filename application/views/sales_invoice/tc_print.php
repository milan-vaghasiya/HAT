<div class="col-md-12 form-group">
    <!--<table class="table top-table-border"> 
        <tr>
            <th colspan="5">MATERIAL TEST CERTIFICATE <br> As Per EN 10204:2004 - Type 3.1</th>
			<th>F/QC/03(00/01.01.16)</th>
        </tr>
        <tr>
            <tr>
				<th class="text-left">Customer Name:</th> <td><?//=$invData->party_name;?></td>
                <th class="text-left">T.C. No.:</th> <td><?//=(!empty($invData->trans_no))?'AE/'.$invData->trans_no:"";?></td>
				<th class="text-left">P.O. No.:</th> <td><?//=(!empty($invData->doc_no))?$invData->doc_no:"";?></td>
			</tr>
			<tr>
                <th class="text-left">Type Of Material:</th> <td><?//=(!empty($material_type))?$material_type:"";?></td>
                <th class="text-left">Date:</th> <td><?//=(!empty($invData->trans_date))?formatDate($invData->trans_date):"";?></td>
				<th class="text-left">Colour Code:</th> <td><?//=(!empty($tcMasterData->color_code))?$tcMasterData->color_code:"";?></td>
			</tr>
            <tr>
                <th class="text-left">Material Grade:</th> <td colspan="5"><?//=(!empty($tcMasterData->material_grade))?$tcMasterData->material_grade:"";?></td>
            </tr>
        </tr>
    </table>-->
    <?php if(!empty($tcMasterData)): ?>
        <table class="table item-list-bb" style="margin-top:8px;">
			<tr>
				<th colspan="13">We hereby certify that material found results as per <?=(!empty($tcMasterData->material_grade))?$tcMasterData->material_grade:""; ?></th>
			</tr>
			<tr class="bg-secondary">
				<th colspan="13">Chemical Composition</th>
			</tr>
			<tr class="text-center bg-secondary">
				<th></th>
				<th>Ref. TC No.</th>
				<th>Size</th>
				<?= $testReportData[0]['chemParam'] ?>
			</tr>
			<tr class="bg-light">
				<th>Min.</th>
				<th></th>
				<th></th>
				<?= $testReportData[0]['chemMinData'] ?>
			</tr>
			<tr class="bg-light">
				<th>Max.</th>
				<th></th>
				<th></th>
				<?= $testReportData[0]['chemMaxData'] ?>
			</tr>
			<?php
				
				foreach($testReportData[0]['chemResult'] as $row)
				{
					$i=1;
					echo '<tr>';
					foreach($row as $val){if($i<=13){echo '<td>'.$val.'</td>';}$i++;}
					echo '</tr>';
				}
			?>
        </table>
    <?php else: ?>
        <table class="table item-list-bb" style="margin-top:8px;">
            <thead class="thead-info"> 
                <tr>
                    <th><h1 style="color: red;">Test Certificate Not Found...!</h1></th>
                </tr>
            </thead>
        </table>
    <?php endif; ?>
<div class="col-md-12 form-group" style="margin-top:8px;">
    <?php if(!empty($tcMasterData)): ?>
        <table class="table item-list-bb">
			<tr class="bg-secondary">
				<th colspan="7">Mechanical Properties</th>
			</tr>
			<tr class="text-center bg-secondary">
				<th></th>
				<th>Ref. TC No.</th>
				<?= $testReportData[0]['mechParam'] ?>
			</tr>
			<tr class="text-center bg-light">
				<th>Min.</th>
				<th></th>
				<?= $testReportData[0]['mechMinData'] ?>
			</tr>
			<tr class="text-center bg-light">
				<th>Max.</th>
				<th></th>
				<?= $testReportData[0]['mechMaxData'] ?>
			</tr>
			<?php
				foreach($testReportData[0]['mechResult'] as $row)
				{
					$i=1;
					echo '<tr class="text-center">';
					foreach($row as $val){if($i<=7){echo '<td>'.$val.'</td>';}$i++;}
					echo '</tr>';
				}
			?>
        </table>
    <?php endif; ?>

    <table class="table item-list-bb" style="margin-top:8px;">
		<tr>
			<td colspan="5">Heat Tretment:- Solution Annealed at 1050°C±20°C Soaked for 2 Hr/Inch Of Thickness Then Water Quenched.</td>
		</tr>
		<tr>
			<td colspan="5">Above material Manufactured, Sampled, Tested, Inspected & Confirming to the Requirements of Material Std. Specification and Purchase Order Requirements.</td>
		</tr>
		<tr>
			<th colspan="5" class="bg-secondary">Description Of Goods</th>
		</tr>
		<tr class="text-center bg-light">
			<th>Sr. No.</th>
			<th>Invoice No.</th>
			<th>Item</th>
			<th>Drawing No.</th>
			<th>Qty.</th>
		</tr>
		<?php $i=1;
			foreach($invData->itemData as $row):
				echo '<tr class="text-center">
					<td>'.$i++.'</td>
					<td>'.$invData->trans_prefix.$invData->trans_no.'</td>
					<td>'.$row->item_name.'</td>
					<td>'.$row->drawing_no.'</td>
					<td>'.floatVal($row->qty).'</td>
				</tr>';
			endforeach;
		?>
    </table>
</div>