<?php
class InspectionTypeModel extends MasterModel
{
    private $inspection_type = "inspection_type";

    public function getDTRows($data)
    {
        $data['tableName'] = $this->inspection_type;
        $data['select'] = 'inspection_type.*';
        $data['searchCol'][] = "inspection_type.inspection_type";
        $columns = array('', '', 'inspection_type');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

    public function getInspectionTypeList()
    {
        $data['tableName'] = $this->inspection_type;
        return $this->rows($data);
    }

    public function getInspectionType($id)
    {
        $data['where']['id'] = $id;
        $data['tableName'] = $this->inspection_type;
        return $this->row($data);
    }

    public function save($data)
    {
        try {
            $this->db->trans_begin();
            if ($this->checkDuplicate($data['inspection_type'], $data['id']) > 0) :
                $errorMessage['inspection_type'] = "Imspection Type is duplicate.";
                $result = ['status' => 0, 'message' => $errorMessage];
            else :
                $result = $this->store($this->inspection_type, $data, 'Inspection Type');
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

    public function checkDuplicate($name, $id = "")
    {
        $data['tableName'] = $this->inspection_type;
        $data['where']['inspection_type'] = $name;

        if (!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id)
    {
        try {
            $this->db->trans_begin();
            $result = $this->trash($this->inspection_type, ['id' => $id], 'Item Category');
            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 2, 'message' => "somthing is wrong. Error : " . $e->getMessage()];
        }
    }
}
