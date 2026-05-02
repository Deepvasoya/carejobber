<script src="{{ asset('admin_assets/global/plugins/tinymce/js/tinymce/jquery.tinymce.min.js') }}"></script>
<script src="{{ asset('admin_assets/global/plugins/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize TinyMCE for description
    tinymce.init({
        selector: '#description',
        height: 200,
        menubar: false,
        forced_root_block: '',
        plugins: [
            'advlist autolink lists link image charmap',
            'searchreplace visualblocks code fullscreen',
            'media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
        relative_urls: true,
        setup: function (editor) {
            editor.on('init', function () {
                console.log('TinyMCE Description initialized');
            });
        }
    });

    // Initialize TinyMCE for benefits
    tinymce.init({
        selector: '#benefits',
        height: 200,
        menubar: false,
        forced_root_block: '',
        plugins: [
            'advlist autolink lists link image charmap',
            'searchreplace visualblocks code fullscreen',
            'media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
        relative_urls: true,
        setup: function (editor) {
            editor.on('init', function () {
                console.log('TinyMCE Benefits initialized');
            });
        }
    });
});
</script>