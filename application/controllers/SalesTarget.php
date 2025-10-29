<?php
class SalesTarget extends MY_Controller{
    private $indexPage = "sales_target/index";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Sales Target";
		$this->data['headData']->controller = "salesTarget";
        $this->data['headData']->pageUrl = "salesTarget";
        $this->data['monthData'] = $this->getMonthListFY();
	}
	
	public function index(){
        $this->data['salesExecutives'] = $this->employee->getSalesExecutives();
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getTargetRows(){
		$postData = $this->input->post();
        $errorMessage = array();
		
        // if(empty($postData['sales_executive']))
        //     $errorMessage['sales_executive'] = "Sales Executive is required.";
        if(empty($postData['month']))
            $errorMessage['month'] = "Month is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$partyData = $this->employee->getTargetRows($postData);
			$hiddenInputs = '<input type="hidden" id="sexecutive" name="executive" value="'.$postData['sales_executive'].'" />';
			$hiddenInputs .= '<input type="hidden" id="smonth" name="smonth" value="'.$postData['month'].'" />';
			$targetData = ''; $tfoot = ''; $i=1; $totalTarget=0;
			if(!empty($partyData)):
				foreach($partyData as $row):
					$targetData .= '<tr>';
						$targetData .= '<td>'.$i++.'</td>';
						$targetData .= '<td>'.$row->party_name.'</td>';
						$targetData .= '<td>'.$row->contact_person.'</td>';
						$targetData .= '<td><input type="number" id="business_target'.$row->id.'" name="business_target[]" class="form-control floatOnly" value="'.$row->business_target.'" />
						<input type="hidden" id="recovery_target'.$row->id.'" name="recovery_target[]" value="'.$row->recovery_target.'" />
						<input type="hidden" id="st_id'.$row->id.'" name="st_id[]" value="'.$row->st_id.'" />
						<input type="hidden" id="party_id'.$row->id.'" name="party_id[]" value="'.$row->id.'" /></td>';
						//$targetData .= '<td><input type="number" id="recovery_target'.$row->id.'" name="recovery_target[]" class="form-control floatOnly" value="'.$row->recovery_target.'" /></td>';
					$targetData .= '</tr>';

					$totalTarget += $row->business_target;
				endforeach;
				$tfoot .='<tr class="thead-info">
					<th colspan="3" style="text-align: left">Total</th>
					<th class="text-right">'.number_format($totalTarget).'</th>
				</tr>';
				$this->printJson(['status'=>1,'message'=>'Success','targetData'=>$targetData,'hiddenInputs'=>$hiddenInputs,'tfoot'=>$tfoot]);
			else:
				$this->printJson(['status'=>0,'message'=>['sales_executive'=>'Something goes wrong...!']]);
			endif;
		endif;
    }

    public function saveTargets(){
        $postData = $this->input->post();
        $errorMessage = array();
		
        // if(empty($postData['executive']))
        //     $errorMessage['sales_executive'] = "Sales Executive is required.";
        if(empty($postData['smonth']))
            $errorMessage['month'] = "Month is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$postData['sales_executive'] = $postData['executive'];
			$postData['month'] = $postData['smonth'];
			unset($postData['executive'],$postData['smonth']);
			$this->printJson($this->employee->saveTargets($postData));
		endif;
    }
}
?>