<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row justify-content-center">
			<div class="col-md-8">
				<?php

				foreach ($soData as $sqData) {
				
					$qrn = str_pad($sqData->quote_rev_no, 2, '0', STR_PAD_LEFT);
					$qrnVal = 'Rev No. ' . $qrn . ' / ' . formatDate($sqData->doc_date);
				?>
					<div class="card">

						<div class="card-body">

							<img src="<?= $this->data['letter_head'] ?>" style="width:100%" class="img">
							<table class="table ">
								<tr>
									<td class="fs-18 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;">SALES QUOTATION</td>
								</tr>
							</table>

							<table class="table table-bordered" style="margin-top:10px;">
								<tr>
									<td rowspan="4" style="width:55%;vertical-align:top;">
										<b>M/S. <?= $sqData->party_name ?></b> <br>
										<small><?= $sqData->party_address ?></small><br><br>
										<b>Kind. Attn. : <?= $sqData->contact_person ?></b><br>
										Contact No. : <?= $sqData->party_mobile ?><br>
										Email : <?= $sqData->contact_email ?>
									</td>
									<th style="width:12%;vertical-align:top;">Qtn. No.</th>
									<td style="width:12%;vertical-align:top;"><?= getPrefixNumber($sqData->trans_prefix, $sqData->trans_no) ?></td>
									<td style="width:21%;vertical-align:top;"><?= $qrnVal ?></td>
								</tr>
								<tr>
									<th>Qtn. Date</th>
									<td colspan="2"><?= formatDate($sqData->trans_date) ?></td>
								</tr>
								<tr>
									<th>Reference</th>
									<td colspan="2"><?= $sqData->ref_by ?> (<?= getPrefixNumber($sqData->ref_prefix, $sqData->ref_no) ?>)</td>
								</tr>
								<tr>
									<th>Ref. Date</th>
									<td colspan="2"><?= (!empty($sqData->ref_date)) ? formatDate($sqData->ref_date) : "" ?></td>
								</tr>
							</table>

							<table class="table table-bordered " style="margin-top:25px;">
								<tr>
									<th style="width:40px;" class="text-center">No.</th>
									<th class="text-left" class="text-center">Item Description</th>
									<th style="width:100px;" class="text-center">Qty</th>
									<th style="width:50px;" class="text-center">UOM</th>
									<th style="width:60px;" class="text-center">Rate<br><small>(<?= $sqData->lr_no ?>)</small></th>
									<th style="width:110px;" class="text-center">Amount<br><small>(<?= $sqData->lr_no ?>)</small></th>
								</tr>
								<?php
								$i = 1;
								$totalQty = 0;
								$totalAmt = 0;
								// print_r($sqData);
								if (!empty($sqData->itemData)) :
									
									foreach ($sqData->itemData as $row) :
										$drg_rev_no = (!empty($row->drg_rev_no)) ? '<b>Drg. No.</b>:' . $row->drg_rev_no : '';
										$rev_no = (!empty($row->rev_no)) ? '<b>Rev. No</b>:' . $row->rev_no : '';
										$rev_no = (!empty($drg_rev_no)) ? ', ' . $rev_no : $rev_no;
										$part_no = (!empty($row->batch_no)) ? '<b>Part No:</b>' . $row->batch_no : '';
										$part_no = (!empty($rev_no)) ? ', ' . $part_no : $part_no;
										$item_name = $row->item_name . '<br>' . $drg_rev_no . $rev_no . $part_no;
										$item_name = (!empty($row->grn_data)) ? $item_name . '<br>' . $row->grn_data : $item_name;
										$item_name = str_replace(["\r\n", "\r", "\n"], "<br/>", $item_name);
										$amount = $row->price * $row->qty;
										echo '<tr>';
										echo '<td class="text-center">' . $i++ . '</td>';
										echo '<td>' . $item_name . '</td>';
										echo '<td class="text-center" style="padding-right:8px;">' . floatVal($row->qty) . '</td>';
										echo '<td class="text-center" style="padding-right:8px;">' . $row->unit_name . '</td>';
										echo '<td class="text-right" style="padding-right:8px;">' . sprintf('%.3f', $row->price) . '</td>';
										echo '<td class="text-right" style="padding-right:8px;">' . sprintf('%.3f', $amount) . '</td>';
										echo '</tr>';
										$totalQty += $row->qty;
										$totalAmt += $amount;
									endforeach;
									if ($sqData->challan_no == 2) :
										echo '<tr>';
										echo '<td class="text-center">' . $i . '</td>';
										echo '<td>Development Charge</td>';
										echo '<td class="text-right" style="padding-right:8px;">-</td>';
										echo '<td class="text-center" style="padding-right:8px;">-</td>';
										echo '<td class="text-right" style="padding-right:8px;">-</td>';
										echo '<td class="text-right" style="padding-right:8px;">' . sprintf('%.3f', $sqData->net_weight) . '</td>';
										echo '</tr>';
										$totalAmt += $sqData->net_weight;
									endif;
								endif;
								?>
								<tr>
									<th colspan="5" class="text-right">Total Amount</th>
									<th class="text-right"><?= sprintf('%.3f', $totalAmt) ?></th>
								</tr>
							</table>
							<h5 class="text-bold">Amount In Words (<?= $sqData->lr_no ?>) : <i><?= numToWordEnglish($totalAmt) ?></i></h3>
								<h4>Terms & Conditions :-</h4>
								<table class="table top-table" style="margin-top:10px;">
									<?php
									if (!empty($sqData->terms_conditions)) :
										$terms = json_decode($sqData->terms_conditions);
										foreach ($terms as $row) :
											echo '<tr>';
											echo '<th class="text-left fs-13" style="width:140px;">' . $row->term_title . '</th>';
											echo '<td class=" fs-12"> : ' . $row->condition . '</td>';
											echo '</tr>';
										endforeach;
									endif;
									?>
								</table>



								<!-- <table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
									<tr>
										<td style="width:50%;" rowspan="3"></td>
										<th colspan="2">For, <?= $this->data['companyData']->company_name ?></th>
									</tr>
									<tr>
										<td colspan="3" height="70"></td>
									</tr>
									<tr>
										<td style="width:25%;" class="text-center">Prepared By</td>
										<td style="width:25%;" class="text-center">Authorised By</td>
									</tr>
								</table>
								<table class="table" style="margin-top:10px;">
									<tr>
										<td style="width:25%;">Qtn. No. & Date : <?= getPrefixNumber($sqData->trans_prefix, $sqData->trans_no) ?>-<?= formatDate($sqData->trans_date) ?></td>
										<td style="width:25%;"></td>
										<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
									</tr>
								</table> -->
						</div>
					</div>

				<?php
				}
				?>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('includes/footer'); ?>