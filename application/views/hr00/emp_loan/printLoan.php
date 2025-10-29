<!-- <link href="<?=base_url();?>assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
<link href="<?=base_url();?>assets/css/style.css?v=<?=time()?>" rel="stylesheet"> -->
<div class="row">
	<div class="col-12">
		<table class="table"><tr><td class="fs-20 text-center" style="letter-spacing: 2px;font-weight:bold;padding:0px !important;"><u>Employee Loan Sanctioned Letter</u></td></tr></table>
		<table class="table" style="margin-top:40px;"><tr>
            <td class="fs-20 text-left">Dear <b><?=$empData->emp_name ?></b> ,</td>
            <td class="fs-20 text-right"><?= date("d/m/Y")?></td></tr></table>
        <p>
        We are pleased to inform you that your loan request has been approved, and we will be releasing the amount of INR <b><?= $loanData->net_amount?></b>.
        </p>
        <table class="table item-list-bb" style="margin-top:10px;">
            <tr>
                <td><b>L. S. No.</b></td>
                <td><?=getPrefixNumber($loanData->trans_prefix,$loanData->trans_no)?></td>
                <td><b>Sanctioned Date</b></td>
                <td><?=(!empty($loanData->trans_date)) ? formatDate($loanData->trans_date) : ""?></td>
            </tr>
            <tr>
                <td><b>Employee Name</b></td>
                <td><?=$empData->emp_name ?></td>
                <td><b>Contact Number</b></td>
                <td><?=$empData->emp_contact ?></td>
            </tr>
            <tr>
                <td><b>Loan Amount</b></td>
                <td><?= $loanData->net_amount?></td>
                <td><b>Loan Tenor (In Months)</b></td>
                <td><?= $loanData->other_gst?></td>

            </tr>
            <tr>
                <td><b>Amount Of EMI</b></td>
                <td><?= floatVal($loanData->other_amount)?></td>

                <td><b>EMI starts from</b></td>
                <td><?= date("01-m-Y", strtotime ( '+1 month' , strtotime ( $loanData->trans_date ) )) ;?></td>
            </tr>
           
        </table>

        <p>
            The Company is glad we could support you in the time of your need, and looks for your continued consistent performances moving forward.
        </p>
        <p>Good luck</p>
        <h5 style="padding-left:450px; padding-top:100px;">For <b><?=$companyData->company_name?></b></h5>
        <h5 style="padding-left:475px; padding-top:40px;">(Authorized Signatory)</h5>
		
		
		
		
	</div>
</div>