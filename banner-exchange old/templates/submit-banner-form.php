
<!-- submit-banner-form.php -->
<?php
if ( isset( $_POST['bes_submit'] ) ) {
    bes_handle_banner_submission();
}
?>

<div class="bes-submit-banner-form-horizontal">
    <form method="post" enctype="multipart/form-data">
        <div class="form-group-horizontal">
            <label for="banner_url">URL del Banner:</label>
            <input type="text" name="banner_url" required>
        </div>
        
        <div class="form-group-horizontal">
            <label for="target_url">URL de Destino:</label>
            <input type="url" name="target_url" required>
        </div>
        
        <div class="form-group-horizontal">
            <input type="submit" name="bes_submit" value="Enviar Banner" class="button-horizontal">
        </div>
    </form>
</div>