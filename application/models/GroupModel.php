<?php
class GroupModel extends MasterModel{
    private $groupMaster = "group_master";

    public function getGroupOnGroupCode($groupCode,$defualtGroup = false){
        $queryData = array();
        $queryData['tableName'] = $this->groupMaster;
        $queryData['where']['group_code'] = $groupCode;
        if($defualtGroup == true)
            $queryData['where']['is_default'] = 1;
        $groupData = $this->row($queryData);
        return $groupData;
    }

    public function getGroupListOnGroupCode($groupCode){
        $queryData = array();
        $queryData['tableName'] = $this->groupMaster;
        $queryData['customWhere'][] = $groupCode;
        $groupData = $this->rows($queryData);
        return $groupData;
    }

    public function getGroup($id){
        $queryData = array();
        $queryData['tableName'] = $this->groupMaster;
        $queryData['where']['id'] = $id;
        $groupData = $this->row($queryData);
        return $groupData;
    }

    public function getGroupList(){
        $queryData = array();
        $queryData['tableName'] = $this->groupMaster;
        $groupData = $this->rows($queryData);
        return $groupData;
    }
}
?>