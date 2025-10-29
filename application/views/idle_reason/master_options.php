<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Master Options</h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save float-right save-form permission-write" onclick="store('addMasterOptions','save');"><i class="fa fa-check"></i> Save</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="addMasterOptions">
                            <div class="col-md-12">
                                <div class="row">
                                    <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
                                    <div class="col-md-6 form-group">
                                        <label for="material_grade">Material Grade</label>
                                        <input type="text" name="material_grade" id="material_grade" class="form-control req" value="<?= (!empty($dataRow->material_grade)) ? $dataRow->material_grade : "" ?>" data-role="tagsinput">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="color_code">Color Code</label>
                                        <input name="color_code" id="color_code" class="form-control req" value="<?= (!empty($dataRow->color_code)) ? $dataRow->color_code : "" ?>" data-role="tagsinput">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="thread_types">Thread Types</label>
                                        <input name="thread_types" id="thread_types" class="form-control req" value="<?= (!empty($dataRow->thread_types)) ? $dataRow->thread_types : "" ?>" data-role="tagsinput">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="machine_idle_reason">Machine Idle Reason</label>
                                        <input name="machine_idle_reason" id="machine_idle_reason" class="form-control req" value="<?= (!empty($dataRow->machine_idle_reason)) ? $dataRow->machine_idle_reason : "" ?>" data-role="tagsinput">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="ppap_level">PPAP Level</label>
                                        <input name="ppap_level" id="ppap_level" class="form-control req" value="<?= (!empty($dataRow->ppap_level)) ? $dataRow->ppap_level : "" ?>" data-role="tagsinput">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label for="dev_charge">Development Charge</label>
                                        <input name="dev_charge" id="dev_charge" class="form-control floatOnly req" value="<?= (!empty($dataRow->dev_charge)) ? $dataRow->dev_charge : "" ?>">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>