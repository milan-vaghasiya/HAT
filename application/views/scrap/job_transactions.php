<form id="scrap">
    <input type="hidden" name="job_id" value="<?=$job_id?>">
    <div class="error general_error"></div>
	<div class="row">
		<div class="col-md-6 form-group"><label>Job card transactions</label></div>
       
	
	</div>
    <div class="table-responsive">
        <table id="requestItems" class="table table-bordered align-items-center">
            <thead class="thead-info">
                <tr>
                    <th style="width:5%">#</th>
                    <th>Operator Name</th>
                    <th >Rejection Qty</th>
                    <th >Rejection Stage</th>
                    <th >Rejection Reason</th>
                    <th>Scrap Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if(!empty($jobCardData)):
                        $i=0;
                        //print_r($jobCardData);
                    
                        foreach($jobCardData as $row):
                          $rejection_qty=$row->rejection_qty-$row->rej_scrap_qty;
                          if($rejection_qty>0)
                          {
                ?>
                        <tr class="text-center">
                            <td><?=$i+1?></td>
                            <td class="text-left">
                                <?=$row->operator_name?>
                                <input type="hidden" name="transaction_id[]" value="<?=$row->id?>">
                               
                            </td>
                            <td><?=$rejection_qty?> </td>
                            <td>
                                <?=$row->rejection_stage?>
                            </td>
                            <td>
                                <?=$row->rejection_reason?></div>
                            </td>
                            <td>

                            <input type="number" name="scrap_qty[]" id="scrap_qty"   max="<?=$rejection_qty?>" 
   onKeyUp="if(this.value><?=$rejection_qty?>){this.value=<?=$rejection_qty?>;}" class="form-control floatOnly">

                            </td>
                        </tr>
                <?php
                          }
                            $i++;
                        endforeach;
                    else:
                ?>
                    <tr>
                        <td colspan="5" class="text-center">No Data Found.</td>
                    </tr>
                <?php
                    endif;
                ?>
            </tbody>
        </table>
    </div>
</form>
            