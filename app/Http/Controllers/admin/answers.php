<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use Illuminate\Http\Request;

class answers extends Controller
{
    public function index(){
        //select all admin
        $answers = Answer::get();
        return view('admins.answers.index')->with('answers', $answers);
    }

    public function delete($answer_id){
        //get answer 
        $answer = Answer::find($answer_id);

        if($answer == null)
            return redirect('admins/answers')->with('error', 'delete answer faild');

        $answer->delete();

        return redirect('admins/answers')->with('success', 'delete answer success');
    }
}
