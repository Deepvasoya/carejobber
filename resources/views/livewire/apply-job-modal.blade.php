<div>
    @if($isOpen)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-briefcase"></i> {{__('Apply for')}} {{ $job->title }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="close" aria-label="Close"></button>
                </div>
                
                <form wire:submit.prevent="submit">
                    <div class="modal-body">
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Resume Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">{{__('Select Resume')}}</label>
                            <div class="d-flex gap-3 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.live="resumeSource" value="existing" id="resumeExisting">
                                    <label class="form-check-label" for="resumeExisting">
                                        {{__('Use Existing Resume')}}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.live="resumeSource" value="upload" id="resumeUpload">
                                    <label class="form-check-label" for="resumeUpload">
                                        {{__('Upload New Resume')}}
                                    </label>
                                </div>
                            </div>

                            @if($resumeSource === 'existing')
                                <select wire:model="selectedCvId" class="form-select @error('selectedCvId') is-invalid @enderror">
                                    <option value="">{{__('-- Select a resume --')}}</option>
                                    @foreach($userCvs as $cv)
                                        <option value="{{ $cv->id }}">
                                            {{ $cv->title ?: __('Resume') }} 
                                            @if($cv->is_default) ({{__('Default')}}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedCvId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                
                                @if($userCvs->isEmpty())
                                    <small class="text-muted">
                                        {{__('No resumes found.')}} <a href="{{ route('my.profile') }}">{{__('Upload one here')}}</a>
                                    </small>
                                @endif
                            @else
                                <input type="file" wire:model="uploadedResume" class="form-control @error('uploadedResume') is-invalid @enderror" accept=".pdf,.doc,.docx">
                                @error('uploadedResume') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">{{__('Accepted formats: PDF, DOC, DOCX (max 10MB)')}}</small>
                                
                                @if($uploadedResume)
                                    <div class="mt-2">
                                        <i class="fas fa-file-alt"></i> {{ $uploadedResume->getClientOriginalName() }}
                                        <span wire:loading wire:target="uploadedResume" class="spinner-border spinner-border-sm ms-2"></span>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Cover Letter -->
                        <div class="mb-3">
                            <label for="coverLetter" class="form-label fw-bold">
                                {{__('Cover Letter')}} <span class="text-muted">({{__('Optional')}})</span>
                            </label>
                            <textarea wire:model="coverLetter" 
                                      id="coverLetter" 
                                      class="form-control @error('coverLetter') is-invalid @enderror" 
                                      rows="6" 
                                      maxlength="2000"
                                      placeholder="{{__('Tell the employer why you\'re a great fit for this position...')}}"></textarea>
                            <div class="d-flex justify-content-between">
                                <div>@error('coverLetter') <span class="text-danger small">{{ $message }}</span> @enderror</div>
                                <small class="text-muted">{{ strlen($coverLetter) }}/2000</small>
                            </div>
                        </div>

                        <!-- Salary -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{__('Current Salary')}}</label>
                                <input type="number" wire:model="currentSalary" class="form-control" placeholder="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{__('Expected Salary')}}</label>
                                <input type="number" wire:model="expectedSalary" class="form-control" placeholder="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{__('Currency')}}</label>
                                <select wire:model="salaryCurrency" class="form-select">
                                    <option value="CAD">CAD</option>
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                </select>
                            </div>
                        </div>

                        <!-- Job Questions -->
                        @if($job->jobQuestions && $job->jobQuestions->count() > 0)
                            <div class="mb-3">
                                <h6 class="fw-bold">{{__('Additional Questions')}}</h6>
                                @foreach($job->jobQuestions as $question)
                                    <div class="mb-3">
                                        <label class="form-label">
                                            {{ $question->question }}
                                            @if($question->is_required) <span class="text-danger">*</span> @endif
                                        </label>
                                        <textarea wire:model="questionAnswers.{{ $question->id }}" 
                                                  class="form-control" 
                                                  rows="3"
                                                  maxlength="1000"
                                                  @if($question->is_required) required @endif></textarea>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="close">
                            {{__('Cancel')}}
                        </button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submit">
                                <i class="fas fa-paper-plane"></i> {{__('Submit Application')}}
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
</div>
