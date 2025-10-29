<form>
    <div class="col-md-12 form-group">
        <input type="hidden" name="job_card_id" id="job_card_id" value="<?=(!empty($job_card_id))?$job_card_id:""?>">
        <div class="table-responsive">
            <table class="table table-striped table-bordered ">
                <tbody>
                    <?php
                        foreach($dataRow as $row):
                    ?>
                        <tr class="text-center thead-info">
                            <th colspan="2">Item Name : <?=$row['item_name']?></th>
                        </tr>
                        <tr>    
                            <th>Process Name </th>
                            <th>Scrape Qty</th>
                        </tr>
                        <?php
                            $totalScrapeQty = 0;
                            foreach($row['processData'] as $processRow):
                                $totalScrapeQty += $processRow['scrape_qty'];
                        ?>
                            <tr>
                                <td><?=$processRow['process_name']?></td>
                                <td><?=$processRow['scrape_qty']?></td>
                            </tr>
                        <?php
                            endforeach;
                        ?>
                        <tr>
                            <th>Total Qty</th>
                            <th>
                                <?=$totalScrapeQty?>
                                <input type="hidden" name="item_id[]" id="item_id" value="<?=$row['item_id']?>">
                                <input type="hidden" name="scrape_qty[]" id="scrape_qty" value="<?=$totalScrapeQty?>">
                            </th>
                        </tr>
                        <tr height="30px" style="background: none;">
                            <td style="border-left:none;border-right:none;border-bottom:none"></td>
                            <td style="border-left:none;border-right:none;border-bottom:none"></td>
                        </tr>
                    <?php
                        endforeach;
                    ?>
                </tbody>                   
            </table>
        </div>
    </div>
</form>