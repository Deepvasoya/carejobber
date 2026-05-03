<script>
    (function () {
        function initFrontEditors() {
            if (window.CarejobberTinyMCE) {
                window.CarejobberTinyMCE.init(document, '#description,#benefits');
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFrontEditors);
        } else {
            initFrontEditors();
        }
    })();
</script>
