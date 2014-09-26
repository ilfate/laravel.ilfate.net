<?php

class CodeController extends \BaseController
{

    /**
     * Main page
     *
     * @return Response
     */
    public function index()
    {
        return View::make('code.index');
    }

    /**
     * Main page
     *
     * @return Response
     */
    public function engine()
    {
        return View::make('code.engine');
    }

    /**
     * Main page
     *
     * @return Response
     */
    public function stars()
    {
        return View::make('code.stars');
    }
}
