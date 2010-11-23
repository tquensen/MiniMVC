<?php

class Helper_L10n extends MiniMVC_Helper
{
    protected $i18n = array();

    /**
     *
     * @param string|null $language the language to get the l10n data for (null for current language)
     * @param string|null $key if a key is given, return only the specific date instead of an array with all data
     * @return <type>
     */
    public function getL10nData($language = null, $key = null) {
        $language = $language ? $language : $this->registry->settings->get('currentLanguage');
        if (!isset($this->i18n[$language])) {
            $this->i18n[$language] = $this->registry->helper->i18n->get('_l10n', $language);
        }
        return $key === null ? $this->i18n[$language] : (isset($this->i18n[$language][$key]) ? $this->i18n[$language][$key] : null);
    }

    public function formatNumber($number, $decimals = 0, $language = null, $decimalSeparator = null, $thousandsSeparator = null)
    {
        return number_format(
            (int) $number,
            $decimals,
            $decimalSeparator ? $decimalSeparator : $this->getL10nData($language, 'numberDecimalSeparator'),
            $thousandsSeparator ? $thousandsSeparator : $this->getL10nData($language, 'numberThousandsStep')
        );
    }

    public function formatDate($timestamp = null, $language = null)
    {
        return date($this->getL10nData($language, 'formatDate'), $timestamp ? $timestamp : time());
    }

    public function formatTime($timestamp = null, $showSeconds = false, $language = null)
    {
        return date($this->getL10nData($language, $showSeconds ? 'formatTimeSeconds' : 'formatTime'), $timestamp ? $timestamp : time());
    }

    public function formatDateTime($timestamp = null, $showSeconds = false, $language = null)
    {
        return date($this->getL10nData($language, $showSeconds ? 'formatDateTimeSeconds' : 'formatDateTime'), $timestamp ? $timestamp : time());
    }

    public function formatMonth($month = null, $full = true, $language = null)
    {
        $months = $this->getL10nData($language, 'months');
        if ($month === null || $month > 12 || $month < 1 || !isset($months[$month])) {
            $month = date('n', $month ? $month : time());
        }
        return $full ? $months[$month] : substr($months[$month], 0, 3);
    }

    public function formatDayOfWeek($day = null, $full = true, $language = null)
    {
        $weekdays = $this->getL10nData($language, 'weekdays');
        if ($day == 7) {
            $day = 0;
        }
        if ($day === null || $day > 6 || $day < 0 || !isset($weekdays[$day])) {
            $day = date('w', $day ? $day : time());
        }
        return $full ? $weekdays[$day] : substr($weekdays[$day], 0, 3);
    }
}