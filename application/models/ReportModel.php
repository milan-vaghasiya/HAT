<?php 
class ReportModel extends MasterModel
{
	private $grnTable = "grn_master";
    private $grnItemTable = "grn_transaction";
	private $purchase = "purchase_invoice_master";
    private $purchaseItem = "purchase_invoice_transaction";
    private $itemMaster = "item_master";
    private $partyMaster = "party_master";
    private $purchaseOrderTrans = "purchase_order_trans";    

    public function getInwardRegister($data){
		$data['tableName'] = $this->grnItemTable;
        $data['select'] = "grn_transaction.*,item_master.item_name,grn_master.grn_prefix,grn_master.grn_no,grn_master.grn_date,grn_master.remark,unit_master.unit_name,party_master.party_name";
        $data['join']['item_master'] = "item_master.id = grn_transaction.item_id";
		$data['join']['grn_master'] = "grn_master.id = grn_transaction.grn_id";
        $data['join']['unit_master'] = "grn_transaction.unit_id = unit_master.id";
        $data['join']['party_master'] = "party_master.id = grn_master.party_id";

       return $this->pagingRows($data);
	}

    public function getAutoProduct($data,$automotive){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.*,party_master.party_name,party_master.party_code";
        $data['join']['party_master'] = "party_master.id = item_master.party_id";
        $data['where']['item_master.item_type'] = 1;
        $data['where']['item_master.automotive'] = $automotive;

       return $this->pagingRows($data);
    }

    public function getCustomerAutomotive($data,$automotive=""){
        $data['tableName'] = $this->partyMaster;
        $data['where']['party_master.party_category'] = 1;
        $data['where']['party_master.party_type'] = 1;
        if(!empty($automotive))
            $data['where']['party_master.automotive'] = $automotive;
            
        $data['searchCol'][] = "party_name";
		$data['searchCol'][] = "contact_person";
		$data['searchCol'][] = "party_mobile";
		$data['searchCol'][] = "business_budget";

		$columns = array('', '', 'party_name', 'contact_person', 'party_mobile', 'business_budget');
		if (isset($data['order'])) {
			$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
		}
		return $this->pagingRows($data);
    }

    public function getPurchaseRegister($data){
        $data['tableName'] = $this->purchaseOrderTrans;
        $data['select'] = "purchase_order_trans.*,purchase_order_master.po_date,item_master.item_name,party_master.party_name,purchase_order_master.po_no,purchase_order_master.delivery_date";
        $data['join']['item_master'] = "purchase_order_trans.item_id = item_master.id";
        $data['join']['purchase_order_master'] = "purchase_order_trans.order_id = purchase_order_master.id";
        $data['join']['party_master'] = "purchase_order_master.party_id = party_master.id";
        return $this->pagingRows($data);
    }

    public function getPurchaseInvData($order_id,$item_id){
        $data['tableName'] = $this->purchase;
        $data['select'] = "purchase_invoice_master.*,purchase_invoice_transaction.qty";
        $data['join']['purchase_invoice_transaction'] = "purchase_invoice_master.id = purchase_invoice_transaction.purchase_id";
        $data['where']['purchase_invoice_master.order_id'] = $order_id;
        $data['where']['purchase_invoice_transaction.item_id'] = $item_id;
        $result = $this->rows($data);
        $response['inv_no'] = "";
        $response['inv_date'] = "";
        $response['inv_qty'] = 0; 
        $i=0;
        if(!empty($result))
        {
            foreach($result as $row)
            {
                if($i==0){
                    $response['inv_no'] = $row->inv_no;
                    $response['inv_date'] = $row->inv_date;
                } else {
                    $response['inv_no'] .= ",".$row->inv_no;
                    $response['inv_date'] .= ",".$row->inv_date;
                }
                $response['inv_qty'] += $row->qty;
                $i++; 
            }
        }
        return $response;
    }

    public function getStockStatement($data){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.*,party_master.party_name";
        $data['join']['party_master'] = "party_master.id = item_master.party_id";
        $data['where']['item_master.item_type'] = 1;

        return $this->pagingRows($data);
    }

    public function getStockVerification($data)
    {
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "item_master.*";
        $data['where']['item_master.item_type'] = 1;

        return $this->pagingRows($data);
    }

    public function getSupplierPurchase($data,$type){
        $data['tableName'] = $this->partyMaster;
        $data['where']['party_type'] = $type;
        $data['where']['party_category'] = 3;
        return $this->pagingRows($data);      
    }

    public function getItemData($party_id,$item_type=""){
        $data['tableName'] = $this->purchaseItem;
        $data['select'] = "purchase_invoice_transaction.*,item_master.item_name";
        $data['join']['purchase_invoice_master'] = "purchase_invoice_master.id = purchase_invoice_transaction.purchase_id";
        $data['join']['item_master'] = "purchase_invoice_transaction.item_id = item_master.id";
        $data['where']['purchase_invoice_master.party_id'] = $party_id;
        if(!empty($item_type)):
            $data['where']['item_master.item_type'] = $item_type;
        else:
            $data['where']['item_master.item_type!='] = 2;
        endif;
        $data['group_by'][] = 'purchase_invoice_transaction.item_id';
        $result = $this->rows($data);
        $response['item_name'] = ""; 
        $i=0;
        if(!empty($result))
        {
            foreach($result as $row)
            {
                if($i==0){
                    $response['item_name'] = $row->item_name;
                } else {
                    $response['item_name'] .= ",<br>".$row->item_name;
                }
                $i++; 
            }
        }
        return $response;
    }
}
?>