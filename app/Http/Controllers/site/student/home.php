<?php

namespace App\Http\Controllers\site\student;

use App\Http\Controllers\Controller;
use App\Http\Resources\subjectsResource;
use App\Http\Resources\term_SubjectResource;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Term;
use Illuminate\Http\Request;

class home extends Controller
{
    public function index(){
        //get student or vender
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        if($student->year_id == null)
            return $this::faild(trans('site.student must choose an academic year'), 400, 'E00');

        $terms = Term::where('status', 1)
                        ->whereHas('Year', function($query) use($student){
                            $query->where('id', $student->year_id);
                        })->with(['Subjects' => function($q){
                            $q->active();
                        }])
                        ->get();

        return $this::success(trans('auth.success'), 400, 'terms', term_SubjectResource::collection($terms));
    }
}
