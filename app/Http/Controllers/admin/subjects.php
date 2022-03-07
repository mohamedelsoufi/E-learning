<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\subjects\add;
use App\Models\Image;
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
            'parms'             => 'curriculum=' . $request->get('curriculum') .
                                    '&&level=' . $request->get('level') . 
                                    '&&year=' . $request->get('year').
                                    '&&term=' . $request->get('term'),
        ]);
    }

    public function delete($subject_id){
        //get Subject 
        $subject = Subject::find($subject_id);
        $parms = 'curriculum=' . $_GET['curriculum'] .
                '&&level=' . $_GET['level'] .
                '&&year=' . $_GET['year'] .
                '&&term=' . $_GET['term'];

        if($subject == null)
            return redirect('admins/subjects?' . $parms)->with('error', 'delete faild');

        $subject->status = -1;
        $subject->save();

        return redirect('admins/subjects?' . $parms)->with('success', 'delete subject success');
    }

    public function createView(Request $request){
        $term_id = $request->get('term');
        $terms = Term::active()->where('year_id', $request->get('year'))->get();
        return view('admins.subjects.create')->with([
            'terms'     => $terms,
            'term_id'   => $term_id,
        ]);
    }

    public function create(add $request){
        $parms= 'curriculum=' . $_GET['curriculum'] .
                '&&level=' . $_GET['level'] .
                '&&year=' . $_GET['year'] .
                '&&term=' . $_GET['term'];
        try{
            DB::beginTransaction();
                //get cate status
                ($request->status== 1)? $active = 1: $active = 0;

                //create country
                $new_subject = Subject::create([
                    'status'            => $active,
                    'term_id'           => $request->term_id,
                ]);

                //add image
                $path = $this->upload_image($request->file('image'),'uploads/subjects', 100, 100);
                Image::create([
                    'imageable_id'   => $new_subject->id,
                    'imageable_type' => 'App\Models\Subject',
                    'src'            => $path,
                ]);

                foreach($request->subjects as $key=>$subject){
                    SubjectTranslation::create([
                        'name'              => $subject['name'],
                        'locale'            => $key,
                        'subject_id'        => $new_subject['id'],
                    ]);
                }
            DB::commit();
            return redirect('admins/subjects?' . $parms)->with('success', 'add subject success');
        } catch(\Exception $ex){
            //if there are error
            return redirect('admins/subjects?' . $parms)->with('error', 'add subject faild');
        }
    }

    public function editView($subject_id, Request $request){
        $subject = Subject::find($subject_id);
        $terms = Term::active()->where('year_id', $request->get('year'))->get();
        $parms= 'curriculum=' . $_GET['curriculum'] .
                '&&level=' . $_GET['level'] .
                '&&year=' . $_GET['year'] .
                '&&term=' . $_GET['term'];

        //if Curriculum not found
        if($subject == null)
            return redirect('admins/subjects?' . $parms);
        
        return view('admins.subjects.edit')->with([
            'subject'       => $subject,
            'terms'         => $terms,
        ]);
    }

    public function edit($subject_id, add $request){
        $parms= 'curriculum=' . $_GET['curriculum'] .
                '&&level=' . $_GET['level'] .
                '&&year=' . $_GET['year'] .
                '&&term=' . $_GET['term'];
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

                //update image
                if($request->has('image') != null){
                    $path = $this->upload_image($request->file('image'),'uploads/subjects', 100, 100);

                    if($subject->Image == null){
                        //if user don't have image 
                        Image::create([
                            'imageable_id'   => $subject->id,
                            'imageable_type' => 'App\Models\Subject',
                            'src'            => $path,
                        ]);

                    } else {
                        //if subjects have image
                        $oldImage = $subject->Image->src;

                        if(file_exists(base_path('public/uploads/subjects/') . $oldImage)){
                            unlink(base_path('public/uploads/subjects/') . $oldImage);
                        }

                        $subject->Image->src = $path;
                        $subject->Image->save();
                    }
                }

            DB::commit();
            return redirect('admins/subjects?' . $parms)->with('success', 'add subject success');
        } catch(\Exception $ex){
            //if there are error
            return redirect('admins/subjects?' . $parms)->with('error', 'add subject faild');
        }
    }
}
