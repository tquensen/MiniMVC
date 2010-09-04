<?php
class BlubbComments extends MiniMVC_Model
{

    
    public function __toString()
    {
        $data = parent::__toString();
        $data .= '<div style="border: #ccc 1px solid; margin-left: 20px;">'.(string) $this->getUser().'</div>';
        return $data;
    }

}