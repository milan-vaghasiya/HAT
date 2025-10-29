<html>
    <head>
        <title>
            <?=$pageHeader?>
        </title>
    </head>
    <body style="padding:10px;">
        <table class="table table-bordered itemList">
            <tr>
                <th style="width:70%;">AKSHAR ENGINEERS</th>
                <th style="width:30%;">
                    <img src="<?=base_url("assets/images/logo_text.png")?>" alt="logo" style="width:20%;">
                </th>
            </tr>
            <tr>
                <th>PRODUCTION MONITORING REPORT</th>
                <th>F/PRD/03 (01/16.09.21)</th>
            </tr>
            <tr>
                <th colspan="2"><?=(!empty($machineData))?$machineData->item_name." Production Sheet":""?></th>
            </tr>
        </table>
        <table class="table table-bordered itemList">
            <tr>
                <th class="text-left">Part</th>
                <td style="width: 150px;"><?=$jobData->product_name?></td>

                <th class="text-left">Size</th>
                <td style="width: 150px;"><?=$jobData->part_size?></td>

                <th class="text-left">Customer Code</th>
                <td style="width: 150px;"><?=(!empty($jobData->party_code))?$jobData->party_code:"Self"?></td>

                <th class="text-left">Prod. Sheet No.</th>
                <td style="width: 100px;"><?=$jobData->job_number?></td>
            </tr>
            <tr>
                <th class="text-left">Wo. No.</th>
                <td><?=(!empty($jobData->wo_no))?$jobData->wo_no:"Self"?></td>

                <th class="text-left">PO Qty.</th>
                <td><?=(!empty($jobData->so_trans_id))?$jobData->so_qty:"-"?></td>

                <th class="text-left">Machine No.</th>
                <td><?=(!empty($machineData))?$machineData->item_code:""?></td>

                <th class="text-left">Date</th>
                <td><?=formatDate($jobData->job_date)?></td>
            </tr>

            <tr>
                <th class="text-left">Operation</th>
                <td><?=$processData->process_name?></td>

                <th class="text-left">Material</th>
                <td>
                    <?= (!empty($reqMaterials['material_name']))?$reqMaterials['material_name']:''; ?>
                </td>
                
                <th class="text-left">Setting Time</th>
                <td><?=(!empty($sapData->setting_time)?$sapData->setting_time:'')?></td>
                
                <th class="text-left">Programmer/Setter</th>
                <td><?=(!empty($sapData->emp_name)?$sapData->emp_name:'')?></td>
            </tr>
        </table>
        <table class="table table-bordered itemList">
            <thead>
                <tr>
                    <th class="text-left" colspan="7" style="width:60%">
                        <u>Setting Parameters:</u>
                    </th>
                    <th class="text-left" style="width:10%">
                        Before Weight:
                    </th>
                    <th style="width:10%"><?=(!empty($jobApprovalData->pre_finished_weight)?$jobApprovalData->pre_finished_weight:'')?></th>
                    <th class="text-left" style="width:10%">
                        After Weight:
                    </th>
                    <th style="width:10%"><?=(!empty($jobApprovalData->finished_weight)?$jobApprovalData->finished_weight:'')?></th>
                </tr>
                <tr>
                    <th style="width:10%">Tool No.</th>
                    <th style="width:25%">Insert</th>
                    <th style="width:5%">Corner Redius</th>
                    <th style="width:5%">Grade</th>
                    <th style="width:5%">Make</th>
                    <th style="width:5%">Cutting Speed / RPM</th>
                    <th style="width:5%">Feed.</th>
                    <th colspan="4">Down Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $settingCount = count($settingData);
                $idleCount = count($idleReasonList);
                $idleRow = ceil($idleCount/2);
               
                $loopCount =($idleRow > $settingCount)?($idleRow):$settingCount;
                
                    $i=0;
                    for($j = 0; $j<$loopCount;$j++){
                        $row = !empty($settingData[$j])?$settingData[$j]:'';
                        ?>
                        <tr>
                            <td class="text-center"><?=!empty($row->tool_no)?$row->tool_no:''?></td>
                            <td><?=!empty($row->insert_name)?$row->insert_name:''?></td>
                            <td class="text-center"><?=!empty($row->corner_radius)?$row->corner_radius:''?></td>
                            <td class="text-center"><?=!empty($row->grade)?$row->grade:''?></td>
                            <td class="text-center"><?=!empty($row->make)?$row->make:''?></td>
                            <td class="text-center"><?=!empty($row->cutting_speed)?$row->cutting_speed:''?></td>
                            <td class="text-center"><?=!empty($row->feed)?$row->feed:''?></td>
                            <td colspan="2"><?=!empty($idleReasonList[$i]->remark)?(!empty($idleReasonList[$i]->code)?'['.$idleReasonList[$i]->code.'] ':'').$idleReasonList[$i]->remark:''?></td>
                            <td colspan="2"><?=!empty($idleReasonList[$i+1]->remark)?(!empty($idleReasonList[$i+1]->code)?'['.$idleReasonList[$i+1]->code.'] ':'').$idleReasonList[$i+1]->remark:''?></td>
                        </tr>
                       <?php
                        $i = $i+2;
                    }
               
                    
                ?>
            </tbody>
        </table>
        <table class="table table-bordered itemList">
            <thead>
                <tr>
                    <th class="text-left" colspan="11">
                        <u>Production Data</u>
                    </th>
                </tr>
                <tr>
                    <th rowspan="2">Date</th>
                    <th rowspan="2">Operator</th>
                    <th colspan="4">Time</th>
                    <th colspan="5">Qty.</th>
                </tr>
                <tr>
                    <th>T.T.</th>
                    <th>C.T.</th>
                    <th>D.T.</th>
                    <th>Remark</th>
                    <th>Actual</th>
                    <th>Rework</th>
                    <th>Rej.</th>
                    <th>Total</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($productionData as $row):
                        
                        $idleTimeData = $this->productionReportsNew->getIdleTimeReasonForOee(['entry_date' => $row->entry_date, 'shift_id' => $row->shift_id, 'machine_id' => $row->machine_id, 'process_id' => $row->process_id, 'operator_id' => $row->operator_id, 'product_id' => $row->product_id, 'job_card_id' => $row->job_card_id ]);
                        $td = $idleTimeData['td'];
                        $row->idle_time = $idleTimeData['total_idle_time'];
                        
                        $plan_time = !empty($row->shift_hour) ? $row->shift_hour : 660;
                        $total_load_unload_time=($row->total_load_unload_time*$row->production_qty)/60;
                        $runTime = $plan_time - $row->idle_time-$total_load_unload_time;
                        
                        echo '<tr class="text-center">
                            <td>'.(formatDate($row->entry_date)).'</td>
                            <td>'.$row->operator_name.'</td>
                            <td>'.$runTime.'</td>
                            <td>'.$row->cycle_time.'</td>
                            <td>'.$row->idle_time.'</td>
                            <td></td>
                            <td>'.floatVal($row->ok_qty).'</td>
                            <td>'.floatVal($row->rw_qty).'</td>
                            <td>'.floatVal($row->rej_qty).'</td>
                            <td>'.floatVal($row->production_qty).'</td>
                            <td></td>
                        </tr>';
                    endforeach;
                ?>
            </tbody>
        </table>
    </body>
</html>