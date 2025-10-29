<?php
class EmployeeFacilityModel extends MasterModel
{
    private $employee_facility = "facility_master";
	
    public function getDTRows($data)
    {
        $data['tableName'] = $this->employee_facility;
        $data['searchCol'][] = "ficility_type";
        $data['serachCol'][] = "is_returnable";
		$columns = array('','','ficility_type','is_returnable');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getEmployeeFacility($id)
    {
        $data['where']['id'] = $id;
        $data['tableName'] = $this->employee_facility;
        return $this->row($data);
    }

    public function save($data)
    {
        return $this->store($this->employee_facility,$data);
    }

    public function delete($id)
    {
        return $this->trash($this->employee_facility,['id'=>$id]);
    }

    public function getEmployeeFacilityList()
    {
        $data['tableName'] = $this->employee_facility;
        return $this->rows($data);
    }
}
?>