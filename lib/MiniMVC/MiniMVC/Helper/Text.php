<?php

class Helper_Text extends MiniMVC_Helper
{

    public function sanitize($input, $lower = false, $maxlength = false)
    {
        $list = $this->getReplaceList();
        $output = str_replace(array_keys($list), array_values($list), $input);
        $output = preg_replace('/[^A-Za-z0-9_-]+/', '-', $output);
        if ($maxlength) {
            $output = mb_substr($output, 0, $maxlength);
        }
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

    public function truncate($content, $maxLength, $cutSize = 50,
            $insertAfter = '...')
    {
        if (mb_strlen($content, 'UTF-8') <= $maxLength) {
            return $content;
        }
        $content = mb_substr($content, 0, $maxLength);
        //try to cut after a sentence
        $LastCharSentence = array();
        $lastCharSentence['questionmark'] = mb_strrpos($content, '? ');
        $lastCharSentence['exclamationmark'] = mb_strrpos($content, '! ');
        $lastCharSentence['period'] = mb_strrpos($content, '. ');
        //or at least after a word
        $lastChar = array();
        $lastChar['space'] = mb_strrpos($content, ' ');
        $lastChar['newline'] = mb_strrpos($content, "\n");

        if (max($lastCharSentence) != 0 && (max($lastCharSentence) + 1) > ($maxLength - $cutSize)) {
            return mb_substr($content, 0, max($lastCharSentence) + 2) . $insertAfter;
        } elseif (max($lastChar) > $maxLength - $cutSize) {
            return mb_substr($content, 0, max($lastChar) + 1) . $insertAfter;
        } else {
            return $content . $insertAfter;
        }
    }

    public function esc($content, $print = true)
    {
        if ($print) {
            echo htmlspecialchars($content);
            return;
        }
        return htmlspecialchars($content);
    }

    public function raw($content, $print = true)
    {
        if ($print) {
            echo html_entity_decode($content, ENT_QUOTES, 'UTF-8');
            return;
        }
        return html_entity_decode($content, ENT_QUOTES, 'UTF-8');
    }

}