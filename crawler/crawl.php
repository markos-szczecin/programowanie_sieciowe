<?php
require_once 'Crawler.php';
try {
    if (php_sapi_name() === 'cli') {
        $data = array(
            'url_start' => @$argv[1],
            'depth' => @$argv[2],
            'search' => @$argv[3],
            'time' => @$argv[4],
            'in_domain' => @$argv[5]
        );
        $data = getData($data);
    } else {
        $data = getData($_POST);
    }

    Crawler::setTimeLimit($data['time']);
    $crawler = new Crawler();
    $crawler
        ->setDepth($data['depth'])
        ->setUrl($data['url_start'])
        ->setSameHost($data['in_domain'])
        ->setSearchText($data['search']);
    $crawler->crawl();
} catch (Exception $e) {
    die($e->getMessage());
}

/**
 * @return array
 */
function getData(array $data = array()) : array
{
    if (empty($data['url_start'])) {
        $data['url_start'] = 'http://zut.edu.pl';
    }
    if (empty($data['depth'])) {
        $data['depth'] = 3;
    }
    if (!isset($data['search'])) {
        $data['search'] = '';
    }
    if (empty($data['time'])) {
        $data['time'] = 180;
    }
    if (empty($data['in_domain'])) {
        $data['in_domain'] = true;
    }

    return $data;
}