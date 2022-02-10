<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\curriculums\add;
use App\Models\Country;
use App\Models\Curriculum;
use App\Models\curriculumTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class curriculums extends Controller
{
    public function index(){
        $curriculums = Curriculum::where('status', '!=', -1)->get();
        return view('admins.curriculums.index')->with('curriculums', $curriculums);
    }

    public function delete($country_id){
        //get country 
        $curriculum = curriculum::find($country_id);

        if($curriculum == null)
            return redirect('admins/curriculums')->with('error', 'delete faild');

        $curriculum->status = -1;
        $curriculum->save();

        return redirect('admins/curriculums')->with('success', 'delete country success');
    }

    public function createView(){
        $countries = Country::active()->get();
        return view('admins.curriculums.create')->with('countries', $countries);
    }

    public function create(add $request){
        try{
            DB::beginTransaction();
                //get cate status
                ($request->status== 1)? $active = 1: $active = 0;

                //create country
                $new_curriculums = Curriculum::create([
                    'status'            => $active,
                    'country_id'        => $request->country_id,
                ]);

                foreach($request->curriculums as $key=>$curriculum){
                    curriculumTranslation::create([
                        'name'              => $curriculum['name'],
                        'locale'            => $key,
                        'curriculum_id'     => $new_curriculums['id'],
                    ]);
                }
            DB::commit();
            return redirect('admins/curriculums')->with('success', 'add curriculum success');
        } catch(\Exception $ex){
            //if there are error
            return redirect('admins/curriculums')->with('error', 'add curriculum faild');
        }
    }
}
