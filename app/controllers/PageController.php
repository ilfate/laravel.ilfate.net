<?php

use Helper\Breadcrumbs;

class PageController extends \BaseController
{
    /**
     * @var Breadcrumbs
     */
    protected $breadcrumbs;

    /**
     * @param Breadcrumbs $breadcrumbs
     */
    public function __construct(Breadcrumbs $breadcrumbs) {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * Main page
     *
     * @return Response
     */
    public function index()
    {
        return View::make('pages.index');
    }

    /**
     * Cv page
     *
     * @return Response
     */
    public function cv()
    {
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . __FUNCTION__), 'CV');
        return View::make('pages.cv');
    }

    /**
     * Skills page
     *
     * @return Response
     */
    public function skills()
    {
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . 'cv'), 'CV');
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . __FUNCTION__), 'Skills');
        return View::make('pages.skills');
    }

    /**
     * My photo page
     *
     * @return Response
     */
    public function photo()
    {
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . __FUNCTION__), 'Photo');
        $data = array(
            'images_gallery' => array(
                array('img' => '/images/my/baikal1.jpg', 'down-shift' => 0.1),
                array('img' => '/images/my/berlin1.jpg'),
                array('img' => '/images/my/snow1.jpg', 'down-shift' => 0.1),
                array('img' => '/images/my/berlin2.jpg'),
                array('img' => '/images/my/tu1.jpg'),
                array('img' => '/images/my/code1.jpg'),
                array('img' => '/images/my/snow3.jpg'),
                array('img' => '/images/my/ilfate2.jpg'),
                array('img' => '/images/my/tu2.jpg'),
                array('img' => '/images/my/snow0.jpg'),
                array('img' => '/images/my/aust2.jpg', 'down-shift' => 0.1),
                array('img' => '/images/my/aust3.jpg'),
                array('img' => '/images/my/is1.jpg'),
                array('img' => '/images/my/ilfate2.png'),

            )
        );
        return View::make('pages.photo', $data);
    }
}
