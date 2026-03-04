<?php
namespace App\Http\Controllers\Api;

use App\City;
use App\Country;
use App\Models\Course;
use App\DegreeLevel;
use App\DegreeType;
use App\Helpers\DataArrayHelper;
use App\Models\Hobby;
use App\Http\Controllers\Controller;
use App\JobExperience;
use App\JobSkill;
use App\Language;
use App\LanguageLevel;
use App\ProfileCv;
use App\ProfileEducation;
use App\ProfileExperience;
use App\Models\ProfileExtraService;
use App\Models\ProfileInternship;
use App\ProfileLanguage;
use App\Models\ProfileReference;
use App\ProfileSkill;
use App\ProfileSummary;
use App\ResultType;
use App\Models\SocialLink;
use App\State;
use App\User;
use Carbon\Carbon;
use Dompdf\Exception;
use GuzzleHttp\Promise\RejectionException;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Stripe\Person;
use Symfony\Component\VarDumper\Cloner\Data;
use Vinkla\Hashids\Facades\Hashids;
use Auth ;
class ApiController extends Controller
{
    use \App\Traits\CommonUserFunctions;

    public function userInfo($id){
        $response = DataArrayHelper::getBasicData($id);
        if(!$response["success"])
        {
            return response()->json($response,500);
        }
        else
        {
            return response()->json($response,200);

        }
    }
    public function getDegreetype(Request $request,$degreetype){
        return response()->json(DataArrayHelper::getDegreetype($degreetype));
    }

    public function getCountries()
    {
       return response()->json(DataArrayHelper::getCountry());
    }
    public function getState(Request $request,$countryid)
    {
        return response()->json(DataArrayHelper::getState($countryid));
    }
    public function getCity(Request $request,$stateId)
    {
        return response()->json(DataArrayHelper::getCity($stateId));
    }
    public function getNationality() {
        return response()->json(DataArrayHelper::getNationality());
    }

//Delete Api's For Resume Section
    public function destroy_user_education(Request $request)
    {
     //   $education_id= Hashids::decode($request->educationID)[0];
        $education_id= $request->id;
        // $user_id=Auth::user()->id;
        $user_id = $request->user_id;
        $data=ProfileEducation::where('id',$education_id)->where('user_id',$user_id)->delete();
        if(($data>0)){
            return response()->json(['status'=>true,'message'=>'Record has been deleted'],200);
        }
        else{
            return response()->json(['status' => false,'error' => 'fail to delete'],500);
        }
    }

    public function destroy_user_experience(Request $request)
    {
        $experience_id= Hashids::decode($request->experienceID)[0];
        $user_id=Auth::user()->id;
        $data=ProfileExperience::where('id',$experience_id)->where('user_id',$user_id)->delete();
        if(($data>0)){
            return response()->json(['status'=>true,'message'=>'Record has been deleted'],200);
        }
        else{
            return response()->json(['status' => false,'error' => 'fail to delete'],500);
        }
    }

    public function destroy_user_skill(Request $request)
    {
        $skill_id = Hashids::decode($request->skillsID)[0];
        $user_id=Auth::user()->id;
        $data=ProfileSkill::where('id',$skill_id)->where('user_id',$user_id)->delete();
        if(($data>0)){
            return response()->json(['status'=>true,'message'=>'Record has been deleted'],200);
        }
        else{
            return response()->json(['status' => false,'error' => 'fail to delete'],500);
        }
    }

    public function destroy_user_experiance(Request $request){

        //  $experience_id = Hashids::decode($request->Experience_id)[0];
         $experience_id = $request->id ;
        // $user_id = Auth::user()->id;
        $user_id = $request->user_id ;
        $data = ProfileExperience::where('id', $experience_id)->where('user_id',$user_id)->delete();

        if(($data>0)){
            return response()->json(['status'=>true,'message'=>'Record has been deleted'],200);
        }
        else{
            return response()->json(['status' => false,'error' => 'fail to delete'],500);
        }
    }

    public function destroy_user_skills(Request $request){

          //  $experience_id = Hashids::decode($request->id)[0];
          $skill_id = $request->id ;
          // $user_id = Auth::user()->id;
          $user_id = $request->user_id ;
          $data = ProfileSkill::where('id', $skill_id)->where('user_id',$user_id)->delete();
  
          if(($data>0)){
              return response()->json(['status'=>true,'message'=>'Record has been deleted'],200);
          }
          else{
              return response()->json(['status' => false,'error' => 'fail to delete'],500);
          }

    }

    public function destroy_user_language(Request $request)
    {
       // $lang_id = Hashids::decode($request->langID)[0];
        $lang_id = $request->id ;
        // $user_id=Auth::user()->id;
        $user_id = $request->user_id ;

        $data = ProfileLanguage::where('id', $lang_id)->where('user_id',$user_id)->delete();

        if(($data > 0)){
            return response()->json(['status'=>true,'message'=>'Record has been deleted'],200);
        }
        else{
            return response()->json(['status' => false,'error' => 'fail to delete'],500);
        }
    }

    public function destroy_user_hobby(Request $request)
    {
       // $hobby_id= Hashids::decode($request->hobbyID)[0];
        $hobby_id = $request->id ; 
        // $user_id=Auth::user()->id;
        $user_id= $request->user_id ;
        $data=Hobby::where('id', $hobby_id)->where('user_id', $user_id)->delete();
        if(($data>0)){
            return response()->json(['status'=>true,'message'=>'Record has been deleted'],200);
        }
        else{
            return response()->json(['status' => false,'error' => 'fail to delete'],500);
        }
    }

    public function destroy_user_course(Request $request)
    {
        // $course_id= Hashids::decode($request->course_id)[0];
        $course_id = $request->id ;
        // $user_id = Auth::user()->id;
        $user_id = $request->user_id ;

        $data = Course::where('id', $course_id)->where('user_id', $user_id)->delete();
        if(($data>0)){
            return response()->json(['status'=>true,'message'=>'Record has been deleted'],200);
        }
        else{
            return response()->json(['status' => false,'error' => 'fail to delete'],500);
        }
    }

    public function destroy_user_internship(Request $request)
    {
            $intern_id= Hashids::decode($request->intern_id)[0];
            $user_id=Auth::user()->id;
            $data=ProfileInternship::where('id',$intern_id)->where('user_id',$user_id)->delete();
            if(($data>0)){
                return response()->json(['status'=>true,'message'=>'Record has been deleted'],200);
            }
            else{
                return response()->json(['status' => false,'error' => 'fail to delete'],500);
            }
    }
    public function destroy_user_reference(Request $request)
    {
            $ref_id= Hashids::decode($request->ref_id)[0];
            $user_id=Auth::user()->id;
            $data=ProfileReference::where('id',$ref_id)->where('user_id',$user_id)->delete();
            if(($data>0)){
                return response()->json(['status'=>true,'message'=>'Record has been deleted'],200);
            }
            else{
                return response()->json(['status' => false,'error' => 'fail to delete'],500);
            }
    }
    public function destroy_user_extra_activities(Request $request)
    {
        $extra_id= Hashids::decode($request->extraID)[0];
            $user_id=Auth::user()->id;

            $data=ProfileExtraService::where('id',$extra_id)->where('user_id',$user_id)->delete();
            if(($data>0)){
                return response()->json(['status'=>true,'message'=>'Record has been deleted'],200);
            }
            else{
                return response()->json(['status' => false,'error' => 'fail to delete'],500);
            }
    }
    public function destroy_user_web_social_link(Request $request)
    {
            $social_id= Hashids::decode($request->social_id)[0];
            $user_id=Auth::user()->id;
            $data=SocialLink::where('id',$social_id)->where('user_id',$user_id)->delete();
            if(($data>0)){
                return response()->json(['status'=>true,'message'=>'Record has been deleted'],200);
            }
            else{
                return response()->json(['status' => false,'error' => 'fail to delete'],500);
            }
    }

    public function addBlankHobbies(Request $request)
    {
        $hobbies = new Hobby();
        $hobbies->user_id = Auth::user()->id;
        $hobbies->hobby = $request->input('name');
        $hobbies->resume_id = $request->input('resume_id');
        $hobbies->save();
        $hobbies = Hobby::select('hobby','id')->where('user_id', Auth::user()->id)->get();
        if (count($hobbies) > 0) {
            $msg = $hobbies;
        }
        return response()->json(['status' => true, 'data' => $msg], 200);
    }
    public function addBlankSkill(Request $request)
    {
        $skills = new ProfileSkill();
        $skills->user_id = Auth::user()->id;
        $skills->job_skill_id = null;//$request->input('language');
        $skills->job_experience_id = 1;
        $skills->skill_percentage = $request->input('skill_percentage');
        $skills->resume_id = $request->input('resume_id');
        $skills->save();
        $skills = ProfileSkill::select( 'id' , 'skill_percentage' , 'job_skill_id' , 'job_experience_id')->where('user_id' , Auth::user()->id)->get();
        if (count($skills) > 0) {
            $msg = $skills;
        }
        return response()->json(['status' => true, 'data' => $msg], 200);
    }
    public function addBlankWebSocialLink(Request $request)
    {
        $msg="";
        if(SocialLink::select('id','website' , 'url')->where('user_id', Auth::user()->id)->count() < 21) {
            $web_social = new SocialLink();
            $web_social->user_id = Auth::user()->id;
            $web_social->website = $request->input('label');
            $web_social->resume_id = $request->input('resume_id');
            $web_social->url = $request->input('link');
            $web_social->save();
        }
        $social_links = SocialLink::select('id','website' , 'url')->where('user_id', Auth::user()->id)->get();
        if (count($social_links) > 0) {
            $msg = $social_links;
        }
        return response()->json(['status' => true, 'data' => $msg], 200);
    }
    public function addBlankEmployment(Request $request)
    {
        $employe_history = new ProfileExperience();
        $employe_history->user_id = Auth::user()->id;
        $employe_history->title = '';
        $employe_history->company = '';
        $employe_history->resume_id = $request->input('resume_id');
        $employe_history->save();
        $msg='';
        $user_experience = ProfileExperience::select( 'id' , 'title', 'company', 'description' , 'country_id' , 'state_id' , 'city_id' , 'date_start' , 'date_end' , 'is_currently_working')->where('user_id', Auth::user()->id)->get();
        if (count($user_experience) > 0) {

            $msg = $user_experience;
        }
        return response()->json(['status' => true, 'data' => $msg], 200);
    }
    public function addBlankEducation(Request $request)
    {
        ProfileEducation::create(['user_id' => Auth::user()->id , 'resume_id' => $request->input('resume_id')]);
      /*  $education = new ProfileEducation();
        $education->user_id = Auth::user()->id;
        $education->degree_title = $request->input('degree_name');
        $education->institution = $request->input('institute');
        $education->degree_level_id = $request->input('degree_level');
        $education->degree_result = $request->input('result');
        $education->result_type_id = $request->input('result_type');
        $education->date_completion = $request->input('complete_type');
        $education->country_id = $request->input('country');
        $education->state_id = $request->input('state');
        $education->city_id = $request->input('city');*/
//        $education->date_start = Carbon::parse($request->input('date1'));
//        $education->date_end = Carbon::parse($request->input('date2'));
//        $education->save();

        $msg = '';
        $user_education = ProfileEducation::select('id','degree_title', 'major_subjects_value' ,  'degree_type_id','degree_level_id', 'date_completion','country_id', 'state_id','city_id','institution', 'date_completion','degree_result','result_type_id','date_started' , 'is_studying')->where('user_id', Auth::user()->id)->get();
        if (count($user_education) > 0) {
            $msg = $user_education;
        }
        return response()->json(['status' => true, 'data' => $msg], 200);
    }
    public function addBlankCourses(Request $request)
    {
        $course = new Course();
        $course->user_id = Auth::user()->id;
        $course->course_name = $request->input('course');
        $course->course_institute = $request->input('institution');
        $course->startdate = Carbon::parse($request->input('date1'));
        $course->enddate = Carbon::parse($request->input('date2'));
        $course->resume_id = $request->input('resume_id');
        $course->save();
        $msg='';
        $course = Course::select('id','course_name', 'course_duration', 'course_institute','startdate','enddate')->where('user_id', Auth::user()->id)->get();
        if (count($course) > 0) {

            $msg = $course;
        }
        return response()->json(['status' => true, 'data' => $msg], 200);
    }
    public function addBlankLanguage(Request $request)
    {
        $language = new ProfileLanguage();
        $language->user_id = Auth::user()->id;
        $language->language_id = $request->input('language');
        $language->language_level_id = $request->input('level');
        $language->resume_id = $request->input('resume_id');
        $language->save();
        $msg ='';
        $languages = ProfileLanguage::select( 'id' , 'language_id','language_level_id')->where('user_id', $language->user_id)->get();
        if (count($languages) > 0) {
            $msg = $languages;
        }
        return response()->json(['status' => true, 'data' => $msg], 200);
    }
    public function addBlankExtraActivities(Request $request)
    {
        ProfileExtraService::create(['user_id' => Auth::user()->id , 'resume_id' => $request->input('resume_id')]);
        $msg ='';
        $extra_activities =  ProfileExtraService::select( 'id' , 'title', 'company' , 'country_id' , 'state_id' , 'city_id' , 'date_start' , 'date_end' , 'is_currently_working')->where('user_id', Auth::user()->id)->get();
        if (count($extra_activities) > 0) {
            $msg = $extra_activities;
        }
        return response()->json(['status' => true, 'data' => $msg], 200);
    }
    public function addBlankReferences(Request $request)
    {
        $reference = new ProfileReference();
        $reference->user_id = Auth::user()->id;
        $reference->ref_name = $request->input('full_name');
        $reference->ref_company = $request->input('company');
        $reference->ref_phone = $request->input('phone');
        $reference->ref_email = $request->input('email');
        $reference->resume_id = $request->input('resume_id');
        $reference->save();
        $msg='';
        $user_reference = ProfileReference::select('id','ref_name', 'ref_company', 'ref_phone', 'ref_email')->where('user_id', Auth::user()->id)->get();
        if (count($user_reference) > 0) {
            $msg = $user_reference;
        }
        return response()->json(['status' => true, 'data' => $msg], 200);
    }
    public function addBlankInternship(Request $request)
    {
        $internship = new ProfileInternship();
        $internship->user_id = Auth::user()->id;
        $internship->job_title = $request->input('job_title');
        $internship->employer_name = $request->input('employer');
        $internship->country_id = $request->input('country');
        $internship->state_id = $request->input('state');
        $internship->city_id = $request->input('city');
        $internship->resume_id = $request->input('resume_id');
        $internship->save();
        $msg='';
        $user_internships= ProfileInternship::select('id','job_title', 'employer_name', 'start_date','end_date', 'is_working', 'country_id','state_id', 'city_id', 'desc')->where('user_id',  Auth::user()->id)->get();
        if (count($user_internships) > 0) {
            $msg = $user_internships;
        }
        return response()->json(['status' => true, 'data' => $msg], 200);
    }

    // normal Store Functions
    public function personal_info_store(Request $request)
    {

        try{

            $validator = \Validator::make($request->all(), [
                // 'job_title'=>'required',
                'first_name'=>'required',
                'last_name'=>'required',
                'email'=>'required',
                'phone'=>'required',
                'id_card_no'=>'required',
                'nationality'=>'required',
                'dob'=>'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            else{
               // $personal_info = User::find(Auth::user()->id);
               $personal_info = User::find($request->user_id);
//                return [$request->all() , $personal_info , Carbon::parse($request->input('dob'))];
                // $personal_info->country_id = $request->input('country_id');
                // $personal_info->city_id = $request->input('city_id');
                // $personal_info->state_id = $request->input('state_id');
                // $personal_info->title = $request->input('job_title');
                $personal_info->first_name = $request->input('first_name');
                $personal_info->last_name = $request->input('last_name');
                $personal_info->email = $request->input('email');
                $personal_info->mobile_num = $request->input('phone');
                $personal_info->national_id_card_number = $request->input('id_card_no');
                $personal_info->nationality_id = $request->input('nationality');
                $personal_info->date_of_birth = ($request->input('dob'));
                $personal_info->update();

            return response()->json(['status' => true,'message' => 'Record has been added'],200);
        }
        }
            catch (\Exception $e){
            return response()->json(['status' => false,'error' => $e->getMessage()],500);
        }

    }
    public function web_social_link_store(Request $request){
        try {
            $validator = \Validator::make($request->all(), [
                'label' => 'required',
                'link' => 'required',

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {


              //  $web_social= Hashids::decode($request->input('social_id'))[0];

                $web_social = SocialLink::updateOrCreate(
                //  ['id' => $web_social],
                    ['id' => $request->id],
                    [
                     //'user_id' => Auth::user()->id,
                     'user_id' => $request->user_id,
                     'website' => $request->input('label'),
                     'resume_id' => $request->input('resume_id'),
                     'url' => $request->input('link')
                    ]
                );
                $web_social->save();

                return response()->json(['status' => true, 'message' => 'Record has been added'], 200);
            }
        }
        catch (\Exception $e){
            return response()->json(['status' => false,'error' => $e->getMessage()],500);
        }
    }
    public function employment_history_store(Request $request){
        try {

            $validator = \Validator::make($request->all(), [
                'job_title' => 'required',
                'employer' => 'required',
                'country' => 'required',
                'state' => 'required',
                'city' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            else {
               // $employe_history_id = Hashids::encode($request->input('experienceID'));
                if($request->input('date2') == "null"){ $end_date = null; } else { $end_date = $request->input('date2'); }
                if($request->is_currently_working == "true") {
                    $is_currently_working = 1; $end_date = null;
                    $start_date = $request->input('date1');
                }
                elseif($request->is_currently_working == "false"){
                    $is_currently_working = 0;
                    $date = explode(',' , $request->input('date2'));
                    $start_date = $date[0];
                    $end_date = $date[1];
                }
                $employe_history = ProfileExperience::updateOrCreate(
                    //['id' => $employe_history_id],
                    ['id' => $request->id],
                    [
                        // 'user_id' => Auth::user()->id,
                        'id' => $request->id,
                        'user_id' => $request->user_id,
                        'title' => $request->input('job_title'),
                        'company' => $request->input('employer'),
                        'country_id' => $request->input('country'),
                        'state_id' => $request->input('state'),
                        'city_id' => $request->input('city'),
                        'date_start' => $start_date,
                        'date_end' => $end_date,
                        'description' => $request->input('desc'),
                        'is_currently_working' => $is_currently_working,
                        'resume_id' => $request->input('resume_id'),
                    ]
                );
                $employe_history->save();
                // Update search index
                $user = User::find($request->user_id);
                if ($user) {
                    $this->updateUserFullTextSearch($user);
                }
                return response()->json(['status' => true, 'message' => 'Record has been added'], 200);
            }
        }
        catch (\Exception $e){
            return response()->json(['status' => false,'error' => $e->getMessage()],500);
        }
    }
    public function education_store(Request $request){
        try {

            $validator = \Validator::make($request->all(), [
                'degree_name' => 'required',
                'institute' => 'required',
                'degree_level' => 'required',
                'result' => 'required',
                'result_type' => 'required',
                'complete_type' => 'required',
                'country' => 'required',
                'state' => 'required',
                'city' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
               // $education_id = Hashids::decode($request->input('educationID'))[0];
                if($request->input('date2') == "null"){ $end_date = null; } else { $end_date = $request->input('date2'); }
                if($request->is_studying == "true") {
                    $is_studying = 1; $end_date = null;
                    $start_date = $request->input('date1');
                }
                elseif($request->is_studying == "false"){
                    $is_studying = 0;
                    $date = explode(',' , $request->input('date2'));
                    $start_date = $date[0];
                    $end_date = $date[1];
                }
//                return [$request->all(), $start_date , $end_date , $is_studying];
                $education = ProfileEducation::updateOrCreate(
                  //  ['id' => $education_id],
                    ['id' => $request->id],
                    [
                      //  'user_id' => Auth::user()->id,
                        'user_id' => $request->user_id,
                        'degree_title' => $request->input('degree_name'),
                        'institution' => $request->input('institute'),
                        'degree_level_id' => $request->input('degree_level'),
                        'degree_result' => $request->input('result'),
                        'degree_type_id' => $request->input('complete_type'),
                        'result_type_id' => $request->input('result_type'),
                        'country_id' => $request->input('country'),
                        'state_id' => $request->input('state'),
                        'city_id' => $request->input('city'),
                        'is_studying' => $is_studying,
                        'display_result' => $request->input('display_result') == "true" ? 1 : 0,
                        'date_started' => $start_date,
                        'date_completion' => $end_date,
                        'major_subjects_value' => $request->input('major_subjects_value'),
                        'resume_id' => $request->input('resume_id'),
                    ]
                );
                $education->save();
                // Update search index
                $user = User::find($request->user_id);
                if ($user) {
                    $this->updateUserFullTextSearch($user);
                }
                return response()->json(['status' => true, 'message' => 'Record has been added'], 200);
            }
        }

        catch (\Exception $e){
            return response()->json(['status' => false,'error' => $e->getMessage()],500);
        }
    }
    public function skills_store(Request $request){
       // $skills_id = Hashids::decode($request->input('skillsID'))[0];
        $skills = ProfileSkill::updateOrCreate(
           // ['id' => $skills_id],
            ['id' => $request->id],
            [
               // 'user_id' => Auth::user()->id,
                'user_id' => $request->user_id,
                'job_skill_id' => $request->input('job_skill_id'),
                'job_experience_id' => $request->input('job_experience_id'),
                // 'skill_percentage' => $request->input('skill_percentage'),
                // 'resume_id' => $request->input('resume_id'),
            ]
        );
        $skills->save();
        // Update search index
        $user = User::find($request->user_id);
        if ($user) {
            $this->updateUserFullTextSearch($user);
        }
        return response()->json(['status' => true, 'message' => 'Record has been added'], 200);
    }
    public function updateCvTemplate(Request $request) {
        // $profile_cv=ProfileCv::find($request->resume_id);
        $profile_cv = ProfileCv::find($request->id);
        $profile_cv->selected_template = $request->template_id;
        $profile_cv->selected_color = $request->template_color;
        $profile_cv->save();
        return response()->json(['status' => true, 'message' => 'Record has been updated'], 200);
    }

    public function updateUserSummary(Request $request){
//return [$request->all() , (int)$request->resume_id];
        $profile_summary = ProfileSummary::updateOrCreate(
            // ['resume_id' => (int)$request->resume_id , 'user_id' => Auth::user()->id ],
            ['resume_id' => (int)$request->resume_id , 'user_id' => $request->user_id ],
            [
                // 'user_id' => Auth::user()->id,
                'user_id' => $request->user_id,
                'resume_id' => (int)$request->resume_id,
                'summary' => $request->summary,
            ]
        );
//      $profile_summary->save();
        // Update search index
        $user = User::find($request->user_id);
        if ($user) {
            $this->updateUserFullTextSearch($user);
        }
        return response()->json(['status' => true, 'message' => 'Summary updated'], 200);
    }
    public function updateProfileImage(Request $request) {
        $fileName = '';
        // $user = User::find(Auth::user()->id);
        $user = User::find($request->user_id);
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            // $fileName = \ImgUploader::UploadImage('user_images', $image, Auth::user()->id, 300, 300, false);
            $fileName = \ImgUploader::UploadImage('user_images', $image, $request->user_id, 300, 300, false);
            $user->image = $fileName;
        }
        $user->save();
        return $fileName;
    }
    public function updateUserCVTitle(Request $request){
        try {
            // $profile_cv=ProfileCv::find($request->resume_id);
            $profile_cv = ProfileCv::find($request->id);
            $profile_cv->title = $request->title;
            $profile_cv->save();
            return response()->json(['status' => true, 'message' => 'Title updated'], 200);
        } catch(\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function language_store(Request $request){
        //$language_id= Hashids::decode($request->input('langID'))[0];
        $language = ProfileLanguage::updateOrCreate(
                    // ['id' => $language_id],
                    ['id' => $request->id],
                    [
                        // 'user_id' => Auth::user()->id,
                        'user_id' => $request->user_id,
                        'language_id' => $request->input('language'),
                        'language_level_id' => $request->input('level'),
                       // 'resume_id' => $request->input('resume_id'),
                    ]
                );
                $language->save();
                // Update search index
                $user = User::find($request->user_id);
                if ($user) {
                    $this->updateUserFullTextSearch($user);
                }
                return response()->json(['status' => true, 'message' => 'Record has been added'], 200);
    }
    public function courses_store(Request $request){
        try {
            $validator = \Validator::make($request->all(), [
                'course' => 'required',
                'institution' => 'required',
                'date1' => 'required',
                'date2' => 'required',
            ]);
           
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
                //$cource_id= Hashids::decode($request->input('course_id'))[0];
                $course = Course::updateOrCreate(
                    // ['id' => $cource_id],
                    ['id' => $request->id],
                    [
                    //  'user_id' => Auth::user()->id,
                     'user_id' => $request->user_id,
                     'course_name' => $request->input('course'),
                     'course_institute' => $request->input('institution'),
                     'startdate' => $request->input('date1'),
                     'resume_id' => $request->input('resume_id'),
                     'enddate' => $request->input('date2')]
                );
                $course->save();
                return response()->json(['status' => true, 'message' => 'Record has been added'], 200);
            }
        }
        catch (\Exception $e){
            return response()->json(['status' => false,'error' => $e->getMessage()],500);
        }
    }
    public function extra_activities_store(Request $request){
        try {

            $validator = \Validator::make($request->all(), [
                'title' => 'required',
                'employer' => 'required',
                'country' => 'required',
                'city' => 'required',
                'state' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
               // $extra_activities_id= Hashids::decode($request->input('extraID'))[0];
                if($request->input('date2') == "null"){ 
                    $end_date = null; 
                    } else { 
                        $end_date = $request->input('date2');
                     }
                if($request->is_currently_working == "true") {
                    $is_currently_working = 1; 
                    $end_date = null;
                    $start_date = $request->input('date1');
                }
                else if($request->is_currently_working == "false"){
                    $is_currently_working = 0;
                    $date = explode(',' , $request->input('date2'));
                     $start_date = $date[0];
                    // $end_date = $date[1];
                    $end_date = null;
                }
                $extra_activities = ProfileExtraService::updateOrCreate(
                    // ['id' => $extra_activities_id],
                    ['id' => $request->id],
                    [
                        // 'user_id' => Auth::user()->id,
                        'user_id' => $request->user_id,
                        'title' => $request->input('title'),
                        'company' => $request->input('employer'),
//                      'course_name' => $request->input('course'),
                        'country_id' => $request->input('country'),
                        'is_currently_working' => $is_currently_working,
                        'date_start' => $start_date,
                        'city_id' => $request->input('city'),
                        'state_id' => $request->input('state'),
                        'date_end' => $end_date,
                        'resume_id' => $request->input('resume_id'),
                    ]
                );
                return response()->json(['status' => true, 'message' => 'Record has been added'], 200);
            }
        }
        catch (\Exception $e){
            return response()->json(['status' => false,'error' => $e->getMessage()],500);
        }
    }
    public function hobbies_store(Request $request){

        try {

            $validator = \Validator::make($request->all(), [
                'name' => 'required',

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
                $hobbies = Hobby::updateOrCreate(
                    [
                        'id' => Hashids::decode($request->input('hobbyID'))
                    ],
                    [
                        // 'user_id' => Auth::user()->id,
                        'user_id' => $request->user_id,
                        'hobby' => $request->input('name'),
                        'resume_id' => $request->input('resume_id'),
                    ]
                );
                $hobbies->save();
                return response()->json(['status' => true, 'message' => 'Hobby has been added'], 200);
            }
        }
        catch (\Exception $e){
            return response()->json(['status' => false,'error' => $e->getMessage()],500);
        }
    }
    public function reference_store(Request $request){
        try {

            $validator = \Validator::make($request->all(), [
                'full_name' => 'required',
                'company' => 'required',
                'phone' => 'required',
                'email' => 'required',

            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
              //  $id= Hashids::decode($request->input('ref_id'))[0];

                $reference = ProfileReference::updateOrCreate(
                    // ['id' =>$id ],
                    ['id' =>$request->id ],
                    [
                    //  'user_id' => Auth::user()->id,
                     'user_id' => $request->user_id,
                     'ref_name' => $request->input('full_name'),
                     'ref_company' => $request->input('company'),
                     'ref_phone' => $request->input('phone'),
                     'ref_email' => $request->input('email'),
                    //'resume_id' => $request->input('resume_id'),
                     ]
                );
                $reference->save();


                return response()->json(['status' => true, 'message' => 'Record has been added'], 200);
            }
        }
        catch (\Exception $e){
            return response()->json(['status' => false,'error' => $e->getMessage()],500);
        }
    }
    public function internhsip_store(Request $request)
    {
        try {

            $validator = \Validator::make($request->all(), [
                'job_title' => 'required',
                'employer' => 'required',
                'country' => 'required',
                'state' => 'required',
                'city' => 'required',
            ]);


            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            } else {
                //$intern_id= Hashids::decode($request->input('intern_id'))[0];
                if($request->input('date2') == "null"){ $end_date = null; } else { $end_date = $request->input('date2'); }
                if($request->is_working == "true") {
                    $is_working = 1; $end_date = null;
                    $start_date = $request->input('date1');
                }
                elseif($request->is_working == "false"){
                    $is_working = 0;
                    $date = explode(',' , $request->input('date2'));
                    $start_date = $date[0];
                    $end_date = $date[1];
                }
                $internship = ProfileInternship::updateOrCreate(
                    // ['id' => $intern_id],
                    ['id' => $request->id],
                    [
                    'job_title' => $request->input('job_title'),
                    'employer_name' => $request->input('employer'),
                    'country_id' => $request->input('country'),
                    'state_id' => $request->input('state'),
                    'city_id' => $request->input('city'),
                    'start_date' => $start_date,
                    'is_working' => $is_working,
                    'end_date' => $end_date,
                    'resume_id' => $request->input('resume_id'),
                        ]
                    );
                $internship->save();
                return response()->json(['status' => true, 'message' => 'Record has been added'], 200);
            }
        }
        catch (\Exception $e){
            return response()->json(['status' => false,'error' => $e->getMessage()],500);
        }

    }




    public function create()
    {

        $profile_cv         = ProfileCv::where('user_id', Auth::user()->id)->Where('title', 'like', 'Resume-' . '%')->get();
        $new_cv             = new ProfileCv();
        $new_cv->user_id    = Auth::user()->id;
        $new_cv->title      = "Resume-" . count($profile_cv);
        $new_cv->cv_file    = "test.pdf";
        $new_cv->is_default = 0;
        $new_cv->save();
        $profile_cv_id = $new_cv->id;
        return redirect('user/resume/edit/' . $profile_cv_id);
       // return view('resume.create', compact('profile_cv_id'));
    }


    public function user_info(Request $request){

        $user_info = User::find(Auth::user()->id);
        return response()->json([$user_info]);

    }
    public function general_info_store(Request $request)
    {

        /***
         *  All Dropdown will be like that
         *  data : [
         *
         * degreelevel :{

         * 1 => Matric
         * 2 => BCS
         *
         * }
         * Grade : {
         * 1 => GPA
         * 2 => Percentage
         * }
         *
         * ]
         *
         */
        $user                          = User::find(Auth::user()->id);
        $user->job_title               = $request->job_title;
        $user->name                    = $request->name;
        $user->first_name              = $request->first_name;
        $user->last_name               = $request->last_name;
        $user->phone                   = $request->phone;
        $user->city_id                 = $request->city_id;
        $user->country_id              = $request->country_id;
        $user->resume_email            = $request->resume_email;
        $user->birth_place             = $request->birth_place;
        $user->post_code               = $request->post_code;
        $user->date_of_birth           = Carbon::parse($request->date_of_birth);
        $user->street_address          = $request->street_address;
        $user->nationality_id          = $request->nationality_id;
        $user->national_id_card_number = $request->national_id_card_number;
        $user->save();
        return response()->json([$user]);

    }


    public function profile_cv(Request $request){
        $profile_cv = ProfileCv::where('user_id', Auth::user()->id)->Where('title', 'like', 'Resume-' . '%')->get();

        if (count($profile_cv) < 1) {
            $new_cv             = new ProfileCv();
            $new_cv->user_id    = Auth::user()->id;
            $new_cv->title      = "Resume-" . $request->job_title;
            $new_cv->cv_file    = "my_test.pdf";
            $new_cv->is_default = 0;
            $new_cv->save();

            return response()->json(["new_cv" , $new_cv]);
        } else {
            $cv        = ProfileCv::find($profile_cv[0]->id);
            $cv->title = "Resume-" . $request->job_title;
            $cv->save();
            return response()->json(["cv" , $cv]);
        }

    }


    public function get_profile_summary(Request $request){
        $user_summary = ProfileSummary::where('user_id',Auth::user()->id)->first();
        return response()->json([$user_summary]);
    }


    public function save_profile_summary(Request $request){
        $save_profile_summary = new ProfileSummary();
        $u_id = Auth::user()->id;
        $save_profile_summary->user_id = $u_id;
        $save_profile_summary->summary = $request->summary;
        $save_profile_summary->save();
        return response()->json([$save_profile_summary]);
    }


    public function education(Request $request)
    {
        if ($request->id == 0) {
            $max = ProfileEducation::where('user_id', Auth::user()->id)->max('row_order');
//            dd($max);
            $max++;
            $education                  = new ProfileEducation();
            $education->user_id         = Auth::user()->id;
            $education->position        = $request->position;
            $education->profile_cv_id   = $request->profile_cv_id;
            $education->degree_level_id = $request->degree_level_id;
            $education->degree_type_id  = $request->degree_type_id;
            $education->degree_title    = $request->degree_title;
            $education->country_id      = $request->country_id;
            $education->state_id        = $request->state_id;
            $education->city_id         = $request->city_id;
            $education->date_completion = $request->date_completion;
            $education->institution     = $request->institution;
            $education->degree_result   = $request->degree_result;
            $education->result_type_id  = $request->result_type_id;
            $education->row_order       = $max;
//            dd($education);
            $education->save();
            return $education->id;
        } else {
            $education                  = ProfileEducation::find($request->id);
            $education->degree_level_id = $request->degree_level_id;
            $education->degree_type_id  = $request->degree_type_id;
            $education->position        = $request->position;
            $education->degree_title    = $request->degree_title;
            $education->country_id      = $request->country_id;
            $education->state_id        = $request->state_id;
            $education->city_id         = $request->city_id;
            $education->date_completion = $request->date_completion;
            $education->institution     = $request->institution;
            $education->degree_result   = $request->degree_result;
            $education->result_type_id  = $request->result_type_id;
            $education->profile_cv_id   = $request->profile_cv_id;
            $education->save();
            return $education->id;
        }



    }
    public function del_education(Request $request)
    {
//        echo "Hello world"; exit;
        dd($request);
        $data = ProfileEducation::find(49);
        $data->delete();
        return response()->json(["Success"]);
    }
    public function skill(Request $request)
    {
//        return response()->json(["Hello there here is skill method"]); exit;
        if ($request->id == 0) {
            $max = ProfileSkill::where('user_id', Auth::user()->id)->max('row_order');

            $max++;
            $skill                    = new ProfileSkill();
            $skill->user_id           = Auth::user()->id;
            $skill->job_skill_id      = $request->job_skill_id;
            $skill->position          = $request->position;
            $skill->job_experience_id = $request->job_experience_id;
            $skill->profile_cv_id     = $request->profile_cv_id;
            $skill->row_order         = $max;
//            dd($skill);
            $skill->save();
            return response()->json([$skill->id]);
        } else {
         //   $skill                    = ProfileSkill::find($request->id);
            $skill                    = new ProfileSkill();
            $skill->exists =true;
            $skill->id=$request->id;
            $skill->job_skill_id      = $request->job_skill_id;
//            $skill->position          = $request->position ?? 1;
            $skill->job_experience_id = $request->job_experience_id;
            $skill->profile_cv_id     = $request->profile_cv_id;
            $skill->save();
            return $skill->id;
        }
    }
    public function del_skill(Request $request)
    {
        $data = ProfileSkill::find($request->del_id);
        $data->delete();
    }
    public function employment(Request $request)
    {

//        echo "Hello Employment Section"; exit;
        if ($request->id == 0) {
            $max = ProfileExperience::where('user_id', Auth::user()->id)->max('row_order');
//            dd($max);
            $max++;
            $emp                       = new ProfileExperience();
            $emp->user_id              = Auth::user()->id;
            $emp->title                = $request->title;
            $emp->position             = $request->position;
            $emp->company              = $request->company;
            $emp->country_id           = $request->country_id;
            $emp->state_id             = $request->state_id;
            $emp->city_id              = $request->city_id;
            $emp->date_start           = $request->date_start;
            $emp->date_end             = $request->date_end;
            $emp->is_currently_working = $request->is_currently_working;
            $emp->description          = $request->description;
            $emp->profile_cv_id        = $request->profile_cv_id;
            $emp->row_order            = $max;
//            dd($emp);
            $emp->save();
            // Update search index
            $user = User::find(Auth::user()->id);
            if ($user) {
                $this->updateUserFullTextSearch($user);
            }
            return $emp->id;
        } else {
            $emp                       = ProfileExperience::find($request->id);
            $emp->user_id              = Auth::user()->id;
            $emp->title                = $request->title;
            $emp->position             = $request->position;
            $emp->company              = $request->company;
            $emp->country_id           = $request->country_id;
            $emp->state_id             = $request->state_id;
            $emp->city_id              = $request->city_id;
            $emp->date_start           = $request->date_start;
            $emp->date_end             = $request->date_end;
            $emp->is_currently_working = $request->is_currently_working;
            $emp->description          = $request->description;
            $emp->profile_cv_id        = $request->profile_cv_id;
            $emp->save();
            // Update search index
            $user = User::find(Auth::user()->id);
            if ($user) {
                $this->updateUserFullTextSearch($user);
            }
            return $emp->id;
        }
    }
    public function del_employment(Request $request)
    {
        $data = ProfileExperience::find($request->del_id);
        $data->delete();
    }
    public function language(Request $request)
    {
//        echo "Language Section"; exit;
        if ($request->id == 0) {
            $max = ProfileLanguage::where('user_id', Auth::user()->id)->max('row_order');
//            dd($max);
            $max++;
            $language                    = new ProfileLanguage();
            $language->user_id           = Auth::user()->id;
            $language->position          = $request->position;
            $language->language_id       = $request->language_id;
            $language->language_level_id = $request->language_level_id;
            $language->profile_cv_id     = $request->profile_cv_id;
            $language->row_order         = $max;
//            dd($language);
            $language->save();
            return $language->id;
        } else {
            $language                    = ProfileLanguage::find($request->id);
            $language->user_id           = Auth::user()->id;
            $language->language_id       = $request->language_id;
            $language->position             = $request->position;
            $language->language_level_id = $request->language_level_id;
            $language->profile_cv_id     = $request->profile_cv_id;
            $language->save();
            return $language->id;
        }
    }
    public function del_language(Request $request)
    {
        $data = ProfileLanguage::find($request->del_id);
        $data->delete();
    }
    public function social(Request $request)
    {
//        echo "Social Section"; exit;
        if ($request->id == 0) {
            $request->id=null;
            $max = SocialLink::where('user_id', Auth::user()->id)->max('row_order');
//            dd($max);
            $max++;
            $social                = new SocialLink();
            $social->user_id       = Auth::user()->id;
            $social->position             = $request->position;
            $social->website       = $request->website;
            $social->url           = $request->url;
            $social->profile_cv_id = $request->profile_cv_id;
            $social->row_order     = $max;
//            dd($social);
            $social->save();

            return $social->id;
        } else {
            $social                = SocialLink::find($request->id);
            $social->user_id       = Auth::user()->id;
            $social->position      = $request->position;
            $social->website       = $request->website;
            $social->url           = $request->url;
            $social->profile_cv_id = $request->profile_cv_id;
            $social->save();
            return $social->id;
        }
    }
    public function del_social(Request $request)
    {
        $data = SocialLink::find($request->del_id);
        $data->delete();
    }
    public function hobbies(Request $request)
    {
//        echo "Hobbies Section"; exit;
        if ($request->id == 0) {
            $max = Hobby::where('user_id', Auth::user()->id)->max('row_order');
//            dd($max);
            $max++;
            $hobby                = new Hobby();
            $hobby->user_id       = Auth::user()->id;
            $hobby->position             = $request->position;
            $hobby->hobby         = $request->hobby;
            $hobby->profile_cv_id = $request->profile_cv_id;
            $hobby->row_order     = $max;
            $hobby->save();
            return $hobby->id;
        } else {
            $hobby                = Hobby::find($request->id);
            $hobby->user_id       = Auth::user()->id;
            $hobby->position             = $request->position;
            $hobby->hobby         = $request->hobby;
            $hobby->profile_cv_id = $request->profile_cv_id;
            $hobby->save();
            return $hobby->id;
        }
    }
    public function del_hobbies(Request $request)
    {
        $data = Hobby::find($request->del_id);
        $data->delete();
    }
    public function courses(Request $request)
    {

//        echo "Course Section"; exit;
        if ($request->id == 0) {
            $max = Course::where('user_id', Auth::user()->id)->max('row_order');
//            dd($max);
            $max++;
            $course                   = new Course();
            $course->user_id          = Auth::user()->id;
            $course->position         = $request->position;
            $course->course_name      = $request->course_name;
            $course->course_duration  = $request->course_duration;
            $course->course_institute = $request->course_institute;
            $course->profile_cv_id    = $request->profile_cv_id;
            $course->row_order        = $max;
            $course->save();
            return $course->id;
        } else {
            $course                   = Course::find($request->id);
            $course->user_id          = Auth::user()->id;
            $course->position         = $request->position;
            $course->course_name      = $request->course_name;
            $course->course_duration  = $request->course_duration;
            $course->course_institute = $request->course_institute;
            $course->profile_cv_id    = $request->profile_cv_id;
//            dd($course);
            $course->save();
            return $course->id;
        }
    }
    public function del_courses(Request $request)
    {
        $data = Course::find($request->del_id);
        $data->delete();
    }
    public function user_data()
    {
        // $country = Country::all();
        $country                              = Country::where('lang', 'en')->get()->pluck('country', 'id');
        // $country = Country::pluck('country', 'id')->all();
        //  dd($country);
        //   dd($country);
        // $cities = City::all();
        //  $cities = City::pluck('city', 'id')->all();
        //  $state= State::pluck('state', 'id')->all();
        $degree_type                          = DegreeType::where('lang', 'en')->get()->pluck('degree_type', 'id');
        $result                               = ResultType::where('lang', 'en')->get()->pluck('result_type', 'id');
        $degree_level                         = DegreeLevel::where('lang', 'en')->get()->pluck('degree_level', 'id');
        $job_skill                            = JobSkill::where('lang', 'en')->get()->pluck('job_skill', 'id');
        $job_experience                       = JobExperience::where('lang', 'en')->get()->pluck('job_experience', 'id');
        $all_languages                        = Language::pluck('lang', 'id')->all();
        $language_level_id                    = LanguageLevel::where('lang', 'en')->get()->pluck('language_level', 'id');
        // dd($language_level_id);
        // dd(Auth::user());
        $user                                 = Auth::user();
        $hobbies                              = Hobby::where('user_id', $user->id)->get();
        $education                            = ProfileEducation::where('user_id', $user->id)->get();
        $skills                               = ProfileSkill::where('user_id', $user->id)->get();
        $proffessional_experience             = ProfileExperience::where('user_id', $user->id)->get();
        $languages                            = ProfileLanguage::where('user_id', $user->id)->get();
        $social_links                         = SocialLink::where('user_id', $user->id)->get();
        $courses                              = Course::where('user_id', $user->id)->get();
        $arr                                  = array();
        $arr['user_general_info']             = $user;
        $arr['user_education']                = $education;
        $arr['user_skills']                   = $skills;
        $arr['user_proffessional_experience'] = $proffessional_experience;
        $arr['user_languages']                = $languages;
        $arr['user_social_links']             = $social_links;
        $arr['user_courses']                  = $courses;
        $arr['user_hobbies']                  = $hobbies;
        $arr['all_countries']                 = $country;
        //    $arr['all_cities'] = $cities;
        //    $arr['all_states'] = $state;
        $arr['all_degree_types']              = $degree_type;
        $arr['all_results']                   = $result;
        $arr['all_degree_level']              = $degree_level;
        $arr['all_job_skills']                = $job_skill;
        $arr['all_job_experiences']           = $job_experience;
        $arr['all_languages']                 = $all_languages;
        $arr['all_language_levels']           = $language_level_id;
        $json_arr                             = json_encode($arr);
        return $json_arr;
    }
    public function edit($id)
    {
        $profile_cv    = ProfileCv::where('user_id', Auth::user()->id)->first();
        $profile_cv_id = $profile_cv->id;
        return view('resume.resumetest', compact('profile_cv_id'));
    }
    public function user_data_1($profile_cv_id)
    {

        // $country = Country::all();
        $country                              = Country::where('lang', 'en')->get()->pluck('country', 'id');
        //   dd($country);
        // $cities = City::all();
        $cities                               = City::pluck('city', 'id')->all();
        $state                                = State::pluck('state', 'id')->all();
        $degree_type                          = DegreeType::where('lang', 'en')->get()->pluck('degree_type', 'id');
        $result                               = ResultType::where('lang', 'en')->get()->pluck('result_type', 'id');
        $degree_level                         = DegreeLevel::where('lang', 'en')->get()->pluck('degree_level', 'id');
        $job_skill                            = JobSkill::where('lang', 'en')->get()->pluck('job_skill', 'id');
        $job_experience                       = JobExperience::where('lang', 'en')->get()->pluck('job_experience', 'id');
        $all_languages                        = Language::pluck('lang', 'id')->all();
        $language_level_id                    = LanguageLevel::where('lang', 'en')->get()->pluck('language_level', 'id');
        // dd($language_level_id);
        // dd(Auth::user());
        $user                                 = Auth::user();



        $user->user_state_name  = $user->state;
        $user->user_city_name  = $user->city;
        $user->user_country_name   = $user->country;


        $hobbies                              = Hobby::where('user_id', $user->id)->where('profile_cv_id', $profile_cv_id)->get();
        $education                            = ProfileEducation::where('user_id', $user->id)->where('profile_cv_id', $profile_cv_id)
            ->with('degreeLevel')
            ->with('degreeType')
            ->with('resultType')
            ->with('country')
            ->with('state')
            ->with('city')
            ->get();
        $skills                               = ProfileSkill::where('user_id', $user->id)->where('profile_cv_id', $profile_cv_id)->with('jobSkill')->with('jobExperience')->get();




//        dd($skills);

        $proffessional_experience             = ProfileExperience::where('user_id', $user->id)
            ->where('profile_cv_id', $profile_cv_id)
            ->with('country')
            ->with('state')
            ->with('city')
            ->get();
        $languages                            = ProfileLanguage::where('user_id', $user->id)->where('profile_cv_id', $profile_cv_id)
        ->with('language')
        ->with('languageLevel')
        ->get();
        $social_links                         = SocialLink::where('user_id', $user->id)->where('profile_cv_id', $profile_cv_id)->get();
        $courses                              = Course::where('user_id', $user->id)->where('profile_cv_id', $profile_cv_id)->get();
        $arr                                  = array();
        $arr['user_general_info']             = $user;
        $arr['user_education']                = $education;
        $arr['user_skills']                   = $skills;
        $arr['user_proffessional_experience'] = $proffessional_experience;
        $arr['user_languages']                = $languages;
        $arr['user_social_links']             = $social_links;
        $arr['user_courses']                  = $courses;
        $arr['user_hobbies']                  = $hobbies;
        $arr['all_countries'] = $country;
        $arr['all_cities'] = $cities;
        $arr['all_states'] = $state;
        $arr['all_degree_types']              = $degree_type;
        $arr['all_results']                   = $result;
        $arr['all_degree_level']              = $degree_level;
        $arr['all_job_skills']                = $job_skill;
        $arr['all_job_experiences']           = $job_experience;
        $arr['all_languages']                 = $all_languages;
        $arr['all_language_levels']           = $language_level_id;
        $json_arr                             = json_encode($arr);
        return $json_arr;
    }
    public function get_states($id)
    {
        $states        = State::where('country_id', $id)->pluck('state', 'state_id');
        $arr           = array();
        $arr['states'] = $states;
        return $arr;
    }
    public function get_cities($id)
    {
        $cities        = City::where('state_id', $id)->pluck('city', 'city_id');
        $arr           = array();
        $arr['cities'] = $cities;
        return $arr;
    }


    public function get_degree_types($id){
        $degree_types = DegreeType::where('degree_type_id',$id)->pluck('degree_type','degree_type_id');
        $arr = array();
        $arr['degree_types'] = $degree_types;
        return $arr;
    }

    public function login_user_info(){
        if(Auth::check()){
            $user_info = Auth::user()->id ;
            return response()->json([
                'data' => $user_info 
            ]);
        }
        else
        {
            return response()->json([
                'data' => null,
                'message' => 'User is Not Login'
            ]);
        }
    }

}
