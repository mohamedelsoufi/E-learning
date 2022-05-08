<?php

namespace App\Http\Controllers\site\guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\answersResource;
use App\Http\Resources\classTypeResource;
use App\Http\Resources\countryResource;
use App\Http\Resources\curriculumResource;
use App\Http\Resources\main_subjectResource;
use App\Http\Resources\materialResource;
use App\Http\Resources\questionsResource;
use App\Http\Resources\subjectsResource;
use App\Http\Resources\teacher_classesTypeResourc;
use App\Models\Answer;
use App\Models\Class_type;
use App\Models\Country;
use App\Models\Curriculum;
use App\Models\Main_subject;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Teacher;
use App\Traits\response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class home extends Controller
{
    use response;
    public function teachersBysubject(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'subject_id'    => 'required|exists:subjects,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors()->first(), 403, 'E03');
        }
        //gey subject
        $subject = Subject::find($request->subject_id);

        //online
        $online_teachers = Teacher::active()
                                    ->where('online', 1)
                                    ->where('main_subject_id', $subject->main_subject_id)
                                    ->whereHas('Teacher_years', function($qeury) use($subject){
                                        $qeury->where('year_id', $subject->Term->year_id);
                                    })
                                    ->limit(5);
        //offline
        $offline_teachers = Teacher::active()
                                    ->where('online', 0)
                                    // ->whereHas('Available_classes', function($qeury) use($request){
                                    //     $qeury->where('to', '>', date('Y-m-d H:i:s'))
                                    //             ->where('subject_id', $request->get('subject_id'))
                                    //             ->whereDoesntHave('Student_classes')
                                    //             ->whereHas('Class_type', function($q){
                                    //                 $q->active();
                                    //             });
                                    // })
                                    ->where('main_subject_id', $subject->main_subject_id)
                                    ->whereHas('Teacher_years', function($qeury) use($subject){
                                        $qeury->where('year_id', $subject->Term->year_id);
                                    });
        // ->inRandomOrder()

        return response()->json([
            'successful'                => true,
            'message'                   => trans('auth.success'),
            'online_teachers_count'     => $online_teachers->count(),
            'offline_teachers_count'     => $offline_teachers->count(),
            'online_teachers'   => teacher_classesTypeResourc::collection($online_teachers->get()),
            'offline_teachers'  => teacher_classesTypeResourc::collection($offline_teachers->paginate(5))->response()->getData(true),
        ], 200);
    }

    public function online_teachers_bysubject(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'subject_id'    => 'required|exists:subjects,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors()->first(), 403, 'E03');
        }
        
        //get subject
        $subject = Subject::find($request->subject_id);

        //online
        $online_teachers = Teacher::active()
                                    ->where('online', 1)
                                    ->where('main_subject_id', $subject->main_subject_id)
                                    ->whereHas('Teacher_years', function($qeury) use($subject){
                                        $qeury->where('year_id', $subject->Term->year_id);
                                    })
                                    ->limit(5);

        return response()->json([
            'successful'            => true,
            'message'               => trans('auth.success'),
            'online_teachers_count' => $online_teachers->count(),
            'online_teachers'       => teacher_classesTypeResourc::collection($online_teachers->get()),
        ], 200);
    }

    public function materials(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'subject_id'    => 'required|exists:subjects,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors()->first(), 403, 'E03');
        }

        //get materials
        $materials = Subject::find($request->get('subject_id'))->Materials;

        return $this->success(
            trans('auth.success'),
            200,
            'materials',
            materialResource::collection($materials)
        );
    }

    public function classes_type_cost(Request $request){
        // validate registeration request
        $validator = Validator::make($request->all(), [
            'teacher_id'     => 'required|integer|exists:teachers,id',
            'subject_id'     => 'required|integer|exists:subjects,id',
        ]);

        if($validator->fails()){
            return $this::faild($validator->errors()->first(), 403);
        }

        $classes_type = Class_type::active()->get();

        return $this->success(
            trans('auth.success'),
            200,
            'classes_type',
            classTypeResource::collection($classes_type)
        );
    }

    public function countries(){
        $countries = Country::active()->get();

        return $this->success(
            trans('auth.success'),
            200,
            'countries',
            countryResource::collection($countries)
        );
    }

    public function curriculums(){
        $curriculums = Curriculum::active()->get();

        return $this->success(
            trans('auth.success'),
            200,
            'curriculums',
            curriculumResource::collection($curriculums)
        );
    }

    public function main_subjects(){
        $main_subjects = Main_subject::active()->get();

        return $this->success(
            trans('auth.success'),
            200,
            'subjects',
            main_subjectResource::collection($main_subjects)
        );
    }

    public function subjects(){
        $subject = Subject::active()->get();

        return $this->success(
            trans('auth.success'),
            200,
            'subjects',
            subjectsResource::collection($subject)
        );
    }

    public function answers(Request $request){
        //validation
        $validator = Validator::make($request->all(), [
            'question_id'    => 'required|exists:questions,id',
        ]);

        if($validator->fails()){
            return response::faild($validator->errors()->first(), 403, 'E03');
        }

        //get answers
        $answers = Answer::active()
                            ->where('question_id', $request->get('question_id'))
                            ->orderBy('id', 'desc')
                            ->paginate(5);

        return response()->json([
            'successful'        => true,
            'message'           => trans('auth.success'),
            'answers_count'     => Answer::where('question_id', $request->get('question_id'))->count(),
            'answers'           => answersResource::collection($answers)->response()->getData(true),
        ], 200);
    }

    public function questions(Request $request){
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

    public function Terms_and_Conditions(Request $request){
        ($request->header('lang') == 'ar')? $lang = 'ar': $lang = 'en';

        if($lang == 'en'){
            return $this->Terms_and_Conditions_en_response();
        }

        return $this->Terms_and_Conditions_ar_response();
    }

    public function Terms_and_Conditions_en_response(){
        return '<div style="margin-left: 15px; margin-right: 15px;">
        <div style="text-align: center;">
            <h1 >Terms and Conditions</h2>
            <p>Please read the terms and conditions of the use stipulated accurately before using this site.</p>
            <p>Through these conditions, we explain how to deal according to the binding rules for using our site</p>
        </div>
    
        <h2>A-privacy Policy</h1>
            <div>The privacy policy governs the way our platform collects, uses, maintains and discloses information collected from users (teachers and students) and the privacy policy applies to all products and services offered by the platform and the purpose of using this information is to improve the level of service The Services constitute acceptance of the Privacy Policy</div>
            <div style="margin: 25px;">
                <ui>
                    <li style="margin-top: 20px; margin-bottom: 10px; font-weight: bold;">Personally Identifiable Information: Personal Information</li>
                        <div style="margin-left: 25px;margin-right: 25px;">
                            <div>We may collect personally identifiable information from Users in a variety of ways, including, but not limited to, when Users visit and log on to our Platform, and in connection with other activities, services, features or resources we make available on the Platform. Users may be asked, as the case may be, for name, email address and credit card information. We do not collect personally identifiable information from Users unless they voluntarily provide such information to us. Users can always refuse to provide personally identifiable information, which would prevent them from participating in certain Platform-related activities.</div>
                        </div>
                    <li style="margin-top: 20px;margin-bottom: 10px;font-weight: bold;">Non-Personally Identifiable Information: Usage Data</li>
                        <div style="margin-left: 25px;margin-right: 40px;">
                            <div>Agreeing to the Terms and Conditions means agreeing to share certain personal and entered information; Including access to the quotas and our authority to amend the data.
                                We may collect non-personally identifiable information about Users whenever they interact with the Platform. Non-personally identifiable information may include browser name, type of computer and technical information about Users connection to the Platform, such as the operating system, Internet service providers and other similar informationHow we use the information collected</div>
                        </div>
                    <li style="margin-top: 20px;margin-bottom: 10px;font-weight: bold;">How we use the information collected</li>
                    <div style="margin-left: 25px;margin-right: 40px;">
                        <div>Our website may collect and use users personal information for the following purposes</div>
                    </div>
                    <div style="margin-left: 60px;margin-right: 40px;">
                        <div>
                            <div style="margin-bottom: 7px;">− Improving customer service: The information collected helps us respond to customer requests and support needs more effectively.</div>
                            <div style="margin-bottom: 7px;">− To personalize your user experience, we may use information in the aggregate to understand how our users as a group use the services and resources available on the Site.</div>
                            <div style="margin-bottom: 7px;">− Platform improvement: We may use the information collected to improve our products and services.</div>
                            <div style="margin-bottom: 7px;">− Third Party Information Sharing: We may use the information users provide about themselves when placing an order only to provide the service for that request. We do not share this information with third parties except to the extent necessary to provide the service.</div>
                            <div style="margin-bottom: 7px;">− conduct a promotion, contest, survey, or other feature of the Application: to send information to users that they agree to receive about topics we think are of interest to them. To direct periodic emails to them We may use the email address to send User information and updates relating to their order. It may also be used to respond to their inquiries, questions and/or other requests.</div>
                        </div>
                    </div>
                </ui>
            </div>
        <h2>B-Sharing user information</h1>
            <div style="margin-left: 25px;margin-right: 40px;margin-bottom: 15px;">
                <div>We do not sell, trade or rent personally identifiable information to other users. We may share generic aggregated demographic information that is not linked to any personally identifiable information relating to users, with our business partners, trusted affiliates and advertisers for the purposes described above. We may use third party service providers to help us operate the Site or administer activities on our behalf, such as sending out newsletters or conducting surveys. We may share user information with these parties for those limited purposes</div>
            </div>
            <div style="margin-left: 60px;margin-right: 40px;">
                <div>
                    <div style="margin-bottom: 7px;">- User Information Others Provide: We may receive information that other people provide to us, some of which may be about you. For example, when other users you know use our Service, they may provide us with your phone number from their mobile address book (just as you may provide information about them), or they may direct a message to you or direct messages to groups you belong to.</div>
                </div>
            </div>
    
    
    
        <h2>C-lessons</h1>
        <div style="margin-left: 60px;margin-right: 40px;">
            <div>
                <div style="margin-bottom: 7px;color: blue;">− We have the right to access data and video and audio calls to ensure the right of all parties.</div>
                <div style="margin-bottom: 7px;color: blue;">− Being present and adhering to the timings of the classes and avoiding any interruptions, conversations or side actions that lead to deviation from the context and purpose of the class.</div>
            </div>
        </div>
    
    
        <h2>D-Communication and Violations</h1>
            <div style="margin-left: 60px;margin-right: 40px;">
                <div>
                    <div style="margin-bottom: 7px;color: blue;">− Not to use invalid or offensive words, and if this is proven, the platform has the right to debit the violating person’s wallet and delete the account.</div>
                    <div style="margin-bottom: 7px;color: blue;">- It is forbidden to publish any offensive, racist or inciting to violence publications.</div>
                    <div style="margin-bottom: 7px;color: blue;">− It is not allowed to record classes and send them to students without prior permission from the platform.</div>
                    <div style="margin-bottom: 7px;color: blue;">− Avoid taking any personal data from both parties, whether by phone number or any social media</div>
                </div>
            </div>
    
        <h2>E-To compensate</h1>
            <div style="margin-left: 25px;margin-right: 40px;margin-bottom: 15px;">
                <div>User agrees not to claim any compensation and not hold us liable for any and all damages, losses and expenses of any kind (including reasonable legal fees and costs) related to, arising out of or in any way in connection with any of the following: (a) User access to our Service or its use, including information provided in connection with it; (b) the user breach of the Terms of Service; or (c) any misrepresentation made by the user. The user is obligated to cooperate fully with us in the defense or settlement of any claim.</div>
            </div>
    
        <h2>F-Waiver of Liability</h1>
        <div style="margin-left: 25px;margin-right: 40px;margin-bottom: 15px;">
            <div style="margin-bottom: 7px;color: blue;">The user is obligated not to use the service for any illegal or unauthorized purpose. It undertakes not to interfere with or disrupt any other user s experience. We are not responsible for any user interaction in the “communication” feature on the site, any violation of these terms may lead to financial penalties or account deletion.
                The platform is not responsible for any data damage, loss of account, direct or indirect damage; But we seek to secure you by all available and possible means, according to what has been agreed upon in these announced conditions.</div>
        </div>
    </div>
    ';
    }

    public function Terms_and_Conditions_ar_response(){
        return '<div style="margin-left: 15px; margin-right: 15px;direction: rtl">
        <div style="text-align: center;">
            <h1 >الشروط والأحكام</h2>
            <p>يُرجى قراءة شروط وأحكام الاستخدام المنصوص عليها  بدقة  قبل استخدام هذا الموقع .</p>
            <p>من خلال هذه الشروط  نوضح كيفية التعامل وفق القواعد الملزمة لاستخدام موقعنا</p>
        </div>
    
        <h2>أ-سياسة الخصوصية</h1>
            <div>تحكم سياسة الخصوصية الطريقة التي تجمع، تستخدم ، وتحافظ  وتكشف عبرها منصتنا عن المعلومات التي يتم جمعها من المستخدمين ( معلمون و طلاب) وتنطبق سياسة الخصوصية على جميع المنتجات والخدمات التي تقدمها المنصة  ويتمثل  الهدف من استخدام هذه المعلومات في  تحسين مستوى الخدمة، و يعد استخدام هذه الخدمات بمثابة موافقة على سياسة الخصوصية</div>
    
            <div style="margin: 25px;">
                <ui>
                    <li style="margin-top: 20px; margin-bottom: 10px; font-weight: bold;">معلومات التعريف الشخصية: المعلومات الشخصية</li>
                        <div style="margin-left: 25px;margin-right: 25px;">
                            <div>قد نقوم بجمع معلومات التعريف الشخصية من المستخدمين بطرق متنوعة، بما في ذلك ،على سبيل المثال لا الحصر، عندما يزور المستخدمون منصتنا ويسجلون دخولهم عليها، وفيما يتعلق بأنشطة ، أو خدمات ، أو مزايا أو موارد أخرى نوفرها على المنصة. قد يُطلب من المستخدمين، حسب الأحوال، الاسم ، وعنوان البريد الإلكتروني ،ومعلومات بطاقة الائتمان. ولا نجمع معلومات التعريف الشخصية من المستخدمين إلا  إذا قدموا هذه المعلومات لنا طوعاً. ويمكن للمستخدمين دائمًا رفض تقديم معلومات تُحدد الهوُية الشخصية ، علمًا بأن ذلك من شأنه منعهم من المشاركة في أنشطة معينة متعلقة بـالمنصة.</div>
                        </div>
    
                    <li style="margin-top: 20px;margin-bottom: 10px;font-weight: bold;">معلومات التعريف غير الشخصية: بيانات الاستخدام</li>
                        <div style="margin-left: 25px;margin-right: 40px;">
                            <div>الموافقة على الشروط والأحكام تعني الموافقة على مشاركة بعض المعلومات الشخصية والمدخلة؛ منها الإطلاع علي الحصص وصلاحيتنا في التعديل البيانات.
                                قد نقوم بجمع معلومات التعريف غير الشخصية عن المستخدمين كلما تفاعلوا مع المنصة، و قد تتضمن معلومات التعريف غير الشخصية اسم المتصفح، ونوع جهاز الحاسب والمعلومات التقنية حول وسيلة اتصال المستخدمين بـالمنصة، مثل نظام التشغيل و موفري خدمة الإنترنت ومعلومات أخرى مماثلة.
                                </div>
                        </div>
    
                    <li style="margin-top: 20px;margin-bottom: 10px;font-weight: bold;">كيف نستخدم المعلومات التي يتم جمعها:</li>
                    <div style="margin-left: 25px;margin-right: 40px;">
                        <div>يمكن لـموقعنا أن يجمع وأن يستخدم المعلومات الشخصية للمستخدمين للأغراض التالية</div>
                    </div>
                    <div style="margin-left: 60px;margin-right: 40px;">
                        <div>
                            <div style="margin-bottom: 7px;">− تحسين خدمة العملاء: تساعدنا المعلومات التي يتم جمعها على الاستجابة لطلبات العملاء واحتياجات الدعم بشكل أكثر فعالية.</div>
                            <div style="margin-bottom: 7px;">− إضفاء طابعٍ شخصيٍّ على تجربة المستخدم، فقد نستخدم المعلومات في المُجمل لفهم كيفية استخدام مستخدمينا كمجموعةٍ للخدمات والموارد المتوفرة على الموقع.</div>
                            <div style="margin-bottom: 7px;">− تحسين المنصة: قد نستخدم المعلومات التي يتم جمعها لتحسين منتجاتنا وخدماتنا.</div>
                            <div style="margin-bottom: 7px;">− مشاركة الغير في المعلومات : قد نستخدم المعلومات التي يقدمها المستخدمون عن أنفسهم عند تقديم طلب فقط لتوفير الخدمة لهذا الطلب. نحن لا نشارك هذه المعلومات مع الغير  إلا بالقدر اللازم لتقديم الخدمة.</div>
                            <div style="margin-bottom: 7px;">− لقيام بعرضٍ ترويجيٍّ، أو مسابقةٍ، أو استبيان، أو مزية أخرى للـ”التطبيق”: لإرسال معلومات لمن يوافق من الستخدمين على تلقيها عن المواضيعٍ التي نعتقد أنها تثير اهتمامهم. لتوجيه رسائل البريد الإلكتروني الدوري لهم يجوز أن نستخدم عنوان البريد الإلكتروني لإرسال معلومات المستخدم والتحديثات المتعلقة بطلبه. كما يمكن استخدامه للرد على استفساراتهم وأسئلتهم و/أو طلباتهم الأخرى.</div>
                        </div>
                    </div>
                </ui>
            </div>
        <h2>ب- مشاركة المعلومات الخاصة بالمستخدم</h1>
            <div style="margin-left: 25px;margin-right: 40px;margin-bottom: 15px;">
                <div>نحن لا نبيع، أو نتاجر أو نؤجر معلومات التعريف الشخصية للمستخدمين الآخرين. وقد نشارك معلومات ديموجرافية مُجمعة عامةً وغير مرتبطة بأية معلومات تعريف شخصية تتعلق بالمستخدمين ، مع شركائنا التجاريين والشركات التابعة الموثوق فيها والشركات المُعلنة للأغراض الموضحة أعلاه. وقد نستخدم مزودي خدمات من الغير لمساعدتنا في تشغيل الموقع  أو إدارة الأنشطة نيابة عنا، مثل إرسال رسائل إخبارية أوعمل استطلاعات رأي . ويجوز لنا مشاركة معلومات المستخدم مع هذه الجهات لتلك الأغراض المحدودة .</div>
            </div>
            <div style="margin-left: 60px;margin-right: 40px;">
                <div>
                    <div style="margin-bottom: 7px;">- المعلومات التي يقدمها الآخرون عن المستخدم: قد نتلقى معلومات يقدمها لنا أشخاص آخرون، بعضها قد يكون عنك. على سبيل المثال، عندما يستخدم مستخدمون آخرون أنت على معرفة بهم خدمتنا، قد يزودونا برقم هاتفك من دفتر عناوين الهاتف المحمول الخاص بهم (تماما كما قد تقدم أنت معلومات عنهم )، أو قد يوجهون  إليك رسالة أو يوجهون رسائل إلى المجموعات التي تنتمي إليها.</div>
                </div>
            </div>
    
    
    
        <h2>ت‌- الدروس</h1>
        <div style="margin-left: 60px;margin-right: 40px;">
            <div>
                <div style="margin-bottom: 7px;color: blue;">− لنا الحق في الاطلاع على البيانات والمكالمات المرئية والصوتية لضمان حق جميع الأطراف.</div>
                <div style="margin-bottom: 7px;color: blue;">−	التواجد والالتزام بمواعيد الحصص وتجنب أي مقاطعات أو أحاديث أو أفعال جانبية تؤدي للخروج عن سياق الحصة والهدف منها.</div>
            </div>
        </div>
    
    
        <h2>ث‌-	التواصل والمخالفات</h1>
            <div style="margin-left: 60px;margin-right: 40px;">
                <div>
                    <div style="margin-bottom: 7px;color: blue;">− عدم استخدام الألفاظ الغير صالحة أو المخلة وفي حال ثبوت ذلك يحق للمنصة الخصم من محفظة الشخص المخالف وحذف الحساب.</div>
                    <div style="margin-bottom: 7px;color: blue;">- ممنوع نشر أي منشورات مسيئة أو عنصرية أو محرضة على العنف.</div>
                    <div style="margin-bottom: 7px;color: blue;">− غير مسموح بتسجيل حصص وإرسالها للطلاب بدون إذن مسبق من المنصة.</div>
                    <div style="margin-bottom: 7px;color: blue;">− تجنب تناول أي بيانات شخصية من الطرفين سواء رقم الهاتف أو أي وسيلة تواصل إجتماعي</div>
                </div>
            </div>
    
        <h2>ج‌-	التعويض</h1>
            <div style="margin-left: 25px;margin-right: 40px;margin-bottom: 15px;">
                <div>يوافق المستخدم على عدم المطالبة بأي تعويض وعدم تحميلنا مسؤولية أي وجميع الأضرار والخسائر والنفقات من أي نوع كانت (بما في ذلك الرسوم والتكاليف القانونية المعقولة) المتعلقة أو الناشئة عن أو بأي شكل من الأشكال فيما يتعلق بأي مما يلي: (أ) وصول المستخدم إلى خدمتنا أو استخدامها، بما في ذلك المعلومات المقدمة والمتعلقة بها ؛ (ب) مخالفة المستخدم  لشروط الخدمة؛ أو (ج) أي تحريف قام به المستخدم . ويلتزم المستخدم بالتعاون بشكل كامل معنا في الدفاع أو في تسوية أي مطالبة.</div>
            </div>
    
        <h2>ح‌-	تنازل عن المسؤولية</h1>
        <div style="margin-left: 25px;margin-right: 40px;margin-bottom: 15px;">
            <div style="margin-bottom: 7px;color: blue;">على المستخدم  الالتزام بعدم استخدام الخدمة لأي غرض غير قانوني أو غير مصرح به. ويلتزم بألا  يتدخل أو يعطل تجربة أي مستخدم آخر. ولا نُسأل عن أي تفاعل للمستخدم  في مزية "التواصل " على الموقع أي انتهاك لهذه الشروط قد يؤدي إلى توقيع عقوبات مادية أو حذف الحساب. 
                المنصة غير مسؤولة عن أي ضرر في البيانات أو إضاعة حساب أو ضرر مباشر أو غير مباشر؛ لكن نسعى لتأمينك بكل السبل المتاحة والممكنة وفقاً لما تمت الموافقة عليه في هذه الشروط المعلنة.
                </div>
        </div>
    </div>
    ';
    }

}
