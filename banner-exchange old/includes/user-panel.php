<?php

// Mostrar las estadísticas de usuario
function bes_display_user_statistics() {
    if (!is_user_logged_in()) {
        return '<p>Debes estar logueado para ver esta página.</p>';
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'bes_banners';
    $user_id = get_current_user_id();

    $banners = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d",
        $user_id
    ));

    ob_start();
    ?>
    <div class="bes-user-panel">
        <h2>🚀 Panel de usuario UPinAds</h2>
        <div class="bes-info-box">
            <p>En nuestro sistema, cada impresión de tu banner te otorga 1 crédito, mientras que cada clic genera 20 créditos. Estos créditos se convierten automáticamente en más impresiones y clics para tus banners, asegurando un intercambio justo y optimizado para maximizar tu visibilidad. ¡Cuantos más créditos acumules, mayor será el alcance de tu campaña! 🚀</p>
        </div>
                <center><h3>🚀 Mis campañas</h3></center>

        <div class="bes-card-container">
            <?php foreach ($banners as $index => $banner): ?>
                <?php if ($index % 2 == 0): ?>
                    <div class="bes-card-row">
                <?php endif; ?>
                
                <div class="bes-card">
                    <div class="bes-card-header">
                        <h3>ID CAMPAÑA: <span class="banner-id"><?php echo $banner->id; ?></span></h3>
                        <img src="<?php echo esc_url($banner->banner_url); ?>" class="banner-image" alt="Banner">
                    </div>
                    <div class="bes-card-body">
                        <p><strong>URL del Banner:</strong> <a href="<?php echo esc_url($banner->banner_url); ?>" target="_blank"><?php echo esc_url($banner->banner_url); ?></a></p>
                        <hr class="bes-divider">
                        <p><strong>URL de la Campaña:</strong> <a href="<?php echo esc_url($banner->target_url); ?>" target="_blank"><?php echo esc_url($banner->target_url); ?></a></p>
                        <hr class="bes-divider">
                        <p><strong>Impresiones Recibidas:</strong> <?php echo $banner->impressions; ?></p>
                        <hr class="bes-divider">
                        <p><strong>Clicks Recibidos:</strong> <?php echo $banner->clicks; ?></p>
                        <hr class="bes-divider">
                        <p><strong>Créditos Disponibles:</strong> <?php echo $banner->credits; ?></p>
                        <hr class="bes-divider">
                        <p><strong>Aprobado:</strong> <?php echo $banner->approved ? 'Sí' : 'No'; ?></p>
                    </div>
                    <div class="bes-card-footer">
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="delete-form">
                            <?php wp_nonce_field('delete_banner_nonce', 'delete_nonce'); ?>
                            <input type="hidden" name="action" value="delete_banner">
                            <input type="hidden" name="banner_id" value="<?php echo esc_attr($banner->id); ?>">
                            <input type="submit" name="delete_banner" value="Borrar" class="button button-danger">
                        </form>
                    </div>
                </div>

                <?php if ($index % 2 == 1 || $index == count($banners) - 1): ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <style>
        body {
            background-color: #ffffff; /* Fondo blanco */
        }
        .bes-user-panel {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
        }
        .bes-user-panel h2 {
            margin-bottom: 20px;
            color: #003366;
            text-align: center;
            font-family: 'Arial', sans-serif;
        }
        .bes-info-box {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #003366;
            background-color: #e6f2ff;
            border-radius: 8px;
            font-family: 'Arial', sans-serif;
        }
        .bes-card-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .bes-card-row {
            display: flex;
            gap: 20px;
        }
        .bes-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: calc(50% - 10px);
            overflow: hidden;
        }
        .bes-card-header {
            text-align: center;
            background-color: #003366;
            padding: 10px;
        }
        .bes-card-header h3 {
            margin: 0;
            font-family: 'Arial', sans-serif;
            color: white; /* Texto en blanco */
        }
        .banner-id {
            color: white; /* Texto en blanco */
        }
        .banner-image {
            max-width: 100%;
            max-height: 150px;
            margin-top: 10px;
            border-radius: 4px;
        }
        .bes-card-body {
            padding: 15px;
            font-family: 'Arial', sans-serif;
        }
        .bes-card-body p {
            margin: 10px 0;
        }
        .bes-card-body a {
            color: #003366;
            text-decoration: none;
        }
        .bes-card-body a:hover {
            text-decoration: underline;
        }
        .bes-card-footer {
            padding: 15px;
            text-align: center;
        }
        .button-danger {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Arial', sans-serif;
        }
        .button-danger:hover {
            background-color: #c9302c;
        }
        .delete-form {
            display: inline-block;
        }
        .bes-divider {
            border: none;
            border-top: 1px solid #ddd;
            margin: 10px 0;
        }
    </style>
    <?php
    return ob_get_clean();
}

// Registrar el shortcode para mostrar las estadísticas de usuario
function bes_register_user_panel_shortcode() {
    add_shortcode('bes_user_statistics', 'bes_display_user_statistics');
}
add_action('init', 'bes_register_user_panel_shortcode');

// Manejar la solicitud de eliminación de banner
function bes_handle_delete_banner_request() {
    if (isset($_POST['delete_banner']) && check_admin_referer('delete_banner_nonce', 'delete_nonce')) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bes_banners';
        $banner_id = intval($_POST['banner_id']);
        $wpdb->delete($table_name, ['id' => $banner_id]);
    }
}
add_action('admin_post_delete_banner', 'bes_handle_delete_banner_request');
add_action('admin_post_nopriv_delete_banner', 'bes_handle_delete_banner_request');