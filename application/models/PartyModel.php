<?php
class PartyModel extends MasterModel
{
	private $partyMaster = "party_master";
	private $countries = "countries";
	private $states = "states";
	private $cities = "cities";
	public function getDTRows($data, $party_category, $party_type = 1)
	{
		$data['tableName'] = $this->partyMaster;
		$data['where']['party_category'] = $party_category;
		$data['where']['party_type'] = $party_type;
		if(!empty($data['process_id']) && isset($data['process_id'])){ $data['customWhere'][] = "FIND_IN_SET('".$data['process_id']."', process_id)"; }

		if($party_category != 1){
			$data['searchCol'][] = "party_name";
			$data['searchCol'][] = "contact_person";
			$data['searchCol'][] = "party_mobile";
			$data['searchCol'][] = "party_code";
			$columns = array('', '', 'party_name', 'contact_person', 'party_mobile', 'party_code');
		} else {
			$data['searchCol'][] = "";
			$data['searchCol'][] = "";
			$data['searchCol'][] = "party_name";
			$data['searchCol'][] = "contact_person";
			$data['searchCol'][] = "party_mobile";
			$data['searchCol'][] = "party_code";
			$data['searchCol'][] = "currency";
			$columns = array('', '', 'party_name', 'contact_person', 'party_mobile', 'party_code','currency');
		}
		if (isset($data['order'])) {
			$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
		}
		return $this->pagingRows($data);
	}
	
	public function getCustomerList($party_type = 1)
	{
		$data['tableName'] = $this->partyMaster;
		$data['select'] = 'party_master.*,MAX(item_master.item_code) as last_part';
		$data['leftJoin']['item_master'] = 'item_master.party_id = party_master.id';
		$data['where']['party_master.party_category'] = 1;
		if(!empty($party_type)){$data['where']['party_type'] = $party_type;}
		$data['group_by'][] = 'party_master.id';
		return $this->rows($data);
	}
	
	public function getVendorList($party_type = 1)
	{
		$data['tableName'] = $this->partyMaster;
		$data['where']['party_category'] = 2;
		$data['where']['party_type'] = $party_type;
		return $this->rows($data);
	}
	
	public function getSupplierList($party_type = 1)
	{
		$data['tableName'] = $this->partyMaster;
		$data['where']['party_category'] = 3;
		$data['where']['party_type'] = $party_type;
		return $this->rows($data);
	}
	
	public function getPartyList($party_type="", $party_category=""){
		$data['tableName'] = $this->partyMaster;
		if(!empty($party_category))
			$data['where_in']['party_category'] = $party_category;
		if(!empty($party_type))
			$data['where']['party_type'] = $party_type;
		return $this->rows($data);
	}
	
	// get only parties
	public function getSCSDList($party_category=""){
		$data['tableName'] = $this->partyMaster;
		if(!empty($party_category)){$data['where_in']['party_category'] = $party_category;}
		else{$data['where']['party_category != '] = 4;}
		$data['where']['party_type'] = 1;
		return $this->rows($data);
	}
	
	public function getParty($id){
		$data['tableName'] = $this->partyMaster;
		$data['select'] = 'party_master.*,currency.inrrate';
		$data['leftJoin']['currency'] = 'currency.currency = party_master.currency';
		$data['where']['party_master.id'] = $id;
		return $this->row($data);
	}
	
	public function salesTransactions($id, $limit = "")
	{
		$queryData['tableName'] = 'trans_child';
		$queryData['where']['trans_main_id'] = $id;
		return $this->rows($queryData);
	}
	
	/**Updated BY Mansee @ 27-12-2021 Line No : 104-105 */
	public function save($data)
	{
		try {
			$this->db->trans_begin();
			if ($this->checkDuplicate($data['party_name'], $data['party_category'], $data['id']) > 0) :
				$errorMessage['party_name'] = "Company name is duplicate.";
				$result = ['status' => 0, 'message' => $errorMessage];
			else :
				$data['opening_balance'] = (!empty($data['opening_balance'])) ? $data['opening_balance'] : 0;
				if (empty($data['id'])) :
					$groupCode = ($data['party_category'] == 1) ? "SD" : "SC";
					$groupData = $this->group->getGroupOnGroupCode($groupCode, true);
					$data['group_id'] = $groupData->id;
					$data['group_name'] = $groupData->name;
					$data['group_code'] = $groupData->group_code;
					$data['cl_balance'] = $data['opening_balance'] = $data['opening_balance'] * $data['balance_type'];
				else :
					$partyData = $this->getParty($data['id']);
					$data['opening_balance'] = $data['opening_balance'] * $data['balance_type'];
					if ($partyData->opening_balance > $data['opening_balance']) :
						$varBalance = $partyData->opening_balance - $data['opening_balance'];
						$data['cl_balance'] = $partyData->cl_balance - $varBalance;
					elseif ($partyData->opening_balance < $data['opening_balance']) :
						$varBalance = $data['opening_balance'] - $partyData->opening_balance;
						$data['cl_balance'] = $partyData->cl_balance + $varBalance;
					else :
						$data['cl_balance'] = $partyData->cl_balance;
					endif;
				endif;
				$result = $this->store($this->partyMaster, $data, 'Party');
				$data['party_id'] = (!empty($data['id'])) ? $data['id'] : $result['insert_id'];
				$this->saveGst($data);
			endif;
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}
	public function savePartyApproval($data)
	{
		try {
			$this->db->trans_begin();
			$result = $this->store($this->partyMaster, $data, 'Party');
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}
	public function checkDuplicate($name, $party_category, $id = "")
	{
		$data['tableName'] = $this->partyMaster;
		$data['where']['party_name'] = $name;
		$data['where']['party_category'] = $party_category;
		if (!empty($id))
			$data['where']['id !='] = $id;
		return $this->numRows($data);
	}
	public function saveCity($ctname, $state_id, $country_id)
	{
		try {
			$this->db->trans_begin();
			$queryData = ['id' => '', 'name' => $ctname, 'state_id' => $state_id, 'country_id' => $country_id];
			$cityData = $this->store($this->cities, $queryData, 'Party');
			$result = $cityData['insert_id'];
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}
	public function saveState($statename, $country_id)
	{
		try {
			$this->db->trans_begin();
			$queryData = ['id' => '', 'name' => $statename, 'country_id' => $country_id];
			$stateData = $this->store($this->states, $queryData, 'Party');
			$result = $stateData['insert_id'];
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}
	public function delete($id)
	{
		try {
			$this->db->trans_begin();
			$result = $this->trash($this->partyMaster, ['id' => $id], 'Party');
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
		}
	}
	public function getCountryById($country_id)
	{
		$data['tableName'] = $this->countries;
		$data['where']['id'] = $country_id;
		$data['order_by']['name'] = "ASC";
		return $this->row($data);
	}
	
	public function getCountries()
	{
		$data['tableName'] = $this->countries;
		$data['order_by']['name'] = "ASC";
		return $this->rows($data);
	}
	public function getCurrency()
	{
		$data['tableName'] = 'currency';
		return $this->rows($data);
	}
	public function getStates($id, $stateId = "")
	{
		$data['tableName'] = $this->states;
		$data['where']['country_id'] = $id;
		$data['order_by']['name'] = "ASC";
		$state = $this->rows($data);
		$html = '<option value="">Select State</option>';
		foreach ($state as $row) :
			$selected = (!empty($stateId) && $row->id == $stateId) ? "selected" : "";
			$html .= '<option value="' . $row->id . '" ' . $selected . '>' . $row->name . '</option>';
		endforeach;
		return ['status' => 1, 'result' => $html];
	}
	public function getCities($id, $cityId = "")
	{
		$data['tableName'] = $this->cities;
		$data['where']['state_id'] = $id;
		$data['order_by']['name'] = "ASC";
		$city = $this->rows($data);
		$html = '<option value="">Select City</option>';
		foreach ($city as $row) :
			$selected = (!empty($cityId) && $row->id == $cityId) ? "selected" : "";
			$html .= '<option value="' . $row->id . '" ' . $selected . '>' . $row->name . '</option>';
		endforeach;
		return ['status' => 1, 'result' => $html];
	}
	public function getPartyListOnGroupCode($groupCode = ['"BA"', '"CS"'])
	{
		$data['tableName'] = $this->partyMaster;
		$data['where_in']['group_code'] = $groupCode;
		$result = $this->rows($data);
		return $result;
	}
    public function getCurrencyRate($currency){
		$data['tableName'] = 'currency';
		$data['where']['currency'] = $currency;
		return $this->row($data);
	}
	//-----------  API Function Start -----------//
	public function getPartyList_api($limit, $start, $party_type = 0)
	{
		$data['tableName'] = $this->partyMaster;
		if (!empty($party_type))
			$data['where']['party_category'] = $party_type;
		$data['length'] = $limit;
		$data['start'] = $start;
		return $this->rows($data);
	}
	public function getCount($party_type = 0)
	{
		$data['tableName'] = $this->partyMaster;
		if (!empty($party_type))
			$data['where']['party_category'] = $party_type;
		return $this->numRows($data);
	}
    
    //created By Karmi @28/05/2022
    public function getPartyListOnSystemCode($systemCode)
	{
		$data['tableName'] = $this->partyMaster;
		$data['where']['system_code'] = $systemCode;
		$result = $this->row($data);
		return $result;
	}
	//----------- API Function End -----------//
	/**
	 * Created By  Mansee @ 25-12-2021
	 */
	public function saveGst($data)
	{
		$queryData['where']['id'] = $data['party_id'];
		$queryData['select'] = 'party_master.*';
		$queryData['tableName'] = $this->partyMaster;
		$contactData = $this->row($queryData);
		$contactArr = new stdClass();
		if (!empty($contactData->json_data)) {
			$contactArr = json_decode($contactData->json_data);
		}
		// $data['party_address'] = $contactData->party_address;
		// $data['party_pincode'] = $contactData->party_pincode;
        
        if(empty($data['gstin'])){$data['gstin']='EXP-'.$data['party_id'];}
		$contactArr->{$data['gstin']} =  [
			'party_address' => $data['party_address'],
			'party_pincode' => $data['party_pincode'],
			'delivery_address' => $data['delivery_address'],
			'delivery_pincode' => $data['delivery_pincode']
		];
		return $this->store($this->partyMaster, ['id' => $data['party_id'], 'json_data' => json_encode($contactArr)], 'Party');
	}
	/**
	 * Created By  Mansee @ 25-12-2021
	 */
	public function deleteGst($party_id, $gstin)
	{
		$data['where']['id'] = $party_id;
		$data['select'] = 'json_data';
		$data['tableName'] = $this->partyMaster;
		$contactData = $this->row($data)->json_data;
		$contactArr = json_decode($contactData);
		unset($contactArr->{$gstin});
		$result = $this->store($this->partyMaster, ['id' => $party_id, 'json_data' => json_encode($contactArr)], 'Party');
		return $result;
	}
	
	public function getPartyState($id){
		$data['tableName'] = 'states';
		$data['where']['id'] = $id;
		return $this->row($data);
	}
}