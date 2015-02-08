<?php

use Helper\Breadcrumbs;
use Illuminate\Support\Facades\Session;

class GuessGameAdminController extends \BaseController
{
    const PATH_TO_FILES = '/images/game/guess/';
    const USER_EDIT_RIGHTS = 2;

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
        $series = Series::orderBy('name')->get();
        $user = User::getUser();
        $editAllowed = $this->checkForUserEditRights($user);
        View::share('editAllowed', $editAllowed);
        View::share('series', $series);

        return View::make('games.guess.admin.index');
    }



    public function addSeries()
    {
        $user = User::getUser();
        if (!$this->checkForUserEditRights($user)) {
            return Redirect::to('tcg/login');
        }
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
        $user = User::getUser();
        if (!$this->checkForUserEditRights($user)) {
            return Response::json('forbidden', 400);
        }
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
        $fileInPath = public_path() . self::PATH_TO_FILES . $filename;
        while (file_exists($fileInPath)) {
            $filename = str_random(16) . '.' . $extension;
            $fileInPath = public_path() . self::PATH_TO_FILES . $filename;
        }
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
        $user = User::getUser();
        if (!$this->checkForUserEditRights($user)) {
            return Redirect::to('tcg/login');
        }
        $id = (int) $id;
        $images = SeriesImage::where('series_id', '=', $id)->get();
        $sortedImages = [1 => [], 2 => [], 3 => []];
        foreach ($images as $value) {
            $sortedImages[$value['difficulty']][] = $value->toArray();
        }
        View::share('images', $sortedImages);
        View::share('seriesId', $id);
        return View::make('games.guess.admin.series');
    }

    public function deleteImage($id)
    {
        $user = User::getUser();
        if (!$this->checkForUserEditRights($user)) {
            return Response::json('forbidden', 400);
        }

        $image = SeriesImage::select('id', 'url', 'series_id')->where('id', $id)->first();
        $filename = public_path() . self::PATH_TO_FILES . $image->url;
        $seriesId = $image->series_id;
        if (file_exists($filename)) {
            unlink($filename);
            SeriesImage::where('id', '=', $id)->delete();
            return Redirect::to('GuessSeries/admin/series/' . $seriesId);
        }
        return Redirect::to('GuessSeries/admin/');
    }

    public function toggleActive($id) {
        $user = User::getUser();
        if (!$this->checkForUserEditRights($user)) {
            return Redirect::to('tcg/login');
        }

        $series = Series::where('id', $id)->first();
        if ($series->active) {
            $series->active = 0;
        } else {
            $series->active = 1;
        }
        $series->save();
        return Redirect::to('GuessSeries/admin/');
    }

    public function liveStream()
    {
        $games = GuessStats::getLastGames();
        View::share('games', $games);
        return View::make('games.guess.admin.liveStream');
    }

    public function generateImages()
    {
        $user = User::getUser();
        if (!$this->checkForUserEditRights($user)) {
            return Response::json('forbidden', 400);
        }
        
        $seriesId = Input::get('seriesId', null);
        if ($seriesId) {
            $images = SeriesImage::where('series_id', '=', $seriesId)->get();
            foreach ($images as $image) {
                file_put_contents(public_path() . self::PATH_TO_FILES . $image->url, $image->image);
                //file_put_contents(/home/ilfate/www/php/ilfate.net/public/images/game/guess/SLKdXTrglwvDm3ia.jpg): failed to open stream: Permission denied
            }
        }
    }

    public function checkForUserEditRights($user)
    {
        if ($user->rights < self::USER_EDIT_RIGHTS) {
            return false;
        }
        return true;
    }
}
