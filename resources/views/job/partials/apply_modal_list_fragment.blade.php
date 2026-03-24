@php
    $user = Auth::user();
    $userCvs = $user->profileCvs;
@endphp
<div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
    <div class="modal-header" style="background: #17d27c; color: white; border-radius: 12px 12px 0 0; padding: 20px 30px;">
        <h5 class="modal-title" id="applyJobListModalLabel" style="font-weight: 600; font-size: 20px;">
            <i class="fas fa-paper-plane me-2"></i>{{ __('Apply for') }} {{ $job->title }}
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
    </div>

    <form action="{{ route('post.job.apply', $job->slug) }}" method="POST" enctype="multipart/form-data" id="applyJobListForm">
        @csrf
        <div class="modal-body" style="padding: 30px;">
            <div class="mb-4">
                <h6 class="mb-3" style="font-weight: 600; color: #333; font-size: 16px;">
                    <i class="fas fa-file-alt text-primary me-2"></i>{{ __('Select Your Resume') }}
                </h6>

                @if($userCvs && $userCvs->count() > 0)
                    <div class="row g-3 mb-3">
                        @foreach($userCvs as $cv)
                            <div class="col-md-6">
                                <div class="cv-card apply-list-cv-card" style="border: 2px solid #e0e0e0; border-radius: 10px; padding: 15px; cursor: pointer; transition: all 0.3s;"
                                     onclick="applyListSelectCv({{ $cv->id }}, this)">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="cv_id" value="{{ $cv->id }}" id="apply_list_cv_{{ $cv->id }}" class="apply-list-cv-radio" style="margin-right: 12px; width: 18px; height: 18px;">
                                        <div class="flex-grow-1">
                                            <div style="font-weight: 600; color: #333; font-size: 15px;">{{ $cv->title ?? __('Resume') }}</div>
                                            <small class="text-muted">
                                                <i class="fas fa-file-pdf text-danger"></i> {{ __('Uploaded') }} {{ $cv->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="upload-new-cv">
                    <label class="d-flex align-items-center" style="cursor: pointer; padding: 15px; border: 2px dashed #ccc; border-radius: 10px; background: #f8f9fa;">
                        <input type="radio" name="cv_option" value="upload" id="apply_list_upload_new_cv" style="margin-right: 12px; width: 18px; height: 18px;" onchange="applyListToggleCvUpload()">
                        <div>
                            <strong style="color: #333; font-size: 15px;">{{ __('Upload a new resume') }}</strong>
                            <br><small class="text-muted">{{ __('PDF, DOC, DOCX (Max 5MB)') }}</small>
                        </div>
                    </label>
                    <div id="apply_list_cv_upload_field" style="display: none; margin-top: 15px;">
                        <input type="file" name="cv" id="apply_list_cv_file" class="form-control" accept=".pdf,.doc,.docx" onchange="applyListShowFileName(this)">
                        <div id="apply_list_file_name_display" class="mt-2 text-muted small"></div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="apply_list_cover_letter" class="form-label" style="font-weight: 600; color: #333; font-size: 16px;">
                    <i class="fas fa-envelope text-success me-2"></i>{{ __('Cover Letter') }} <span class="text-muted">({{ __('Optional') }})</span>
                </label>
                <textarea name="cover_letter" id="apply_list_cover_letter" class="form-control" rows="6"
                          placeholder="{{ __('Tell the employer why you are a great fit for this position...') }}"
                          maxlength="2000"
                          style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; font-size: 14px;"></textarea>
                <div class="d-flex justify-content-between mt-2">
                    <small class="text-muted">{{ __('Maximum 2000 characters') }}</small>
                    <small class="text-muted"><span id="apply_list_char_count">0</span> / 2000</small>
                </div>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="apply_list_terms" name="terms" required style="width: 18px; height: 18px;">
                <label class="form-check-label ms-2" for="apply_list_terms" style="color: #666; font-size: 14px;">
                    {{ __('I agree to the') }} <a href="{{ route('cms', ['slug' => 'terms-of-use']) }}" target="_blank" rel="noopener">{{ __('Terms and Conditions') }}</a>
                </label>
            </div>
        </div>

        <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 20px 30px;">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 10px 24px; border-radius: 6px;">
                {{ __('Cancel') }}
            </button>
            <button type="submit" class="btn btn-primary apply-job-list-submit" style="padding: 10px 30px; border-radius: 6px; background: #17d27c; border: none;">
                <i class="fas fa-paper-plane me-2"></i>{{ __('Submit Application') }}
            </button>
        </div>
    </form>
</div>
