<?php

namespace App\Http\Controllers\site\student;

use App\Http\Controllers\Controller;
use App\Http\Resources\answersResource;
use App\Models\Answer;
use App\Traits\response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class answers extends Controller
{
    use response;
    public function index(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'question_id'    => 'required|exists:questions,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors(), 403, 'E03');
        }

        //get answers
        $answers = Answer::active()
                            ->where('question_id', $request->get('question_id'))
                            ->paginate(5);

        return $this->success(trans('auth.success'),
                                200,
                                'questions',
                                answersResource::collection($answers)->response()->getData(true),
                            );
    }

    public function create(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'answer'         => 'required|string|min:3|max:2000',
            'question_id'    => 'required|exists:questions,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors(), 403, 'E03');
        }

        //get student or vender
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        //create Question
        Answer::create([
            'answerable_id'    => $student->id,
            'answerable_type'  => 'App\Models\Student',
            'question_id'      => $request->get('question_id'),
            'answer'           => $request->get('answer'),
        ]);

        return $this->success(trans('site.add answer success'), 200);
    }

    public function delete(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'answer_id'         => 'required|exists:answers,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors(), 403, 'E03');
        }

        //get student or vender
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        //get answer
        $answer = Answer::where('answerable_id', $student->id)
                            ->where('answerable_type', 'App\Models\Student')
                            ->find($request->get('answer_id'));

        if($answer == null)
            return $this::faild(trans('site.answer not found'), 404, 'E04');

        //delete question
        if($answer->delete())
            return $this->success(trans('site.delete answer success'), 200);

        return $this::faild(trans('site.delete answer faild'), 400, 'E00');
    }

    public function update(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'answer'         => 'required|string|min:3|max:2000',
            'answer_id'      => 'required|exists:answers,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors(), 403, 'E03');
        }

        //get student or vender
        if (! $student = auth('student')->user()) {
            return $this::faild(trans('auth.student not found'), 404, 'E04');
        }

        //get answer
        $answer = Answer::where('answerable_id', $student->id)
                        ->where('answerable_type', 'App\Models\Student')
                        ->find($request->get('answer_id'));

        if($answer == null)
            return $this::faild(trans('site.answer not found'), 404, 'E04');

        //update answer
        if($answer->update(['answer'=> $request->get('answer')]))
            return $this->success(trans('site.update answer success'), 200);

        return $this::faild(trans('site.update answer faild'), 400, 'E00');
    }
}
