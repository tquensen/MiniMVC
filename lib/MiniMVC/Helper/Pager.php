<?php

class Helper_Pager extends MiniMVC_Helper
{
    private $entries = 0;
    private $entriesPerPage = 0;
    private $currentPage = 0;
    private $pages = 0;
    private $maxPages = 0;
    private $showInactive = true;
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
     * @param bool $showInactive whether inactive first/last/prev/next/.. elements should be displayed with class=inactive" or completely removed
     * @return Helper_Pager the pager object
     */
    public function get($entries, $entriesPerPage, $url, $currentPage = 1, $maxPages = 11, $showInactive = false)
    {
        $pager = new Helper_Pager($this->module);
        $pager->init($entries, $entriesPerPage, $url, $currentPage, $maxPages, $showInactive);
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
     * @param bool $showInactive whether inactive first/last/prev/next/.. elements should be displayed with class=inactive" or completely removed
     */
    public function init($entries, $entriesPerPage, $url, $currentPage = 1, $maxPages = 11, $showInactive = false)
    {
        $t = $this->registry->helper->i18n->get('_pager');
        $this->entries = $entries;
        $this->entriesPerPage = $entriesPerPage;
        $this->currentPage = $currentPage;
        $this->url = $url;
        $this->maxPages = $maxPages;
        $this->pages = (int)ceil($entries / $entriesPerPage);
        $this->showInactive = $showInactive;


        $this->labels = array(
            'first' => '« ' . $t->first,
            'previous1000' => '« -1000',
            'previous100' => '« -100',
            'previous10' => '« -10',
            'previous' => '« -1',
            'spacer' => '...',
            'next' => '+1 »',
            'next10' => '+10 »',
            'next100' => '+100 »',
            'next1000' => '+1000 »',
            'last' => $t->last . ' »',
        );
    }

    /**
     * Defines the labels for the pager
     *
     * The default labels are:
     * array(
     *      'first' => '« ' . $t->first,
     *      'previous1000' => '« -1000',
     *      'previous100' => '« -100',
     *      'previous10' => '« -10',
     *      'previous' => '« -1',
     *      'spacer' => '...',
     *      'next' => '+1 »',
     *      'next10' => '+10 »',
     *      'next100' => '+100 »',
     *      'next1000' => '+1000 »',
     *      'last' => $t->last . ' »',
     * );
     *
     * if omitted, the default label will be used,
     * if false, the element won't be displayed,
     * if true, the resulting page number will be used (not valid for spacer)
     * example for true: on current page 14, first becomes 1, previous10 becomes 4 and so on
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
        return $this->registry->helper->partial->get('pager', $this->getForPartial(), $module);
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
        $pages_spacing = floor(($this->maxPages - 1) / 2);


        if ($this->labels['first'] !== false) {
            if ($this->currentPage != 1) {
                $links[] = array('isLink' => true, 'label' => $this->labels['first'] !== true ? $this->labels['first'] : '1', 'link' => $this->getUrl(1), 'active' => true, 'type' => 'first');
            } elseif ($this->showInactive) {
                $links[] = array('isLink' => false, 'label' => $this->labels['first'] !== true ? $this->labels['first'] : '1', 'link' => '', 'active' => false, 'type' => 'first');
            }
        }

        if ($this->labels['previous1000'] !== false) {
            if ($this->currentPage > 1000) {
                $links[] = array('isLink' => true, 'label' => $this->labels['previous1000'] !== true ? $this->labels['previous1000'] : $this->currentPage - 1000, 'link' => $this->getUrl($this->currentPage - 1000), 'active' => true, 'type' => 'previous1000');
            } elseif ($this->showInactive) {
                $links[] = array('isLink' => false, 'label' => $this->labels['previous1000'] !== true ? $this->labels['previous1000'] : $this->currentPage - 1000, 'link' => '', 'active' => false, 'type' => 'previous1000');
            }
        }

        if ($this->labels['previous100'] !== false) {
            if ($this->currentPage > 100) {
                $links[] = array('isLink' => true, 'label' => $this->labels['previous100'] !== true ? $this->labels['previous100'] : $this->currentPage - 100, 'link' => $this->getUrl($this->currentPage - 100), 'active' => true, 'type' => 'previous100');
            } elseif ($this->showInactive) {
                $links[] = array('isLink' => false, 'label' => $this->labels['previous100'] !== true ? $this->labels['previous100'] : $this->currentPage - 100, 'link' => '', 'active' => false, 'type' => 'previous100');
            }
        }

        if ($this->labels['previous10'] !== false) {
            if ($this->currentPage > 10) {
                $links[] = array('isLink' => true, 'label' => $this->labels['previous10'] !== true ? $this->labels['previous10'] : $this->currentPage - 10, 'link' => $this->getUrl($this->currentPage - 10), 'active' => true, 'type' => 'previous10');
            } elseif ($this->showInactive) {
                $links[] = array('isLink' => false, 'label' => $this->labels['previous10'] !== true ? $this->labels['previous10'] : $this->currentPage - 10, 'link' => '', 'active' => false, 'type' => 'previous10');
            }
        }

        if ($this->labels['previous'] !== false) {
            if ($this->currentPage > 1) {
                $links[] = array('isLink' => true, 'label' => $this->labels['previous'] !== true ? $this->labels['previous'] : $this->currentPage - 1, 'link' => $this->getUrl($this->currentPage - 1), 'active' => true, 'type' => 'previous');
            } elseif ($this->showInactive) {
                $links[] = array('isLink' => false, 'label' => $this->labels['previous'] !== true ? $this->labels['previous'] : $this->currentPage - 1, 'link' => '', 'active' => false, 'type' => 'previous');
            }
        }

        if ($this->pages <= $this->maxPages) {
            if ($this->labels['spacer'] !== false && $this->showInactive) {
                $links[] = array('isLink' => false, 'label' => $this->labels['spacer'], 'link' => '', 'active' => false, 'type' => 'spacer');
            }

            for ($i = 1; $i <= $this->pages; $i++) {
                $isLink = ($this->currentPage == $i) ? false : true;
                $links[] = array('isLink' => $isLink, 'label' => $i, 'link' => $this->getUrl($i), 'active' => false, 'type' => 'number');
            }

            if ($this->labels['spacer'] !== false && $this->showInactive) {
                $links[] = array('isLink' => false, 'label' => $this->labels['spacer'], 'link' => '', 'active' => true, 'type' => 'spacer');
            }
        } else {
            if ($this->currentPage <= $pages_spacing + 1) {
                if ($this->labels['spacer'] !== false && $this->showInactive) {
                    $links[] = array('isLink' => false, 'label' => $this->labels['spacer'], 'link' => '', 'active' => false, 'type' => 'spacer');
                }
                for ($i = 1; $i <= $pages_spacing * 2 + 1; $i++) {
                    $isLink = ($this->currentPage == $i) ? false : true;
                    $links[] = array('isLink' => $isLink, 'label' => $i, 'link' => $this->getUrl($i), 'active' => true, 'type' => 'number');
                }
                if ($this->labels['spacer'] !== false) {
                    $links[] = array('isLink' => false, 'label' => $this->labels['spacer'], 'link' => '', 'active' => true, 'type' => 'spacer');
                }
            } elseif ($this->currentPage >= $this->pages - $pages_spacing) {
                if ($this->labels['spacer'] !== false) {
                    $links[] = array('isLink' => false, 'label' => $this->labels['spacer'], 'link' => '', 'active' => true, 'type' => 'spacer');
                }
                for ($i = $this->pages - $pages_spacing * 2; $i <= $this->pages; $i++) {
                    $isLink = ($this->currentPage == $i) ? false : true;
                    $links[] = array('isLink' => $isLink, 'label' => $i, 'link' => $this->getUrl($i), 'active' => true, 'type' => 'number');
                }
                if ($this->labels['spacer'] !== false && $this->showInactive) {
                    $links[] = array('isLink' => false, 'label' => $this->labels['spacer'], 'link' => '', 'active' => false, 'type' => 'spacer');
                }
            } else {
                if ($this->labels['spacer'] !== false) {
                    $links[] = array('isLink' => false, 'label' => $this->labels['spacer'], 'link' => '', 'active' => true, 'type' => 'spacer');
                }

                for ($i = $this->currentPage - $pages_spacing; $i <= $this->currentPage + $pages_spacing; $i++) {
                    $isLink = ($this->currentPage == $i) ? false : true;
                    $links[] = array('isLink' => $isLink, 'label' => $i, 'link' => $this->getUrl($i), 'active' => true, 'type' => 'number');
                }

                if ($this->labels['spacer'] !== false) {
                    $links[] = array('isLink' => false, 'label' => $this->labels['spacer'], 'link' => '', 'active' => true, 'type' => 'spacer');
                }
            }
        }

        if ($this->labels['next'] !== false) {
            if ($this->currentPage < $this->pages) {
                $links[] = array('isLink' => true, 'label' => $this->labels['next'] !== true ? $this->labels['next'] : $this->currentPage + 1, 'link' => $this->getUrl($this->currentPage + 1), 'active' => true, 'type' => 'next');
            } elseif ($this->showInactive) {
                $links[] = array('isLink' => false, 'label' => $this->labels['next'] !== true ? $this->labels['next'] : $this->currentPage + 1, 'link' => '', 'active' => false, 'type' => 'next');
            }
        }

        if ($this->labels['next10'] !== false) {
            if ($this->currentPage + 9 < $this->pages) {
                $links[] = array('isLink' => true, 'label' => $this->labels['next10'] !== true ? $this->labels['next10'] : $this->currentPage + 10, 'link' => $this->getUrl($this->currentPage + 10), 'active' => true, 'type' => 'next10');
            } elseif ($this->showInactive) {
                $links[] = array('isLink' => false, 'label' => $this->labels['next10'] !== true ? $this->labels['next10'] : $this->currentPage + 10, 'link' => '', 'active' => false, 'type' => 'next10');
            }
        }

        if ($this->labels['next100'] !== false) {
            if ($this->currentPage + 99 < $this->pages) {
                $links[] = array('isLink' => true, 'label' => $this->labels['next100'] !== true ? $this->labels['next100'] : $this->currentPage + 100, 'link' => $this->getUrl($this->currentPage + 100), 'active' => true, 'type' => 'next100');
            } elseif ($this->showInactive) {
                $links[] = array('isLink' => false, 'label' => $this->labels['next100'] !== true ? $this->labels['next100'] : $this->currentPage + 100, 'link' => '', 'active' => false, 'type' => 'next100');
            }
        }

        if ($this->labels['next1000'] !== false) {
            if ($this->currentPage + 999 < $this->pages) {
                $links[] = array('isLink' => true, 'label' => $this->labels['next1000'] !== true ? $this->labels['next1000'] : $this->currentPage + 1000, 'link' => $this->getUrl($this->currentPage + 1000), 'active' => true, 'type' => 'next1000');
            } elseif ($this->showInactive) {
                $links[] = array('isLink' => false, 'label' => $this->labels['next1000'] !== true ? $this->labels['next1000'] : $this->currentPage + 1000, 'link' => '', 'active' => false, 'type' => 'next1000');
            }
        }

        if ($this->labels['last'] !== false) {
            if ($this->currentPage != $this->pages) {
                $links[] = array('isLink' => true, 'label' => $this->labels['last'] !== true ? $this->labels['last'] : $this->pages, 'link' => $this->getUrl($this->pages), 'active' => true, 'type' => 'last');
            } elseif ($this->showInactive) {
                $links[] = array('isLink' => true, 'label' => $this->labels['last'] !== true ? $this->labels['last'] : $this->pages, 'link' => '', 'active' => false, 'type' => 'last');
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