<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="emp_id" id="emp_id" value="<?=(!empty($dataRow->emp_id))?$dataRow->emp_id:$emp_id; ?>" />

            <div class="col-md-6 form-group">
                <label for="old_pf_no">Old Pf No</label>
                <input type="text" name="old_pf_no" class="form-control" value="<?=(!empty($dataRow->old_pf_no))?$dataRow->old_pf_no:""?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="basic_ruls">Basic Rules & Regulation</label>
                <div class="input-group">
                    <input type="file" name="basic_ruls" class="form-control-file" style="width:90%;" />
                    <div class="input-group-append">
                        <?php if(!empty($dataRow->basic_ruls))
                        {?>
                            <a class="btn btn-sm btn-outline-info" href="<?=base_url('assets/uploads/emp_doc/basic rules & regulation/'.$dataRow->basic_ruls)?>" target="_blank" datatip="Download" flow="down" datatip="Download" flow="down"><i class="fa fa-download"></i></a>
                        <?php }
                        ?>
                    </div>
                </div>
                
            </div>            

            <hr style="width:100%;">
			<div class="col-md-12 row">
                <div class="col-md-12"><h5>Aadhar Details : </h5></div>
            </div>
            <div class="col-md-4 form-group">
                <label for="aadhar_name">Aadhar Name</label>
                <input type="text" name="aadhar_name" class="form-control" value="<?=(!empty($dataRow->aadhar_name))?$dataRow->aadhar_name:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="aadhar_no">Aadhar No</label>
                <input type="text" name="aadhar_no" class="form-control" value="<?=(!empty($dataRow->aadhar_no))?$dataRow->aadhar_no:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="aadhar_docs">Aadhar Image</label>
                <div class="input-group">
                <input type="file" name="aadhar_docs" class="form-control-file" style="width:80%;"/>
                    <div class="input-group-append">
                        <?php if(!empty($dataRow->aadhar_docs))
                        {?>
                            <a class="btn btn-sm btn-outline-info" href="<?=base_url('assets/uploads/emp_doc/aadhar_docs/'.$dataRow->aadhar_docs)?>" target="_blank"><i class="fa fa-download"></i></a>
                        <?php }
                        ?>
                    </div>
                </div>
                
            </div>       

            <hr style="width:100%;">
			<div class="col-md-12 row">
                <div class="col-md-12"><h5>Pan Details : </h5></div>
            </div>
            <div class="col-md-4 form-group">
                <label for="pan_name">Pan Name</label>
                <input type="text" name="pan_name" class="form-control" value="<?=(!empty($dataRow->pan_name))?$dataRow->pan_name:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="pan_no">Pan No</label>
                <input type="text" name="pan_no" class="form-control" value="<?=(!empty($dataRow->pan_no))?$dataRow->pan_no:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="pan_docs">Pan Image</label>
                <div class="input-group">
                <input type="file" name="pan_docs" class="form-control-file" style="width:80%;"/>
                    <div class="input-group-append">
                        <?php if(!empty($dataRow->pan_docs))
                        {?>
                            <a class="btn btn-sm btn-outline-info" href="<?=base_url('assets/uploads/emp_doc/pan_docs/'.$dataRow->pan_docs)?>" target="_blank" datatip="Download" flow="down"><i class="fa fa-download"></i></a>
                        <?php }
                        ?>
                    </div>
                </div>
                
            </div>       

            <hr style="width:100%;">
			<div class="col-md-12 row">
                <div class="col-md-12"><h5>Other Documents: </h5></div>
            </div>
            <div class="col-md-4 form-group">
                <label for="confirm_letter_docs">Confirmation Letter Image</label>
                <div class="input-group">
                <input type="file" name="confirm_letter_docs" class="form-control-file" style="width:80%;"/>
                    <div class="input-group-append">
                        <?php if(!empty($dataRow->confirm_letter_docs))
                        {?>
                            <a class="btn btn-sm btn-outline-info" href="<?=base_url('assets/uploads/emp_doc/confirm_letter_docs/'.$dataRow->confirm_letter_docs)?>" target="_blank" datatip="Download" flow="down"><i class="fa fa-download"></i></a>
                        <?php }
                        ?>
                    </div>
                </div>
                
            </div>   
            <div class="col-md-4 form-group">
                <label for="emp_detail_docs">Employee Detail Image</label>
                <div class="input-group">
                <input type="file" name="emp_detail_docs" class="form-control-file" style="width:80%;"/>
                    <div class="input-group-append">
                        <?php if(!empty($dataRow->emp_detail_docs))
                        {?>
                            <a class="btn btn-sm btn-outline-info" href="<?=base_url('assets/uploads/emp_doc/emp_detail_docs/'.$dataRow->emp_detail_docs)?>" target="_blank" datatip="Download" flow="down"><i class="fa fa-download"></i></a>
                        <?php }
                        ?>
                    </div>
                </div>
                
            </div>   
            <div class="col-md-4 form-group">
                <label for="pf_agreement_docs">PF Agreement Image</label>
                <div class="input-group">
                <input type="file" name="pf_agreement_docs" class="form-control-file" style="width:80%;" />
                    <div class="input-group-append">
                        <?php if(!empty($dataRow->pf_agreement_docs))
                        {?>
                            <a class="btn btn-sm btn-outline-info" href="<?=base_url('assets/uploads/emp_doc/pf_agreement_docs/'.$dataRow->pf_agreement_docs)?>" target="_blank" datatip="Download" flow="down"><i class="fa fa-download"></i></a>
                        <?php }
                        ?>
                    </div>
                </div>
                
            </div>   
        </div>
    </div>
</form>  