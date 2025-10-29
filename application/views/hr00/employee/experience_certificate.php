<?php

    $gender1 = '';
    $gender2 = '';
  if($empData->emp_gender== 'Female')
  {
    $gender1 = 'Her';
    $gender2 = 'She';
    $gender3 = 'Her';
  }
  else
  {
    $gender1 = 'His';
    $gender2 = 'He';
    $gender3 = 'him';
  }

?>
<div class="row">
	<div class="col-12">
        <h2 style="padding-left:610px;"><b><u><?=date('M dS Y')?></u></b></h2>
		 <h2 style="padding-left:300px; padding-right:100px;"><b><u>Experience Certificate</u></b></h2>
		 <h2 style="padding-left:270px; padding-right:50px;"><b><u>To Whomsoever It May Concern</u></b></h2>

        <p style="padding-left:30px; padding-right:30px;">This is to certify that <b><?=$empData->emp_name?></b> is employed with our Company <b><?=$companyData->company_name?></b> from <b><?=formatDate($empData->emp_joining_date, 'dS M, Y')?></b> to <b><?=formatDate($empData->emp_relieve_date,'dS M, Y')?></b> as <b><?=$empData->title?></b> for a period of 2 years and 1 month. </p>
        <p  style="padding-left:30px; padding-right:30px;"><?=($empRes != '')? $empRes:$empResMain->exp_responsibilities;?> </p>
        <p  style="padding-left:30px; padding-right:30px;"><?= $gender1 ?> Exposure in these areas is very good. During <?= $gender1 ?> tenure with us, <?= $gender2 ?> ably handled major responsibilities and found <?= $gender3 ?> to be hardworking and very productive.</p>

        <p  style="padding-left:30px; padding-right:30px;">We have found <?= $gender3 ?> to be self-starter who is motivated, duty bound, and a highly committed team player with strong conceptual knowledge.</p>
		<p  style="padding-left:30px; padding-right:30px;">We at <b><?=$companyData->company_name?></b> Wish <?= $gender3 ?> all success in <?= $gender1 ?> future endeavors.</p>
		
		<h5 style="padding-left:450px; padding-top:100px;">For <b><?=$companyData->company_name?></b></h5>
        <h5 style="padding-left:525px; padding-top:40px;">Group Head</h5>
		
		
		
		
	</div>
</div>