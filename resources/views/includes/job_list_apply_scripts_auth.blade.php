@if (Auth::check())
    function applySubmitOverlayShow() {
        var $o = $('#applySubmitPageOverlay');
        if (!$o.length) return;
        $o.addClass('is-visible').attr('aria-hidden', 'false');
        $('body').css('overflow', 'hidden');
    }

    function applySubmitOverlayHide() {
        var $o = $('#applySubmitPageOverlay');
        if (!$o.length) return;
        $o.removeClass('is-visible').attr('aria-hidden', 'true');
        $('body').css('overflow', '');
    }

    function applyJobListShowModal() {
        var el = document.getElementById('applyJobListModal');
        if (!el) return;
        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(el).show();
        } else if (window.jQuery && jQuery.fn.modal) {
            jQuery(el).modal('show');
        }
    }

    function applyJobListHideModal() {
        var el = document.getElementById('applyJobListModal');
        if (!el) return;
        if (window.bootstrap && window.bootstrap.Modal) {
            var inst = window.bootstrap.Modal.getInstance(el);
            if (inst) inst.hide();
        } else if (window.jQuery && jQuery.fn.modal) {
            jQuery(el).modal('hide');
        }
    }

    function applyListSelectCv(cvId, element) {
        var modal = document.getElementById('applyJobListModal');
        if (!modal) return;
        modal.querySelectorAll('.apply-list-cv-card').forEach(function(card) {
            card.style.borderColor = '#e0e0e0';
            card.style.background = '#fff';
        });
        element.style.borderColor = '#667eea';
        element.style.background = '#f8f9ff';
        var radio = document.getElementById('apply_list_cv_' + cvId);
        if (radio) radio.checked = true;
        var uploadOpt = document.getElementById('apply_list_upload_new_cv');
        if (uploadOpt) uploadOpt.checked = false;
        var uploadField = document.getElementById('apply_list_cv_upload_field');
        if (uploadField) uploadField.style.display = 'none';
    }

    function applyListToggleCvUpload() {
        var uploadField = document.getElementById('apply_list_cv_upload_field');
        var uploadOpt = document.getElementById('apply_list_upload_new_cv');
        if (!uploadField || !uploadOpt) return;
        if (uploadOpt.checked) {
            uploadField.style.display = 'block';
            document.querySelectorAll('#applyJobListModal input.apply-list-cv-radio').forEach(function(r) {
                r.checked = false;
            });
            document.querySelectorAll('#applyJobListModal .apply-list-cv-card').forEach(function(card) {
                card.style.borderColor = '#e0e0e0';
                card.style.background = '#fff';
            });
        } else {
            uploadField.style.display = 'none';
        }
    }

    function applyListShowFileName(input) {
        var display = document.getElementById('apply_list_file_name_display');
        if (!display || !input.files || !input.files[0]) return;
        display.innerHTML = '<i class="fas fa-file-pdf text-danger me-2"></i>' + input.files[0].name;
    }

    function applyListBindCoverLetterCounter() {
        var ta = document.getElementById('apply_list_cover_letter');
        var cc = document.getElementById('apply_list_char_count');
        if (!ta || !cc) return;
        cc.textContent = String(ta.value.length);
        ta.oninput = function() {
            cc.textContent = String(ta.value.length);
        };
    }

    $(document).on('click', '.js-job-list-open-apply', function(e) {
        e.preventDefault();
        var slug = $(this).data('job-slug');
        if (!slug) return;
        var $dialog = $('#applyJobListModal .modal-dialog');
        $dialog.html(
            '<div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);"><div class="modal-body text-center py-5 text-muted"><div class="spinner-border text-secondary" role="status"></div></div></div>'
        );
        applyJobListShowModal();
        $.ajax({
            url: "{{ url('job-apply-modal') }}/" + encodeURIComponent(slug),
            type: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            success: function(data) {
                if (data.success && data.html) {
                    $dialog.html(data.html);
                    applyListBindCoverLetterCounter();
                } else {
                    swal({
                        title: "{{ __('Error') }}",
                        text: data.message || "{{ __('Could not load apply form.') }}",
                        icon: "error"
                    });
                    applyJobListHideModal();
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON
                    .message : "{{ __('Request failed.') }}";
                swal({
                    title: "{{ __('Error') }}",
                    text: msg,
                    icon: "error"
                });
                applyJobListHideModal();
            }
        });
    });

    $(document).on('submit', '#applyJobListForm', function(e) {
        e.preventDefault();
        var form = this;
        var $btn = $(form).find('.apply-job-list-submit');
        $btn.prop('disabled', true);
        applySubmitOverlayShow();
        $.ajax({
            url: $(form).attr('action'),
            type: "POST",
            data: new FormData(form),
            processData: false,
            contentType: false,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            success: function(res) {
                $btn.prop('disabled', false);
                if (res.success) {
                    applyJobListHideModal();
                    if (res.redirect_url) {
                        window.location.href = res.redirect_url;
                        return;
                    }
                    swal({
                        title: "{{ __('Success') }}",
                        text: res.message ||
                            "{{ __('You have successfully applied for this job') }}",
                        icon: "success"
                    });
                    window.location.reload();
                } else {
                    swal({
                        title: "{{ __('Error') }}",
                        text: res.message || "{{ __('Something went wrong.') }}",
                        icon: "error"
                    });
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false);
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON
                    .message : "{{ __('Something went wrong.') }}";
                swal({
                    title: "{{ __('Error') }}",
                    text: msg,
                    icon: "error"
                });
            },
            complete: function() {
                applySubmitOverlayHide();
            }
        });
    });
@endif
