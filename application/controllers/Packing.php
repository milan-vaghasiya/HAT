<?php
class Packing extends MY_Controller{
    private $indexPage = "packing/index";
    private $packingForm = "packing/form";

    public function __construct(){
        parent::__construct();
        //echo '<br><br><hr><h1 style="text-align:center;color:red;">We are sorry!<br>Your ERP is Updating New Features</h1><hr><h2 style="text-align:center;color:green;">Thanks For Co-operate</h1>';exit;
        $this->data['headData']->pageTitle = "Packing";
		$this->data['headData']->controller = "packing";
		$this->data['headData']->pageUrl = "packing";
    }

    public function index(){
        $this->data['tableHeader'] = getDispatchDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows($status=0){
        $data = $this->input->post(); $data['status'] = $status;
		$result = $this->packings->getDTRows($data);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;

            if($row->packing_status == 0):
				$row->packing_status_label = '<span class="badge badge-pill badge-danger m-1">Pending</span>';
            else:
                $row->packing_status_label = '<span class="badge badge-pill badge-success m-1">Complete</span>';
			endif;

            $sendData[] = getPackingData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addPacking(){
        $data['trans_prefix'] = "PCK/".$this->shortYear.'/';
        $data['trans_no'] = $this->packings->getNextNo();
        $this->data['trans_number'] = getPrefixNumber($data['trans_prefix'],$data['trans_no']);
        $this->data['boxData'] =  $this->packings->getConsumable(1);
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->load->view($this->packingForm,$this->data);
    }

    public function getSalesOrderNoListForPacking(){
        $order_id = ($this->input->post('order_id'))?$this->input->post('order_id'):""; 
        $item_id = ($this->input->post('item_id'))?$this->input->post('item_id'):""; 
        $result = $this->packings->getSalesOrderNoListForPacking($item_id);
        $dataRows = '<option value="">Select Sales Order No.</option>';
        foreach($result as $row):
            $selected = (!empty($order_id) && $order_id == $row->id)?"selected":"";
            $dataRows .= '<option value="'.$row->id.'" '.$selected.'>[ '.$row->party_code.' ] '.(getPrefixNumber($row->trans_prefix,$row->trans_no)).' ( Cust. PO. No. : '.$row->doc_no.' )</option>';
        endforeach;
        $this->printJson(['orderNoList'=>$dataRows]);
    }

    public function getItemList(){
        $order_id = $this->input->post('order_id');
        $item_id = $this->input->post('item_id');
        if(empty($order_id)):
            $itemData = $this->item->getItemList(1);
            $options = '<option value="" data-trans_child_id="0">Selec Product</option>';
            foreach($itemData as $row):
                $selected = (!empty($item_id) && $item_id == $row->id)?"selected":"";
                $options .= '<option value="'.$row->id.'" data-trans_child_id="0" '.$selected.'>'.$row->item_code.'</option>';
            endforeach;
        else:
            $itemData = $this->packings->getSalesOrderItemsForPacking($order_id);
            $options = '<option value="" data-trans_child_id="0">Selec Product</option>';
            foreach($itemData as $row):
                $selected = (!empty($item_id) && $item_id == $row->id)?"selected":"";
                $options .= '<option value="'.$row->id.'" data-trans_child_id="'.$row->trans_child_id.'" '.$selected.'>'.$row->item_code.'</option>';
            endforeach;
        endif;
        $this->printJson(['itemList'=>$options]);
    }

    public function getPackingMaterial(){
        $data = $this->input->post();

        $options = '<option value="" data-qty_per_box="" data-ref_id="" data-wt_pcs="" data-size="">Packing Material</option>';
        if($data['packing_type'] == 1):
            $boxData = $this->packings->getRegularPackingBoxOnItem($data['item_id'],$data['ref_id']);
            $options .= '<option value="0" data-qty_per_box="" data-ref_id="-1" data-wt_pcs="" data-size="" '.(($data['ref_id'] != "" && $data['ref_id'] == -1)?"selected":"").'>Without Packing</option>';
            foreach ($boxData as $row) :
                $box=$this->packings->getBoxQty($row->id);
                $selected = (!empty($data['ref_id']) && $data['ref_id'] == $row->id)?"selected":"";
                $options .= '<option value="'.$row->box_id.'" data-wt_pcs="'.$row->wt_pcs.'" data-qty_per_box="'.$row->qty_per_box.'" data-ref_id="'.$row->id.'" data-size="'.$row->size.'" '.$selected.'>' . $row->item_name . '[ Qty/box : '.$row->qty_per_box.' | Total Box : '.($row->total_box-$box->total_box).' | Stock Qty. : '.($row->total_qty-$row->export_qty).'  ]</option>';
            endforeach;            
        else:
            $boxData = $this->packings->getConsumable(1);
            foreach ($boxData as $row) :
                $selected = (!empty($data['box_id']) && $data['box_id'] == $row->id)?"selected":"";
                $options .= '<option value="'.$row->id.'" data-qty_per_box="" data-ref_id="" data-wt_pcs="" data-size="'.$row->size.'" '.$selected.'>' . $row->item_name . '</option>';
            endforeach;
        endif;
        $this->printJson(['status'=>1,'material_options'=>$options]);
    }

    public function batchWiseItemStock(){
		$data = $this->input->post();
        $result = $this->packings->batchWiseItemStock($data);
		$this->printJson($result);
	}

    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        
        if(empty($data['item_data']))
            $errorMessage['item_name_error'] = "Packing Details is required.";
        
        if(!empty($data['item_data'])):
            $batchData = array();
            foreach($data['item_data'] as $key=>$row):
                if($data['is_final'] == 1 && $data['entry_type'] == "Regular"):
                    $batchData = (array) json_decode($row['batch_data']);
                    if(empty($batchData)):
                        $errorMessage['batch_data_'.$key] = "Batch details is required.";
                    endif;
                else:
                    $row['batch_data'] = array();
                endif;

                if($data['is_final'] == 1 && $data['entry_type'] == "Export"):
                    if($row['ref_id'] == -1):
                        $errorMessage['box_id_'.$key] = "Packing Material is required.";
                    endif;
                endif;
            endforeach;
        endif;

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            if(empty($data['id'])):
                $data['trans_prefix'] = "PCK/".$this->shortYear.'/';
                $data['trans_no'] = $this->packings->getNextNo();
                $data['trans_number'] = getPrefixNumber($data['trans_prefix'],$data['trans_no']);
            endif;

            if($data['is_final'] == 1):
                $data['packing_status'] = 1;
            endif;

            $data['total_net_weight'] = array_SUM(array_column($data['item_data'],'net_wt'));
            $data['total_packing_weight'] = array_SUM(array_column($data['item_data'],'packing_wt'));
            $data['total_wooden_box_weight'] = array_SUM(array_column($data['item_data'],'wooden_wt'));
            $data['total_gross_weight'] = array_SUM(array_column($data['item_data'],'gross_wt'));
            $data['created_by'] = $this->loginId;
			//print_r($data);exit;
            $this->printJson($this->packings->save($data));
        endif;
    }

    public function edit($id){
        $this->data['boxData'] =  $this->packings->getConsumable(1);
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['dataRow'] = $this->packings->getPackingData($id);  
        $this->load->view($this->packingForm,$this->data);      
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->packings->delete($id));
        endif;
    }

    public function packingPdf($id,$type=0){
        $packingMasterData = $this->packings->getPackingData($id);
        $packageData = $this->packings->packingTransGroupByPackage($id);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		
		$logo=base_url('assets/images/logo.png?v='.time());
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png?v='.time());

        $dataArray=array();
        foreach($packageData as $row){
            $row->itemData=$this->packings->packingTransByPackage($id,$row->package_no);
            $dataArray[]=$row;
        }
        
        $this->data['packingData']=$dataArray;
        $this->data['pdf_type'] = $type;
		$pdfData = $this->load->view('packing/packing_print',$this->data,true);        
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName= preg_replace('/[^A-Za-z0-9]/',"_",$packingMasterData->trans_number).'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css?v='.time()));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
        $mpdf->setTitle($packingMasterData->trans_number);
        if($packingMasterData->is_final == 0):
            $mpdf->SetWatermarkText('TENTATIVE',0.05);
            $mpdf->showWatermarkText = true;
        else:
		    $mpdf->SetWatermarkImage($logo,0.03,array(120,60));
		    $mpdf->showWatermarkImage = true;
		endif;
		
				
		$mpdf->SetHTMLHeader("");
		$mpdf->SetHTMLFooter("");
		$mpdf->AddPage('P','','','','',5,5,15,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');
    }
    
    public function updateItemName(){
        $data = $this->input->post();
        if(empty($data['id'])):
            $this->printJson(['status'=>2,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->packings->updateItemName($data));
        endif;
    }
    
    //Created By KArmi @04/08/2022
    public function printPackingTag(){
        $data = $this->input->post();
        $packingData = $this->packings->getPackingDataForPrint($data['packingid'],$data['item_id']); 
        $jobData = json_decode($packingData->json_data);
        $j=1;$jobCard = "";
        if(!empty($jobData)):
            foreach($jobData as $row):
                if($j==1){$jobCard = $row->batch_no;}else{
                    $jobCard .= ','.$row->batch_no;
                }$j++;
            endforeach;

        endif;
        
        $styleData = '';
		$pageData = Array();$p=1;$pdata = $styleData.'';

		for($i=1;$i<=$data['print_qty'];$i++){
			if($i > 0){	
                if($data['print_id'] == 1):
                        $pdata .= '<div style="width:100mm;height:50mm;text-align:center;float:left;padding:1mm 1mm;">
                                        <table style="width:100%;border:1px solid" class="table top-table-border">
                                            <tr><td style="font-size:13px;"><b>Customer Ref.No.  :- </b>'.$packingData->party_code.'</td></tr>
                                            <tr><td style="font-size:13px;"><b>Item Description  :- </b>'.$packingData->item_name.'</td></tr>
                                            <tr><td style="font-size:13px;"><b>Quantity per Box  :- </b>'.$packingData->qty_per_box.'</td></tr>
                                            <tr><td style="font-size:13px;"><b>Po Number         :- </b>'.$packingData->trans_no.'</td> </tr>
                                            <tr><td style="font-size:13px;"><b>Job Card No.      :- </b>'.$jobCard.'</td></tr>
                                            <tr><td style="font-size:13px;"><b>Gross Weight(kg)  :- </b>'.$packingData->gross_wt.'</td></tr>
                                            <tr><td style="font-size:13px;"><b>Remarks           :- </b> Total Box- '.$packingData->total_box.' ('.$packingData->total_qty.'Pcs)</td></tr>
                                        </table>
                                    </div>';
                elseif($data['print_id'] == 2):
                    $pdata .= '<div style="width:100mm;height:50mm;text-align:center;float:left;padding:1mm 1mm;">
                                        <table style="width:100%;border:1px solid" class="table top-table-border">
                                            <tr><th><u>JAY JALARAM PRECISION COMPONENT LLP</u></th></tr>
                                            <tr><td style="font-size:12px;"><b>DISPATCH DATE    :- </b>'.formatDate($data['dispatch_date']).'</td></tr>
                                            <tr><td style="font-size:12px;"><b>WO/Part No           :- </b>'.$packingData->part_no.'</td></tr>
                                            <tr><td style="font-size:12px;"><b>INVOICE NO.      :- </b>'.$data['inv_no'].'</td></tr>
                                            <tr><td style="font-size:12px;"><b>L.R. NO.         :- </b>'.$data['lr_no'].'  '.$data['trans_way'].'</td></tr>
                                            <tr><td style="font-size:12px;"><b>LOT QTY          :- </b>'.$data['lot_qty'].'</td></tr>
                                            <tr><td style="font-size:12px;"><b>1BOX QTY         :- </b>'.$packingData->qty_per_box.'</td></tr>
                                            <tr><td style="font-size:12px;"><b>BOX QTY          :- </b>'.$packingData->total_box.' BOX</td></tr>
                                        </table>
                                    </div>';
                else:
                    $pdata .= '<div style="width:100mm;height:50mm;text-align:center;float:left;padding:1mm 1mm;">
                                        <table style="width:100%;border:1px solid" class="table top-table-border">
                                            <tr><th><u>JAY JALARAM PRECISION COMPONENT LLP</u></th></tr>
                                            <tr><td style="font-size:12px;"><b>DISPATCH DATE    :- </b>'.formatDate($data['dispatch_date']).'</td></tr>
                                            <tr><td style="font-size:12px;"><b>PART NO          :- </b>'.$packingData->part_no.'</td></tr>
                                            <tr><td style="font-size:12px;"><b>INVOICE NO.      :- </b>'.$data['inv_no'].'  '.$data['trans_way'].'</td></tr>
                                            <tr><td style="font-size:12px;"><b>P.O.NO.          :- </b>'.$packingData->trans_no.'</td></tr>
                                            <tr><td style="font-size:12px;"><b>LOT QTY          :- </b></td></tr>
                                            <tr><td style="font-size:12px;"><b>1BOX QTY         :- </b>'.$packingData->qty_per_box.'</td></tr>
                                            <tr><td style="font-size:12px;"><b>TOTAL BOX QTY    :- </b>'.$packingData->total_box.' BOX</td></tr>
                                        </table>
                                    </div>';
                endif;	
			}
		}
        
		
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 50]]);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->setTitle("Packing Tag ".$data['print_id']);
        $mpdf->AddPage('P','','','','',0,0,3,2,2,2);
        $mpdf->WriteHTML($pdata);
        $mpdf->Output('tags_print.pdf','I');
    }
    
    
    public function getPackingItems(){
        $data=$this->input->post();
        $itemData=$this->packings->getPackingItems($data['id'],'item_id');
        $options ='<option value="">Select Item</option>';
        if(!empty($itemData)){
            foreach($itemData as $row):
                $itemName=(!empty($row->item_code)?'['.$row->item_code.'] ':'').$row->item_name;
                $options .= '<option value="'.$row->item_id.'" >'.$itemName.'</option>';
            endforeach;
        }
        $this->printJson(['status'=>1,'options'=>$options]);
    }
    
    //Created By Meghavi @27/08/2022
    public function getItm(){
        $this->printJson($this->packings->getItemList($this->input->post('id')));
    }
}
?>