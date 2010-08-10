<?php

class Helper_Pager extends MiniMVC_Helper
{  
    private $entries = 0;
    private $entriesPerPage = 0;
    private $currentPage = 0;
    private $pages = 0;
    private $maxPages = 0;
    private $url = '';
    private $labels = array();

    /**
     * create and init a new pager object
     *
     * @param integer $entries the total number of entries
     * @param integer $entriesPerPage the nuber of entries on one page
     * @param string $url the pager url - use {page} and {pages} as placeholders, values in brackets will be removed for page 1. Example: http://example.com/foo(?p={page})
     * @param integer $currentPage the current page
     * @param integer $maxPages how many previous/next pages are displayed in the pager. number should be odd (maxPages=5 will result in ... [4][5][6][7][8] ... where 6 is the current page)
     * @return Helper_Pager the pager object
     */
    public function get($entries, $entriesPerPage, $url, $currentPage = 1, $maxPages = 11)
    {
        $pager = new Helper_Pager($this->module);
        $pager->init($entries, $entriesPerPage, $url, $currentPage, $maxPages);
        return $pager;
    }

    /**
     * initiates this pager object
     *
     * @param integer $entries the total number of entries
     * @param integer $entriesPerPage the nuber of entries on one page
     * @param string $url the pager url - use {page} and {pages} as placeholders, values in brackets will be removed for page 1. Example: http://example.com/foo(?p={page})
     * @param integer $currentPage the current page
     * @param integer $maxPages how many previous/next pages are displayed in the pager. number should be odd (maxPages=5 will result in ... [4][5][6][7][8] ... where 6 is the current page)
     */
    public function init($entries, $entriesPerPage, $url, $currentPage = 1, $maxPages = 11)
    {
        $this->entries = $entries;
        $this->entriesPerPage = $entriesPerPage;
        $this->currentPage = $currentPage;
        $this->url = $url;
        $this->maxPages = $maxPages;
        $this->pages = (int) ceil($entries / $entriesPerPage);

        $this->labels = array(
            'first' => '&lt;&lt;',
            'previous' => '&lt;',
            'next' => '&gt;',
            'last' => '&gt;&gt;',
            'spacer' => '...'
        );
    }

    /**
     * Defines the labels for the pager
     *
     * The default labels are:
     * array(
     *      'first' => '&lt;&lt;',
     *      'previous' => '&lt;',
     *      'next' => '&gt;',
     *      'last' => '&gt;&gt;',
     *      'spacer' => '...'
     *  );
     *
     * @param array $labels an array with labels
     */
    public function setLabels($labels)
    {
        if (!is_array($labels)) {
            return false;
        }

        foreach ($labels as $key => $label) {
            if (isset($this->labels[$key])) {
                $this->labels[$key] = $label;
            }
        }
    }

    /**
     *
     * @param string $module the module to look for the pager partial. pass null to use the default partial in /Partial/
     * @return string the html output of the pager partial
     */
    public function getHtml($module = null)
    {
        return $this->registry->helper->Partial->get('pager', $this->getForPartial(), $module);
    }

    /**
     *
     * @return array an array to use in the partial
     */
    public function getForPartial()
    {
        return array('pages' => $this->pages, 'links' => $this->getLinks());
    }

    /**
     *
     * @return integer the total number of pages
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     *
     * @return array an array with the links to display
     */
    public function getLinks()
    {
        $links = array();

        if ($this->labels['first']) {
            if ($this->currentPage == 1) {
                $links[] = array('isLink' => false, 'label' => $this->labels['first'], 'link' => '', 'active' => false, 'type' => 'first');
            } else {
                $links[] = array('isLink' => true, 'label' => $this->labels['first'], 'link' => $this->getUrl(1), 'active' => false, 'type' => 'first');
            }
        }
        if ($this->labels['previous']) {
            if ($this->currentPage == 1) {
                $links[] = array('isLink' => false, 'label' => $this->labels['previous'], 'link' => '', 'active' => false, 'type' => 'previous');
            } else {
                $links[] = array('isLink' => true, 'label' => $this->labels['previous'], 'link' => $this->getUrl($this->currentPage - 1), 'active' => false, 'type' => 'previous');
            }
        }

        if ($this->pages <= $this->maxPages) {
            for ($i = 1; $i <= $this->pages; $i++) {
                $active = ($this->currentPage == $i) ? true : false;
                $isLink = !$active;
                $links[] = array('isLink' => $isLink, 'label' => $i, 'link' => $this->getUrl($i), 'active' => $active, 'type' => 'number');
            }
        } else {
            $pages_spacing = (int)(($this->maxPages - 1) / 2);

            if ($this->currentPage <= $pages_spacing + 1) {
                for ($i = 1; $i <= $pages_spacing * 2 + 1; $i++) {
                    $active = ($this->currentPage == $i) ? true : false;
                    $isLink = !$active;
                    $links[] = array('isLink' => $isLink, 'label' => $i, 'link' => $this->getUrl($i), 'active' => $active, 'type' => 'number');
                }
                if ($this->labels['spacer']) {
                    $links[] = array('isLink' => false, 'label' => $this->labels['spacer'], 'link' => '', 'active' => false, 'type' => 'spacer');
                }
            } elseif ($this->currentPage >= $this->pages - $pages_spacing) {
                if ($this->labels['spacer']) {
                    $links[] = array('isLink' => false, 'label' => $this->labels['spacer'], 'link' => '', 'active' => false, 'type' => 'spacer');
                }
                for ($i = $this->pages - $pages_spacing * 2; $i <= $this->pages; $i++) {
                    $active = ($this->currentPage == $i) ? true : false;
                    $isLink = !$active;
                    $links[] = array('isLink' => $isLink, 'label' => $i, 'link' => $this->getUrl($i), 'active' => $active, 'type' => 'number');
                }
            } else {
                if ($this->labels['spacer']) {
                    $links[] = array('isLink' => false, 'label' => $this->labels['spacer'], 'link' => '', 'active' => false, 'type' => 'spacer');
                }

                for ($i = $this->currentPage - $pages_spacing; $i <= $this->currentPage + $pages_spacing; $i++) {
                    $active = ($this->currentPage == $i) ? true : false;
                    $isLink = !$active;
                    $links[] = array('isLink' => $isLink, 'label' => $i, 'link' => $this->getUrl($i), 'active' => $active, 'type' => 'number');
                }

                if ($this->labels['spacer']) {
                    $links[] = array('isLink' => false, 'label' => $this->labels['spacer'], 'link' => '', 'active' => false, 'type' => 'spacer');
                }
            }
        }

        if ($this->labels['next']) {
            if ($this->currentPage == $this->pages) {
                $links[] = array('isLink' => false, 'label' => $this->labels['next'], 'link' => '', 'active' => false, 'type' => 'next');
            } else {
                $links[] = array('isLink' => true, 'label' => $this->labels['next'], 'link' => $this->getUrl($this->currentPage + 1), 'active' => false, 'type' => 'next');
            }
        }
        if ($this->labels['last']) {
            if ($this->currentPage == $this->pages) {
                $links[] = array('isLink' => false, 'label' => $this->labels['last'], 'link' => '', 'active' => false, 'type' => 'last');
            } else {
                $links[] = array('isLink' => true, 'label' => $this->labels['last'], 'link' => $this->getUrl($this->pages), 'active' => false, 'type' => 'last');
            }
        }

        return $links;
    }

    /**
     *
     * @return integer the offset for the current page
     */
    public function getOffset()
    {
        return ($this->currentPage - 1) * $this->entriesPerPage;
    }

    /**
     *
     * @return integer the number of entries per page
     */
    public function getLimit()
    {
        return $this->entriesPerPage;
    }

    /**
     * generates an url for the given page number
     * 
     * placeholders {page} and {pages} will be replaced with the given page and the total pages
     * anything wrapped in brackets will be removed for the first page
     * 
     * http://example.com/foo(?p={page}) will become
     * http://example.com/foo for page=1 and
     * http://example.com/foo?p=X for any other page X
     *
     * @param integer $currentPage the page number
     * @return string returns the url with replaced {page} and {pages} placeholders
     */
    private function getUrl($currentPage)
    {
        $search = array(
            '{page}',
            '{pages}'
        );

        $replace = array(
            $currentPage,
            $this->pages
        );

        $replaced = str_replace($search, $replace, $this->url);

        if ($currentPage == 1) {
            return preg_replace('#\(([^\)]*)\)#', '', $replaced);
        } else {
            return preg_replace('#\(([^\)]*)\)#', '$1', $replaced);
        }
    }

}
?>