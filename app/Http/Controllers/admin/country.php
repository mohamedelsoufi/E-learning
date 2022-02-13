<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\countries\add;
use App\Models\Country as ModelsCountry;
use App\Models\CountryTranslation;
use Astrotomic\Translatable\Locales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class country extends Controller
{
    public function index(){
        $countries = ModelsCountry::where('status', '!=', -1)->get();
        return view('admins.country.index')->with('countries', $countries);
    }

    public function delete($country_id){
        //get country 
        $country = ModelsCountry::find($country_id);

        if($country == null)
            return redirect('admins/countries')->with('error', 'delete faild');

        $country->status = -1;
        $country->save();

        return redirect('admins/countries')->with('success', 'delete country success');
    }

    public function createView(){
        return view('admins.country.create');
    }

    public function create(add $request){
        try{
            DB::beginTransaction();
                //get cate status
                ($request->status== 1)? $active = 1: $active = 0;

                //create country
                $new_country = ModelsCountry::create([
                    'status'            => $active,
                    'dialing_code'      => $request->dialing_code,
                ]);

                foreach($request->countries as $key=>$country){
                    CountryTranslation::create([
                        'name'              => $country['name'],
                        'locale'            => $key,
                        'country_id'        => $new_country['id'],
                    ]);
                }
            DB::commit();
            return redirect('admins/countries')->with('success', 'add country success');
        } catch(\Exception $ex){
            //if there are error
            return redirect('admins/countries')->with('error', 'add country faild');
        }
    }

    public function editView($country_id){
        $country = ModelsCountry::find($country_id);

        //if admin not found
        if($country == null)
            return redirect('admins/countries');
        
        return view('admins.country.edit')->with([
            'country' => $country,
        ]);
    }

    public function edit($country_id,add $request){
        $country = ModelsCountry::find($country_id);
        $CountriesTranslation = CountryTranslation::where('country_id', $country_id)->get();

        try{
            DB::beginTransaction();
                //get cate status
                ($request->status== 1)? $active = 1: $active = 0;

                //edit country
                $country->name              = $request->countries['en']['name'];
                $country->status            = $active;
                $country->dialing_code      = $request->dialing_code;
                $country->save();

                //change all country trans name
                foreach($CountriesTranslation as $CountryTranslation){
                    $CountryTranslation->name = $request->countries[$CountryTranslation->locale]['name'];
                    $CountryTranslation->save();
                }

            DB::commit();
            return redirect('admins/countries')->with('success', 'add country success');
        } catch(\Exception $ex){
            //if there are error
            return redirect('admins/countries')->with('error', 'add country faild');
        }
    }
}