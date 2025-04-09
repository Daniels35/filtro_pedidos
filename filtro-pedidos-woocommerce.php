<?php
/*
Plugin Name: Filtro de Pedidos WooCommerce
Description: Agrega una sección personalizada para filtrar pedidos por fecha y estado desde el menú de WooCommerce.
Author: Daniel Diaz Tag Marketing
Version: 1.0
*/

if (!defined('ABSPATH')) exit;

add_action('admin_menu', 'ddtm_agregar_submenu_filtro_pedidos');

function ddtm_agregar_submenu_filtro_pedidos() {
    add_submenu_page(
        'woocommerce',
        'Filtro de Pedidos',
        'Filtro de Pedidos',
        'manage_woocommerce',
        'filtro-pedidos',
        'ddtm_pagina_filtro_pedidos',
        56 // posición opcional
    );
}

function ddtm_pagina_filtro_pedidos() {
    ?>
    <div class="wrap">
        <h1>Filtro de Pedidos</h1>
        <form method="GET">
            <input type="hidden" name="page" value="filtro-pedidos">
            <label>Desde: <input type="date" name="desde" value="<?php echo esc_attr($_GET['desde'] ?? '') ?>"></label>
            <label>Hasta: <input type="date" name="hasta" value="<?php echo esc_attr($_GET['hasta'] ?? '') ?>"></label>
            <label>Estado:
                <select name="estado">
                    <option value="">Todos</option>
                    <?php
                    $estados = wc_get_order_statuses();
                    foreach ($estados as $clave => $nombre) {
                        $selected = (isset($_GET['estado']) && $_GET['estado'] == $clave) ? 'selected' : '';
                        echo "<option value='{$clave}' {$selected}>{$nombre}</option>";
                    }
                    ?>
                </select>
            </label>
            <input type="submit" class="button button-primary" value="Filtrar">
        </form>
        <hr>
        <?php
        if (isset($_GET['desde']) || isset($_GET['hasta']) || isset($_GET['estado'])) {
            $args = [
                'limit' => -1,
                'status' => $_GET['estado'] ?: array_keys(wc_get_order_statuses()),
            ];
            if (!empty($_GET['desde']) && !empty($_GET['hasta'])) {
                $args['date_created'] = $_GET['desde'] . '...' . $_GET['hasta'];
            }

            $orders = wc_get_orders($args);
            if (!empty($orders)) {
                echo "<table class='widefat fixed striped'>";
                echo "<thead><tr><th>ID</th><th>Fecha</th><th>Estado</th><th>Total</th><th>Cliente</th></tr></thead><tbody>";
                foreach ($orders as $order) {
                    echo "<tr>
                        <td><a href='" . esc_url(get_edit_post_link($order->get_id())) . "'>#{$order->get_id()}</a></td>
                        <td>" . $order->get_date_created()->date('Y-m-d') . "</td>
                        <td>" . wc_get_order_status_name($order->get_status()) . "</td>
                        <td>" . wc_price($order->get_total()) . "</td>
                        <td>" . $order->get_formatted_billing_full_name() . "</td>
                    </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No se encontraron pedidos para los filtros seleccionados.</p>";
            }
        }
        ?>
    </div>
    <?php
}
