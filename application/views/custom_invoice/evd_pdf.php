	<div class="row">
		<div class="col-12" style="padding:0px 10px;">
			<table class="table">
				<tr>
					<td class="fs-18 text-center" style="letter-spacing: 1px;font-weight:bold;padding:0px !important;"> EXPORT VALUE DELCARATION</td>
				</tr>
				<tr>
					<td class="text-center" ><u>(See Rule 7 of Customs Valuation (Determination of Value of Export Goods) Rules,2007.)</u></td>
				</tr>
			</table>
			
			<p><b>1. Shipping Bill No & Date :</b></p>
			<p><b>2. Invoice No. & Date: <?=$dataRow->doc_no?> & <?=date('d.m.Y',strtotime($dataRow->doc_date))?></b></p>
			<p><b>3. Nature Of Transaction</b></p>
			<table>
				<tr>
					<td class="text-right" style="width:22mm;">Sale:</td>
					<td style="border:1px solid #000000;width:13mm;height:20px;vertical-align:middle;text-align:center;"><img src="<?=base_url('assets/images/check_icon.png')?>" style="width:10px;"></td>
					<td class="text-right" style="width:46mm;">Sale On Consignment:</td>
					<td style="border:1px solid #000000;width:13mm;height:20px;vertical-align:middle;text-align:center;"></td>
					<td class="text-right" style="width:22mm;">Gift:</td>
					<td style="border:1px solid #000000;width:13mm;height:20px;vertical-align:middle;text-align:center;"></td>
				
					<td class="text-right" style="width:22mm;">Sample:</td>
					<td style="border:1px solid #000000;width:13mm;height:20px;vertical-align:middle;text-align:center;"></td>
					<td class="text-right" style="width:21mm;">Other:</td>
					<td style="border:1px solid #000000;width:13mm;height:20px;vertical-align:middle;text-align:center;"></td>
				</tr>
			</table><br>
			
			<p><b>4. Method Of Valuation <small>(See Export Valuation Rules)</small></b></p>
			<table>
				<tr>
					<td class="text-right" style="width:22mm;">Rule 3:</td>
					<td style="border:1px solid #000000;width:13mm;height:20px;vertical-align:middle;text-align:center;"></td>
					<td class="text-right" style="width:46mm;">Rule 4:</td>
					<td style="border:1px solid #000000;width:13mm;height:20px;vertical-align:middle;text-align:center;"></td>
					<td class="text-right" style="width:22mm;">Rule 5:</td>
					<td style="border:1px solid #000000;width:13mm;height:20px;vertical-align:middle;text-align:center;"></td>		
					<td class="text-right" style="width:21mm;">Rule 6:</td>
					<td style="border:1px solid #000000;width:13mm;height:20px;vertical-align:middle;text-align:center;"></td>	
					<td class="text-right" style="width:35mm;" colspan="2">&nbsp;</td>	
				</tr>
			</table><br>
			
			<table style="padding-left:0px;">
				<tr>
					<td style="width:60mm;padding-left:0px;" rowspan="2"><p><b>5. Whether Seller And Buyer Are Related</b></p></td>
					<td class="text-right" style="width:22mm;">Yes</td>
					<td style="border:1px solid #000000;width:13mm;height:20px;vertical-align:middle;text-align:center;"></td>
					<td class="text-right" style="width:22mm;">No</td>
					<td style="border:1px solid #000000;width:13mm;height:20px;vertical-align:middle;text-align:center;"><img src="<?=base_url('assets/images/check_icon.png')?>" style="width:10px;"></td>
					<td style="width:70mm;">&nbsp;</td>
				</tr>
				<tr><td style="width:140mm;" colspan="5">&nbsp;</td></tr>
			</table><br>
			
			<table style="padding-left:0px;">
				<tr>
					<td style="width:60mm;padding-left:0px;" rowspan="2"><p><b>6. If Yes ,Whether Relationship<br>Has Influenced The Price</b></p></td>
					<td class="text-right" style="width:22mm;">Yes</td>
					<td style="border:1px solid #000000;width:13mm;height:20px;vertical-align:middle;text-align:center;"></td>
					<td class="text-right" style="width:22mm;">No</td>
					<td style="border:1px solid #000000;width:13mm;height:20px;vertical-align:middle;text-align:center;"><img src="<?=base_url('assets/images/check_icon.png')?>" style="width:10px;"></td>
					<td style="width:70mm;">&nbsp;</td>
				</tr>
				<tr><td style="width:140mm;" colspan="5">&nbsp;</td></tr>
			</table>
			<?php $terms = explode(PHP_EOL, $dataRow->terms_conditions);?>
			<p><b>7. Terms Of Payment : </b><?=(!empty($terms[1])) ? $terms[1] : ''?></b></p>
			
			<p><b>8. Terms Of Delivery : </b><?=(!empty($terms[0])) ? $terms[0] : ''?></b></p>
			
			<p><b>9. Previous Export Of Identical/Similer Goods,If Any Shipping Bill No. And Date:</b></p>
			
			<p><b>10.Any Other Relevant Information(Attach Separate sheet,If Necessary)</b></p>
			<p><b>Shipping Bill No & Date :</b></p>
			
			<p><u><b>Declaration</b></u></p>
			<p>1.I/We Hereby Declare That The Information Finished Above Is True , Complete And Correct In Every Respect.</p>
			<p>2. I/We Also Undertake To Bring To The Notice Of Proper Officer Any Particulars Which Subsequently Come To My/Our Knowladge Which Will Have Bearing On A Valuation.</p>
			
			<table style="margin-top:100px;">
				<tr>
					<td style="width:65%;text-align:left;">
						<b>Place: </b> Chhapara - Rajkot<br>
						<b>Date: </b> <?=date('d.m.Y',strtotime($dataRow->doc_date))?>
					</td>
					<th style="width:35%;text-align:center;">SIGNATURE OF THE EXPORTER</th>
				</tr>
			</table>
		</div>
	</div>