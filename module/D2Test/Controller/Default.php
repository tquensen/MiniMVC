<?php
class D2Test_Default_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {
        $test = $this->registry->db->em->getRepository('TestUser')->findOneBy(array('id' => '1'));
        var_dump($test);
        $test = new TestUser();
        $test->setName('foo');
        $this->registry->db->em->persist($test);
        $this->registry->db->em->flush();
        /*
        foreach ($this->registry->db->em->getRepository('TestWurst')->findAll() as $wurst)
        {
            echo $wurst->name.'<br />';
        }
         */
    }

    public function addAction($params)
    {
        if (isset($params['name']) && $params['name']) {
            $wurst = new TestWurst();
            $wurst->name = $params['name'];
            $this->registry->db->em->persist($wurst);
            $this->registry->db->em->flush();
            echo 'YEAH';
        }
    }
}
