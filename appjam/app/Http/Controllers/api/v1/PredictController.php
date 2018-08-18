<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Predict;
use App\User;

class PredictController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $predictArr = [];
        $users = User::all();
        foreach($users as $r) {
            $userId = $r->getAttribute('id');

            $predicts = Predict::where('user_id', '=', $userId)->get();

            if(count($predicts) > 0) {
                $pred = [];
                foreach ($predicts as $rr) {
                    array_push($pred, ["movie_name" => $rr->getAttribute('movie_name'), "movie_score" => $rr->getAttribute('movie_score')]);
                }

                array_push($predictArr, ["user_id" => $userId, $pred]);
            }
        }

        return response()->json([
            $predictArr
        ],200,[],JSON_UNESCAPED_UNICODE);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Predict::create($request->all());
        return response()->json([
            'success'
        ],200,[],JSON_UNESCAPED_UNICODE);
    }

}
