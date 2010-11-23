<?php

class Helper_L10n extends MiniMVC_Helper
{
    protected $i18n = array();

    /**
     *
     * @param string|null $key if a key is given, return only the specific date instead of an array with all data
     * @param string|null $language the language to get the l10n data for (null for current language)
     * @return <type>
     */
    public function getL10nData($key = null, $language = null) {
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
            $decimalSeparator ? $decimalSeparator : $this->getL10nData('numberDecimalSeparator', $language),
            $thousandsSeparator ? $thousandsSeparator : $this->getL10nData('numberThousandsStep', $language)
        );
    }

    public function formatDate($timestamp = null, $language = null)
    {
        return date($this->getL10nData('formatDate', $language), $timestamp ? $timestamp : time());
    }

    public function formatTime($timestamp = null, $showSeconds = false, $language = null)
    {
        return date($this->getL10nData($showSeconds ? 'formatTimeSeconds' : 'formatTime', $language), $timestamp ? $timestamp : time());
    }

    public function formatDateTime($timestamp = null, $showSeconds = false, $language = null)
    {
        return date($this->getL10nData($showSeconds ? 'formatDateTimeSeconds' : 'formatDateTime', $language), $timestamp ? $timestamp : time());
    }

    public function formatMonth($month = null, $full = true, $language = null)
    {
        $months = $this->getL10nData('months', $language);
        if ($month === null || $month > 12 || $month < 1 || !isset($months[$month])) {
            $month = date('n', $month ? $month : time());
        }
        return $full ? $months[$month] : substr($months[$month], 0, 3);
    }

    public function formatDayOfWeek($day = null, $full = true, $language = null)
    {
        $weekdays = $this->getL10nData('weekdays', $language);
        if ($day == 7) {
            $day = 0;
        }
        if ($day === null || $day > 6 || $day < 0 || !isset($weekdays[$day])) {
            $day = date('w', $day ? $day : time());
        }
        return $full ? $weekdays[$day] : substr($weekdays[$day], 0, 3);
    }

    public function formatCustom($key = null, $parameter = null, $language = null)
    {
        $format = $this->getL10nData('format'.  ucfirst($key), $language);
        if (is_array($format) && isset($format['callable'])) {
            return call_user_func_array($format['callable'], array_merge(isset($format['parameter']) ? (array) $format['parameter'] : array(), (array) $parameter));
        }
        return date((string) $format, $parameter ? $parameter : time());
    }
}