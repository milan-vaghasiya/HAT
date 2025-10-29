<?php
class HolidaysModel extends MasterModel{
    private $holidays = "holidays";
    public function getDTRows($data){
        $data['tableName'] = $this->holidays;

        $data['where']['holidays.holiday_date >='] = $this->startYearDate;
        $data['where']['holidays.holiday_date <='] = $this->endYearDate;

        $data['searchCol'][] = "holiday_date";
        $data['searchCol'][] = "holiday_type";
        $data['searchCol'][] = "title";

		$columns =array('','','holiday_date','holiday_type','title');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getholiday($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->holidays;
        return $this->row($data);
    }

    public function save($data){
        $data['title'] = trim($data['title']);
        if($this->checkDuplicate($data['title'],$data['id']) > 0):
            $errorMessage['title'] = "Title is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
        else:
            return $this->store($this->holidays,$data,'Holidays');
        endif;
    }

    public function checkDuplicate($title,$id=""){
        $data['tableName'] = $this->holidays;
        $data['where']['title'] = $title;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->holidays,['id'=>$id],'Holidays');
    }
}
?>