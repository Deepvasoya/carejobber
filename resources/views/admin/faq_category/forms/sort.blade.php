{!! APFrmErrHelp::showErrorsNotice($errors) !!}
@include('flash::message')
<div class="form-body">
    <h3>Drag and Drop to Sort the Categories</h3>
    <div class="form-group mb-3">
        {!! Form::select('lang', ['' => 'Select Language']+$languages, config('default_lang'), array('class'=>'form-control', 'id'=>'lang', 'onchange'=>'refresh_faq_category_sort_data();')) !!}
    </div>
    <div id="faq_category_sort_data_div">
        <p class="text-muted">Loading categories...</p>
    </div>
</div>
@push('scripts') 
<script>
    $(document).ready(function () {
        refresh_faq_category_sort_data();
    });
    
    function refresh_faq_category_sort_data() {
        var language = $('#lang').val();
        
        // Show loading state
        $("#faq_category_sort_data_div").html('<p class="text-muted">Loading categories...</p>');
        
        $.ajax({
            type: "GET",
            url: "{{ route('faq.category.sort.data') }}",
            data: {lang: language},
            success: function (responseData) {
                $("#faq_category_sort_data_div").html(responseData);
                
                // Initialize sortable
                if (typeof $.fn.sortable !== 'undefined') {
                    $('#sortable').sortable({
                        placeholder: 'ui-sortable-placeholder',
                        cursor: 'move',
                        opacity: 0.8,
                        update: function (event, ui) {
                            var faqCategoryOrder = $(this).sortable('toArray').toString();
                            $.post("{{ route('faq.category.sort.update') }}", {
                                faqCategoryOrder: faqCategoryOrder, 
                                _method: 'PUT', 
                                _token: '{{ csrf_token() }}'
                            }).done(function() {
                                // Optional: Show success message
                                console.log('Order updated successfully');
                            });
                        }
                    });
                    $("#sortable").disableSelection();
                } else {
                    console.error('jQuery UI Sortable not loaded');
                    $("#faq_category_sort_data_div").prepend('<div class="alert alert-danger">jQuery UI is not loaded. Drag and drop functionality is not available.</div>');
                }
            },
            error: function() {
                $("#faq_category_sort_data_div").html('<div class="alert alert-danger">Failed to load categories. Please try again.</div>');
            }
        });
    }
</script>
@endpush
