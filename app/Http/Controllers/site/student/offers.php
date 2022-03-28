<?php

namespace App\Http\Controllers\site\student;

use App\Http\Controllers\Controller;
use App\Http\Resources\offersResource;
use App\Models\Offer;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class offers extends Controller
{
    public function index(){
        $offers = Offer::get();

        return $this->success(
            trans('auth.success'),
            200,
            'offers',
            offersResource::collection($offers)
        );
    }

    public function take_offer(Request $request){
        // validate registeration request
        $validator = Validator::make($request->all(), [
            'offer_id'     => 'required|integer|exists:offers,id',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors()->first(), 403);
        }

        try{
            DB::beginTransaction();
            //get student
            if (! $student = auth('student')->user()) {
                return $this::faild(trans('auth.student not found'), 404, 'E04');
            }

            //get offer
            $offer = Offer::find($request->get('offer_id'));

            //check if student balance Not enough
            if($student->balance - $offer->price < 0)
                return $this->faild(trans('site.your balance not enough'), 200);

            //tacke offer
            $student->free      += $offer->classes_count;
            $student->balance   -= $offer->price;    //tack class cost from student
            $student->save();

            DB::commit();
            return $this->success(trans('auth.success'), 200);
        } catch(\Exception $ex){
            //if there are error
            return $this->faild(trans('auth.faild'), 200);
        }
    }
}