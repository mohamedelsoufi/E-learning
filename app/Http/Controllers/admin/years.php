<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\years\add;
use App\Models\Level;
use App\Models\Year;
use App\Models\YearTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class years extends Controller
{
    public function index(){
        $years = Year::where('status', '!=', -1)->get();
        return view('admins.years.index')->with('years', $years);
    }

    public function delete($level_id){
        //get level 
        $year = Year::find($level_id);

        if($year == null)
            return redirect('admins/years')->with('error', 'delete faild');

        $year->status = -1;
        $year->save();

        return redirect('admins/years')->with('success', 'delete level success');
    }

    public function createView(){
        $levels = Level::active()->get();
        return view('admins.years.create')->with('levels', $levels);
    }

    public function create(add $request){
        try{
            DB::beginTransaction();
                //get cate status
                ($request->status== 1)? $active = 1: $active = 0;

                //create country
                $new_year = Year::create([
                    'status'            => $active,
                    'level_id'          => $request->level_id,
                ]);

                foreach($request->years as $key=>$year){
                    YearTranslation::create([
                        'name'              => $year['name'],
                        'locale'            => $key,
                        'year_id'          => $new_year['id'],
                    ]);
                }
            DB::commit();
            return redirect('admins/years')->with('success', 'add year success');
        } catch(\Exception $ex){
            //if there are error
            return redirect('admins/years')->with('error', 'add year faild');
        }
    }

    public function editView($year_id){
        $year = Year::find($year_id);
        $levels = Level::active()->get();

        //if Curriculum not found
        if($year == null)
            return redirect('admins/years');
        
        return view('admins.years.edit')->with([
            'year'          => $year,
            'levels'        => $levels,
        ]);
    }

    public function edit($year_id, add $request){
        try{
            $year = Year::find($year_id);
            $yearsTranslation = YearTranslation::where('year_id', $year_id)->get();
            
            DB::beginTransaction();
                //get cate status
                ($request->status== 1)? $active = 1: $active = 0;

                //edit curriculum
                $year->status            = $active;
                $year->level_id          = $request->level_id;
                $year->save();

                //change all year trans name
                foreach($yearsTranslation as $yearTranslation){
                    $yearTranslation->name = $request->years[$yearTranslation->locale]['name'];
                    $yearTranslation->save();
                }

            DB::commit();
            return redirect('admins/years')->with('success', 'add year success');
        } catch(\Exception $ex){
            //if there are error
            return redirect('admins/years')->with('error', 'add year faild');
        }
    }
}