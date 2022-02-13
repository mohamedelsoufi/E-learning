<?php

namespace App\Http\Controllers\site\student;

use App\Http\Controllers\Controller;
use App\Http\Resources\questionsResource;
use App\Models\Question;
use App\Traits\response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class questions extends Controller
{
    use response;
    public function index(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'subject_id'       => 'required|exists:subjects,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors(), 403, 'E03');
        }

        //get questions
        $questions = Question::active()
                                ->where('subject_id', $request->get('subject_id'))
                                ->paginate(5);

        return $this->success(trans('auth.success'),
                                200,
                                'questions',
                                questionsResource::collection($questions)->response()->getData(true),
                            );
    }

    public function create(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'question'         => 'required|string|min:3|max:2000',
            'subject_id'       => 'required|exists:subjects,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors(), 403, 'E03');
        }

        //get student or vender
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        //create Question
        Question::create([
            'student_id'    => $student->id,
            'subject_id'    => $request->get('subject_id'),
            'question'      => $request->get('question'),

        ]);

        return $this->success(trans('site.add question success'), 200);
    }

    public function delete(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'question_id'         => 'required|string|exists:questions,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors(), 403, 'E03');
        }

        //get student or vender
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        //get question
        $question = Question::where('student_id', $student->id)->find($request->get('question_id'));

        if($question == null)
            return $this::faild(trans('site.question not found'), 404, 'E04');

        //delete question
        if($question->delete())
            return $this->success(trans('site.delete question success'), 200);

        return $this::faild(trans('site.delete question faild'), 400, 'E00');
    }

    public function update(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'question'         => 'required|string|min:3|max:2000',
            'question_id'         => 'required|string|exists:questions,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors(), 403, 'E03');
        }

        //get student or vender
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        //get question
        $question = Question::where('student_id', $student->id)->find($request->get('question_id'));

        if($question == null)
            return $this::faild(trans('site.question not found'), 404, 'E04');

        //update question
        if($question->update(['question'=> $request->get('question')]))
            return $this->success(trans('site.update question success'), 200);

        return $this::faild(trans('site.update question faild'), 400, 'E00');
    }
}