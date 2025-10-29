<?php
class LeadModel extends MasterModel{
    private $appointmentTable = "crm_appointments";
    private $partyMaster = "party_master";
    private $salesEnquiryMaster = "sales_enquiry";
    private $salesEnquiryTrans = "sales_enquiry_transaction";
    private $salesQuotation = "sales_quotation";
    private $salesQuotationTrans = "sales_quote_transaction";
    private $itemMaster = "item_master";
    private $countries = "countries";
    private $states = "states";
    private $cities = "cities";

    public function getLeadData(){
        $data['tableName'] = $this->partyMaster;
        $data['where']['party_category'] = 1;
        $data['where']['party_type'] = 2;
        return $this->rows($data);
    }

    public function getVendorList(){
        $data['tableName'] = $this->partyMaster;
        $data['where']['party_category'] = 2;
        return $this->rows($data);
    }

    public function getSupplierList(){
        $data['tableName'] = $this->partyMaster;
        $data['where']['party_category'] = 3;
        return $this->rows($data);
    }

    public function getParty($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->partyMaster;
        return $this->row($data);
    }    

    public function save($data){
        try{
            $this->db->trans_begin();
        if($this->checkDuplicate($data['party_name'],$data['id']) > 0):
            $errorMessage['party_name'] = "Company name is duplicate.";
            $result = ['status'=>0,'message'=>$errorMessage];
        else:
            $result = $this->store($this->partyMaster,$data,'Lead');
        endif;
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function checkDuplicate($name,$id=""){
        $data['tableName'] = $this->partyMaster;
        $data['where']['party_name'] = $name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function saveCity($ctname,$state_id,$country_id){
        try{
            $this->db->trans_begin();
        $queryData = ['id'=>'','name'=>$ctname,'state_id'=>$state_id,'country_id'=>$country_id];
        $cityData = $this->store($this->cities,$queryData,'Party');
        $result = $cityData['insert_id'];
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function saveState($statename,$country_id){
        try{
            $this->db->trans_begin();
        $queryData = ['id'=>'','name'=>$statename,'country_id'=>$country_id];
        $stateData = $this->store($this->states,$queryData,'Party');
        $result = $stateData['insert_id'];
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->partyMaster,['id'=>$id],'Party');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function getCountries()
    {
        $data['tableName'] = $this->countries;
        $data['order_by']['name'] = "ASC";
        return $this->rows($data);
    }

    public function getStates($id,$stateId="")
    {
        $data['tableName'] = $this->states;
        $data['where']['country_id'] = $id;
        $data['order_by']['name'] = "ASC";
        $state = $this->rows($data);

        $html= '<option value="">Select State</option>';
        foreach($state as $row):
            $selected = (!empty($stateId) && $row->id == $stateId)?"selected":"";
            $html .= '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
        endforeach;

        return ['status'=>1,'result'=>$html];
    }

    public function getCities($id,$cityId = "")
    {
        $data['tableName'] = $this->cities;
        $data['where']['state_id'] = $id;
        $data['order_by']['name'] = "ASC";
        $city = $this->rows($data);

        $html= '<option value="">Select City</option>';
        foreach($city as $row):
            $selected = (!empty($cityId) && $row->id == $cityId)?"selected":"";
            $html .= '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
        endforeach;

        return ['status'=>1,'result'=>$html];
    }
	
    public function getAppointments($lead_id){
        $data['tableName'] = $this->appointmentTable;
        $data['where']['lead_id'] = $lead_id;
        return $this->rows($data);
    }

    public function setAppointment($data){
		$appointment_status = 0;
		$queryData['where']['lead_id'] = $data['lead_id'];
        $queryData['tableName'] = $this->appointmentTable;
        $prev_appointments = $this->rows($queryData);
        $appointment_status = count($prev_appointments);
        $result = $this->store($this->appointmentTable,$data,'Appointment');
		
		return  $result;
    }

    public function deleteAppointment($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->appointmentTable,['id'=>$id],'Appointment');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function updateAppointmentStatus($data){
        try{
            $this->db->trans_begin();
        $result = $this->store($this->appointmentTable,$data,'Appointment');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function setLeadStatus($data){
        return $this->store($this->partyMaster,$data,'Lead');
    }

	public function getInquiries(){
		$data['tableName'] = $this->salesEnquiryMaster;
		return $this->rows($data);
	}
	
	public function getInquiryData($id){
		$data['tableName'] = $this->salesEnquiryMaster;
		$data['where']['id'] = $id;
		return $this->row($data);
	}
	
	public function getSalesQuotation(){
		$data['tableName'] = $this->salesQuotation;
		return $this->rows($data);
	}
	
	public function getSalesQuotationById($id){
		$data['tableName'] = $this->salesQuotation;
		$data['where']['id'] = $id;
		$result = $this->row($data);
		$transData['tableName'] = $this->salesQuotationTrans;
		$transData['where']['quote_id'] = $id;
		$result->trans = $this->rows($transData);
		return $result;
	}
	
	public function getItemUnit($id){
		$data['tableName'] = 'unit_master';
		$data['where']['id'] = $id;
		return $this->row($data);
		
	}
		
}
?>