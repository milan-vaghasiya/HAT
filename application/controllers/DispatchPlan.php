<?php
class DispatchPlan extends MY_Controller{
	
	private $indexPage = "dispatch_plan/index";
	private $planIndexPage = "dispatch_plan/plan_index";
    private $formPage = "dispatch_plan/form";

	public function __construct()	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Dispatch Plan";
		$this->data['headData']->controller = "dispatchPlan";
	}
	
	public function index(){
		$this->load->view($this->indexPage,$this->data);
	}
	
    public function getDispatchPlan()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $orderData = $this->dispatchPlan->getDispatchPlan($data);
            $tbody = "";$i = 1;
            
            foreach ($orderData as $row) :
                $pendingQty = $row->qty - ($row->dispatch_qty + $row->plan_qty);
                if($pendingQty > 0){
                    $production = $this->dispatchPlan->getWIPQtyForDispatchPlan(['sales_order_id'=>$row->trans_main_id,'so_trans_id'=>$row->id,'item_id'=>$row->item_id]);
                    $wip_qty = (!empty($production->job_qty))?$production->job_qty-$production->total_out_qty:0;
                    $rtdStock = $this->store->getItemCurrentStock($row->item_id,$this->RTD_STORE->id);

                    $selectBox = '<input type="checkbox" name="so_trans_id[]" id="so_trans_id_'.$i.'" class="filled-in chk-col-success BulkRequest" value="'.$row->id.'"><label for="so_trans_id_'.$i.'"></label>';
                    $tbody .= '<tr>
                        <td>'.$selectBox.'</td>
                        <td>' . $i++ . '</td>
                        <td>' . formatDate($row->cod_date) . '</td>
                        <td>' . getPrefixNumber($row->trans_prefix,$row->trans_no) . '</td>
                        <td>' . $row->doc_no . '</td>
                        <td>' . formatDate($row->doc_date) . '</td>
                        <td>' . $row->party_code . '</td>
                        <td class="text-left">' . $row->item_code . '</td>
                        <td class="text-left">' . $row->item_name . '</td>
                        <td class="text-left">' . $row->item_size . '</td>
                        <td class="text-left">' . $row->grn_data . '</td>
                        <td>' . floatVal($row->qty) . '</td>
                        <td>' . floatVal($row->dispatch_qty) . '</td>
                        <td>' . floatVal($row->plan_qty) . '</td>
                        <td>' . floatVal($pendingQty) . '</td>
                        <td>'.floatval($wip_qty).'</td>
                        <td>'.floatval($rtdStock->qty).'</td>';
                    $tbody .= '</tr>';
                }
            endforeach;
			

            $this->printJson(['status' => 1, 'tbody' => $tbody]);
        endif;
    }
 
    public function addPlan(){
        $data = $this->input->post();
        $this->data['dataRow'] = $this->salesOrder->getSOTransactions($data['id']);
        $this->load->view($this->formPage, $this->data);
    }

    public function savePlan(){
        $data = $this->input->post();
        $errorMessage = array();
        if(!isset($data['plan_qty'])){
            $errorMessage['general_error'] = "Plan Qty is required.";
        }else{
            $totalPlanQty = array_sum($data['plan_qty']);
            if($totalPlanQty == 0){
                $errorMessage['general_error'] = "Plan Qty is required.";
            }
        }
        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
            $this->printJson($this->dispatchPlan->savePlan($data));
        endif;
    }

    public function plannedSo(){
        $this->data['tableHeader'] = getDispatchDtHeader('plannedSo');    
        $this->load->view($this->planIndexPage,$this->data);
    }

    public function getPlanDTRows(){
		$data = $this->input->post(); 
        $result = $this->dispatchPlan->getPlanDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++; $row->ref_no = '';
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getPlannedSOData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function printPlanning($trans_number){
        $planData= $this->dispatchPlan->getPlanData($trans_number);
        $logo=base_url('assets/images/logo.png');
	
		
        $pdfData = '<table class="table top-table-border" style="margin-top:10px;">
        <thead>
            <tr>
                <th>#</th>
                <th>Delivery Date</th>
                <th>So No</th>
                <th>Cust Po No</th>
                <th>Party Code</th>
                <th>Item</th>
                <th>Plan Qty</th>
            </tr>
        </thead><tbody>';
        $i=1;
        foreach($planData as $row){
        $pdfData .= '<tr>
                    <td>'.$i++.'</td>
                    <td class="text-center"> '.formatDate($row->cod_date).' </td>
                    <td class="text-center">  '.(getPrefixNumber($row->so_prefix,$row->so_no)).' </td>
                    <td class="text-center"> '.$row->doc_no.' </td>
                    <td class="text-center"> '.$row->party_code.' </td>
                    <td class="text-center"> '.$row->item_name.' </td>
                    <td class="text-center"> '.$row->qty.' </td>
                </tr>';
                
        }
        $pdfData .= '</tbody></table>';
		$htmlHeader = '<table class="table">
                            <tr>
                                <th class="text-left" rowspan="2"> <img src="'.$logo.'" class="img" style="height:60px"></th>
                                <th class="text-right">
                                    Plan No : '.$planData[0]->trans_number.'
                                </th>
                            </tr>
                            <tr>
                                <th class="text-right">
                                    Plan Date : '.formatDate($planData[0]->trans_date).'
                                </th>
                            </tr>
                    </table><hr>';
                
    
		$mpdf = $this->m_pdf->load();
		$pdfFileName=$trans_number.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		$mpdf->showWatermarkImage = true;
		$mpdf->SetProtection(array('print'));
        $mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->SetHTMLFooter("");
		$mpdf->AddPage('L','','','','',4,4,30,80,4,4,'','','','','','','','','','A4-L');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }
}
?>