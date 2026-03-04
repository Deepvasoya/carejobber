<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Job;
use App\JobApply;
use App\JobQuestionAnswer;
use App\ProfileCv;
use App\Events\JobApplied;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ImgUploader;

class ApplyJobModal extends Component
{
    use WithFileUploads;

    public $jobSlug;
    public $job;
    public $isOpen = false;
    
    public $resumeSource = 'existing';
    public $selectedCvId = null;
    public $uploadedResume = null;
    public $coverLetter = '';
    public $currentSalary = '';
    public $expectedSalary = '';
    public $salaryCurrency = 'CAD';
    public $questionAnswers = [];

    protected $listeners = ['openApplyModal' => 'open'];

    public function mount($jobSlug = null)
    {
        if ($jobSlug) {
            $this->jobSlug = $jobSlug;
            $this->job = Job::where('slug', $jobSlug)->first();
        }
    }

    public function open($jobSlug = null)
    {
        if ($jobSlug) {
            $this->jobSlug = $jobSlug;
            $this->job = Job::where('slug', $jobSlug)->first();
        }
        
        if (!$this->job) {
            session()->flash('error', __('Job not found.'));
            return;
        }
        
        $this->isOpen = true;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->reset(['resumeSource', 'selectedCvId', 'uploadedResume', 'coverLetter', 'currentSalary', 'expectedSalary', 'questionAnswers']);
    }

    public function rules()
    {
        $rules = [
            'coverLetter' => 'nullable|string|max:2000',
            'currentSalary' => 'nullable|integer',
            'expectedSalary' => 'nullable|integer',
            'salaryCurrency' => 'nullable|string|max:5',
        ];

        if ($this->resumeSource === 'existing') {
            $rules['selectedCvId'] = 'required|exists:profile_cvs,id';
        } else {
            $rules['uploadedResume'] = 'required|file|mimes:pdf,doc,docx|max:10240';
        }

        if ($this->job && $this->job->jobQuestions) {
            foreach ($this->job->jobQuestions as $question) {
                $rules['questionAnswers.' . $question->id] = 'nullable|string|max:1000';
            }
        }

        return $rules;
    }

    public function submit()
    {
        $user = Auth::user();
        if (!$user) {
            session()->flash('error', __('Please login to apply.'));
            return redirect()->route('login');
        }

        $this->validate();

        // Check if already applied
        $existingApplication = JobApply::where('user_id', $user->id)
            ->where('job_id', $this->job->id)
            ->first();

        if ($existingApplication) {
            session()->flash('error', __('You have already applied for this job.'));
            $this->close();
            return;
        }

        $cvId = null;
        $resumeSource = 'existing_cv';

        if ($this->resumeSource === 'existing') {
            $cvId = $this->selectedCvId;
        } else {
            // Upload new resume to public/cvs (same as existing CV upload system)
            $resume = $this->uploadedResume;
            $destinationPath = \ImgUploader::real_public_path() . 'cvs/';
            $extension = $resume->getClientOriginalExtension();
            $fileName = \Str::slug(pathinfo($resume->getClientOriginalName(), PATHINFO_FILENAME), '-') . '-' . time() . '-' . rand(1, 999) . '.' . $extension;
            $resume->move($destinationPath, $fileName);

            // Create ProfileCv record
            $profileCv = new ProfileCv();
            $profileCv->user_id = $user->id;
            $profileCv->cv_file = $fileName;
            $profileCv->title = pathinfo($resume->getClientOriginalName(), PATHINFO_FILENAME);
            $profileCv->is_default = ProfileCv::where('user_id', $user->id)->count() === 0 ? 1 : 0;
            $profileCv->save();

            $cvId = $profileCv->id;
            $resumeSource = 'uploaded';
        }

        // Create application
        $jobApply = new JobApply();
        $jobApply->user_id = $user->id;
        $jobApply->job_id = $this->job->id;
        $jobApply->cv_id = $cvId;
        $jobApply->cover_letter = $this->coverLetter;
        $jobApply->resume_source = $resumeSource;
        $jobApply->current_salary = $this->currentSalary ?: null;
        $jobApply->expected_salary = $this->expectedSalary ?: null;
        $jobApply->salary_currency = $this->salaryCurrency;
        $jobApply->status = 'applied';
        $jobApply->save();

        // Save question answers
        if ($this->questionAnswers) {
            foreach ($this->questionAnswers as $questionId => $answer) {
                if (!empty($answer)) {
                    $questionAnswer = new JobQuestionAnswer();
                    $questionAnswer->job_question_id = $questionId;
                    $questionAnswer->job_apply_id = $jobApply->id;
                    $questionAnswer->answer = $answer;
                    $questionAnswer->save();
                }
            }
        }

        // Check package quota
        if ((bool) config('jobseeker.is_jobseeker_package_active')) {
            if ($user->jobs_quota > 0) {
                $user->availed_jobs_quota += 1;
                $user->save();
            }
        }

        // Fire event (if ProfileCv exists)
        $cv = ProfileCv::find($cvId);
        if ($cv) {
            event(new JobApplied($this->job, $jobApply, $cv));
        }

        session()->flash('success', __('You have successfully applied for this job!'));
        $this->close();
        $this->dispatch('applicationSubmitted');
    }

    public function render()
    {
        $userCvs = [];
        if (Auth::check()) {
            $userCvs = ProfileCv::where('user_id', Auth::id())->get();
        }

        return view('livewire.apply-job-modal', [
            'userCvs' => $userCvs,
            'job' => $this->job,
        ]);
    }
}
