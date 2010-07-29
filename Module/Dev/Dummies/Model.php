<?php
/**
 * @property {name}Table $_table
 * @method {name}Table getTable()
 */
class {name} extends MiniMVC_Model
{

	public function __construct($table = null)
	{
        if ($table) {
            $this->_table = $table;
        } else {
            $this->_table = {name}Table::getInstance();
        }
	}

}