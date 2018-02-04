<?php
class Crawler
{
    private $depth = 2;
    private $url;
    private $results = array();
    private $sameHost = false;
    private $host;
    private $searchText;
    private $endTime;


    private function checkTime()
    {
        if (date('Y-m-d H:i:s') >= $this->endTime) {
            exit;
        }
    }
    /**
     * @param int $timeout
     */
    public function setTimeLimit(int $timeout) : Crawler
    {
        $this->startTime = date('Y-m-d H:i:s', strtotime('+ ' . $timeout . 'seconds'));

        return $this;
    }

    /**
     * @param string $text
     *
     * @return Crawler
     */
    public function setSearchText(string $text) : Crawler
    {
        $this->searchText = $text;

        return $this;
    }

    /**
     * @param int $depth
     *
     * @return Crawler
     */
    public function setDepth(int $depth) : Crawler
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * @return array
     */
    public function getResults() : array
    {
        return $this->results;
    }

    /**
     * @param bool $sameHost
     *
     * @return Crawler
     */
    public function setSameHost(bool $sameHost) : Crawler
    {
        $this->sameHost = $sameHost;

        return $this;
    }

    private function setHost(string $host) : Crawler
    {
        $this->host = $host;

        return $this;
    }
    /**
     * @param string $url
     *
     * @return Crawler
     */
    public function setUrl(string $url) : Crawler
    {
        $this->url = $url;
        $this->setHost($this->getHostFromUrl($url));

        return $this;
    }

    public function crawl()
    {
        if (empty($this->url)) {
            echo $this->url;
            throw new Exception('Błąd');
        }
        $this->_crawl($this->url, $this->depth);

        usort($this->results, function($a, $b) {
            if ($a['url'] == $b['url']) return 0;
            return (strtolower($a['url']) > strtolower($b['url'])) ? 1 : -1;
        });
        return $this->results;
    }

    /**
     * @param string $url
     * @param int $depth
     * 
     * @return string|void
     */
    private function _crawl(string $url, int $depth)
    {
        static $seen = array();

        echo 'Time ' . date('Y-m-d H:i:s') . ' ZBADANO ' . $url;
        $content = @file_get_contents($url);
        if ($content) {
            $this->searchOnSite($url, $content);
        } else {
            echo ' <span class="red">[Błędny link]</span>';
        }
        $this->checkTime();
        if (php_sapi_name() === 'cli') {
            echo PHP_EOL;
        } else {
            echo '<br />';
        }

        if (empty($url)) return;
        if (!$url = $this->buildUrl($this->url, $url)) {
            return;
        }
        if ($depth === 0 || isset($this->results[$url])) {
            return;
        }
        $dom = new \DOMDocument('1.0');
        @$dom->loadHTMLFile($url);
        $this->results[$url] = array(
            'url' => $url,
            'depth' => $depth
        );

        $crawled = $seen;
        $anchors = $dom->getElementsByTagName('a');

        foreach ($anchors as $element)
        {

            if (!$href = $this->buildUrl($url, $element->getAttribute('href'))) {
                continue;
            }

            if (!in_array($href, $seen)) {
                $seen[] = $href;
            }

        }
        $crawl = array_diff($seen, $crawled);

        if (!empty($crawl)) {
            array_map(array($this, '_crawl'), $crawl, array_fill(0, count($crawl), $depth - 1));
        }

        return $url;
    }

    /**
     * @param string $url
     *
     * @return Crawler
     */
    private function searchOnSite(string $url, $content) : Crawler
    {
        if ($this->searchText) {
            if (stripos($content, $this->searchText)) {
                echo ' <span class="green">[Znaleziono szukany tekst]</span>';
            }
        }

        return $this;
    }

    /**
     * @param string $url
     * @param string $href
     *
     * @return string
     */
    private function buildUrl(string $url, string $href) : string
    {
        $url = trim($url);
        $href = trim($href);
        if (0 !== strpos($href, 'http'))
        {
            if (0 === strpos($href, 'javascript:') || 0 === strpos($href, '#'))
            {
                return false;
            }
            $path = '/' . ltrim($href, '/');
            if (extension_loaded('http'))
            {
                $new_href = http_build_url($url, array('path' => $path), HTTP_URL_REPLACE, $parts);
            }
            else
            {
                $parts = parse_url($url);
                $new_href = $this->buildUrlFromParts($parts);
                $new_href .= $path;
            }
            if (0 === strpos($href, './') && !empty($parts['path']))
            {
                if (!preg_match('@/$@', $parts['path'])) {
                    $path_parts = explode('/', $parts['path']);
                    array_pop($path_parts);
                    $parts['path'] = implode('/', $path_parts) . '/';
                }
                $new_href = $this->buildUrlFromParts($parts) . $parts['path'] . ltrim($href, './');
            }
            $href = $new_href;
        }
        if ($this->sameHost && $this->host != $this->getHostFromUrl($href)) {
            return false;
        }

        return $href;
    }


    /**
     * @param array $parts
     *
     * @return string
     */
    private function buildUrlFromParts(array $parts) : string
    {
        $new_href = $parts['scheme'] . '://';
        if (isset($parts['user']) && isset($parts['pass'])) {
            $new_href .= $parts['user'] . ':' . $parts['pass'] . '@';
        }
        $new_href .= $parts['host'];
        if (isset($parts['port'])) {
            $new_href .= ':' . $parts['port'];
        }
        return $new_href;
    }


    /**
     * @param string $url
     *
     * @return string|array
     */
    private function getHostFromUrl(string $url)
    {
        $parts = parse_url($url);
        preg_match("@([^/.]+)\.([^.]{2,6}(?:\.[^.]{2,3})?)$@", $parts['host'], $host);

        return array_shift($host);
    }


}