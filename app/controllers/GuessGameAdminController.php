<?php

use Helper\Breadcrumbs;
use Illuminate\Support\Facades\Session;

class GuessGameAdminController extends \BaseController
{
    const PATH_TO_FILES = '/images/game/guess/';
    /**
     *
     */
    public function __construct()
    {

    }

    /**
     * Display a listing of the games.
     *
     * @return Response
     */
    public function index()
    {
//        $game[self::GAME_CURRENT_QUESTION] = $this->getNewQuestion($game['turn']);
//        $this->saveGame($game);
//        //}
//
//        if ($game['turn'] == 1) {
//            $firstQuestion = json_encode($this->exportQuestion($game[self::GAME_CURRENT_QUESTION]));
//        } else {
//            $firstQuestion = '{}';
//        }
//        View::share('firstQuestion', $firstQuestion);
//
//        View::share('page_title', 'Guess series game');

        $series = Series::orderBy('name')->get();
        View::share('series', $series);

        return View::make('games.guess.admin.index');
    }



    public function addSeries()
    {
        if (Request::getMethod() == 'POST') {
            $series = new Series();
            $series->name = Input::get('name');
            $series->year = (int) Input::get('year');
            $series->difficulty = (int) Input::get('difficulty');

            if ($series->name) {
                $series->save();
            }
            return Redirect::to('GuessSeries/admin');
        }
        return View::make('games.guess.admin.addSeries');
    }

    public function addImage()
    {
        $seriesId = Input::get('id');
        $difficulty = Input::get('difficulty') ?: 1;
        if (!$seriesId) {
            return Response::json('no id', 400);
        }
        $series = Series::find($seriesId);
        if (!$series) {
            return Response::json('no series found', 400);
        }

        $file = Input::file('file');
        $destinationPath = public_path() . self::PATH_TO_FILES;


        $extension = $file->getClientOriginalExtension();
        $filename = str_random(16) . '.' . $extension;
        $upload_success = $file->move($destinationPath, $filename);

        if ($upload_success) {
            $fileRaw = file_get_contents($destinationPath . $filename);
            $image = new SeriesImage();
            $image->image = $fileRaw;
            $image->url = $filename;
            $image->series_id = $seriesId;
            $image->difficulty = $difficulty;

            $image->save();

            //unlink($destinationPath . $filename);
            return Response::json('success', 200);
        } else {
            return Response::json('error', 400);
        }
    }

    public function seriesInfo($id)
    {
        $id = (int) $id;
        $images = SeriesImage::where('series_id', '=', $id)->get();
        View::share('images', $images->toArray());
        return View::make('games.guess.admin.series');
    }

    public function generateImages()
    {
        $seriesId = Input::get('seriesId', null);
        if ($seriesId) {
            $images = SeriesImage::where('series_id', '=', $seriesId)->get();
            foreach ($images as $image) {
                file_put_contents(public_path() . '/images/game/guess/' . $image->url, $image->image);
                //file_put_contents(/home/ilfate/www/php/ilfate.net/public/images/game/guess/SLKdXTrglwvDm3ia.jpg): failed to open stream: Permission denied
            }
        }
    }
}
