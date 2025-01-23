<?php

function bes_admin_menu() {
    add_menu_page(
        'Gestión de Banners',
        'Banner Exchange',
        'manage_options',
        'bes_admin',
        'bes_admin_page',
        'dashicons-images-alt2',
        6
    );
}
add_action('admin_menu', 'bes_admin_menu');

function bes_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'bes_banners';

    // Verificar si se ha enviado una solicitud de aprobación, rechazo o eliminación
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['approve_banner'])) {
            $banner_id = intval($_POST['banner_id']);
            $wpdb->update(
                $table_name,
                ['approved' => 1],
                ['id' => $banner_id]
            );
        }

        if (isset($_POST['reject_banner'])) {
            $banner_id = intval($_POST['banner_id']);
            $wpdb->delete(
                $table_name,
                ['id' => $banner_id]
            );
        }

        if (isset($_POST['delete_banner'])) {
            $banner_id = intval($_POST['banner_id']);
            $wpdb->delete(
                $table_name,
                ['id' => $banner_id]
            );
        }

        if (isset($_POST['add_credits'])) {
            $user_id = intval($_POST['user_id']);
            $credits = intval($_POST['credits']);
            $wpdb->query($wpdb->prepare(
                "UPDATE $table_name SET credits = credits + %d WHERE user_id = %d",
                $credits, $user_id
            ));
        }
    }

    $banners = $wpdb->get_results("SELECT * FROM $table_name");
    $users = get_users();

    ?>
    <div class="wrap">
        <h1>Gestión de Banners</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Banner URL</th>
                    <th>Target URL</th>
                    <th>Impresiones</th>
                    <th>Clicks</th>
                    <th>Créditos</th>
                    <th>Aprobado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($banners as $banner): ?>
                    <tr>
                        <td><?php echo $banner->id; ?></td>
                        <td><?php echo get_userdata($banner->user_id)->user_login; ?></td>
                        <td><?php echo esc_url($banner->banner_url); ?></td>
                        <td><?php echo esc_url($banner->target_url); ?></td>
                        <td><?php echo $banner->impressions; ?></td>
                        <td><?php echo $banner->clicks; ?></td>
                        <td><?php echo $banner->credits; ?></td>
                        <td><?php echo $banner->approved ? 'Sí' : 'No'; ?></td>
                        <td>
                            <?php if (!$banner->approved): ?>
                                <form method="post" style="display:inline;">
                                    <?php wp_nonce_field('approve_banner_nonce', 'approve_nonce'); ?>
                                    <input type="hidden" name="banner_id" value="<?php echo esc_attr($banner->id); ?>">
                                    <input type="submit" name="approve_banner" value="Aprobar" class="button button-primary">
                                </form>
                                <form method="post" style="display:inline;">
                                    <?php wp_nonce_field('reject_banner_nonce', 'reject_nonce'); ?>
                                    <input type="hidden" name="banner_id" value="<?php echo esc_attr($banner->id); ?>">
                                    <input type="submit" name="reject_banner" value="Rechazar" class="button button-secondary">
                                </form>
                            <?php endif; ?>
                            <form method="post" style="display:inline;">
                                <?php wp_nonce_field('delete_banner_nonce', 'delete_nonce'); ?>
                                <input type="hidden" name="banner_id" value="<?php echo esc_attr($banner->id); ?>">
                                <input type="submit" name="delete_banner" value="Borrar" class="button button-danger">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Agregar Créditos</h2>
        <form method="post">
            <?php wp_nonce_field('add_credits_nonce', 'credits_nonce'); ?>
            <select name="user_id">
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo esc_attr($user->ID); ?>"><?php echo esc_html($user->user_login); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="credits" required>
            <input type="submit" name="add_credits" value="Agregar Créditos" class="button button-primary">
        </form>
    </div>
    <?php
}