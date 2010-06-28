<?php

class MiniMVC_Pager
{
    /**
     *
     * @var MiniMVC_Registry
     */
    protected $registry = null;
    
    private $entries = 0;
    private $entriesPerPage = 0;
    private $currentPage = 0;
    private $pages = 0;
    private $maxPages = 0;
    private $url = '';
    private $labels = array();

    public function __construct($entries, $entriesPerPage, $url, $currentPage = 1, $maxPages = 11)
    {
        $this->registry = MiniMVC_Registry::getInstance();
        
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

    public function getHtml($module = null)
    {
        return $this->registry->helper->Partial->get('pager', $this->getForPartial(), $module);
    }

    public function getForPartial()
    {
        return array('pages' => $this->pages, 'links' => $this->getLinks());
    }

    public function getPages()
    {
        return $this->pages;
    }

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

    public function getOffset()
    {
        return ($this->currentPage - 1) * $this->entriesPerPage;
    }

    public function getLimit()
    {
        return $this->entriesPerPage;
    }

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