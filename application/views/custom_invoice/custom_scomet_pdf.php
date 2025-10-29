<div class="row">
	<div class="col-12">
		<table class="table">
			<tr><td class="fs-18 text-center" style="letter-spacing: 1px;font-weight:bold;padding:0px !important;">Exports Statement on Sanction Warranty Against Exports to <?=$dataRow->country_of_final_destonation?></td></tr>
		</table>
    </div>

    <div>
        <p style="line-height:1.65em;">We,<b> <?=$companyInfo->company_name?>,</b>India Hereby Certify That We Are The Exporter Of The Goods Described Below And Accept The Responsibilities Of The Exporter By Signing This Statement To Confirm That The Shipment(S) Concerned Does Not Require A License.</p>

        <table class="table">
            <tr><td> <b> Name of Exporter : </b><?=$companyInfo->company_name?></td></tr>
            <tr><td> <b> Invoice No. : </b><?=$dataRow->doc_no?></td></tr>
            <tr><td> <b> Value USD / EURO etc. : </b> <?= $partyData->currency ?> <?=$dataRow->net_amount?></td></tr>
            <tr><td> <b> Mode of Shipment : </b> <?= $shipment ?> </td></tr>
            <tr><td> <b> Port of Discharge : </b><?= $extraField->port_of_discharge?></td></tr>
            <tr><td><b> Place of Destination : </b><?= $extraField->place_of_delivery?></td></tr>
        </table>

        <p>We, <b> <?=$companyInfo->company_name?> </b> Further Undertake and Confirm</p>
        <ol type="A" style="list-style-position: outside;padding-left: 10%;">
            <li style="padding-left: 10%;padding-bottom:10px;"> Our Products Do Not Fall Under Restricted Or Negative List Of Items Under Ftp 2015-20 , Not These Are Categorized Under SCOMET (Special Chemicals Organisms ,Materials Equipment & Technologies ) List</br></li>
            <li style="padding-left: 10%;margin-bottom:10px;"> Export Of Our Products Are Neither Covered Under Eu Registration 423/2007 No. The Custom Is Listed Under OFAC (Office Of Foreign Asset Control Under U.S. Department Of Treasury) Sdn List.</br></li>
            <li style="padding-left: 10%;padding-bottom:10px;"> Supplier Stated In The Export Invoice(S) Are Not Meant For Any Military /Unclear Activities Or Development. The Goods Stated In The Invoice Do Not Confirm To Unclear Transfer Or Proliferation Activities.</br></li>
            <li style="padding-left: 10%;padding-bottom:10px;"> The Supplier Do Not Contravene The Resolution 1929 (2010) Of United Nations Security Council Or Provisions Of INFCIRC/254/REV.9/PART 2 (IAEA DOCUMENTS)</li>
        </ul> 

        <p>Thank You,</p>
        <h5 style="padding-left:450px; padding-top:70px;"><b>SIGNATURE OF THE EXPORTER</b></h5>
        
    </div>
		
	
</div>