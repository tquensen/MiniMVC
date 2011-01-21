<?php
class MiniMVC_HttpCache {

    /**
     *
     * @var MiniMVC_View
     */
	protected $view = null;
    /**
     *
     * @var MiniMVC_Registry
     */
	protected $registry = null;

    protected $settings = array();

    /**
     *
     * @param MiniMVC_View $view the view containing the complete response
     * @param array $settings the configuration of this cache
     */
	public function __construct($view, $settings = array())
	{
		$this->view = $view;
		$this->registry = MiniMVC_Registry::getInstance();

        $this->settings = array_merge(array(

        ), (array) $settings);
	}
}
?>
