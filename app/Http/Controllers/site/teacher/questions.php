<?php

namespace App\Http\Controllers\site\teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\questionsResource;
use App\Models\Question;
use App\Models\Subject;
use App\Traits\response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class questions extends Controller
{
    use response;
    public function index(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'year_id'          => 'nullable|exists:years,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors()->first(), 403, 'E03');
        }

        //get teacher
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }

        if($request->get('year_id') == null){//if teacher not enter year_id
            //get questions
            $questions = Question::active()
                                    ->whereHas('Subject', function($query) use($teacher){
                                        $query->where('main_subject_id', $teacher->main_subject_id);
                                    })
                                    ->orderBy('id', 'desc');

        } else {    //if teacher enter year_id
            $subject = Subject::active()
                            ->where('main_subject_id', $teacher->main_subject_id)
                            ->whereHas('Term', function($query) use($request){
                                $query->where('year_id', $request->get('year_id'));
                            })
                            ->first();

            if($subject == null){
                return $this->faild(trans('site.this year not has your subject'), 404,'E04');
            }

            //get questions
            $questions = Question::active()
                                    ->where('subject_id', $subject->id)
                                    ->orderBy('id', 'desc');
        }

        return response()->json([
            'successful'        => true,
            'message'           => trans('auth.success'),
            'questions_count'   => $questions->count(),
            'questions'         => questionsResource::collection($questions->paginate(5))->response()->getData(true),
        ], 200);
    }
}
