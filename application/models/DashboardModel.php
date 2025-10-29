<?php
class DashboardModel extends MasterModel{
	
    public function getTodayPurchase(){
        $data['tableName'] = "grn_transaction";
        $data['select'] = 'SUM(grn_transaction.price*grn_transaction.qty) AS purchase_amount';
        $data['join']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
        $data['where']['grn_master.grn_date'] = date("Y-m-d");
        return $this->row($data);
    }

    
    public function getTodaySales(){
        $data['tableName'] = "trans_main";
        $data['select'] = 'SUM(trans_main.taxable_amount) AS sales_amount';
        $data['where']['trans_main.trans_date'] = date("Y-m-d");
        $data['where']['trans_main.entry_type'] =6;
        return $this->row($data);
    }

    public function getProductionAnalysis(){
        $data['tableName']='job_approval';
        $data['select'] ='SUM(job_approval.in_qty - job_approval.total_prod_qty) AS pend_prod_qty,SUM(job_approval.ok_qty - job_approval.total_out_qty) AS pend_move_qty,(CASE WHEN job_approval.in_process_id = 0 THEN "Raw Material" ELSE process_master.process_name END ) AS process_name';
        $data['leftJoin']['process_master'] = "process_master.id = job_approval.in_process_id";
        $data['leftJoin']['job_card'] = "job_card.id = job_approval.job_card_id";
        $data['where']['job_card.order_status'] = 2;
        $data['having'][]='(SUM(job_approval.in_qty - job_approval.total_prod_qty)>0 OR SUM(job_approval.ok_qty - job_approval.total_out_qty)>0)';
        $data['group_by'][]='job_approval.in_process_id';
        return $this->rows($data);
    }

    public function getTopProduct(){
        $data['tableName'] = "trans_child";
        $data['select'] = 'SUM(trans_child.taxable_amount) AS sales_amount,item_master.item_name,item_master.item_code';
        $data['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $data['where']['trans_child.entry_type'] =6;
        $data['group_by'][] = 'trans_child.item_id';
        $data['order_by']['SUM(trans_child.taxable_amount)'] ='DESC';
        $data['limit'] =10;
        return $this->rows($data);
    }
}
?>