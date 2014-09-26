<?php

class PageController extends \BaseController
{
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
        return View::make('pages.cv');
    }

    /**
     * Skills page
     *
     * @return Response
     */
    public function skills()
    {
        return View::make('pages.skills');
    }

    /**
     * My photo page
     *
     * @return Response
     */
    public function photo()
    {
        $data = array(
            'images_gallery' => array(
                array('img' => '/images/my/baikal1.jpg'),
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
