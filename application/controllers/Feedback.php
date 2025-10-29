<?php
class Feedback extends MY_Controller
{
    private $indexPage = "feedback/index";
    private $formPage = "feedback/form";
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Feedback";
		$this->data['headData']->controller = "feedback";
		$this->data['headData']->pageUrl = "feedback";
	}
	
	public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){  
        $data = $this->input->post(); 
        $data['status'] = $status;
        $result = $this->feedback->getDTRows($data);;
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getFeedbackData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
   
    public function generateDemand(){
        $this->data['trans_prefix'] = n2y(date('Y')).n2m(date('m'));
        $this->data['trans_no'] = $this->feedback->nextTransNo($this->data['trans_prefix']);
        $this->data['feedbackPoint'] = $this->feedback->getFeedbackPoint();
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->formPage,$this->data);
    }
    
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['party_id']))
			$errorMessage['party_id'] = "Customer is required.";
		if(empty($data['survey_from']))
			$errorMessage['survey_from'] = "Survey From is required.";
		if(empty($data['survey_to']))
			$errorMessage['survey_to'] = "Survey To is required.";	
		if($data['survey_to'] < $data['survey_from'])
			$errorMessage['survey_to'] = "Invalid Survey To.";	
	    if(empty($data['param_id']))
			$errorMessage['param_id'] = "Feedback Parameter is required.";		
	
			
        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            unset($data['paramSelect']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->feedback->save($data));
        endif;
    }

    public function edit(){
        $id = $this->input->post('id');
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->data['dataRow'] = $this->feedback->getFeedback($id);
        $this->data['feedbackPoint'] = $this->feedback->getFeedbackPoint();
        $this->load->view($this->formPage,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->feedback->delete($id));
        endif;
    }
    
    public function printFeedback($id){
        $feedbackData= $this->feedback->getFeedback($id);
        $logo=base_url('assets/images/logo.png');
        $footerImg = $this->data['footerImg'] = base_url('assets/images/lh_footer.png');
	
		$pdfData = '<table class="table top-table-border">
                    <tr>
                        <td rowspan="2" style="width:55%;vertical-align:top;"><b >M/s. : '.$feedbackData->party_name.'</b><br>
                        <small>'.$feedbackData->party_address.'</small>
                        </td>
                        <td><b>Date :</b> '.formatDate($feedbackData->feedback_at).'</td>
                    </tr>
                    <tr>
                        <td><b>Survey Period : </b>'.formatDate($feedbackData->survey_from).' TO '.formatDate($feedbackData->survey_to).'</td>
                    </tr>
                    <tr>
                        <td colspan="2"><b>Level of Satisfaction :</b> 10 - EXCELLENT, 8 - SATISFIED, 5 - NEEDS IMPROVEMENT</td>
                    </tr>
        </table>';
         /** Transaction Data */
        $pdfData .= '<table class="table top-table-border" style="margin-top:10px;">
        <thead>
            <tr>
                <th>Parameter</th>
                <th>Excellent</th>
                <th>Satisfactory</th>
                <th>Needs Improvement</th>
            </tr>
        </thead><tbody>';
        $observed_value = 0;
        foreach($feedbackData->transData as $row){
            if($row->grade == 1){
                $observed_value +=5;
            } elseif($row->grade == 2){
                $observed_value +=8;
            }elseif($row->grade == 3){
                $observed_value +=10;
            }
        $pdfData .= '
                <tr>
                    <td>'.$row->parameter.'</td>
                    <td class="text-center">
                        '.((!empty($row->grade) && $row->grade == 3) ? '<img style="width:15px;display:inline-block;vertical-align:middle;" src="'.base_url('assets/images/check_icon.png').'">':'').'
                    </td>
                    <td class="text-center">
                        '.((!empty($row->grade) && $row->grade == 2) ? '<img style="width:15px;display:inline-block;vertical-align:middle;" src="'.base_url('assets/images/check_icon.png').'">':'').'
                    </td>
                    <td class="text-center">
                        '.((!empty($row->grade) && $row->grade == 1) ? '<img style="width:15px;display:inline-block;vertical-align:middle;" src="'.base_url('assets/images/check_icon.png').'">':'').'
                    </td>
                </tr>';
                
        }
      
        $pdfData .= '<tr><td colspan="4" style="padding:5px 8px;"><b>Any other suggestions for betterment of mutual business relation :</b><br><br>'.$feedbackData->other_suggestions.'</td></tr>
                    <tr><td colspan="4" style="padding:5px 8px;"><b>Comments (if any) :</b><br><br>'.$feedbackData->comments.'</td></tr>';

        $total_value = count($feedbackData->transData) *10;
       
        $rating = ($observed_value/$total_value)*100;
        $grade ='';
        if($rating >=90){
            $grade ='A';
        }elseif($rating >=80 && $rating < 90){
            $grade ='B';
        }else{
            $grade ='C';
        }
        $pdfData .= '<tr ><td colspan="4" style="padding:5px 8px;"><b>Rating :</b><br><br>Customer satisfaction Index (CSI) = [(Observed value) / (Total Value)] x 100 = <b>'.number_format($rating,2).' %</b></td>  </tr>
                    <tr><td colspan="4" style="padding:5px 8px;"><b>Grade : </b>'.$grade.'
                    <br><br>
                    Grade : A - Excellent (Above 90%), B - Satisfactory (80% to 90%), C - Needs Improvement (Below 80%).
                    </td></tr>';

        $pdfData .= '
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr >
                        <td class="text-left" ><b>For :- Customer,</b> </td>
                        <td class="text-left" colspan="3"><b>For :- AKSHAR ENGINEERS,</b> </td>
                    </tr>
                    <tr>
                        <td  class="text-left" >
                            <b>Name :</b> '.$feedbackData->feedback_by.'	
                        </td>
                    <td class="text-left" colspan="3"><b>Name : </b></td>
                    </tr>
                    <tr>
                        <td class="text-left" > <b>Signature :</b> </td> 
                        <td class="text-left" colspan="3"> <b>Signature :</b> </td>
                    </tr>
                    <tr>
                        <td ><b>Date :</b> '.formatDate($feedbackData->feedback_at).'</td>
                        <td colspan="3"><b>Date :</b> '.formatDate($feedbackData->feedback_at).'</td>
                    </tr>
				';
        
        $pdfData .=	'</tbody></table>';
		$htmlHeader = '<table style="border-bottom:1px solid #000000;">
                            <tr>
                                <th><img src="'.$logo.'" class="img" style="height:45px"></th>
                                <td class="fs-20 text-center"><b>Customer Satisfaction Survery Report</b></td>
                                <td class="fs-12 text-right">F/MKT/04 (00/01.01.16)</td>
                            </tr>
                    </table>';
                
        
       
       
		
					

		$mpdf = $this->m_pdf->load();
		$pdfFileName=$feedbackData->trans_prefix.sprintf('%04d',$feedbackData->trans_no).'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
        $mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter($htmlFooter);
		$mpdf->AddPage('P','','','','',10,10,25,5,4,4,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }
}

?>