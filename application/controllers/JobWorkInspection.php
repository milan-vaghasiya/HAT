<?php 
class JobWorkInspection extends MY_Controller{
    private $indexPage = "job_work_insp/index";
    private $inspForm = "job_work_insp/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Job Work Inspection";
		$this->data['headData']->controller = "jobWorkInspection";
		$this->data['headData']->pageUrl = "jobWorkInspection";
	}
	
	public function index(){
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->data['jobData'] = $this->jobcard_v2->jobCardNoList();
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($job_id=""){
        $data = $this->input->post(); $data['job_id'] = $job_id;
        $result = $this->jobWorkInspection->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getJobWorkInspectionData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function inInspection($id)
	{
        $this->data['dataRow'] = $this->jobWorkInspection->getJobWorkData($id);
		$this->data['inInspectData'] = $this->jobWorkInspection->getInInspection($id);   
		$this->data['paramData'] =  $this->item->getPreInspectionParam($this->data['dataRow']->product_id,1);
		$this->load->view($this->inspForm,$this->data);
	}

	public function saveInInspection(){
		$data = $this->input->post();
        $errorMessage = Array();

		if(empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";

        $insParamData = $this->item->getPreInspectionParam($data['item_id']);

        if(count($insParamData) <= 0)
            $errorMessage['general'] = "Item Parameter is required.";

        $pre_inspection = Array();
        if(!empty($insParamData)):
            foreach($insParamData as $row):
                $param = Array();
                for($j = 1; $j <= 10; $j++):
                    $param[] = $data['sample'.$j.'_'.$row->id];
                    unset($data['sample'.$j.'_'.$row->id]);
                endfor;
                $param[] = $data['result_'.$row->id];
                $pre_inspection[$row->id] = $param;
                unset($data['result_'.$row->id]);
            endforeach;
        endif;

        $data['observation_sample'] = json_encode($pre_inspection);
        $data['param_count'] = count($insParamData);
		
		if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->jobWorkInspection->saveInInspection($data));
        endif;
	}

	public function inInspection_pdf($id){
		$this->data['inInspectData'] = $this->jobWorkInspection->getInInspection($id);
		$this->data['paramData'] =  $this->item->getPreInspectionParam($this->data['inInspectData']->item_id);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		$response="";
		$logo=base_url('assets/images/logo.png');
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
		
		$pdfData = $this->load->view('job_work_insp/printInInspection',$this->data,true);
		
		$inInspectData = $this->data['inInspectData'];
		
		$htmlHeader = '<img src="'.$this->data['letter_head'].'" class="img">';
		$htmlFooter = '<table class="table table-top" style="margin-top:10px;border-bottom:1px solid #000000;">
						<tr>
							<td style="width:50%;"></td>
							<td style="width:25%;" class="text-center">Prepared By</td>
							<td style="width:25%;" class="text-center">Approved By</td>
						</tr>
					</table>
					<table class="table top-table" style="margin-top:10px;">
						<tr>
							<!--<td style="width:25%;">PO No. & Date : </td>-->
							<td style="width:25%;"></td>
							<td style="width:25%;text-align:right;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',5,5,58,25,5,5,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');		
	}
}
?>