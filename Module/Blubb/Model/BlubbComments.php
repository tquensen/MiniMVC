<?php
class BlubbComments extends MiniMVC_Model
{
    protected $user = null;

	public function __construct($table = null)
	{
        if ($table) {
            $this->_table = $table;
        } else {
            $this->_table = new BlubbCommentsTable();
        }
	}

    public function setUser($user, $update = true)
    {
        if ($update || !$this->user) {
            $this->user = $user;
        }
    }

    public function getUser()
    {
        return $this->user;
    }

    public function __toString()
    {
        $data = parent::__toString();
        $data .= '<div style="border: #ccc 1px solid; margin-left: 20px;">'.(string) $this->user.'</div>';
        return $data;
    }

}