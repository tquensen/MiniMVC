<?php

class Helper_Text extends MiniMVC_Helper
{

    public function sanitize($input, $lower = false)
    {
        $list = $this->getReplaceList();
        $output = str_replace(array_keys($list), array_values($list), $input);
        $output = preg_replace('/[^A-Za-z0-9_-]+/', '-', $output);
        return $lower ? strtolower($output) : $output;
    }

    public function getReplaceList()
    {
        $replace = array(
            'Ü' => 'Ue',
            'Ö' => 'Oe',
            'Ä' => 'Ae',
            'ü' => 'ue',
            'ö' => 'oe',
            'ä' => 'ae',
            'ß' => 'ss',
            '@' => '-at-'
        );

        return $replace;
    }

}