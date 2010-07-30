<?php
class Blubb_Default_Controller extends MiniMVC_Controller
{
    public function indexAction($params)
    {
        $commentslist = BlubbCommentsTable::getInstance()->loadWithUser(null, 'u.username DESC');
        foreach ($commentslist as $comment) {
            echo '<br />Comment '.$comment->id.' ('.$comment->message.')<br />';
            if ($user = $comment->getBlubbUser()) {
                echo ' - User '.$user->id.' ('.$user->username.')<br />';
            } else {
                echo ' - Kein User:<br />';
            }
            
        }

        echo '<br /><br />------------------------------------------------<br /><br />';

        $userlist = BlubbUserTable::getInstance()->loadWithRelations(null, 'u.username DESC');
        foreach ($userlist as $user) {
            echo '<br />User '.$user->id.' ('.$user->username.')<br />';
            $blubber = $user->getBlubber(true);
            echo ' - Blubber:<br />';
            foreach ($blubber as $blubb) {
                echo ' &nbsp; - '.$blubb->id.' ('.$blubb->name.')<br />';
            }
            $comments = $user->getBlubbComments(true);
            echo ' - Comments:<br />';
            foreach ($comments as $comment) {
                echo ' &nbsp; - '.$comment->id.' ('.$comment->message.')<br />';
            }
        }

        echo '<br /><br />------------------------------------------------<br /><br />';

        $userlist = BlubbUserTable::getInstance()->loadAll('username DESC');
        foreach ($userlist as $user) {
            echo '<br />User '.$user->id.' ('.$user->username.')<br />';
            $blubber = $user->loadBlubber('user_id = '.$user->id);
            echo ' - Blubber:<br />';
            foreach ($blubber as $blubb) {
                echo ' &nbsp; - '.$blubb->id.' ('.$blubb->name.')<br />';
            }
            $comments = $user->loadBlubbComments('user_id = '.$user->id);
            echo ' - Comments:<br />';
            foreach ($comments as $comment) {
                echo ' &nbsp; - '.$comment->id.' ('.$comment->message.')<br />';
            }
        }

        echo '<br /><br />------------------------------------------------<br /><br />';
        $start = microtime(true);
        $entries = BlubberTable::getInstance()->loadWithComments('a.id < 40', 'a.name ASC, c.user_id ASC');//BlubberTable::getInstance()->loadWithComments();
        echo '<br />TIME FULL: '.number_format(microtime(true)-$start, 6, ',','').'s';
        echo 'ENTRIES:'."<br />";
        foreach ($entries as $entry) {
            echo (string) $entry;
        }

        $entries = BlubberTable::getInstance()->loadWithNumComments(null, 'a.name ASC');//BlubberTable::getInstance()->loadWithComments();
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
