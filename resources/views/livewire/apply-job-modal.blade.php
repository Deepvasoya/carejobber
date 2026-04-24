<div id="apply-job-modal-wrapper">
    @if($isOpen)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <div class="modal-header" style="border-bottom: 1px solid #f0f0f0; padding: 25px 30px;">
                    <h4 class="modal-title" style="font-weight: 600; color: #333; margin: 0;">
                        @if($job)
                            <i class="fas fa-paper-plane me-2"></i>{{ __('Apply for') }} {{ $job->title }}
                        @else
                            {{ __('Apply for this job') }}
                        @endif
                    </h4>
                    <button type="button" class="btn-close" wire:click="close" aria-label="Close"></button>
                </div>
                
                <form wire:submit.prevent="submit">
                    <div class="modal-body" style="padding: 30px;">
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Select a your CV -->
                        <div class="mb-4">
                            <label class="form-label" style="font-weight: 500; color: #666; font-size: 14px; margin-bottom: 15px;">
                                {{__('Select a your CV')}}
                            </label>
                            
                            <!-- Existing CVs as Cards -->
                            @if($userCvs->isNotEmpty())
                                <div class="row g-3 mb-3">
                                    @foreach($userCvs as $cv)
                                        <div class="col-md-6">
                                            <div class="cv-card" wire:click="$set('selectedCvId', {{ $cv->id }})" 
                                                 style="cursor: pointer; padding: 20px; border-radius: 8px; border: 2px solid {{ $selectedCvId == $cv->id ? '#2563eb' : '#e5e7eb' }}; background: {{ $selectedCvId == $cv->id ? '#eff6ff' : '#fff' }}; transition: all 0.2s;">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="fas fa-file-pdf" style="font-size: 32px; color: {{ $selectedCvId == $cv->id ? '#2563eb' : '#6b7280' }};"></i>
                                                    </div>
                                                    <div style="flex: 1;">
                                                        <div style="font-weight: 600; color: #333; font-size: 15px;">
                                                            {{ $cv->title ?: 'cv_candidate' }}
                                                        </div>
                                                        <div style="color: #6b7280; font-size: 13px;">PDF</div>
                                                    </div>
                                                    @if($selectedCvId == $cv->id)
                                                        <i class="fas fa-check-circle" style="color: #2563eb; font-size: 20px;"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Or Upload New CV -->
                            <div class="text-center my-3" style="color: #9ca3af; font-size: 14px;">
                                {{__('or upload your CV')}}
                            </div>

                            <!-- Upload Area -->
                            <div class="upload-area" style="border: 2px dashed #d1d5db; border-radius: 8px; padding: 30px; text-align: center; background: #f9fafb; cursor: pointer;" 
                                 onclick="document.getElementById('fileUpload').click()">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 40px; color: #9ca3af; margin-bottom: 10px;"></i>
                                <div style="color: #6b7280; font-size: 14px; margin-bottom: 5px;">
                                    {{__('Upload CV (doc, docx, pdf)')}}
                                </div>
                                <input type="file" id="fileUpload" wire:model="uploadedResume" class="d-none" accept=".pdf,.doc,.docx">
                                
                                @if($uploadedResume)
                                    <div class="mt-3 p-3" style="background: #fff; border-radius: 6px; display: inline-block;">
                                        <i class="fas fa-file-alt text-primary"></i> 
                                        <strong>{{ $uploadedResume->getClientOriginalName() }}</strong>
                                        <span wire:loading wire:target="uploadedResume" class="spinner-border spinner-border-sm ms-2"></span>
                                    </div>
                                @endif
                                
                                @error('uploadedResume') 
                                    <div class="text-danger small mt-2">{{ $message }}</div> 
                                @enderror
                            </div>
                        </div>

                        <!-- Message / Cover Letter -->
                        <div class="mb-4">
                            <label for="coverLetter" class="form-label" style="font-weight: 500; color: #666; font-size: 14px;">
                                {{__('Message')}}
                            </label>
                            <textarea wire:model="coverLetter" 
                                      id="coverLetter" 
                                      class="form-control @error('coverLetter') is-invalid @enderror" 
                                      rows="6" 
                                      maxlength="2000"
                                      style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; font-size: 14px; resize: none;"
                                      placeholder="{{__('Tell the employer why you\'re a great fit for this position...')}}"></textarea>
                            <div class="d-flex justify-content-between mt-2">
                                <div>@error('coverLetter') <span class="text-danger small">{{ $message }}</span> @enderror</div>
                                <small class="text-muted">{{ strlen($coverLetter) }}/2000</small>
                            </div>
                        </div>

                        <!-- Additional Questions -->
                        @if($job && $job->jobQuestions && $job->jobQuestions->count() > 0)
                        <div class="mb-4">
                            <label class="form-label" style="font-weight: 500; color: #666; font-size: 14px; margin-bottom: 15px;">
                                <i class="fas fa-question-circle text-warning me-1"></i>{{__('Additional Questions')}}
                            </label>
                            @foreach($job->jobQuestions as $question)
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 500; color: #444; font-size: 14px;">
                                    {{ $loop->iteration }}. {{ $question->question_title }}
                                </label>
                                <textarea wire:model="questionAnswers.{{ $question->id }}"
                                          class="form-control @error('questionAnswers.'.$question->id) is-invalid @enderror"
                                          rows="2"
                                          maxlength="1000"
                                          style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px; font-size: 14px;"
                                          placeholder="{{__('Your answer...')}}"></textarea>
                                @error('questionAnswers.'.$question->id)
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Terms and Conditions -->
                        <div class="mb-0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="acceptTerms" id="acceptTerms" style="margin-top: 4px;">
                                <label class="form-check-label" for="acceptTerms" style="font-size: 14px; color: #666;">
                                    {{__('You accept our')}} 
                                    <a href="{{ url('/page/terms-of-use') }}" target="_blank" style="color: #2563eb; text-decoration: none;">{{__('Terms and Conditions')}}</a> 
                                    {{__('and')}} 
                                    <a href="{{ url('/page/privacy-policy') }}" target="_blank" style="color: #2563eb; text-decoration: none;">{{__('Privacy Policy')}}</a>
                                </label>
                            </div>
                            @error('acceptTerms') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="modal-footer" style="border-top: 1px solid #f0f0f0; padding: 20px 30px;">
                        <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled" 
                                style="background: #2563eb; border: none; padding: 14px; font-size: 16px; font-weight: 600; border-radius: 8px;">
                            <span wire:loading.remove wire:target="submit">
                                {{__('Apply Job')}}
                            </span>
                            <span wire:loading wire:target="submit">
                                <span class="spinner-border spinner-border-sm me-1"></span> {{__('Submitting...')}}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.hook('component.init', ({ component, cleanup }) => {
                if (component.name === 'apply-job-modal') {
                    console.log('Apply modal component initialized');
                    window.openApplyJobModal = function(jobSlug) {
                        console.log('Opening modal for job:', jobSlug);
                        component.call('open', jobSlug);
                    };
                }
            });
        });
    </script>
</div>
