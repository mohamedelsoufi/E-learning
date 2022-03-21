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
            // 'subject_id'       => 'required|exists:subjects,id',
            'year_id'          => 'required|exists:years,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors()->first(), 403, 'E03');
        }

        //get teacher
        if (! $teacher = auth('teacher')->user()) {
            return $this::faild(trans('auth.teacher not found'), 404, 'E04');
        }

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
                                ->orderBy('id', 'desc')
                                ->paginate(5);

        return response()->json([
                                'successful'        => true,
                                'message'           => trans('auth.success'),
                                'questions_count'   => Question::where('subject_id', $subject->id)->count(),
                                'questions'         => questionsResource::collection($questions)->response()->getData(true),
                            ], 200);
    }
}
