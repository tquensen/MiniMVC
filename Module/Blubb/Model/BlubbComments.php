<?php
class BlubbComments extends MiniMVC_Model
{

	public function __construct($table = null)
	{
        if ($table) {
            $this->_table = $table;
        } else {
            $this->_table = new BlubbCommentsTable();
        }
	}

    public function __toString()
    {
        $data = parent::__toString();
        $data .= '<div style="border: #ccc 1px solid; margin-left: 20px;">'.(string) $this->getUser().'</div>';
        return $data;
    }

}