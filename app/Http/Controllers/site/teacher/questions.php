<?php

namespace App\Http\Controllers\site\teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\questionsResource;
use App\Models\Question;
use App\Traits\response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class questions extends Controller
{
    use response;
    public function index(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'subject_id'       => 'required|exists:subjects,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors()->first(), 403, 'E03');
        }

        //get questions
        $questions = Question::active()
                                ->where('subject_id', $request->get('subject_id'))
                                ->orderBy('id', 'desc')
                                ->paginate(5);

        return response()->json([
                                'successful'        => true,
                                'message'           => trans('auth.success'),
                                'questions_count'   => Question::where('subject_id', $request->get('subject_id'))->count(),
                                'questions'         => questionsResource::collection($questions)->response()->getData(true),
                            ], 200);
    }
}
