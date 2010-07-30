<?php
class Blubber extends MiniMVC_Model
{

	public function __construct($table = null)
	{
        if ($table) {
            $this->_table = $table;
        } else {
            $this->_table = new BlubberTable();
        }
	}
    /*
    public function loadUser($reload = false)
    {
        if (!$this->user_id) {
            return null;
        }
        $user = BlubbUserTable::getInstance()->loadOne($this->user_id, $reload);
        $this->setUser($user);
    }

    public function loadComments($condition = null, $order = null, $limit = null, $offset = null)
    {
        if (!$this->id) {
            return null;
        }
        $comments = BlubbCommentsTable::getInstance()->load('blubb_id = '.$this->id . ($condition ? ' AND '. $condition : ''), $order, $limit, $offset);
        foreach ($comments as $comment) {
            $this->setComment($comment, $comment->id);
        }
        return $comments;
    }

    */

    public function __toString()
    {
        $data = parent::__toString();
        $data .= '<div style="border: #ccc 1px solid; margin-left: 20px;">'.(string) $this->getBlubbUser().'</div>';
        foreach ($this->getBlubbComments(true) as $comment) {
            $data .= '<div style="border: #ccc 1px solid; margin-left: 20px;">'.(string) $comment.'</div>';
        }
        return $data;
    }

}