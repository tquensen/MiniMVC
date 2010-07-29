<?php
class Blubber extends MiniMVC_Model
{
    protected $comments = array();
    protected $user = null;

	public function __construct($table = null)
	{
        if ($table) {
            $this->_table = $table;
        } else {
            $this->_table = new BlubberTable();
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

    public function addComment($comment, $identifier = null, $update = true)
    {
        if (!$identifier) {
            $this->comments[] = $comment;
            return;
        }
        if ($update || !isset($this->comments[$identifier])) {
            $this->comments[$identifier] = $comment;
        }
    }

    public function getComment($identifier)
    {
        return isset($this->comments[$identifier]) ? $this->comments[$identifier] : null;
    }

    public function __toString()
    {
        $data = parent::__toString();
        $data .= '<div style="border: #ccc 1px solid; margin-left: 20px;">'.(string) $this->user.'</div>';
        foreach ($this->comments as $comment) {
            $data .= '<div style="border: #ccc 1px solid; margin-left: 20px;">'.(string) $comment.'</div>';
        }
        return $data;
    }

}