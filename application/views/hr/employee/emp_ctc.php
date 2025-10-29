<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="emp_id" id="emp_id" value="<?=(!empty($dataRow->emp_id))?$dataRow->emp_id:$emp_id; ?>" />
            <input type="hidden" name="is_active" id="is_active" value="<?=(!empty($dataRow->is_active))?$dataRow->is_active:"1"; ?>" />
            <div class="col-md-8 form-group">
                <label for="basic_salary">Basic Salary</label>
                <div class="input-group">
                    <input type="text" name="basic_salary" id="basic_salary" class="form-control numericOnly req" value="<?=(!empty($dataRow->basic_salary))?$dataRow->basic_salary:""; ?>" />
                    <div class="input-group-append">
                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
                            <i class="fas fa-sync-alt"></i> Load
                        </button>
                    </div>
                </div>           
            </div>
            <!-- <div class="col-md-8 form-group">
                <label for="ctc">C.T.C.</label>
                <div class="input-group">
                    <input type="text" name="ctc" id="ctc" class="form-control numericOnly req" value="<?=(!empty($dataRow->ctc))?$dataRow->ctc:""; ?>" />
                    <div class="input-group-append">
                        <button type="button" class="btn waves-effect waves-light btn-success float-right loaddata" title="Load Data">
                            <i class="fas fa-sync-alt"></i> Load
                        </button>
                    </div>
                </div>
            </div> -->
            <div class="col-md-4 form-group">
                <label for="effect_start">Effect Start</label>
                <input type="date" name="effect_start" id="effect_start" class="form-control req" value="<?=(!empty($dataRow->effect_start))?$dataRow->effect_start:date('Y-m-d'); ?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="sa">S.A.</label>
                <input type="text" name="sa" id="sa" class="form-control req" value="<?=(!empty($dataRow->sa))?$dataRow->sa:"0"; ?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="hra">H.R.A.</label>
                <input type="text" name="hra" id="hra" class="form-control" value="<?=(!empty($dataRow->hra))?$dataRow->hra:""; ?>" readonly />
            </div>
            <div class="col-md-4 form-group">
                <label for="ca">C.A.</label>
                <input type="text" name="ca" id="ca" class="form-control" value="<?=(!empty($dataRow->ca))?$dataRow->ca:""; ?>" readonly />
            </div>
            <div class="col-md-4 form-group">
                <label for="pf">P.F.</label>
                <input type="text" name="pf" id="pf" class="form-control" value="<?=(!empty($dataRow->pf))?$dataRow->pf:""; ?>" readonly />
                <input type="hidden" name="pf_per" id="pf_per" value="<?=(!empty($dataRow->pf_per))?$dataRow->pf_per:""; ?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="bonus">Bonus</label>
                <input type="text" name="bonus" id="bonus" class="form-control" value="<?=(!empty($dataRow->bonus))?$dataRow->bonus:""; ?>" readonly />
            </div>
            <div class="col-md-4 form-group">
                <label for="variable_pay">Variable Pay</label>
                <input type="text" name="variable_pay" id="variable_pay" class="form-control" value="<?=(!empty($dataRow->variable_pay))?$dataRow->variable_pay:""; ?>" readonly />
            </div>
            <div class="col-md-4 form-group">
                <label for="gratuity">Gratuity</label>
                <input type="text" name="gratuity" id="gratuity" class="form-control" value="<?=(!empty($dataRow->gratuity))?$dataRow->gratuity:""; ?>" readonly />
            </div>
            <div class="col-md-4 form-group">
                <label for="gross_salary">Gross Salary</label>
                <input type="text" name="gross_salary" id="gross_salary" class="form-control" value="<?=(!empty($dataRow->gross_salary))?$dataRow->gross_salary:""; ?>" readonly />
            </div>
            <div class="col-md-4 form-group">
                <label for="ctc">C.T.C.</label>
                <input type="text" name="ctc" id="ctc" class="form-control numericOnly req" value="<?=(!empty($dataRow->ctc))?$dataRow->ctc:""; ?>" readonly />
            </div>
            <div class="col-md-6 form-group">    
                <input type="hidden" name="total_ctc" id="total_ctc" value="<?=(!empty($dataRow->total_ctc))?$dataRow->total_ctc:"0"; ?>" />
                <span class="badge badge-pill badge-success totalctc font-14 font-medium">TOTAL CTC.: <?=(!empty($dataRow->total_ctc))?$dataRow->total_ctc:"0"; ?></span>
            </div>
            <div class="col-md-6 form-group">
                <button type="button" class="btn btn-outline-primary float-right promotion" title="Promotion">
                    <i class="fas fa-chart-line"></i> Promotion
                </button>
            </div>
        </div>
    </div>
</form>  
<script>
    $(document).ready(function(){
	    $(document).on('click','.loaddata',function(){
            var IsValid = 1;
            var basic_salary = $("#basic_salary").val();
            var sa = $("#sa").val();
            if($("#basic_salary").val() == '' || $("#basic_salary").val() == '0'){ $(".basic_salary").html('Basic Salary is required'); IsValid = 0; } 
            if(parseFloat(basic_salary).toFixed(0) < 9000){ $(".basic_salary").html('Basic Salary Invalid.'); IsValid = 0; } 
            if($("#sa").val() == ''){ $(".sa").html('S.A. is required'); IsValid = 0; }
            if(IsValid){
                $.ajax({
                    url: base_url + controller + '/calculateCtc',
                    data: {basic_salary:basic_salary, sa:sa},
                    type: "POST",
                    dataType:"json",
                    success:function(data)
                    {
                        $("#hra").val(data.hra);
                        $("#ca").val(data.ca);
                        $("#sa").val(data.sa);
                        $("#pf").val(data.pf);
                        $("#pf_per").val(data.pf_per);
                        $("#bonus").val(data.bonus);
                        $("#variable_pay").val(data.variable_pay);
                        $("#gratuity").val(data.gratuity);
                        $("#gross_salary").val(data.gross);
                        var totalctc = parseFloat(data.basicSalary) +  parseFloat(data.hra) +  parseFloat(data.ca) +  parseFloat(data.sa) +  parseFloat(data.pf) +  parseFloat(data.bonus);
                        $("#ctc").val(totalctc);
                        $(".totalctc").html("TOTAL CTC.: " + totalctc);
                        $("#total_ctc").val(totalctc);
                    }
                });
            }
        });

        $(document).on('keyup change','#sa',function(){
            var basic_salary = $("#basic_salary").val();
            var hra = $("#hra").val();
            var ca = $("#ca").val();
            var sa = $("#sa").val();
            var pf = $("#pf").val();
            var pf_per = $("#pf_per").val();
            var bonus = $("#bonus").val();

            var gross = (parseFloat(basic_salary) + parseFloat(hra) + parseFloat(ca) + parseFloat(sa));
            var pf = ((parseFloat(pf_per) * (parseFloat(basic_salary) + parseFloat(sa) + parseFloat(ca))) / 100);
            
            var totalctc = parseFloat(basic_salary) +  parseFloat(hra) +  parseFloat(ca) +  parseFloat(sa) +  parseFloat(pf) +  parseFloat(bonus);
            $(".totalctc").html("TOTAL CTC.: " + totalctc.toFixed(0));
            $("#ctc").val(totalctc.toFixed(0)); $("#total_ctc").val(totalctc.toFixed(0)); $("#gross_salary").val(gross.toFixed(0)); $("#pf").val(pf.toFixed(0));
        });

        $(document).on('click','.promotion',function(){
            $("#id").val("");
            $("#ctc").val("");
            $("#basic_salary").val("");
            $("#hra").val("");
            $("#ca").val("");
            $("#sa").val(0);
            $("#pf").val("");
            $("#pf_per").val("");
            $("#bonus").val("");
            $("#variable_pay").val("");
            $("#gratuity").val("");
            $("#gross_salary").val("");
        });
    });
</script>