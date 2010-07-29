<?php
class Blubb_Default_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {
        $start = microtime(true);
        $entries = BlubberTable::getInstance()->loadWithComments('id', array('>=', 2), 'name ASC', 3, 1);//BlubberTable::getInstance()->loadWithComments();
        echo '<br />TIME FULL: '.number_format(microtime(true)-$start, 6, ',','').'s';
        echo 'ENTRIES:'."<br />";
        foreach ($entries as $entry) {
            echo (string) $entry;
        }

        $entries = BlubberTable::getInstance()->loadWithNumComments(null, null, 'name ASC', 3, 0);//BlubberTable::getInstance()->loadWithComments();
        echo '<br />TIME FULL: '.number_format(microtime(true)-$start, 6, ',','').'s';
        echo 'ENTRIES:'."<br />";
        foreach ($entries as $entry) {
            echo (string) $entry;
        }
        return $this->view->parse('default/index');
    }

    public function widgetAction($params)
    {
        return $this->view->parse('default/widget');
    }
}
