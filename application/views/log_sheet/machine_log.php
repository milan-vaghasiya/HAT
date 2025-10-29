<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Machine Log</h4>
                            </div>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='processTable' class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>#</th>
                                        <th>Log date</th>
                                        <th>Job Card No</th>
                                        <th>Part Code</th>
                                        <th>Process</th>
                                        <th>Machine</th>
                                        <th>Operator</th>
                                        <th>Part Count</th>
                                        <th>Production Qty</th>
                                        <th>Cycle Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i=1;
                                    if (!empty($machineLogData)) {
                                        foreach ($machineLogData as $row) {
                                    ?>
                                            <tr class="text-center">
                                                <td><?=$i++?></td>
                                                <td><?=formatDate($row->log_date)?></td>
                                                <td><?=getPrefixNumber($row->job_prefix,$row->job_no)?></td>
                                                <td><?=$row->part_code?></td>
                                                <td><?=$row->process_name?></td>
                                                <td><?=$row->item_name?></td>
                                                <td><?=$row->emp_name?></td>
                                                <td><?=$row->start_part_count?></td>
                                                <td><?=$row->production_qty?></td>
                                                <td><?=$row->cycle_time?></td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    else
                                    {
                                        ?>
                                        <tr>
                                            <td colspan="10" class="text-center">No Data</td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>