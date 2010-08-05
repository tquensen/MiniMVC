<?php
class Blubb_Default_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {
        if (!$group = GroupTable::getInstance()->loadOneBy('name = ?', 'Horstgroup'))
        {
            $group = GroupTable::getInstance()->create(array('name' => 'Horstgroup'));
        }

        $user = new BlubbUser();
        $user->username = 'Horstilein';
        $group->setUser($user);

        $user = new BlubbUser();
        $user->username = 'Horstilein2';
        $group->setUser($user);

        foreach ($group->getUser(true) as $key => $user) {
            echo 'User '.$user->username.'<br />';
        }

        $group->save(true);

        exit;
        
        $groups = GroupTable::getInstance()->loadAll();
        foreach ($groups as $group) {
        echo '' .$group->name.'<br />';
        $group->deleteUser(true, true, true);
        exit;
        foreach ($group->loadUser() as $user) {
            echo ' - ' .$user->username.'<br />';
        }
        }


        $blubb = BlubbCommentsTable::getInstance()->loadOne(7);
        var_dump(get_class($blubb));
        $blubber = $blubb->loadBlubb();
        echo '##'.$blubber->name.'<br />';

        $commentslist = BlubbCommentsTable::getInstance()->loadWithUser(null, null, 'u.username DESC');
        foreach ($commentslist as $comment) {
            echo '<br />Comment '.$comment->id.' ('.$comment->message.')<br />';
            if ($user = $comment->getUser()) {
                echo ' - User '.$user->id.' ('.$user->username.')<br />';
            } else {
                echo ' - Kein User:<br />';
            }
            
        }

        echo '<br /><br />------------------------------------------------<br /><br />';

        $userlist = BlubbUserTable::getInstance()->loadWithRelations(null, null, 'u.username DESC');
        foreach ($userlist as $user) {
            echo '<br />User '.$user->id.' ('.$user->username.')<br />';
            $blubber = $user->getBlubber();
            echo ' - Blubber:<br />';
            foreach ($blubber as $blubb) {
                echo ' &nbsp; - '.$blubb->id.' ('.$blubb->name.')<br />';
            }
            $comments = $user->getComments();
            echo ' - Comments:<br />';
            foreach ($comments as $comment) {
                echo ' &nbsp; - '.$comment->id.' ('.$comment->message.')<br />';
            }
            $groups = $user->getGroups();
            echo ' - Groups:<br />';
            foreach ($groups as $group) {
                echo ' &nbsp; - '.$group->id.' ('.$group->name.')<br />';
            }
        }

        echo '<br /><br />------------------------------------------------<br /><br />';

        $userlist = BlubbUserTable::getInstance()->loadAll('username DESC');
        foreach ($userlist as $user) {
            echo '<br />User '.$user->id.' ('.$user->username.')<br />';
            $blubber = $user->loadBlubber();
            echo ' - Blubber:<br />';
            foreach ($blubber as $blubb) {
                echo ' &nbsp; - '.$blubb->id.' ('.$blubb->name.')<br />';
            }
            $comments = $user->loadComments();
            echo ' - Comments:<br />';
            foreach ($comments as $comment) {
                echo ' &nbsp; - '.$comment->id.' ('.$comment->message.')<br />';
            }
            $groups = $user->loadGroups();
            echo ' - Groups:<br />';
            foreach ($groups as $group) {
                echo ' &nbsp; - '.$group->id.' ('.$group->name.')<br />';
            }
        }

        echo '<br /><br />------------------------------------------------<br /><br />';
        $start = microtime(true);
        $entries = BlubberTable::getInstance()->loadWithComments('a.id < ?', 40, 'a.name ASC, c.user_id ASC');//BlubberTable::getInstance()->loadWithComments();
        echo '<br />TIME FULL: '.number_format(microtime(true)-$start, 6, ',','').'s';
        echo 'ENTRIES:'."<br />";
        foreach ($entries as $entry) {
            echo (string) $entry;
        }

        $entries = BlubberTable::getInstance()->loadWithNumComments(null, null, 'a.name ASC');//BlubberTable::getInstance()->loadWithComments();
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
