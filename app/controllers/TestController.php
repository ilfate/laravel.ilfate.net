<?php

use Helper\Breadcrumbs;

class TestController extends \BaseController
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
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . __FUNCTION__), 'Code');
        return View::make('games.vortex.index');
    }

    /**
     * Main page
     *
     * @return Response
     */
    public function engine()
    {
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . 'index'), 'Code');
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . __FUNCTION__), 'Engine');
        return View::make('code.engine');
    }

    /**
     * Main page
     *
     * @return Response
     */
    public function stars()
    {
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . 'index'), 'Code');
        $this->breadcrumbs->addLink(action(__CLASS__ . '@' . __FUNCTION__), 'Stars');
        return View::make('code.stars');
    }
}
