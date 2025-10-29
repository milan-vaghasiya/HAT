<div class="row">
		<div class="col-12" style="padding:0px 10px;">
			<table class="table">
				<tr>
					<td class="fs-18 text-center" style="letter-spacing: 1px;font-weight:bold;padding:0px !important;">DRAWBACK  DECLARATION</td>
				</tr>
				<tr>
					<td class="text-center" ><u>(To be filed for export goods under claim for Drawback)</u></td>
				</tr>
			</table>
			
            <table class="table" style="margin-top:20px;">
			    <tr>
                    <td><b> Shipping Bill No:</b></td>
			        <td style="text-align:right;">
						<b>Date: </b> <?=date("d.m.Y",strtotime($dataRow->trans_date))?>
					</td>
                </tr>
			</table>
            <table style="margin-top:20px;">
                <tr>
                    <td><p>I/We Jay Jalaram Precision Component LLP do hereby declare that: -</p></td><br>
                </tr>    
                <tr>
                    <td><b>i.</b>	The quality and specification of the goods as stated in this Shipping Bill are in accordance with the terms of the export contract entered into with the buyer/consignee in pursuance of which the goods are being exported.</td><br><br>
                </tr>
                <tr>
                    <td><b>ii.</b>	 I/We are not claiming benefit under the Engineering Products Export (Replenishment of Iron and steel Intermediates) Scheme, notified by the Ministry of Commerce in Notification No. 539 RE/92-97 dated 1.3.95.</td><br><br>
                </tr>
                <tr>
                    <td><b>iii.</b>    There is no change in the manufacturing formula and in the quantum per unit of the imported material or components, if any utilised in the manufacture of export goods and the material or components declared in the application under Rule 6 or 7 of the Drawback Rules, 1995 to have been imported, continue to be so imported and are not obtained from indigenous sources.</td><br><br>
                </tr>
                <tr>
                    <td><b>iv.</b>    The export have not been manufactured by availing the procedure under Rule 12(1)(b)/13(1)(b) of the Central Excise Rules, 1944.</td><br><br>
                </tr>
                <tr>
                    <td  class="text-center" ><b>OR</b></td><br><br>
                </tr>
                <tr>
                    <td style="padding-left:40px;"><p>The export goods have been manufactured by availing the procedure under Rule 12(1)(b)/13(1)(b) of the Central Excise rules, 1944, but we have claimed/shall be claiming drawback on the basis of special brand rate in terms of Rule 6 of the Drawback Rules, 1995.</p></td><br><br>
                </tr>
                <tr>
                    <td><b>v. </b>    The goods have not been manufactured and/or exported in discharge of export obligation against an Advance License issued under the Duty Exemption Entitlement Scheme (DEEC) declared under the Import and Export Policy.</td><br><br>
                </tr>
                <tr>
                    <td  class="text-center" ><b>OR</b></td><br><br>
                </tr>
                <tr>
                    <td style="padding-left:40px;">  Goods have been manufactured and are being exported in discharge of export obligation under the Duty Exemption Entitlement Scheme (DEEC), in terms of Notification No. 79/95 or 80/95, both dated 31.3.95 or 31/97 dated 1.4.97. However, Drawback has been claimed only in respect of the Central Excise duties leviable on inputs specified in the Drawback Schedule.</td><br><br>
                </tr>
                <tr>
                    <td  class="text-center" ><b>OR</b></td><br><br>
                </tr>
                <tr>
                    <td style="padding-left:40px;">  The goods have been manufactured and are being exported in discharge of export obligation under the Duty Exemption Entitlement Scheme (DEEC), but I/We are claiming Brand rate of drawback fixed under Rule 6 or 7 of the Drawback Rules.<td><br><br>
                </tr>
                <tr>
                    <td><b>vi.</b>  The goods have not been manufactured and/or exported after availing of facility under the Passbook Scheme as contained in para 7.25 of the Export and Import Policy (April 1997-31 March 2002).</td><br><br>
                </tr>
                <tr>
                    <td><b>vii.</b>  The goods have not been manufactured and/or exported by a unit licensed as 100% Export Oriented Unit in terms of Import and Export Policy in force.</td><br><br>
                </tr>
                <tr>
                    <td><b>viii.</b>  The goods have not been manufactured and/or exported by a unit situated in a Free Trade, Export Processing or any other such Zone.</td><br><br>
                </tr>
                <tr>
                    <td><b>ix.</b>  The goods have not been manufactured partly of wholly in bond under Section 65 of the Custom Act, 1962.</td><br><br>
                </tr>
                <tr>
                    <td><b>x.</b>  The present market value of the goods is as follows: -</td><br><br>
                </tr>
            </table>
            <table style="width:100%;text-align:center;margin:0px 80px;" class="table item-list-bb " >
                <tr>
                    <th>S.No.</th>
                    <th>Item No. in the Invoice</th>
                    <th>Market Value</th>
                </tr>
                <?php
                    $i=1;
                    foreach($dataRow->itemData as $row):
                        echo '<tr>
                            <td>'.$i.'</td>
                            <td>'.$i.'</td>
                            <td>'.floatVal($row->price).' '.$partyData->currency.'</td>
                        </tr>';
                        $i++;
                    endforeach;
                ?>
            </table>

            <table style="margin-top:20px;">    
                <tr>
                    <td><b>xi.</b>  The export value of the goods covered by this Shipping Bill is not less than the total value of all imported materials used in manufacture of such goods.</td><br><br>
                </tr>
                <tr>
                    <td><b>xii.</b>  The market price of the goods being exported is not less than the drawback amount being claimed.</td><br><br>
                </tr>
                <tr>
                    <td><b>xiii.</b>  The drawback amount claimed is more that 1% of the FOB value of the export product, or the drawback amount claimed is less than 1% of the FOB value but more than Rs. 500.</td><br><br>
                </tr>
                <tr>
                    <td><b>xiv.</b>  I/We undertake to repatriate export proceeds within six months from date of export and submit the Bank Realisation Certificate (BRC) to Assistant Commissioner (Drawback). In case, the export proceeds are not realised within 6 months, I/We will either furnish extension of time from the R.B.I. and submit BRC within such extended period or will pay back the drawback received against this Shipping Bill.</td><br><br>
                </tr>

            </table>
            <table style="margin-top:100px;">
				<tr>
					<td style="width:65%;text-align:left;">
						<b>Date: </b> <?=date("d.m.Y",strtotime($dataRow->trans_date))?>
					</td>
				</tr>
			</table>
		</div>
	</div>