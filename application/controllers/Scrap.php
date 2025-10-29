<?php
class Scrap extends MY_Controller
{
    private $indexPage = "scrap/index";
    private $productionRejScrap = "scrap/rej_scrap_form";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "scrap";
        $this->data['headData']->controller = "scrap";
    }

    public function index()
    {
        $this->data['headData']->pageUrl = "scrap";
        $this->data['tableHeader'] = getProductionHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows()
    {
        $result = $this->scrap->getDTRows($this->input->post());
        $sendData = array();
        $i = 1;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;

            $sendData[] = getScrapData($row);

        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }


    public function generateRejScrap()
    {
        $data = $this->input->post();
        $this->data['locationList'] = $this->scrap->getJobcardList();

        $this->load->view($this->productionRejScrap, $this->data);
    }

    public function saveProductionRejScrape()
    {
        $data = $this->input->post();

        $errorMessage = array();
        if (empty($data['item_id']))
            $errorMessage['item_id'] = "Item is required.";
        if (empty($data['log_sheet_id'][0]))
            $errorMessage['general_error'] = "Scrap data is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :

            if (empty(array_sum($data['scrap_qty'])) && empty(array_sum($data['ok_qty']))) :
                $errorMessage['general_error'] = "Scrap Or Ok Qty is required.";
                $this->printJson(['status' => 0, 'message' => $errorMessage]);
            else :
                $this->printJson($this->scrap->saveProductionRejScrape($data));
            endif;
        endif;
    }

    public function getRejectionBatchList()
    {
        $data = $this->input->post();
        $batchData = $this->scrap->getRejectionStock($data['location_id']);
        // print_r($this->db->last_query());exit;
        $tbody = '';
        if (!empty($batchData)) {
            $i = 1;
            foreach ($batchData as $row) {
                if ($row->qty > 0) {
                    if (!empty($row->trans_ref_id)) {
                        $logdata = $this->rejectionLog->getRejectionData($row->trans_ref_id);
                        foreach ($logdata as $log) {
                            $scrapData = $this->scrap->getScrapBookTransRejData($log->id);
                            $pending_qty=($log->qty - $scrapData->qty);
                            if ($pending_qty > 0) {
                                $tbody .= '<tr>
                                        <td>' . $i . '</td>
                                        <td>' . $row->batch_no . '</td>
                                        <td>' . $row->location . '</td>
                                        <td>' . (!empty($log->reason) ? $log->reason_name : '') . '<input type="hidden" name="rej_reason[]" value="' . $log->reason . '"></td>
                                        <td>' . (!empty($log->belongs_to_name) ? $log->belongs_to_name : '') . '<input type="hidden" name="rej_stage[]" value="' . (!empty($log->belongs_to) ? $log->belongs_to_name : '') . '">
                                        <input type="hidden" name="log_sheet_id[]" value="' . (!empty($row->id) ? $row->id : '') . '"></td>
                                        <td>' . (!empty($log->vendor_name) ? $log->vendor_name : $log->vendor_name) . '<input type="hidden" value="' . $log->vendor_id . '" name="rej_from[]"></td>
                                        <td>' .$pending_qty . '</td>
                                        <td>
                                            <input type="hidden" name="rej_log_id[]"  value="'.$log->id.'">
                                            <input type="text" name="scrap_qty[]" class="form-control numericOnly batchQty" data-pending_qty="'.$pending_qty.'" data-rowid="' . $i . '"  id="scrapQty'.$i.'" value="0">
                                            <input type="hidden" name="batch_no[]" class="form-control" value="' . $row->batch_no . '">
                                            <input type="hidden" name="ref_no[]" class="form-control" value="' . $row->ref_no . '">
                                            <input type="hidden" name="ref_id[]" class="form-control" value="' . $row->ref_id . '">
                                            <input type="hidden" name="location_id[]" class="form-control" value="' . $row->location_id . '">
                                            <div class="error batch_qty' . $i . '"></div>
                                        </td>
                                        <td>
                                            <input type="text" name="ok_qty[]" data-pending_qty="'.$pending_qty.'" class="form-control numericOnly batchQty" data-rowid="' . $i . '" data-rej_qty="' . $row->qty . '" id="okQty'.$i.'" value="0">
                                          
                                        </td>
                                    </tr>';
                                $i++;
                            }
                        }
                    } 
                }
            }
        } else {
            $tbody = '<tr id="noData"><td class="text-center" colspan="9">No Data Found.</td></tr>';
        }
        
        if(empty($tbody)){ $tbody = '<tr id="noData"><td class="text-center" colspan="9">No Data Found.</td></tr>'; }

        $this->printJson(['status' => 1, 'tbody' => $tbody]);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $this->printJson($this->scrap->delete($id));
        endif;
    }

    public function getTransList(){
        $data=$this->input->post();
        $scrapData = $this->scrap->getScrapBookTransData($data['id']);
        $htmlData='';
        $i=1;
        foreach($scrapData as $row){
            $comment=$this->comment->getComment($row->rej_reason);
            $process_name=!empty($row->rej_stage)?$this->process->getProcess($row->rej_stage)->process_name:'Row Material';
            $vendor_name=!empty($row->rej_from)?$this->party->getParty($row->rej_from)->party_name:'In House';
            $htmlData.='<tr>
                <td>'.$i++.'</td>
                <td>'.$row->process_name.'</td>
                <td>'.$comment->remark.'</td>
                <td>'.$process_name.'</td>
                <td>'.$vendor_name.'</td>
                <td>'.$row->scrap_qty.'</td>
                <td>'.$row->ok_qty.'</td>
            </tr>';
        }
        $this->printJson(['status'=>1,'htmlData'=>$htmlData]);
    }
}
