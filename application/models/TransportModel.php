<?php
class TransportModel extends MasterModel{
    private $transportMaster = "transport_master";

    public function getDTRows($data){
        $data['tableName'] = $this->transportMaster;
        return $this->pagingRows($data);
    }

    public function getTransport($id){
        $data['tableName'] = $this->transportMaster;
        $data['where']['id'] = $id;
        return $this->row($data);
    }
	
    public function getTransportList(){
        $data['tableName'] = $this->transportMaster;
        return $this->rows($data);
    }

    public function save($data){
		return $this->store($this->transportMaster,$data);
	}

    public function delete($id){
		$itemData = $this->getTransport($id);
        return $this->trash($this->transportMaster,['id'=>$id]);
    }
	
}