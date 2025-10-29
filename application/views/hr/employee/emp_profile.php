<?php $this->load->view('includes/header'); 
$profile_pic = 'male_user.png';
if(!empty($empData->emp_profile)){$profile_pic = $empData->emp_profile;}
else
{
	if(!empty($empData->emp_gender) and $empData->emp_gender=="Female"):
		$profile_pic = 'female_user.png';
	else:
		$profile_pic = 'male_user.png';
	endif;
}
?>
<link href="<?=base_url();?>assets/css/icard.css?v=<?=time()?>" rel="stylesheet" type="text/css">
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">
									<?= (!empty($empData->emp_name)) ? $empData->emp_name : "Employee Profile"; ?>
									 - <small><i><?= (!empty($empData->title)) ? $empData->title : "-"; ?> (<?= (!empty($empData->name)) ? $empData->name : ""; ?>)</i></small>
								</h4>
                            </div>
                            <div class="col-md-6">
                                <!--<a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-dark float-right"><i class="fa fa-arrow-left"></i> Back</a>-->
                                <!--<a href="<?= base_url($headData->controller.'/icard/'.$emp_id) ?>" class="btn waves-effect waves-light btn-outline-dark float-right" target="_blank"><i class="fa fa-address-book"></i> Icard</a>-->
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-lg-3 col-xlg-3 col-md-3">
                                    <div class="card">
                                        <div class="card-body p-2">
											<form id="profileForm" action="POST" enctype="multipart/form-data">
												<div class="profile-pic-wrapper">
													<div class="pic-holder">
														<!-- uploaded pic shown here -->
														<img id="profilePic" class="pic" src="<?= base_url('assets/uploads/emp_profile/'.$profile_pic) ?>">
														<Input class="uploadProfileInput" type="file" name="profile_pic" id="newProfilePhoto" accept="image/*" style="opacity: 0;" />
														<label for="newProfilePhoto" class="upload-file-block">
															<div class="text-center">
																<div class="mb-2"><i class="fa fa-camera fa-2x"></i></div>
																<div class="text-uppercase">Update <br /> Profile Photo</div>
															</div>
														</label>
														<input type="hidden" name="emp_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
													</div>
												</div>
											</form>
                                        </div>
										<h4 class="card-title m-t-5 m-b-5 text-center p-1" style="background:#009ee3;color:#FFFFFF;">EMP CODE : <?= (!empty($empData->emp_code)) ? $empData->emp_code : "-"; ?></h4>
										<div class="card-body p-3">
                                            <strong>Phone</strong>
                                            <p class="text-muted"><?= (!empty($empData->emp_contact)) ? $empData->emp_contact : "-"; ?></p>
                                            <strong>Gender</strong>
                                            <p class="text-muted"><?= (!empty($empData->emp_gender)) ? $empData->emp_gender : "-" ?></p>
                                            <strong>Date Of Birth</strong>
                                            <p class="text-muted"><?= (!empty($empData->emp_birthdate)) ? date("d-m-Y", strtotime($empData->emp_birthdate)) : "-"; ?></p>
                                            <strong>Joining Date</strong>
                                            <p class="text-muted"><?= (!empty($empData->emp_joining_date)) ? date("d-m-Y", strtotime($empData->emp_joining_date)) : "-"; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9 col-xlg-9 col-md-9">
                                    <div class="card">
                                        <!-- Tabs -->
                                        <ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="pills-personal-tab" data-toggle="pill" href="#personal" role="tab" aria-controls="pills-personal" aria-selected="true" datatip="Personal" flow="up"><i class="fas fa-address-book"></i></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-workprofile-tab" data-toggle="pill" href="#workprofile" role="tab" aria-controls="pills-workprofile" aria-selected="false" datatip="Work Profile" flow="up">Work Profile</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-documents-tab" data-toggle="pill" href="#documents" role="tab" aria-controls="pills-documents" aria-selected="false">Documents</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-nomination-tab" data-toggle="pill" href="#nomination" role="tab" aria-controls="pills-nomination" aria-selected="false">Nomination</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-education-tab" data-toggle="pill" href="#education" role="tab" aria-controls="pills-education" aria-selected="false">Education</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-salary-tab" data-toggle="pill" href="#salary" role="tab" aria-controls="pills-salary" aria-selected="true">Salary</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-staffSkill-tab" data-toggle="pill" href="#staffSkill" role="tab" aria-controls="pills-staffSkill" aria-selected="true">Staff Skill</a>
                                            </li>
                                            <!--<li class="nav-item"><a class="nav-link" id="pills-facility-tab" data-toggle="pill" href="#facility" role="tab" aria-controls="pills-facility" aria-selected="true">Facility</a></li>-->
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-icard-tab" data-toggle="pill" href="#icard" role="tab" aria-controls="pills-salary" aria-selected="true">I-Card</a>
                                            </li>
                                        </ul>
                                        <!-- Tabs -->
                                        <div class="tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="pills-personal-tab">
                                                <form id="personalDetail">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
                                                            <input type="hidden" name="emp_code" value="<?=(!empty($dataRow->emp_code))?$dataRow->emp_code:""?>" />
                                                            <input type="hidden" name="form_type" value="personal" />
                                                            
                                                            <div class="col-md-4 form-group">
                                                                <label for="emp_name">Employee Name</label>
                                                                <input type="text" name="emp_name" id="emp_name" class="form-control text-capitalize req" placeholder="Emp Name" value="<?=(!empty($dataRow->emp_name))?$dataRow->emp_name:""; ?>" />
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="father_name">Father Name</label>
                                                                <input type="text" name="father_name" class="form-control" value="<?=(!empty($dataRow->father_name))?$dataRow->father_name:""?>" />
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="mother_name">Mother Name</label>
                                                                <input type="text" name="mother_name" class="form-control" value="<?=(!empty($dataRow->mother_name))?$dataRow->mother_name:""?>" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="emp_alias">Alias Name</label>
                                                                <input type="text" name="emp_alias" id="emp_alias" class="form-control" value="<?=(!empty($dataRow->emp_alias))?$dataRow->emp_alias:""; ?>" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="emp_contact">Phone No.</label>
                                                                <input type="number" name="emp_contact" class="form-control numericOnly req" placeholder="Phone No." value="<?=(!empty($dataRow->emp_contact))?$dataRow->emp_contact:""?>" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="emp_alt_contact">Alternate Phone</label>
                                                                <input type="number" name="emp_alt_contact" class="form-control numericOnly" placeholder="Phone No." value="<?=(!empty($dataRow->emp_alt_contact))?$dataRow->emp_alt_contact:""?>" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="emp_gender">Gender</label>
                                                                <select name="emp_gender" id="emp_gender" class="form-control single-select">
                                                                    <option value="">Select Gender</option>
                                                                    <?php
                                                                        foreach($genderData as $value):
                                                                            $selected = (!empty($dataRow->emp_gender) && $value == $dataRow->emp_gender)?"selected":"";
                                                                            echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
                                                                        endforeach;
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="marital_status">Marital Status</label>
                                                                <select name="marital_status" id="marital_status" class="form-control " >
                                                                    <option value="Married" <?=(!empty($dataRow->marital_status) && $dataRow->marital_status == "Married")?"selected":""?>>Married</option>
                                                                    <option value="UnMarried" <?=(!empty($dataRow->marital_status) && $dataRow->marital_status == "UnMarried")?"selected":""?>>UnMarried</option>
                                                                    <option value="Widow" <?=(!empty($dataRow->marital_status) && $dataRow->marital_status == "Widow")?"selected":""?>>Widow</option>
                                                                </select>
                                                            </div>
                                                            
                                                            
                                                            <div class="col-md-3 form-group">
                                                                <label for="emp_birthdate">Date of Birth</label>
                                                                <input type="date" name="emp_birthdate" id="emp_birthdate" class="form-control" value="<?=(!empty($dataRow->emp_birthdate))?$dataRow->emp_birthdate:date("Y-m-d")?>" max="<?=(!empty($dataRow->emp_birthdate))?$dataRow->emp_birthdate:date("Y-m-d")?>" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="emp_email">Emp Email </label>
                                                                <input type="text" name="emp_email" id="emp_email" class="form-control" value="<?=(!empty($dataRow->emp_email))?$dataRow->emp_email:""?>" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="mark_id">Mark of Identification</label>
                                                                <input type="text" name="mark_id" class="form-control" placeholder="Mark of Identification" value="<?=(!empty($dataRow->mark_id))?$dataRow->mark_id:""?>" />
                                                            </div>
                                                            <div class="col-md-12 form-group">
                                                                <label for="emp_address">Address</label>
                                                                <textarea name="emp_address" class="form-control" placeholder="Address" style="resize:none;" rows="1"><?=(!empty($dataRow->emp_address))?$dataRow->emp_address:""?></textarea>
                                                            </div>

                                                            <div class="col-md-12 form-group">
                                                                <label for="permenant_address">Permenant Address</label>
                                                                <textarea name="permenant_address" class="form-control" placeholder="Permenant Address" style="resize:none;" rows="1"><?=(!empty($dataRow->permenant_address))?$dataRow->permenant_address:""?></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer" align="right">
                                                        <button type="button" class="btn btn-outline-success btn-save" onclick="save('personalDetail','editProfile');"><i class="fa fa-check"></i> Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                            
                                            <div class="tab-pane fade" id="workprofile" role="tabpanel" aria-labelledby="pills-workprofile-tab">
                                                <form id="workProfile">
                                                    <div class="card-body">
                                                        <div class="row"> 
                                                            <div class="col-md-3">
                                                                <label for="emp_joining_date">Joining Date</label>
                                                                <input type="date" name="emp_joining_date" id="emp_joining_date" class="form-control" value="<?=(!empty($dataRow->emp_joining_date))?$dataRow->emp_joining_date:date("Y-m-d")?>" max="<?=(!empty($dataRow->emp_joining_date))?$dataRow->emp_joining_date:date("Y-m-d")?>" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="emp_category">Emp Category</label>
                                                                <select name="emp_category" id="emp_category" class="form-control single-select req">
                                                                    <option value="">Select Category</option>
                                                                    <?php
                                                                        foreach($categoryData as $row):
                                                                            $selected = (!empty($dataRow->emp_category) && $row->id == $dataRow->emp_category)?"selected":"";
                                                                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->category.'</option>';
                                                                        endforeach;
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="emp_type">Employee Type</label>
                                                                <select name="emp_type" id="emp_type" class="form-control single-select req " >
                                                                    <option value="">Select Type</option>
                                                                    <option value="1" <?=(!empty($dataRow->emp_type) && $dataRow->emp_type == "1")?"selected":""?>>Permanent (Fix)</option>
                                                                    <option value="2" <?=(!empty($dataRow->emp_type) && $dataRow->emp_type == "2")?"selected":""?>>Permanent (Hourly)</option>
                                                                    <option value="3" <?=(!empty($dataRow->emp_type) && $dataRow->emp_type == "3")?"selected":""?>>Temporary</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="emp_type">Employee Type</label>
                                                                <select name="emp_grade" id="emp_grade" class="form-control single-select req " >
                                                                    <option value="">Select Grade</option>
                                                                    <option value="A" <?=(!empty($dataRow->emp_grade) && $dataRow->emp_grade == "A")?"selected":""?>>Class A</option>
                                                                    <option value="B" <?=(!empty($dataRow->emp_grade) && $dataRow->emp_grade == "B")?"selected":""?>>Class B</option>
                                                                    <option value="C" <?=(!empty($dataRow->emp_grade) && $dataRow->emp_grade == "C")?"selected":""?>>Class C</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="emp_dept_id">Department</label>
                                                                <select name="emp_dept_id" id="emp_dept_id" class="form-control single-select req">
                                                                    <option value="">Select Department</option>
                                                                    <?php
                                                                        foreach($deptRows as $row):
                                                                            $selected = (!empty($dataRow->emp_dept_id) && $row->id == $dataRow->emp_dept_id)?"selected":"";
                                                                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                                                                        endforeach;
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4 from-group">
                                                                <label for="emp_designation">Designation</label>
                                                                <select name="emp_designation" id="emp_designation" class="form-control single-select req" tabindex="-1">
                                                                    <option value="">Select Designation</option>
                                                                    <?php
                                                                        foreach($descRows as $row):
                                                                            $selected = (!empty($dataRow->emp_designation) && $row->id == $dataRow->emp_designation)?"selected":"";
                                                                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->title.'</option>';
                                                                        endforeach;
                                                                    ?>
                                                                </select>
                                                                <input type="hidden" id="designationTitle" name="designationTitle" value="" />
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="emp_sys_desc_id">System Designation</label>
                                                                <select name="sysdescSelect" id="sysdescSelect" data-input_id="emp_sys_desc_id" class="form-control jp_multiselect" multiple="multiple">
                                                                    <?php
                                                                     foreach($systemDesignation as $key=>$value) : 
                                                                        $selected= $emp_sys_desc_idArr = (!empty($dataRow->emp_sys_desc_id)) ? explode(',',$dataRow->emp_sys_desc_id) : array();
                                                                        if(!empty($dataRow->emp_sys_desc_id) && in_array($key,$emp_sys_desc_idArr)){$selected = "selected";}else{ $selected=''; }
                                                                        echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                                                                    endforeach;
                                                                    ?>
                                                                     <input type="hidden" id="emp_sys_desc_id" name="emp_sys_desc_id" value="" />
                                                                </select>
                                                            </div>
															
															<div class="col-md-4 form-group">
																<label for="lt_policy">Late In Policy</label>
																<select name="lt_policy" id="lt_policy" class="form-control single-select">
																	<option value="">Select Policy</option>
																	<?php
																		foreach($attendancePolicies as $row):
																		if($row->policy_type==1):
																			$selected = (!empty($dataRow->lt_policy) && $row->id == $dataRow->lt_policy)?"selected":"";
																			echo '<option value="'.$row->id.'" '.$selected.'>'.$row->policy_name.'</option>';
																		endif;
																		endforeach;
																	?>
																</select>
															</div>															
															<div class="col-md-4 form-group">
																<label for="eo_policy">Early Out Policy</label>
																<select name="eo_policy" id="eo_policy" class="form-control single-select">
																	<option value="">Select Policy</option>
																	<?php
																		foreach($attendancePolicies as $row):
																		if($row->policy_type==2):
																			$selected = (!empty($dataRow->eo_policy) && $row->id == $dataRow->eo_policy)?"selected":"";
																			echo '<option value="'.$row->id.'" '.$selected.'>'.$row->policy_name.'</option>';
																		endif;
																		endforeach;
																	?>
																</select>
															</div>															
															<div class="col-md-4 form-group">
																<label for="lp_policy">L.W.P. Policy</label>
																<select name="lp_policy" id="lp_policy" class="form-control single-select">
																	<option value="">Select Policy</option>
																	<?php
																		foreach($attendancePolicies as $row):
																		if($row->policy_type==3):
																			$selected = (!empty($dataRow->lp_policy) && $row->id == $dataRow->lp_policy)?"selected":"";
																			echo '<option value="'.$row->id.'" '.$selected.'>'.$row->policy_name.'</option>';
																		endif;
																		endforeach;
																	?>
																</select>
															</div>	
                                                            <div class="col-md-3 form-group">
                                                                <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
                                                                <input type="hidden" name="form_type" value="workprofile" />
                                                                
                                                                <label for="ot_applicable">OT Applicable?</label>
                                                                <select name="ot_applicable" id="ot_applicable" class="form-control single-select">
																	<option value="0" <?=($dataRow->ot_applicable == "0")?"selected":""?>>No</option>
                                                                    <option value="1" <?=(!empty($dataRow->ot_applicable) && $dataRow->ot_applicable == "1")?"selected":""?>>Yes</option>
                                                                </select>
                                                            </div>													
															<div class="col-md-3 form-group">
																<label for="shl_policy">Short Leave(<small>Days</small>)</label>
																<input type="text" name="shl_policy" id="shl_policy" class="form-control numericOnly" value="<?=(!empty($dataRow->shl_policy))?$dataRow->shl_policy:"0"?>">
															</div>
															<div class="col-md-3 form-group">
                                                                <label for="traveling_charge">Traveling Charge</label>
                                                                <input type="text" name="traveling_charge" id="traveling_charge" class="form-control floatOnly" value="<?=(!empty($dataRow->traveling_charge))?$dataRow->traveling_charge:""?>">
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="salary_code">Salary Code</label>
                                                                <select name="salary_code" id="salary_code" class="form-control single-select req">
                                                                    <option value="">Select Salary Code</option>
                                                                    <option value="P" <?=(!empty($dataRow->salary_code) && $dataRow->salary_code == "P")?"selected":""?> >P</option>
                                                                    <option value="Z" <?=(!empty($dataRow->salary_code) && $dataRow->salary_code == "Z")?"selected":""?> >Z</option>
                                                                </select>
                                                            </div>	
                                                            <div class="col-md-3 form-group">
                                                                <label for="pf_applicable">PF Applicable</label>
                                                                <select name="pf_applicable" id="pf_applicable" class="form-control " >
                                                                    <option value="0" <?=(!empty($dataRow->pf_applicable) && $dataRow->pf_applicable == "0")?"selected":""?>>No</option>
                                                                    <option value="1" <?=(!empty($dataRow->pf_applicable) && $dataRow->pf_applicable == "1")?"selected":""?>>Yes</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="pf_no">PF Number</label>
                                                                <input type="text" name="pf_no" id="pf_no" class="form-control" value="<?=(!empty($dataRow->pf_no))?$dataRow->pf_no:""?>" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="uan_no">UAN Number</label>
                                                                <input type="text" name="uan_no" id="uan_no" class="form-control" value="<?=(!empty($dataRow->uan_no))?$dataRow->uan_no:""?>" />
                                                            </div>

                                                            <div class="col-md-3 form-group">
                                                                <label for="sal_pay_mode">Payment Mode</label>
                                                                <select name="sal_pay_mode" id="sal_pay_mode" class="form-control single-select">
                                                                    <option value="">Select Payment Mode</option>
                                                                    <option value="CASH" <?=(!empty($dataRow->sal_pay_mode) && $dataRow->sal_pay_mode == "CASH")?"selected":""?> >CASH</option>
                                                                    <option value="BANK" <?=(!empty($dataRow->sal_pay_mode) && $dataRow->sal_pay_mode == "BANK")?"selected":""?> >BANK</option>
                                                                    <option value="CB" <?=(!empty($dataRow->sal_pay_mode) && $dataRow->sal_pay_mode == "CB")?"selected":""?> >CASH / BANK</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-6 form-group">
                                                                <label for="bank_name">Bank Name</label>
                                                                <input type="text" name="bank_name" class="form-control" value="<?=(!empty($dataRow->bank_name))?$dataRow->bank_name:""?>" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="account_no">Account No</label>
                                                                <input type="text" name="account_no" class="form-control" value="<?=(!empty($dataRow->account_no))?$dataRow->account_no:""?>" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="ifsc_code">Ifsc Code</label>
                                                                <input type="text" name="ifsc_code" class="form-control" value="<?=(!empty($dataRow->ifsc_code))?$dataRow->ifsc_code:""?>" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer" align="right">
                                                        <button type="button" class="btn btn-outline-success btn-save" onclick="save('workProfile','editProfile');"><i class="fa fa-check"></i> Save</button>
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="pills-documents-tab">
                                                <form id="getEmpDocuments" enctype="multipart/form-data">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <input type="hidden" name="id" id="id" value="" />
                                                            <input type="hidden" name="emp_id" id="emp_id" value="<?=$emp_id ?>" />

                                                            <div class="col-md-3 form-group">
                                                                <label for="doc_name">Document Name</label>
                                                                <input type="text" name="doc_name" id="doc_name" class="form-control req" value="" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="doc_no">Document No.</label>
                                                                <input type="text" name="doc_no" id="doc_no" class="form-control req" value="" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="doc_file">Document File</label>
                                                                <input type="file" name="doc_file" id="doc_file" class="form-control-file" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="doc_type">Document Type</label>
                                                                <select name="doc_type" id="doc_type" class="form-control req">
                                                                    <option value="">Select Document Type </option>
                                                                    <option value="1">Extra Document</option>
                                                                    <option value="2">Aadhar Card</option>
                                                                    <option value="3">Basic Rules</option>
                                                                </select>              
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer" align="right">
                                                        <button type="button" class="btn btn-outline-success btn-save" onclick="saveEmpDocuments('getEmpDocuments','saveEmpDocumentsParam');"><i class="fa fa-check"></i> Save</button>
                                                    </div>
                                                </form>
                                                <hr>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="inspection" class="table table-bordered align-items-center">
                                                            <thead class="thead-info">
                                                                <tr>
                                                                    <th style="width:5%;">#</th>
                                                                    <th class="text-center">Document Name</th>
                                                                    <th class="text-center">Document No.</th>                        
                                                                    <th class="text-center">Document Type</th>
                                                                    <th class="text-center">Document File</th>
                                                                    <th class="text-center" style="width:10%;">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="docBody">
                                                                <?php
                                                                    if(!empty($docData)):
                                                                        $i=1;
                                                                        foreach($docData as $row):
                                                                            echo '<tr>
                                                                                        <td class="text-center">'.$i++.'</td>
                                                                                        <td class="text-center">'.$row->doc_name.'</td>
                                                                                        <td class="text-center">'.$row->doc_no.'</td>
                                                                                        <td class="text-center">'.$row->doc_type_name.'</td>
                                                                                        <td class="text-center">'.((!empty($row->doc_file))?'<a href="'.base_url('assets/uploads/emp_documents/'.$row->doc_file).'" target="_blank"><i class="fa fa-download"></i></a>':"") .'</td>
                                                                                        <td class="text-center">
                                                                                            <button type="button" onclick="trashEmpDocuments('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                                                                        </td>
                                                                                    </tr>';
                                                                        endforeach;
                                                                    else:
                                                                        echo '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
                                                                    endif;
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        
                                            <div class="tab-pane fade" id="nomination" role="tabpanel" aria-labelledby="pills-nomination-tab">
                                                <form id="getEmpNom">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <input type="hidden" name="id" id="id" class="id" value="" />
                                                            <input type="hidden" name="emp_id" id="emp_id" value="<?= (!empty($dataRow->emp_id)) ? $dataRow->emp_id : $emp_id; ?>" />
                                                            <div class="col-md-4 form-group">
                                                                <label for="nom_name">Name</label>
                                                                <input type="text" id="nom_name" name="nom_name" class="form-control req" placeholder="Name" value="" />
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="nom_gender">Gender</label>
                                                                <select id="nom_gender" name="nom_gender" class="form-control single-select">
                                                                    <?php
                                                                        foreach ($genderData as $value) :
                                                                            echo '<option value="' . $value . '">' . $value . '</option>';
                                                                        endforeach;
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="nom_relation">Relation</label>
                                                                <input type="text" id="nom_relation" name="nom_relation" class="form-control req" placeholder="Relation" value="" />
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="nom_dob">Date of birth</label>
                                                                <input type="date" id="nom_dob" name="nom_dob" class="form-control req" placeholder="mm-dd-yyyy" value="" />
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="nom_proportion">Proportion</label>
                                                                <input type="text" id="nom_proportion" name="nom_proportion" class="form-control" placeholder="Proportion" value="" />
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-save mt-30" onclick="updateEmpNom('getEmpNom','updateEmpNom');"><i class="fa fa-check"></i> Save</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="empNomtbl" class="table table-bordered align-items-center">
                                                            <thead class="thead-info">
                                                                <tr>
                                                                    <th style="width:5%;">#</th>
                                                                    <th>Name</th>
                                                                    <th>Gender</th>
                                                                    <th>Relation</th>
                                                                    <th>Date of birth</th>
                                                                    <th>Proportion</th>
                                                                    <th class="text-center" style="width:10%;">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="empNomBody">
                                                                <?php
                                                                    if (!empty($empNom)) : $i = 1;
                                                                        foreach ($empNom as $row) :
                                                                            echo '<tr>
                                                                                    <td>' . $i++ . '</td>
                                                                                    <td>' . $row->nom_name . '</td>
                                                                                    <td>' . $row->nom_gender . '</td>
                                                                                    <td>' . $row->nom_relation . '</td>
                                                                                    <td>' . $row->nom_dob . '</td>
                                                                                    <td>' . $row->nom_proportion . ' </td>
                                                                                    <td class="text-center">
                                                                                        <button type="button" onclick="trashEmpNom('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                                                                    </td>
                                                                                </tr>';
                                                                        endforeach;
                                                                    else:
                                                                        echo '<tr><td colspan="7" style="text-align:center;">No Data Found</td></tr>';
                                                                    endif;
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="education" role="tabpanel" aria-labelledby="pills-education-tab">
                                                <form id="getEmpEdu">                                                    
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <input type="hidden" name="id" id="id" class="id" value="" />
                                                            <input type="hidden" name="emp_id" id="emp_id" value="<?= (!empty($dataRow->emp_id)) ? $dataRow->emp_id : $emp_id; ?>" />
                                                            <div class="col-md-3 form-group">
                                                                <label for="course">Course</label>
                                                                <input type="text" id="course" name="course" class="form-control req" placeholder="Course" value="" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="university">University/Board</label>
                                                                <input type="text" id="university" name="university"  class="form-control" placeholder="University/Board" value="" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="passing_year">Passing Year</label>
                                                                <input type="text" id="passing_year" name="passing_year"  class="form-control req" placeholder="Passing Year" value="" />
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label for="grade">Per./Grade</label>
                                                                <div class="input-group">
                                                                    <input type="text" id="grade" name="grade"  class="form-control req" placeholder="Per./Grade" value="" />
                                                                    <button type="button" class="btn btn-outline-success btn-save float-right ml-2" onclick="updateEmpEdu('getEmpEdu','updateEmpEdu');"><i class="fa fa-check"></i> Save</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="empEdutbl" class="table table-bordered align-items-center">
                                                            <thead class="thead-info">
                                                                <tr>
                                                                    <th style="width:5%;">#</th>
                                                                    <th>Course</th>
                                                                    <th>University/Board</th>
                                                                    <th>Passing Year</th>
                                                                    <th>Per./Grade</th>
                                                                    <th class="text-center" style="width:10%;">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="empEduBody">
                                                                <?php
                                                                    if (!empty($empEdu)) :
                                                                        $i = 1;
                                                                        foreach ($empEdu as $row) :
                                                                            echo '<tr>
                                                                                <td>' . $i++ . '</td>
                                                                                <td>' . $row->course . '</td>
                                                                                <td>' . $row->university . '</td>
                                                                                <td>' . $row->passing_year . ' </td>
                                                                                <td>' . $row->grade . '</td>
                                                                                <td class="text-center">
                                                                                    <button type="button" onclick="trashEmpEdu('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                                                                </td>
                                                                            </tr>';
                                                                        endforeach;
                                                                    else:
                                                                        echo '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
                                                                    endif;
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="tab-pane fade" id="salary" role="tabpanel" aria-labelledby="pills-salary-tab">
                                                <form id="empCTCStructure" class="p-3">
                                                    <div class="row">
														<div class="col-md-5 form-group">
															<input type="hidden" name="ctc_emp_id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
															<input type="hidden" name="salary_on_paper" id="salary_on_paper" value="<?= (!empty($dataRow->salary_on_paper)) ? $dataRow->salary_on_paper : 0; ?>" />
															<div class="error ctc_emp_id"></div>
															<label for="salary_actual">Actual Salary (8 Hour Wage)</label>
															<input type="text" name="salary_actual" id="salary_actual" class="form-control numericOnly req" value="<?= (!empty($dataRow->salary_actual)) ? $dataRow->salary_actual : ''; ?>" />
														</div>
                                                        <!--<div class="col-md-5 form-group">
															<label for="salary_on_paper">Salary On Paper</label>
															<input type="text" name="salary_on_paper" id="salary_on_paper" class="form-control numericOnly" value="<?= (!empty($dataRow->salary_on_paper)) ? $dataRow->salary_on_paper : ''; ?>" />
														</div>-->
														
													    <div class="col-md-2 form-group">
														    <button type="button" class="btn btn-success btn-save float-right mt-30" onclick="saveSalary('empCTCStructure');">Save</button>
													    </div>
													</div>
                                                </form>
                                            </div>
                                            
                                            <div class="tab-pane fade" id="staffSkill" role="tabpanel" aria-labelledby="pills-staffSkill-tab">
                                                <form id="getStaffSkill">                                                    
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <input type="hidden" name="emp_id" id="emp_id" value="<?=$emp_id ?>" />
                                                            <table class="table excel_table table-bordered">
                                                                <thead class="thead-info">
                                                                    <tr>
                                                                        <th style="width:10%;text-align:center;">#</th>
                                                                        <th style="width:60%;">Skill</th>
                                                                        <th style="width:30%;">Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    if (!empty($skillData)) :
                                                                        $i = 1;
                                                                        $html = "";$ct = "";
                                                                        if(!empty($staffData)){
                                                                            foreach ($staffData as $row) :
                                                                                  $pid = (!empty($row->id)) ? $row->id : "";
                                                                                  $ct =  ' <option value="Y" '.(($row->skill_status == 'Y')?'selected':'').'> [Y] For Skill Available </option>
                                                                                          <option value="X" '.(($row->skill_status == 'X')?'selected':'').'> [X] For Skill have not Required </option>
                                                                                          <option value="G" '.(($row->skill_status == 'G')?'selected':'').'> [G] For Skill require but have not available </option>';
                                                                                      echo '<tr>
                                                                                          <td class="text-center">' . $i . '</td>
                                                                                          <td>' . $row->skill . '</td>
                                                                                          <td>
                                                                                              <select name="skill_status['.($i-1).']" id="skill_status" class="form-control">'.$ct.'</select>
                                                                                              <input type="hidden" name="id[]" id="id" value="'.((!empty($row->id))?$row->id:"").'" />
                                                                                              <input type="hidden" name="skill_id[]" id="skill_id" value="' . $row->skill_id . '" />
                                                                                          </td> 
                                                                                      </tr>'; $i++;
                                                                              endforeach;
                                                                        }else{
                                                                            foreach ($skillData as $row) :
                                                                                  $pid = (!empty($row->id)) ? $row->id : "";
                                                                                  $ct =  ' <option value="Y" > [Y] For Skill Available </option>
                                                                                          <option value="X" > [X] For Skill have not Required </option>
                                                                                          <option value="G" > [G] For Skill require but have not available </option>';
                                                                                      echo '<tr>
                                                                                          <td class="text-center">' . $i . '</td>
                                                                                          <td>' . $row->skill . '</td>
                                                                                          <td>
                                                                                              <select name="skill_status['.($i-1).']" id="skill_status" class="form-control">'.$ct.'</select>
                                                                                              <input type="hidden" name="id[]" id="id" value="" />
                                                                                              <input type="hidden" name="skill_id[]" id="skill_id" value="' . $pid . '" />
                                                                                          </td>
                                                                                      </tr>'; $i++;
                                                                              endforeach;
                                                                        }
                                                                        
                                                                    else :
                                                                        echo '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
                                                                    endif;
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn waves-effect waves-light saveButton btn-outline-success btn-save mt-30" onclick="updateStaffSkill('getStaffSkill','updateStaffSkill');"><i class="fa fa-check"></i> Save </button>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            
                                            
                                            <div class="tab-pane fade" id="facility" role="tabpanel" aria-labelledby="pills-facility-tab">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <form id="getEmpFacility"> 
                                                                <div class="row">
                                                                    <input type="hidden" name="id" id="id" class="id" value="" />
                                                                    <input type="hidden" name="emp_id" id="emp_id" value="<?= (!empty($dataRow->emp_id)) ? $dataRow->emp_id : $emp_id; ?>" />
                                                                    <div class="col-md-4 form-group">
                                                                        <label for="issue_date">Issue Date</label>
                                                                        <input type="date" name="issue_date" id="issue_date" class="form-control" value="<?=(!empty($dataRow->issue_date))?$dataRow->issue_date:date('Y-m-d'); ?>">
                                                                    </div>
                                                                    <div class="col-md-4 form-group">
                                                                        <label for="type">Type</label>
                                                                        <select name="type" id="type" class="form-control single-select req">
                                                                            <option value="">Select Type</option>
                                                                            <?php
                                                                                foreach($typeData as $row):
                                                                                    $selected = (!empty($dataRow->type) && $row->id == $dataRow->type)?"selected":"";
                                                                                    echo '<option value="'.$row->id.'" '.$selected.'>'.$row->remark.'</option>';
                                                                                endforeach;
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-4 form-group">
                                                                        <label for="specs">Specification/Size</label>
                                                                        <input type="text" id="specs" name="specs"  class="form-control req" value="" />
                                                                    </div>
                                                                    <div class="col-md-9 form-group">
                                                                        <label for="description">Description</label>
                                                                        <input type="text" id="description" name="description"  class="form-control" placeholder="description" value="" />
                                                                    </div>
                                                                    <div class="col-md-3 form-group">
                                                                        <button type="button" class="btn btn-outline-success btn-save float-right mt-30" onclick="updateEmpFacility('getEmpFacility','updateEmpFacility');"><i class="fa fa-check"></i> Save</button>
                                                                    </div>
                                                                </div>
                                                            
                                                            </form>
                                                        </div>   
                                                    </div>
                                                </div>

                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table id="empFacilitytbl" class="table table-bordered align-items-center">
                                                            <thead class="thead-info">
                                                                <tr>
                                                                    <th style="width:5%;">#</th>
                                                                    <th>Issue Date</th>
                                                                    <th>Type</th>
                                                                    <th>Specification</th>
                                                                    <th>Description </th>
                                                                    <th class="text-center" style="width:10%;">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="empFacilityBody">
                                                                <?php
                                                                    if (!empty($empFacility)) :
                                                                        $i = 1;
                                                                        foreach ($empFacility as $row) :
                                                                            $type="";
                                                                            if($row->type == 1){
                                                                                $type="Uniform";
                                                                            }elseif($row->type == 2){
                                                                                
                                                                                $type="Quater";
                                                                            }elseif($row->type == 3){
                                                                                
                                                                                $type="Mobile";
                                                                            }
                                                                            echo '<tr>
                                                                                <td>' . $i++ . '</td>
                                                                                <td>' . (formatDate($row->issue_date)) . '</td>
                                                                                <td>' . $row->ficility_type . '</td>
                                                                                <td>' . $row->specs . '</td>
                                                                                <td>' . $row->description . ' </td>
                                                                                <td class="text-center">
                                                                                    <button type="button" onclick="trashEmpFacility('.$row->id.','.$row->emp_id.');" class="btn btn-outline-danger waves-effect waves-light btn-delete permission-remove"><i class="ti-trash"></i></button>
                                                                                </td>
                                                                            </tr>';
                                                                        endforeach;
                                                                    else:
                                                                        echo '<tr><td colspan="6" style="text-align:center;">No Data Found</td></tr>';
                                                                    endif;
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="tab-pane fade" id="icard" role="tabpanel" aria-labelledby="pills-icard-tab">
                                                <form id="getEmpSalary">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <br>
                                                            <div class="col-md-2"></div>
                                                            <div class="col-md-8">
                                                                <div class="icard-1">
                                                                    <div class="icard-header">
                                                                        <table>
                                                                            <tr>
                                                                                <td rowspan="2" style="width:22%;"><img src="<?=base_url('assets/images/vertical_logo.png') ?>" class="logo-img"></td>
                                                                                <td class="company_title text-center" style="width:52%;"><?=$companyInfo->company_name?></td>
                                                                                <td rowspan="2" class="company_address text-center" style="vertical-align:top;width:26%;">Form No. 36</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="company_address"><?=$companyInfo->company_address?><br>Tel. : <?=$companyInfo->company_phone?></td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                    <div class="signature-img"><img src="<?=base_url('assets/uploads/emp_profile/'.$profile_pic) ?>" alt=""></div>
                                                                    <div class="signature-details">
                                                                        <h2 class="title"><?=$empData->emp_name?></h2>
                                                                        <span class="designation"><i><?=$empData->title.' - '.$empData->name?></i> | EMP CODE : <?=$empData->emp_code?></span>
                                                                    </div>
                                                                    <div class="signature-content">
                                                                        <table style="width:100%;">
                                                                            <tr>
                                                                                <th>DOB</th>
                                                                                <td>: <?=date('d-m-Y',strtotime($empData->emp_birthdate))?></td>
                                                                                <th>DOJ</th>
                                                                                <td>: <?=date('d-m-Y',strtotime($empData->emp_joining_date))?></td>
                                                                                <th>Phone</th>
                                                                                <td>: <?=$empData->emp_contact?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th>Address</th>
                                                                                <td colspan="5">: <?=$empData->emp_address?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="6" style="font-size:10px;"><i>This card must be with a person all the time during field work/duty hours. Loss off the card must be reported immediately to HR Dept. If this card found, please return to the company address.</i></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="6" height="25"></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th colspan="3" style="font-size:14px;text-align:center;">Authorized Signatury</th>
                                                                                <th colspan="3" style="font-size:14px;text-align:center;">Employee's Signature</th>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>   
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Column -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    //setTimeout(function(){$("#pills-salary-tab").trigger('click'); },1000);

    $(document).on("change", ".uploadProfileInput", function () {
        var triggerInput = this;
        var currentImg = $(this).closest(".pic-holder").find(".pic").attr("src");
        var holder = $(this).closest(".pic-holder");
        var wrapper = $(this).closest(".profile-pic-wrapper");
        $(wrapper).find('[role="alert"]').remove();
        triggerInput.blur();
        var files = !!this.files ? this.files : [];
        if (!files.length || !window.FileReader) {return;}
        var emp_id = $("#emp_id").val();
        if (/^image/.test(files[0].type)) {
            // only image file
            var reader = new FileReader(); // instance of the FileReader
            reader.readAsDataURL(files[0]); // read the local file

            reader.onloadend = function () {
                $(holder).addClass("uploadInProgress");
                $(holder).find(".pic").attr("src", this.result);
                $(holder).append('<div class="upload-loader"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
                
                var fd = new FormData();
                var files_pics = $('#newProfilePhoto')[0].files;
                if(files_pics.length > 0 ){
                    fd.append('emp_profile',files_pics[0]);
                    fd.append('emp_id',emp_id);
                    $.ajax({
                        url: base_url + controller + '/updateProfilePic',
                        data:fd,
                        type: "POST",
                        processData:false,
                        contentType:false,
                        cache: false,
                        global:false,
                        dataType:"json",
                    }).done(function(data){
                        if(data.status===0){
                            toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                        }else if(data.status==1){ 
                            toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                        }
                        $(holder).removeClass("uploadInProgress");
                        $(holder).find(".upload-loader").remove();
                        $(triggerInput).val("");
                    });
                }
            };
        }
        else{
            toastr.error('Please choose the valid image.', 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            $(wrapper).find('role="alert"').remove();
        }
    });

    $(document).on('keyup change','.claculateSummary',function(){
        var salary_duration = $("input[name=salary_duration]").val();

        var actual_salary = 0;
        if(salary_duration == "H"){
            var ctcAmount = $("#ctc_amount").val() || 0;
            var hourly = parseFloat(parseFloat(ctcAmount) / 8).toFixed(2);
            var day = parseFloat(parseFloat(hourly) * 11).toFixed(2);
            actual_salary = parseFloat(parseFloat(day) * 26).toFixed(0);
        }
        var orgBasicAmount = 0;var orgHraAmount = 0;
        $(".orgEarningHead").each(function(){
            var value = $(this).val() || 0;
            var id = $(this).data('id') || 0;
            var col = $(this).data('col') || "";
            var method = $("#"+id+"_cal_method").val() || 0;
            var system_code = $("#"+id+"_system_code").val() || "";

            var orgAmount = 0;
            if(method == 1){
                orgAmount = parseFloat(value).toFixed(0);
            }else{
                value = parseFloat(((parseFloat(orgBasicAmount) * parseFloat(value))/100)).toFixed(0);
                orgAmount = parseFloat(value).toFixed(0);
            }

            if(system_code == "basic" && salary_duration == "H"){ orgAmount = actual_salary; }
            if(system_code == "hra"){ orgHraAmount = orgAmount; }
            if(system_code == "basic"){ orgBasicAmount = orgAmount; }

            if(system_code == "ca"){
                var travelingCharge = $("#workProfile #traveling_charge").val() || 0;
                if(parseFloat(travelingCharge) > 0){
                    orgAmount = parseFloat((parseFloat(travelingCharge) * 26) + parseFloat(orgAmount)).toFixed(0);
                }
            }

            $("#"+id+col+"summary_value").html(0);
            $("#"+id+col+"summary_value").html(orgAmount);
        });

        var basicAmount = 0;var hraAmount = 0;
        $(".earningHead").each(function(){
            var value = $(this).val() || 0;
            var id = $(this).data('id') || 0;
            var col = $(this).data('col') || "";
            var method = $("#"+id+"_cal_method").val() || 0;
            var system_code = $("#"+id+"_system_code").val() || "";

            var amount = 0;
            if(method == 1){
                amount = parseFloat(parseFloat(value)).toFixed(0);
            }else{
                value = parseFloat(((parseFloat(basicAmount) * parseFloat(value))/100)).toFixed(0);
                amount = parseFloat(parseFloat(value)).toFixed(0);
            }

            if(system_code == "basic" && salary_duration == "H"){ basicAmount = amount; }
            if(system_code == "hra"){ hraAmount = amount; }

            $("#"+id+col+"summary_value").html(0);
            $("#"+id+col+"summary_value").html(amount);
        });

        var grossOrgAmtArray = $(".summaryOrgEarnAmount").map(function(){ return $(this).html(); }).get();
        var grossOrgAmtSum = 0;
        $.each(grossOrgAmtArray,function(){grossOrgAmtSum += parseFloat(this) || 0;}); 
        $("#_org_gross_salary").html(grossOrgAmtSum);
        
        var grossAmtArray = $(".summaryAmount").map(function(){ return $(this).html(); }).get();
        var grossAmtSum = 0;
        $.each(grossAmtArray,function(){grossAmtSum += parseFloat(this) || 0;}); 
        $("#_gross_salary").html(grossAmtSum);
        
        var gross_diff = parseFloat(parseFloat(grossOrgAmtSum) - parseFloat(grossAmtSum)).toFixed(0);
        $("#gross_diff").html(gross_diff);

        var cl_charge = $("#ctc_cl_charge").val() || 0; 
        var cd_charge = $("#ctc_cd_charge").val() || 0;       
        $(".orgDeductionHead").each(function(){
            var value = $(this).val() || 0;
            var id = $(this).data('id') || 0;
            var col = $(this).data('col') || "";
            var method = $("#"+id+"_cal_method").val() || 0;
            var system_code = $("#"+id+"_system_code").val() || "";

            if(salary_duration == "H"){
                value = $("#"+id+"_cal_value").val() || 0;
            }
            
            
            var orgAmount = 0;
            if($.inArray(system_code,["pf","pt","lwf","ccl","ccd"]) !== -1){
                if(system_code == "pf"){
                    var basic_value = 0;
                    basic_value = parseFloat(parseFloat(grossAmtSum) - parseFloat(hraAmount)).toFixed(0);
                    if($("#workProfile #pf_applicable").val() == 1 && parseFloat(basic_value) >= 15000){
                        orgAmount = parseFloat((15000 * parseFloat(value)) / 100).toFixed(0);
                    }else{
                        orgAmount = parseFloat((parseFloat(basic_value) * parseFloat(value)) / 100).toFixed(0);
                    }
                }

                if(system_code == "pt" && parseFloat(value) > 0){
                    if(parseFloat(grossAmtSum) >= 12000){
                        orgAmount = parseFloat(value).toFixed(0);
                    }
                }

                if(system_code == "lwf" && parseFloat(value) > 0){
                    orgAmount = parseFloat(value).toFixed(0);
                }
                
                if(system_code == "ccl"){
                    orgAmount = parseFloat((26 * parseFloat(cl_charge))).toFixed(0);
                }

                if(system_code == "ccd"){
                    orgAmount = parseFloat((26 * parseFloat(cd_charge))).toFixed(0);
                }
            }else{
                if(method == 1){
                    orgAmount = parseFloat(value).toFixed(0);
                }else{
                    value = parseFloat(((parseFloat(grossOrgAmtSum) * parseFloat(value))/100)).toFixed(0);
                    orgAmount = parseFloat(value).toFixed(0);
                }
            }

            $("#"+id+col+"summary_value").html(0);
            $("#"+id+col+"summary_value").html(orgAmount);
        });

        $(".deductionHead").each(function(){
            var value = $(this).val() || 0;
            var id = $(this).data('id') || 0;
            var col = $(this).data('col') || "";
            var method = $("#"+id+"_cal_method").val() || 0;
            var system_code = $("#"+id+"_system_code").val() || "";
            
            var amount = 0;
            if($.inArray(system_code,["pf","pt","lwf","ccl","ccd"])  !== -1){
                if(system_code == "pf"){
                    var basic_value = 0;
                    var basic_value = parseFloat(parseFloat(grossAmtSum) - parseFloat(hraAmount)).toFixed(0);
                    if($("#workProfile #pf_applicable").val() == 1 && parseFloat(basic_value) >= 15000){
                        amount = parseFloat((15000 * parseFloat(value)) / 100).toFixed(0);
                    }else{
                        amount = parseFloat((parseFloat(basic_value) * parseFloat(value)) / 100).toFixed(0);
                    }
                }

                if(system_code == "pt" && parseFloat(value) > 0){
                    if(parseFloat(grossAmtSum) >= 12000){
                        amount = parseFloat(value).toFixed(0);
                    }
                }

                if(system_code == "lwf" && parseFloat(value) > 0){
                    amount = parseFloat(value).toFixed(0);
                }
                
                if(system_code == "ccl"){
                    amount = parseFloat((26 * parseFloat(cl_charge))).toFixed(0);
                }

                if(system_code == "ccd"){
                    amount = parseFloat((26 * parseFloat(cd_charge))).toFixed(0);
                }
            }else{
                if(method == 1){
                    amount = parseFloat(value).toFixed(0);
                }else{
                    value = parseFloat(((parseFloat(grossAmtSum) * parseFloat(value))/100)).toFixed(0);
                    amount = parseFloat(value).toFixed(0);
                }
            }

            $("#"+id+col+"summary_value").html(0);
            $("#"+id+col+"summary_value").html(amount);
        });

        var deductOrgAmtArray = $(".summaryOrgDeductAmount").map(function(){ return $(this).html(); }).get();
        var deductOrgAmtSum = 0;
        $.each(deductOrgAmtArray,function(){deductOrgAmtSum += parseFloat(this) || 0;});
        
        var deductAmtArray = $(".summaryDeductionAmount").map(function(){ return $(this).html(); }).get();
        var deductAmtSum = 0;
        $.each(deductAmtArray,function(){deductAmtSum += parseFloat(this) || 0;}); 

        var net_amount = parseFloat(parseFloat(grossAmtSum) - parseFloat(deductAmtSum)).toFixed(0);
        var org_net_amount = parseFloat(parseFloat(grossOrgAmtSum) - parseFloat(deductOrgAmtSum)).toFixed(0);
        $("#net_salary").html(net_amount);
        $("#org_net_salary").html(org_net_amount);

        if(salary_duration != "H"){$("#ctc_amount").val(net_amount);  $("#org_ctc_amount").val(org_net_amount);}
       
        
        var diff = parseFloat(parseFloat(org_net_amount) - parseFloat(net_amount)).toFixed(0);            
        $("#diff").html(diff);  
    });

    $(document).on('click','#pills-salary-tab,#loadSalaryStructure',function(){
        var emp_id = $("input[name=ctc_emp_id]").val();
        var format_id = $("#ctc_format :selected").val();
        $.ajax({
            url : base_url + controller + '/getEmpActiveSalaryStructure',
            type:'post',
            data : {emp_id:emp_id,format_id:format_id},
            dataType:'json',
            success:function(data){
                $(".ctcStructure").html(data.html);
                $("#ctc_format").val(data.format_id);
                $("#ctc_format").comboSelect();
                $("#ctc_amount").val(data.ctc_amount);
                $("#org_ctc_amount").val(data.org_ctc_amount);

                if(parseFloat(data.ctc_amount) > 0){
                    $(".claculateSummary").trigger('change');
                }
            }
        });
    });
});

function updateEmpNom(formId,fnsave){
	// var fd = $('#'+formId).serialize();
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(); //$('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            $("#empNomBody").html(data.tbodyData);
            $("#nom_name").val("");
            $("#nom_gender").val("");
            $("#nom_relation").val("");
            $("#nom_dob").val("");
            $("#nom_proportion").val("");
        }else{
			initTable();  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }		
	});
}

function trashEmpNom(id,emp_id,name='Record'){
	var send_data = { id:id, emp_id:emp_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/deleteEmpNom',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								initTable(); 
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                $("#empNomBody").html(data.tbodyData);
                            }
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function updateEmpEdu(formId,fnsave){
	// var fd = $('#'+formId).serialize();
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(); //$('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            $("#empEduBody").html(data.tbodyData);
            $("#course").val("");
            $("#university").val("");
            $("#passing_year").val("");
            $("#grade").val("");
        }else{
			initTable();  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }		
	});
}

function trashEmpEdu(id,emp_id,name='Record'){
	var send_data = { id:id, emp_id:emp_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/deleteEmpEdu',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								initTable(); 
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                $("#empEduBody").html(data.tbodyData);
                            }
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function calculateCTC(formId){
	setPlaceHolder();
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/calculateCTC',
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(response){
		if(response.status===0){
			$(".error").html("");
			$.each( response.message, function( key, value ) {$("."+key).html(value);});
		}
		else{
			$('.ctcStructure').html(response.ctcStructure);
        }
		
	});
}

function saveSalary(formId){
	setPlaceHolder();
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/saveSalary',
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(response){
		if(response.status===0){
			$(".error").html("");
			$.each( response.message, function( key, value ) {$("."+key).html(value);});
		}
		else{
			toastr.success(response.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }
		
	});
}

function saveEmpDocuments(formId,fnsave){
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form); 
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(); //$('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            $("#docBody").html(data.tbodyData);
            $("#doc_name").val("");
            $("#doc_no").val("");
            $("#doc_type").val("");
            $("#doc_file").val("");
        }else{
			initTable();  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }
				
	});
}

function trashEmpDocuments(id,emp_id,name='Record'){
	var send_data = { id:id, emp_id:emp_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/deleteEmpDocuments',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								initTable(); 
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                $("#docBody").html(data.tbodyData);
                            }
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function save(formId,fnsave){
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form); 
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable();   
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }else{
			initTable();  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }
				
	});
}
function updateStaffSkill(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/updateStaffSkill',
		data:fd,
		type: "POST",
		dataType:"json",
		success:function(data){
			if(data.status===0){
				$(".error").html("");
				$.each( data.message, function( key, value ) {$("."+key).html(value);});
			}else{
                initTable();
				toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}
		}
	});
}


function updateEmpFacility(formId,fnsave){
	// var fd = $('#'+formId).serialize();
	setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initTable(); //$('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            $("#empFacilityBody").html(data.tbodyData);
            $("#issue_date").val("");
            $("#type").val("");
            $("#specs").val("");
            $("#description").val("");
        }else{
			initTable();  $('#'+formId)[0].reset();$(".modal").modal('hide');   
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }		
	});
}

function trashEmpFacility(id,emp_id,name='Record'){
	var send_data = { id:id, emp_id:emp_id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/deleteEmpFacility',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								initTable(); 
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
                                $("#empFacilityBody").html(data.tbodyData);
                            }
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function updateEmpChargeableFacility(formId,fnsave){
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){  
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }else{ 
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }		
	});
}

function saveEmpSalaryStructure(formId,fnsave){
    setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form); 
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){ 
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
        }				
	});
}
</script>