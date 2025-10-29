<?php
class PayrollModel extends MasterModel{
    private $payrollTrans = "payroll_transaction";
    private $empMaster = "employee_master";
    private $empSalary = "emp_salary_detail";
    private $salaryStructure = "salary_structure";
    private $advanceSal = "advance_salary";
    private $empLoan = "emp_loan";
    
	public function getDTRows($data){		
		$data['tableName'] = $this->payrollTrans;
        $data['group_by'][] = "month";
		return $this->pagingRows($data);
    }
    
    public function getEmpSalary(){
        $data['select'] = "emp_salary_detail.*,employee_master.emp_name,employee_master.emp_code,employee_master.emp_designation,emp_designation.title";
		$data['join']['employee_master'] = "employee_master.id = emp_salary_detail.emp_id";
		$data['join']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
		$data['tableName'] = $this->empSalary;
        return $this->rows($data);
    }

	public function getPayrollData($month){
		$data['select'] = "payroll_transaction.*,employee_master.emp_name,employee_master.emp_code";
		$data['leftJoin']['employee_master'] = "employee_master.id = payroll_transaction.emp_id";
        $data['where']['payroll_transaction.month'] = $month;
        $data['tableName'] = $this->payrollTrans;
		return $this->rows($data);
    }

	public function getSalarySumByMonth($month){
		$data['select'] = "SUM(net_salary) as salary_sum";
        $data['where']['month']=$month;
        $data['tableName'] = $this->payrollTrans;
		return $this->row($data);
    }

    public function save($data){
        try {
            $this->db->trans_begin();

            $this->delete($data['month']);

		    $result = array();
            foreach($data['salary_data'] as $row):
                unset($row['emp_name']);
                $earningData = json_decode($row['earning_data']);
                $deductionData = json_decode($row['deduction_data']);
                $loanData = (!empty($row['loan_data']))?$row['loan_data']:array();
                $advanceData = (!empty($row['loan_data']))?$row['advance_data']:array();

                $row['loan_data'] = (!empty($row['loan_data']))?json_encode($row['loan_data']):NULL;
                $row['advance_data'] = (!empty($row['loan_data']))?json_encode($row['advance_data']):NULL;

                foreach($earningData as $erow):
                    $row[$erow->system_code] = $erow->amount;
                endforeach;

                foreach($deductionData as $drow):
                    $row[$drow->system_code] = $drow->amount;
                endforeach;

                $row['id'] = "";
                $row['ledger_id'] = $data['ledger_id'];
                $row['month'] = $data['month'];
                $row['dept_id'] = $data['dept_id'];
                $row['created_by'] = $data['created_by'];
                $result = $this->store($this->payrollTrans,$row,'Payroll');

                //Advance Salary Deduction Effect
                if(!empty($advanceData)):
                    foreach($advanceData as $adrow):
                        $setData = array();
                        $setData['tableName'] = $this->advanceSal;
                        $setData['where']['id'] = $adrow['id'];
                        $setData['set']['deposit_amount'] = 'deposit_amount, + '.$adrow['amount'];
                        $this->setValue($setData);
                    endforeach;
                endif;

                //Loan EMI Deduction Effect
                if(!empty($loanData)):
                    foreach($loanData as $lrow):
                        $setData = array();
                        $setData['tableName'] = $this->empLoan;
                        $setData['where']['id'] = $lrow['id'];
                        $setData['set']['deposit_amount'] = 'deposit_amount, + '.$lrow['amount'];
                        $this->setValue($setData);
                    endforeach;
                endif;
            endforeach;
            
		    if ($this->db->trans_status() !== FALSE) : 
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }


    public function delete($id){
        try {
            $this->db->trans_begin();

            $payrollData = $this->getPayrollData($id);

            if(!empty($payrollData)):
                foreach($payrollData as $row):
                    $loanData = (!empty($row->loan_data))?json_decode($row->loan_data):array();
                    $advanceData = (!empty($row->advance_data))?json_decode($row->advance_data):array();

                    //Advance Salary Deduction reverse Effect
                    if(!empty($advanceData)):
                        foreach($advanceData as $adrow):
                            $setData = array();
                            $setData['tableName'] = $this->advanceSal;
                            $setData['where']['id'] = $adrow->id;
                            $setData['set']['deposit_amount'] = 'deposit_amount, - '.$adrow->amount;
                            $this->setValue($setData);
                        endforeach;
                    endif;

                    //Loan EMI Deduction reverse Effect
                    if(!empty($loanData)):
                        foreach($loanData as $lrow):
                            $setData = array();
                            $setData['tableName'] = $this->empLoan;
                            $setData['where']['id'] = $lrow->id;
                            $setData['set']['deposit_amount'] = 'deposit_amount, - '.$lrow->amount;
                            $this->setValue($setData);
                        endforeach;
                    endif;
                endforeach;
            endif;

            $result = $this->trash($this->payrollTrans,['month'=>$id],'Payroll');

            if ($this->db->trans_status() !== FALSE) : 
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    /* public function getEmployeeListForSalary($data){
        $queryData = array();
        $queryData['tableName'] = $this->empMaster;
        $queryData['where']['emp_dept_id'] = $data['dept_id'];
        $queryData['where']['emp_type'] = $data['emp_type'];
        return $this->rows($queryData);
    } */

    public function getEmployeeSalaryStructure($id){
        $queryData = array();
        $queryData['tableName'] = $this->empMaster;
        $queryData['select'] = "employee_master.id as emp_id,employee_master.emp_code, employee_master.emp_name, employee_master.traveling_charge, employee_master.pf_applicable, employee_master.emp_type, employee_master.salary_duration, employee_master.salary_code, employee_master.salary_actual, employee_master.salary_on_paper";
        $queryData['where']['employee_master.id'] = $id;
        return $this->row($queryData);
    }

    /*public function getEmployeeListForSalary($data){
        $queryData = array();
        $queryData['tableName'] = $this->salaryStructure;
        $queryData['select'] = "salary_structure.*,employee_master.emp_code, employee_master.emp_name, employee_master.traveling_charge, employee_master.pf_applicable, employee_master.salary_code, employee_master.salary_actual, employee_master.salary_on_paper";
        $queryData['leftJoin']['employee_master'] = "employee_master.id = salary_structure.emp_id";
        if(!empty($data['dept_id'])):
            $queryData['where']['employee_master.emp_dept_id'] = $data['dept_id'];
        endif;
        if(!empty($data['format_id'])){$queryData['where']['salary_structure.format_id'] = $data['format_id'];}
        $queryData['where']['salary_structure.is_active'] = 1;
        return $this->rows($queryData);
    }*/
	
	
    public function getEmployeeListForSalary($data){
        $queryData = array();
        $queryData['tableName'] = $this->empMaster;
        $queryData['select'] = "employee_master.id as emp_id,employee_master.emp_code, employee_master.emp_name, employee_master.traveling_charge, employee_master.pf_applicable, employee_master.emp_type, employee_master.salary_duration, employee_master.salary_code, employee_master.salary_actual, employee_master.salary_on_paper";
        if(!empty($data['dept_id'])):
            $queryData['where']['employee_master.emp_dept_id'] = $data['dept_id'];
        endif;
		if(!empty($data['emp_code'])):
            $queryData['where']['employee_master.emp_code'] = $data['emp_code'];
        endif;
        return $this->rows($queryData);
    }
    
	/*** Get Advance Salary By Month ***/
    public function getAdvanceSal($postData){
		$data['tableName'] = $this->advanceSal;
		$data['select'] = "SUM(advance_salary.sanctioned_amount - advance_salary.deposit_amount) as advanceSal";
        $data['where']['advance_salary.entry_date <= '] = date('Y-m-t',strtotime($postData['month']));
        $data['where']['advance_salary.emp_id'] = $postData['emp_id'];
        return $this->row($data);
    }
    
	/*** Get  By Month ***/
    public function getEmpLoanPending($postData){
		$data['tableName'] = $this->empLoan;
		$data['select'] = "emp_loan.emi_amount as loanEmi, SUM(emp_loan.sanctioned_amount - emp_loan.deposit_amount) as pendingLoan";
        $data['where']['emp_loan.entry_date <= '] = date('Y-m-t',strtotime($postData['month']));
        $data['where']['emp_loan.emp_id'] = $postData['emp_id'];
        $data['where']['emp_loan.sanctioned_by > '] = 0;
        return $this->row($data);
    }

}
?>