<?php $this->load->view('includes/header'); ?>
<form id="machineMaintanLog">
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <input type="hidden" name="id" id="id" value="" />
                            <input type="hidden" name="machine_id" id="machine_id" value="<?=$machine_id?>" />
                            <div class="col-md-9">
                                <h4 class="card-title pageHeader">PREVENTIVE MAINTENANCE RECORD</h4>
                            </div>
                            <div class="col-md-3">  
                                <div class="input-group">
                                    <select name="month" id="month" class="form-control single-select" style="width:70%;">
                                        <option value="">Select Month</option>
                                        <?php
                                            foreach($monthArr as $key=>$value):
												echo '<option value="'.$value.'">'.$key.'</option>';
											endforeach;
                                        ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data"><i class="fas fa-sync-alt"></i> Load</button>
                                    </div>
                                    <div class="error month"></div>
                                </div>
                            </div>
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
                                <thead class="thead-info" id="theadData">
                                    <tr class="text-center">
                                        <th colspan="3"><?=$companyData->company_name?></th>
                                        <th colspan="7">PREVENTIVE MAINTENANCE RECORD</th>
                                        <th colspan="3">F/MNT/02 (00/01.01.16)</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyData">
                                    <tr>
                                        <th colspan="5">M/C Description : <?=$reportData->item_name ?></th>
                                        <th colspan="5">Code No. :  <?=$reportData->item_code ?></th>
                                        <th colspan="3">Year : <?= $this->shortYear ?></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4">Daily Check Point</th>
                                        <th colspan="3">Weekly Check Point</th>
                                        <th colspan="3">Half-Monthly Check Point</th>
                                        <th colspan="3">Monthly Check Point</th>
                                    </tr>
                                    <tr>
                                        <?php
                                            $i=1; $daily = '';
                                            if(!empty($dailyData)):
                                                foreach($dailyData as $row):
                                                    $daily .=  $i++.'. '.$row->activities.'<br>';
                                                endforeach;
                                            else:
                                                $daily .= 'No Data Found!';
                                            endif;
                                            
                                            $j=1; $week = '';
                                            if(!empty($weekData)):
                                                foreach($weekData as $row):
                                                    $week .= $j++.'. '.$row->activities.'<br>';
                                                endforeach;
                                            else:
                                                $week .= 'No Data Found!';
                                            endif;

                                            $k=1; $halfMonthly = '';
                                            if(!empty($halfMonthlyData)):
                                                foreach($halfMonthlyData as $row):
                                                    $halfMonthly .=  $k++.'. '.$row->activities.'<br>';
                                                endforeach;
                                            else:
                                                $halfMonthly .= 'No Data Found!';
                                            endif; 

                                            $l=1; $monthly = '';
                                            if(!empty($monthlyData)):
                                                foreach($monthlyData as $row):
                                                    $monthly .=  $l++.'. '.$row->activities.'<br>';
                                                endforeach;
                                            else:
                                                $monthly .= 'No Data Found!';
                                            endif;                                             
                                        ?>
                                            <td colspan="4"><?php echo $daily ?></td>
                                            <td colspan="3"><?php echo $week; ?></td>
                                            <td colspan="3"><?php echo $halfMonthly; ?></td>
                                            <td colspan="4"><?php echo $monthly; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table id='reportTable2' class="table table-bordered">
                                <thead class="thead-info" id="theadData2">
                                </thead>
                                <tbody id="tbodyData2">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
</form>
<div class="bottomBtn bottom-25 right-25 permission-write">
    <button type="button" class="btn btn-primary btn-rounded font-bold permission-write save-form" style="letter-spacing:1px;" onclick="saveLog('machineMaintanLog');">SAVE MAINTENENCE LOG</button>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/machine-maintan-log.js?v=<?=time()?>"></script>