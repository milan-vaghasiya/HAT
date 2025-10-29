<?php
class GeneralIssueModel extends MasterModel
{
    private $generalIssue = "general_issue";
    private $itemMaster = "item_master";
    // private general_issue = "general_issue";
 
    public function getDTRows($data){
        $data['tableName'] = $this->generalIssue;
        $data['select']='general_issue.*,item_master.item_code,item_master.item_name,issue_by.emp_name as issue_by,collect_by.emp_name as collect_by,item_category.is_return,location_master.store_name,location_master.location';
        $data['leftJoin']['item_master'] = 'item_master.id = general_issue.item_id';
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['leftJoin']['department_master'] = 'department_master.id = general_issue.dept_id';
        $data['leftJoin']['employee_master as issue_by'] = 'issue_by.id = general_issue.dispatch_by';
        $data['leftJoin']['employee_master as collect_by'] = 'collect_by.id = general_issue.collected_by';
        $data['leftJoin']['location_master'] = "location_master.id = general_issue.location_id";

        $data['where']['general_issue.dispatch_date >= '] = $this->startYearDate;
        $data['where']['general_issue.dispatch_date <= '] = $this->endYearDate;
        $data['where']['general_issue.issue_type'] = 2;

        if($data['status'] == 0):
            $data['where']['item_category.is_return'] = 0;
        elseif($data['status'] == 1):
            $data['where']['(general_issue.dispatch_qty - general_issue.return_qty) >'] = 0;
        else:
            $data['where']['(general_issue.dispatch_qty - general_issue.return_qty) <='] = 0;
        endif;

        $data['searchCol'][] = "general_issue.req_no";
        $data['searchCol'][] = "DATE_FORMAT(general_issue.dispatch_date,'%d-%m-%Y')";        
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "general_issue.batch_no";
        $data['searchCol'][] = "general_issue.dispatch_qty";
        $data['searchCol'][] = "issue_by.emp_name";
        $data['searchCol'][] = "collect_by.emp_name";
        
        $columns = array('', '', 'general_issue.req_no','DATE_FORMAT(general_issue.dispatch_date,"%d-%m-%Y")', 'item_master.item_name', 'general_issue.batch_no', 'general_issue.dispatch_qty', 'issue_by.emp_name', 'collect_by.emp_name');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

    public function getNextReqNo($type = 2){
        $queryData = array();
        $queryData['tableName'] = $this->generalIssue;
        $queryData['select'] = "ifnull((MAX(req_no) + 1),1) as req_no";
        $queryData['where']['issue_type'] = $type;
        $queryData['where']['req_date >= '] = $this->startYearDate;
        $queryData['where']['req_date <= '] = $this->endYearDate;
        $result = $this->row($queryData);
        return $result->req_no;
    }

    public function getIssueData($req_no){
        $queryData = array();
        $queryData['tableName'] = $this->generalIssue;
        $queryData['select'] = "general_issue.*,item_master.item_code,item_master.item_name,issue_by.emp_name as issue_by,collect_by.emp_name as collect_by,location_master.store_name,location_master.location";
        $queryData['leftJoin']['item_master'] = 'item_master.id = general_issue.item_id';
        $queryData['leftJoin']['department_master'] = 'department_master.id = general_issue.dept_id';
        $queryData['leftJoin']['employee_master as issue_by'] = 'issue_by.id = general_issue.dispatch_by';
        $queryData['leftJoin']['employee_master as collect_by'] = 'collect_by.id = general_issue.collected_by';
        $queryData['leftJoin']['location_master'] = "location_master.id = general_issue.location_id";

        $queryData['where']['CONCAT(general_issue.req_prefix,general_issue.req_no)'] = $req_no;
        $result = $this->rows($queryData);
        return $result;
    }

    public function save($data){ 
        try {
            $this->db->trans_begin();

            if(!empty($data['req_no'])):
                $req_no = $data['req_prefix'].$data['req_no'];
                $issueData = $this->getIssueData($req_no);
                foreach($issueData as $row):
                    $setData = array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $row->item_id;
                    $setData['set']['qty'] = 'qty, + ' . abs($row->dispatch_qty);
                    $qryresult = $this->setValue($setData);

                    $this->remove('stock_transaction', ['ref_id' => $row->id, 'ref_type' => 34]);
                    $this->trash($this->generalIssue,['id'=>$row->id]);
                endforeach;
            else:
                $data['req_prefix'] = n2y(date('Y'));
                $data['req_no'] = $this->getNextReqNo(2);
            endif;

            foreach ($data['item_id'] as $key => $value) :
                $issueData = [
                    'id' => $data['id'][$key],
                    'req_prefix' => $data['req_prefix'],
                    'req_no' => $data['req_no'],
                    'req_date' => $data['dispatch_date'],
                    'collected_by' => $data['collected_by'],
                    'dept_id' => $data['dept_id'],
                    'item_id' => $value,
                    'location_id' => $data['location_id'][$key],
                    'batch_no' => $data['batch_no'][$key],
                    'dispatch_date' => $data['dispatch_date'],
                    'dispatch_qty' => $data['batch_qty'][$key],
                    'dispatch_by' => $data['dispatch_by'],
                    'created_by'=>$data['created_by'],
                    'remark' => $data['remark'],
                    'issue_type' => 2,
                    'is_delete' => 0
                ];
                $saveIssueData = $this->store($this->generalIssue, $issueData);
                $issueId = (empty($data['id'][$key]))?$saveIssueData['insert_id']:$data['id'][$key];

                $stockTrans = [
                    'id' => "",
                    'location_id' => $data['location_id'][$key],
                    'batch_no' => $data['batch_no'][$key],
                    'trans_type' => 2,
                    'item_id' => $value,
                    'qty' => "-" . $data['batch_qty'][$key],
                    'ref_type' => 34,
                    'ref_id' => $issueId,
                    'ref_no' => $data['req_prefix'].$data['req_no'],
                    'ref_date' => $data['dispatch_date'],
                    'created_by' => $data['created_by']
                ];

                $this->store('stock_transaction', $stockTrans);

                $setData = array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $value;
                $setData['set']['qty'] = 'qty, - ' . $data['batch_qty'][$key];
                $this->setValue($setData);
            endforeach;
            $result =  ['status' => 1, 'message' => 'Material Issue suucessfully.'];

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function delete($req_no)
    {
        try {
            $this->db->trans_begin();
            $issueData = $this->getIssueData($req_no);
            foreach($issueData as $row):
                $setData = array();
                $setData['tableName'] = $this->itemMaster;
                $setData['where']['id'] = $row->item_id;
                $setData['set']['qty'] = 'qty, + ' . abs($row->dispatch_qty);
                $qryresult = $this->setValue($setData);

                $this->remove('stock_transaction', ['ref_id' => $row->id, 'ref_type' => 34]);
                $this->trash($this->generalIssue,['id'=>$row->id]);
            endforeach;

            $result = ['status'=>1,'message'=>"Material Removed suucessfully."];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }

    public function getIssueItemData($id){
        $queryData = array();
        $queryData['tableName'] = $this->generalIssue;
        $queryData['select'] = "general_issue.*,item_master.item_code,item_master.item_name,issue_by.emp_name as issue_by,collect_by.emp_name as collect_by,location_master.store_name,location_master.location";
        $queryData['leftJoin']['item_master'] = 'item_master.id = general_issue.item_id';
        $queryData['leftJoin']['department_master'] = 'department_master.id = general_issue.dept_id';
        $queryData['leftJoin']['employee_master as issue_by'] = 'issue_by.id = general_issue.dispatch_by';
        $queryData['leftJoin']['employee_master as collect_by'] = 'collect_by.id = general_issue.collected_by';
        $queryData['leftJoin']['location_master'] = "location_master.id = general_issue.location_id";

        $queryData['where']['general_issue.id'] = $id;
        $result = $this->row($queryData);
        return $result;
    }

    public function saveReturnMaterial($data){
        try {
            $this->db->trans_begin();

            $issueItemData = $this->getIssueItemData($data['id']);

            $transData = [
                'id' => '',
                'location_id' => $data['location_id'],
                'batch_no' => $data['batch_no'],
                'trans_type' => 1,
                'item_id' => $data['item_id'],
                'qty' => $data['return_qty'],
                'ref_type' => 34,
                'ref_id' => $data['id'],
                'ref_no' => $issueItemData->req_prefix.$issueItemData->req_no,
                'ref_date' => date("Y-m-d"),
                'remark' => $data['remark'],
                'created_by' => $this->loginId
            ];
            $this->store('stock_transaction',$transData);

            $setData = array();
            $setData['tableName'] = $this->generalIssue;
            $setData['where']['id'] = $data['id'];
            $setData['set']['return_qty'] = 'return_qty, + ' . $data['return_qty'];
            $qryresult = $this->setValue($setData);

            $setData = array();
            $setData['tableName'] = $this->itemMaster;
            $setData['where']['id'] = $data['item_id'];
            $setData['set']['qty'] = 'qty, + ' . $data['return_qty'];
            $qryresult = $this->setValue($setData);

            $result = ['status'=>1,'message'=>"Material Returned suucessfully."];
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
}
