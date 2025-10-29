<?php
class Parties extends MY_Apicontroller{
    public function __construct(){
        parent::__construct();
    }

    public function partyList(){
        $partyCategory = ($this->input->post('party_category'))?$this->input->post('party_category'):"";
        $this->data['partyList'] = $this->party->getPartyList($partyCategory);
        $this->printJson(['status'=>1,'message'=>'Recored found.','data'=>$this->data]);
    }
}
?>