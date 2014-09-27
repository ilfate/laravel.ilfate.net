<?php
/**
 * PHPulsar
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Helper;

class Breadcrumbs {
    protected static $links = array();

    /**
     * @param array $links
     */
    public function setLinks($links)
    {
        self::$links = $links;
    }

    /**
     * @param string $url
     * @param string $name
     */
    public function addLink($url, $name)
    {
        self::$links[] = array(
            'url'    => $url,
            'name'   => $name,
            'active' => true
        );
    }

    /**
     * @return array
     */
    public static function getLinks()
    {
        $links = self::$links;
        array_unshift($links, array(
            'url'    => '/',
            'name'   => 'Home',
            'active' => true
        ));
        $links[count($links) - 1]['active'] = false;
        return $links;
    }
} 