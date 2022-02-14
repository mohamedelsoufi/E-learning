<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\subjects\add;
use App\Models\Subject;
use App\Models\SubjectTranslation;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class subjects extends Controller
{
    public function index(Request $request){
        $subjects = Subject::where('status', '!=', -1)
                            ->where('term_id', $request->get('term'))
                            ->get();

        return view('admins.subjects.index')->with([
            'subjects'          => $subjects,
            'curriculum_id'     => $request->get('curriculum'),
            'level_id'          => $request->get('level'),
            'year_id'           => $request->get('year'),
            'term_id'           => $request->get('term'),
        ]);
    }

    public function delete($subject_id){
        //get Subject 
        $subject = Subject::find($subject_id);

        if($subject == null)
            return redirect('admins/subjects')->with('error', 'delete faild');

        $subject->status = -1;
        $subject->save();

        return redirect('admins/subjects')->with('success', 'delete subject success');
    }

    public function createView(){
        $terms = Term::active()->get();
        return view('admins.subjects.create')->with('terms', $terms);
    }

    public function create(add $request){
        try{
            DB::beginTransaction();
                //get cate status
                ($request->status== 1)? $active = 1: $active = 0;

                //create country
                $new_subject = Subject::create([
                    'status'            => $active,
                    'term_id'           => $request->term_id,
                ]);

                foreach($request->subjects as $key=>$subject){
                    SubjectTranslation::create([
                        'name'              => $subject['name'],
                        'locale'            => $key,
                        'subject_id'        => $new_subject['id'],
                    ]);
                }
            DB::commit();
            return redirect('admins/subjects')->with('success', 'add subject success');
        } catch(\Exception $ex){
            //if there are error
            return redirect('admins/subjects')->with('error', 'add subject faild');
        }
    }

    public function editView($subject_id){
        $subject = Subject::find($subject_id);
        $terms = Term::active()->get();

        //if Curriculum not found
        if($subject == null)
            return redirect('admins/subjects');
        
        return view('admins.subjects.edit')->with([
            'subject'       => $subject,
            'terms'         => $terms,
        ]);
    }

    public function edit($subject_id, add $request){
        try{
            $subject = Subject::find($subject_id);
            $subjectsTranslation = SubjectTranslation::where('subject_id', $subject_id)->get();
            
            DB::beginTransaction();
                //get cate status
                ($request->status== 1)? $active = 1: $active = 0;

                //edit subject
                $subject->status           = $active;
                $subject->term_id          = $request->term_id;
                $subject->save();

                //change all term trans name
                foreach($subjectsTranslation as $subjectTranslation){
                    $subjectTranslation->name = $request->subjects[$subjectTranslation->locale]['name'];
                    $subjectTranslation->save();
                }

            DB::commit();
            return redirect('admins/subjects')->with('success', 'add subject success');
        } catch(\Exception $ex){
            //if there are error
            return redirect('admins/subjects')->with('error', 'add subject faild');
        }
    }
}
