<div class="row">
    <div class="col-md-12">
        <div class="py-3">© 2025 MUNext</div>
    </div>
</div>

<script>
(function() {
    var input = document.getElementById('customFile');
    var label = document.querySelector('label[for="customFile"]');
    var display = document.querySelector('.selected-file-name');

    if (!input) return;

    input.addEventListener('change', function(e) {
        var fileName = 'No file chosen';
        if (this.files && this.files.length > 0) {
            fileName = this.files[0].name;
        }
        // Update bootstrap custom-file label (if present)
        if (label) label.textContent = fileName;
        // Update helper text
        if (display) display.textContent = fileName;
    });
})();
</script>

<style>
.h-x1 {
    min-height: 50px;
}
</style>

<?php unset($_SESSION['toast']); ?>