
<div class="row">
	<div class="col-12">
		<table class="table item-list-bb" style="margin-top:10px;">
            <tbody>
                <tr class="text-left">
                    <th colspan="2">M/C Description : <?=$reportData->item_name ?></th>
                    <th>Code No. : <?=$reportData->item_code ?></th>
                    <th>Year : <?= $this->shortYear ?></th>
                </tr>
                <tr>
                    <th>Daily Check Point</th>
                    <th>Weekly Check Point</th>
                    <th>Half-Monthly Check Point</th>
                    <th>Monthly Check Point</th>
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
                        <td><?php echo $daily ?></td>
                        <td><?php echo $week; ?></td>
                        <td><?php echo $halfMonthly; ?></td>
                        <td><?php echo $monthly; ?></td>
                </tr>               
            </tbody>
		</table>
        <table class="table item-list-bb">
            <thead class="thead-info">
                <?php
                    echo $theadData;
                ?>
            </thead>
            <tbody>
                <?php
                    echo $tbodyData;
                ?>
            </tbody>
        </table>
	</div>
</div>