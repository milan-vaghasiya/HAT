
<?php
class EmpRecruitment extends MY_Controller
{
    private $indexPage = "hr/emp_recruitment/index";
    private $empRecruitmentForm = "hr/emp_recruitment/form";

    private $gender = ["M"=>"Male","F"=>"Female","O"=>"Other"];
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Employee Recruitment";
		$this->data['headData']->controller = "hr/empRecruitment";
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "hr/empRecruitment";
        $this->data['tableHeader'] = getHrDtHeader('empRecruitment');
        $this->load->view($this->indexPage,$this->data);
    }
    
    /* new employee */
    public function getDTRows($status=1){
        $data = $this->input->post(); $data['status']=$status;
        $result = $this->empRecruitment->getDTRows($data);
        $sendData = array();$i=1;$count=0;
		foreach($result['data'] as $row):
			$row->sr_no = $i++;  
			$row->loginId = $this->loginId;
			$sendData[] = getEmpRecruitmenteData($row);
		endforeach;
		
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addEmpRecruitment(){
        $this->data['genderData'] = $this->gender;
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['categoryData'] = $this->category->getCategoryList();
        $this->load->view($this->empRecruitmentForm,$this->data);
    }

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['emp_name']))
            $errorMessage['emp_name'] = "Employee name is required.";
        if(empty($data['emp_contact']))
            $errorMessage['emp_contact'] = "Contact is required.";
        if(empty($data['emp_birthdate']))
            $errorMessage['emp_birthdate'] = "DOB is required.";
        if(empty($data['emp_education']))
            $errorMessage['emp_education'] = "Education is required.";
        if(empty($data['emp_gender']))
            $errorMessage['emp_gender'] = "Gender is required.";
        if(empty($data['emp_category']))
            $errorMessage['emp_category'] = "Employee Category is required.";
        if(empty($data['emp_dept_id']))
            $errorMessage['emp_dept_id'] = "Department is required.";
        if(empty($data['emp_designation']))
            $errorMessage['emp_designation'] = "Designation is required.";
        
		
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['emp_name'] = ucwords($data['emp_name']);
            $data['created_by'] = $this->session->userdata('loginId');
            unset($data['emp_password_c']);
            $this->printJson($this->empRecruitment->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->empRecruitment->getEmployee($id);
        $this->data['genderData'] = $this->gender;
        $this->data['deptRows'] = $this->department->getDepartmentList();
        $this->data['descRows'] = $this->employee->getDesignation();
        $this->data['categoryData'] = $this->category->getCategoryList();
        $this->load->view($this->empRecruitmentForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->delete($id));
        endif;
    }
    
    /* employee active/inactive */
    public function activeInactive(){
        $id = $this->input->post('id');
        $value = $this->input->post('is_active');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->employee->activeInactive($id,$value));
        endif;
    }
    
    function empPrint($id){
		$this->data['empData'] = $empData = $this->employee->getEmp($id);
		$this->data['companyData'] = $this->employee->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead-top.png');
		$letter2 = $this->load->view('hr/employee/emp_print',$this->data,true);
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;" rowspan="3"></td>
							<th colspan="2">For, '.$this->data['empData']->emp_name.'</th>
						</tr>
						<tr>';              
        //$pdfData = $htmlHeader.$pdfData;
        
        $letter1 = '<div style="width:100%;padding-left:2rem;padding-right:2rem;">
                        <div class="row"><div class="text-right col-md-12"><b>DATED: '.date("d.m.Y").'</b></div></div>
                        <p> TO.<br>BANK OF INDIA <br> METODA GIDC <br> TAL. LODHIKA, <br> RAJKOT-360 021(GUJARAT) </p>
                        
                        <p>Kind Attend. Branch Manager <br><br> Dear Sir,</p>
                        
                        <p style="text-indent:1rem;text-align:justify;line-height:1.7rem;">
                            <b>'.$empData->emp_name.'</b> 
                            is presently working in our factory since 01 Month and also residing at the Factory
                            staff Quarter. This letter is given only for Residential Proof of the said person to open the bank account in your
                            Branch.<br>
                            We are not responsible for any other activity of this Account Holder related to account you open.
                        </p>
                        <table style="width:100%;margin-top:50mm;">
                            <tr>
                                <td style="width:50%;"><b>01. '.$empData->emp_name.'</b></td>
                                <th>sign</th>
                            </tr>
                        </table>
                        <br>
                        <p style="line-height:1.7rem;">Thanking You.<br>Yours Fathifully</p>
                    </div>';
        
		$mpdf = new \Mpdf\Mpdf();
		$pdfFileName='BANK-ACCOUNT-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->AddPage('P','','','','',3,3,45,3,3,3,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($letter1);
		$mpdf->AddPage('P','','','','',3,3,40,3,3,3,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($letter2);
		$mpdf->Output($pdfFileName,'I');
	}
}
?>