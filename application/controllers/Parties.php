<?php
class Parties extends MY_Controller
{
    private $indexPage = "party/index";
    private $partyForm = "party/form";
    private $automotiveArray = ["1" => 'Yes', "2" => "No"];
    private $contactForm = "party/contact_form";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Parties";
        $this->data['headData']->controller = "parties";
        $this->data['suppliedTypes'] = array('Goods', 'Services', 'Goods,Services');
        $this->data['vendorTypes'] = array('Manufacture', 'Service');
    }

    public function index()
    {
        $this->data['headData']->pageUrl = "parties";
        $this->data['party_category'] = 1;
        $this->data['tableHeader'] = getSalesDtHeader("customer");
        $this->load->view($this->indexPage, $this->data);
    }

    public function vendor()
    {
        $this->data['headData']->pageUrl = "parties/vendor";
        $this->data['party_category'] = 2;
        $this->data['processData'] = $this->process->getProcessList();
        $this->data['tableHeader'] = getProductionHeader("vendor");
        $this->load->view($this->indexPage, $this->data);
    }

    public function supplier()
    {
        $this->data['headData']->pageUrl = "parties/supplier";
        $this->data['party_category'] = 3;
        $this->data['tableHeader'] = getPurchaseDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($party_category)
    {
        $result = $this->party->getDTRows($this->input->post(), $party_category);
        $sendData = array();
        $i = 1;
        $count = 0;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getPartyData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addParty($party_category)
    {
        $this->data['party_category'] = $party_category;
        $this->data['currencyData'] = $this->party->getCurrency();
        $this->data['countryData'] = $this->party->getCountries();
        $this->data['salesExecutives'] = $this->employee->getsalesExecutives();
        $this->data['processDataList'] = $this->process->getProcessList();
        $this->data['automotiveArray'] = $this->automotiveArray;
        $this->load->view($this->partyForm, $this->data);
    }

    public function save()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if (empty($data['party_name']))
            $errorMessage['party_name'] = "Company name is required.";
        if (empty($data['party_category']))
            $errorMessage['party_category'] = "Party Category is required.";
        if (empty($data['contact_person']))
            $errorMessage['contact_person'] = "Contact Person is required.";
        if (empty($data['party_mobile']))
            $errorMessage['party_mobile'] = "Contact No. is required.";
        if (empty($data['country_id']))
            $errorMessage['country_id'] = 'Country is required.';
        if (empty($data['supplied_types']))
            $errorMessage['supplied_types'] = 'Supplied Types are required.';
        if ($data['country_id'] == 101) {
            if (empty($data['gstin']))
                $errorMessage['gstin'] = 'Gstin is required.';
        }
        if (empty($data['state_id'])) {
            if (empty($data['statename']))
                $errorMessage['state_id'] = 'State is required.';
            else
                $data['state_id'] = $this->party->saveState($data['statename'], $data['country_id']);
        }
        if ($data['party_category'] == 2) {
            if (empty($data['process_id']))
                $errorMessage['processSelect'] = 'Production Process is required.';
        } 
        unset($data['statename'], $data['processSelect'],$data['pcode']);
        if (empty($data['city_id'])) {
            if (empty($data['ctname']))
                $errorMessage['city_id'] = 'City is required.';
            else
                $data['city_id'] = $this->party->saveCity($data['ctname'], $data['state_id'], $data['country_id']);
        }
        unset($data['ctname']);
        if (!empty($data['opening_balance']) && empty($data['balance_type']))
            $errorMessage['opening_balance'] = "Please select Type.";
        if (empty($data['party_address']))
            $errorMessage['party_address'] = "Address is required.";
        if (empty($data['party_pincode']))
            $errorMessage['party_pincode'] = "Address Pincode is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['party_name'] = ucwords($data['party_name']);
            $this->printJson($this->party->save($data));
        endif;
    }

    public function edit()
    {
        $id = $this->input->post('id');
        $result = $this->party->getParty($id);
        $result->state = $this->party->getStates($result->country_id, $result->state_id)['result'];
        $result->city = $this->party->getCities($result->state_id, $result->city_id)['result'];
        $this->data['dataRow'] = $result;
        $this->data['processDataList'] = $this->process->getProcessList();
        $this->data['currencyData'] = $this->party->getCurrency();
        $this->data['countryData'] = $this->party->getCountries();
        $this->data['salesExecutives'] = $this->employee->getsalesExecutives();
        $this->data['automotiveArray'] = $this->automotiveArray;
        $this->load->view($this->partyForm, $this->data);
    }

    public function partyDetails()
    {
        $id = $this->input->post('id');
        $result = $this->party->getParty($id);
        $this->printJson($result);
    }

    public function delete()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->party->delete($id));
        endif;
    }

    public function getStates()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $this->printJson($this->party->getStates($id));
        endif;
    }

    public function getCities()
    {
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'message' => 'Something went wrong...Please try again.']);
        else :
            $this->printJson($this->party->getCities($id));
        endif;
    }

    public function partyApproval()
    {
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->party->getParty($id);
        $this->load->view("party/approval_form", $this->data);
    }

    public function savePartyApproval()
    {
        $data = $this->input->post();

        $errorMessage = array();
        if (empty($data['approved_date']))
            $errorMessage['approved_date'] = "Approved Date is required.";
        if (empty($data['approved_by']))
            $errorMessage['approved_by'] = "Approved By is required.";
        if (empty($data['approved_base']))
            $errorMessage['approved_base'] = "Approved Base is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $data['approved_date'] = (!empty($data['approved_date'])) ? date('Y-m-d', strtotime($data['approved_date'])) : null;
            $this->printJson($this->party->savePartyApproval($data));
        endif;
    }


    /**
     * Created BY Mansee @ 25-12-2021
     */
    public function getGstDetail()
    {
        $party_id = $this->input->post('id');
        $result = $this->party->getParty($party_id);

        $this->data['json_data'] = json_decode($result->json_data);
        $this->data['party_id'] = $party_id;
        $this->load->view($this->contactForm, $this->data);
    }
    /**
     * Created BY Mansee @ 25-12-2021
     */
    public function saveGst()
    {
        $data = $this->input->post();

        $errorMessage = array();
        if (empty($data['gstin']))
            $errorMessage['gstin'] = "GST is required.";
        if (empty($data['delivery_address']))
            $errorMessage['delivery_address'] = "Address is required.";
        if (empty($data['delivery_pincode']))
            $errorMessage['delivery_pincode'] = "Pincode is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :

            $response = $this->party->saveGst($data);

            $result = $this->party->getParty($data['party_id']);
            $json_data = json_decode($result->json_data);
            $i = 1;
            $tbodyData = "";
            if (!empty($json_data)) :
                foreach ($json_data as $key => $row) :
                    $tbodyData .= '<tr>
                                <td>' .  $i++ . '</td>
                                <td>' . $key . '</td>
                                <td>' . $row->party_address . '</td>
                                <td>' . $row->party_pincode . '</td>
                                <td>' . $row->delivery_address . '</td>
                                <td>' . $row->delivery_pincode . '</td>
                                <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="trashGst(\'' . $key . '\')"><i class="ti-trash"></i></a>
                                </td>
                            </tr> ';
                endforeach;
            else :
                $tbodyData .= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status' => 1, "tbodyData" => $tbodyData, "partyId" => $data['party_id']]);
        endif;
    }
    /**
     * Created BY Mansee @ 25-12-2021
     */
    public function deleteGst()
    {
        $party = $this->input->post();
        if (empty($party['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->party->deleteGst($party['id'], $party['gstin']);

            $result = $this->party->getParty($party['id']);
            $json_data = json_decode($result->json_data);
            $i = 1;
            $tbodyData = "";
            if (!empty($json_data)) :
                foreach ($json_data as $key => $row) :
                    $tbodyData .= '<tr>
                                <td>' .  $i++ . '</td>
                                <td>' . $key . '</td>
                                <td>' . $row->party_address . '</td>
                                <td>' . $row->party_pincode . '</td>
                                <td>' . $row->delivery_address . '</td>
                                <td>' . $row->delivery_pincode . '</td>
                                <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="trashGst(\'' . $key . '\');"><i class="ti-trash"></i></a>
                                </td>
                            </tr> ';
                endforeach;
            else :
                $tbodyData .= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status' => 1, "tbodyData" => $tbodyData, "partyId" => $party['id']]);
        endif;
    }
}
