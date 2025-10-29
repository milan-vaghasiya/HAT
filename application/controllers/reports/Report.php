<?php
class Report extends MY_Controller
{
    private $irPage = "report/index";
    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Report";
		$this->data['headData']->controller = "reports/report";
	}
	
	public function index(){
        $this->load->view($this->irPage,$this->data);
    }

    /* INWARD REGISTER (STORE) [F ST 01 (00/01.06.20)] */    
    public function inwardRegister(){
        $this->data['pageHeader'] = 'INWARD REGISTER (STORE) [F ST 01 (00/01.06.20)]';
        $this->data['dataUrl'] = 'getInwardRegister';
        $this->data['tableHeader'] = getReportHeader("inwardRegister");
        $this->load->view($this->irPage,$this->data);
    }

    public function getInwardRegister(){        
        $result = $this->reportModel->getInwardRegister($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $sendData[] = [$i++,formatDate($row->grn_date),getPrefixNumber($row->grn_prefix,$row->grn_no),$row->party_name,$row->item_name,$row->qty,$row->unit_name,$row->remark,''];
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    /* LIST  OF NON  PRODUCTS AUTOMOTIVE */
    public function nonAutoProduct(){
        $this->data['pageHeader'] = 'LIST OF PRODUCTS NON AUTOMOTIVE';
        $this->data['dataUrl'] = 'getNonAutoProduct';
        $this->data['tableHeader'] = getReportHeader("nonAutoProduct");
        $this->load->view($this->irPage,$this->data);
    }

    public function getNonAutoProduct(){        
        $result = $this->reportModel->getAutoProduct($this->input->post(),2);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $sendData[] = [$i++,$row->item_name,$row->drawing_no,$row->item_code,$row->party_code,''];
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    /* LIST  OF  PRODUCTS AUTOMOTIVE */
    public function autoProduct(){
        $this->data['pageHeader'] = 'LIST OF PRODUCTS AUTOMOTIVE';
        $this->data['dataUrl'] = 'getAutoProduct';
        $this->data['tableHeader'] = getReportHeader("autoProduct");
        $this->load->view($this->irPage,$this->data);
    }

    public function getAutoProduct(){        
        $result = $this->reportModel->getAutoProduct($this->input->post(),1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $sendData[] = [$i++,$row->item_name,$row->drawing_no,$row->item_code,$row->party_name,''];
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    /* LIST OF CUSTOMERS AUTOMOTIVE */
    public function customerAutomotive(){
        $this->data['pageHeader'] = 'LIST OF CUSTOMERS AUTOMOTIVE';
        $this->data['dataUrl'] = 'getCustomerAutomotive';
        $this->data['tableHeader'] = getReportHeader("customerAutomotive");
        $this->load->view($this->irPage,$this->data);
    }

    public function getCustomerAutomotive(){        
        $result = $this->reportModel->getCustomerAutomotive($this->input->post(),1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $sendData[] = [$i++,$row->party_name."<br><small>".$row->party_address."</small>",$row->contact_person,$row->party_phone,'',$row->party_mobile,$row->party_email,''];
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    /* LIST OF CUSTOMERS GENERALS */
    public function customerGenerals(){
        $this->data['pageHeader'] = 'LIST OF CUSTOMERS NON AUTOMOTIVE';
        $this->data['dataUrl'] = 'getCustomerGenerals';
        $this->data['tableHeader'] = getReportHeader("customerGenerals");
        $this->load->view($this->irPage,$this->data);
    }

    public function getCustomerGenerals(){        
        $result = $this->reportModel->getCustomerAutomotive($this->input->post(),2);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $sendData[] = [$i++,$row->party_name."<br><small>".$row->party_address."</small>",$row->contact_person,$row->party_phone,'',$row->party_mobile,$row->party_email,''];
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    /* PURCHASE MONITORING REGISTER */
    public function purchaseRegister(){
        $this->data['pageHeader'] = 'PURCHASE MONITORING REGISTER';
        $this->data['dataUrl'] = 'getPurchaseRegister';
        $this->data['tableHeader'] = getReportHeader("purchaseRegister");
        $this->load->view($this->irPage,$this->data);
    }

    public function getPurchaseRegister(){        
        $result = $this->reportModel->getPurchaseRegister($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $invData = (object) $this->reportModel->getPurchaseInvData($row->order_id,$row->item_id);
            $invData->inv_date = (!empty($invData->inv_date))? formatDate($invData->inv_date) : "";
            $row->delivery_date = (!empty($row->delivery_date))? formatDate($row->delivery_date) : "";
            $sendData[] = [$i++,formatDate($row->po_date),$row->item_name,$row->party_name,$row->po_no,$row->qty,$row->delivery_date,$invData->inv_no,$invData->inv_date,$invData->inv_qty,'',''];
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    /* STOCK STATEMENT (Finish Product) */
    public function stockStatement(){
        $this->data['pageHeader'] = 'STOCK STATEMENT (Finish Product)';
        $this->data['dataUrl'] = 'getStockStatement';
        $this->data['tableHeader'] = getReportHeader("stockStatement");
        $this->load->view($this->irPage,$this->data);
    }

    public function getStockStatement(){        
        $result = $this->reportModel->getStockStatement($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $sendData[] = [$i++,$row->item_code,$row->item_name,$row->party_name,$row->drawing_no,$row->rev_no,$row->qty,'',''];
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    /* STOCK VERIFICATION REPORT */
    public function stockVerification(){
        $this->data['pageHeader'] = 'STOCK VERIFICATION REPORT';
        $this->data['dataUrl'] = 'getStockVerification';
        $this->data['tableHeader'] = getStoreDtHeader("stockVerification");
        $this->load->view($this->irPage,$this->data);
    }

    public function getStockVerification(){        
        $result = $this->reportModel->getStockVerification($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $sendData[] = [$i++,$row->item_name,$row->item_code,$row->qty,'','',''];
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    /* LIST OF APPROVED SUPPLIER (PURCHASE) */
    public function supplierPurchase(){
        $this->data['pageHeader'] = 'LIST OF APPROVED EXTERNAL PROVIDER - D/PUR/01 (01/01.06.18)';
        $this->data['dataUrl'] = 'getSupplierPurchase';
        $this->data['tableHeader'] = getReportHeader("supplierPurchase");
        $this->load->view($this->irPage,$this->data);
    }

    public function getSupplierPurchase(){        
        $result = $this->reportModel->getSupplierPurchase($this->input->post(),1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $itemData = (object) $this->reportModel->getItemData($row->id);
            $row->approved_date = (!empty($row->approved_date))? formatDate($row->approved_date) : "";
            $sendData[] = [$i++,$row->party_name."<br><small>".$row->party_address."</small>",$row->party_phone,$row->supplied_types,$row->approved_base,$row->approved_date,$row->approved_by,''];
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    /* LIST  OF  APPROVED  SUPPLIER   (SERVICE) */
    public function supplierService(){
        $this->data['pageHeader'] = 'LIST OF APPROVED SUPPLIER (SERVICE)';
        $this->data['dataUrl'] = 'getSupplierService';
        $this->data['tableHeader'] = getReportHeader("supplierService");
        $this->load->view($this->irPage,$this->data);
    }

    public function getSupplierService(){        
        $result = $this->reportModel->getSupplierPurchase($this->input->post(),1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $itemData = (object) $this->reportModel->getItemData($row->id,2);
            $row->approved_date = (!empty($row->approved_date))? formatDate($row->approved_date) : "";
            $sendData[] = [$i++,$row->party_code,$row->party_name."<br><small>".$row->party_address."</small>",$row->contact_person,$row->party_phone,'',$row->party_email,$row->party_mobile,$itemData->item_name,$row->approved_date,$row->approved_by,$row->approved_base,''];
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
}
?>