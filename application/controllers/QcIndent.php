<?php
class QcIndent extends MY_Controller
{
    private $indexPage = "qc_indent/index";
    private $form = "qc_indent/form";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "QCIndent";
        $this->data['headData']->controller = "qcIndent";
        $this->data['headData']->pageUrl = "qcIndent";
    }

    public function index()
    {
        $this->data['tableHeader'] = getQualityDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($status = 0)
    {
        $data = $this->input->post();
        $data['status'] = $status;
        $result = $this->qcIndent->getDTRows($data);
        $sendData = array();
        $i = 1;
        $count = 0;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;

            $sendData[] = getQCIndentData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function approvePreq()
    {
        $data = $this->input->post();

        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $this->printJson($this->purchaseRequest->approvePreq($data));
        endif;
    }

    public function closePreq()
    {
        $data = $this->input->post();

        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $this->printJson($this->purchaseRequest->closePreq($data));
        endif;
    }

    /*  Created By : Avruti @7-12-2021 04:00 PM
        update by : 
        note : po
    */
    public function getPurchaseOrder()
    {
        $data = $this->input->post();
        $this->printJson($this->purchaseRequest->getPurchaseOrder());
    }

    public function edit()
    {
        $data = $this->input->post();
        $this->data['itemData'] = $this->item->getItemLists(str_replace('~', ',', '1~2~3~4~5~6~7'));
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['planningType'] = $this->purchaseRequest->getPurchasePlanningType();
        $this->data['dataRow'] = $this->qcIndent->getPurchaseIndent($data['id']);
        $itemData = $this->item->getItem($this->data['dataRow']->req_item_id);
        $this->data['dataRow']->unit_id = $itemData->unit_id;
        $this->data['dataRow']->item_description = $itemData->description;
        $this->data['dataRow']->min_qty = $itemData->min_qty;
        $this->data['dataRow']->max_qty = $itemData->max_qty;
        $this->data['dataRow']->make_brand = $itemData->make_brand;
        $this->data['dataRow']->lead_time = $itemData->lead_time;

        $this->load->view($this->form, $this->data);
    }

    public function viewPurchaseReq()
    {
        $data = $this->input->post();
        $this->data['dataRow'] = $this->qcIndent->getPurchaseIndent($data['id']);
        $itemData = $this->item->getItem($this->data['dataRow']->req_item_id);
        $this->data['dataRow']->unit_id = $itemData->unit_id;
        $this->data['dataRow']->item_description = $itemData->description;
        $this->data['dataRow']->min_qty = $itemData->min_qty;
        $this->data['dataRow']->max_qty = $itemData->max_qty;
        $this->data['dataRow']->make_brand = $itemData->make_brand;
        $this->data['dataRow']->lead_time = $itemData->lead_time;
        $this->data['dataRow']->drawing_no = $itemData->drawing_no;
        $this->data['dataRow']->item_image = $itemData->item_image;
        $this->load->view("purchase_request/purchase_req_view", $this->data);
    }
}