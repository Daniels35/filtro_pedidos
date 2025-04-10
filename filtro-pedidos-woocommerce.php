<?php
/*
Plugin Name: Filtro de Pedidos WooCommerce
Description: Agrega una sección en WooCommerce para filtrar y exportar pedidos por fecha y estado. Desarrollado por Daniel Diaz (Tag Marketing).
Author: Daniel Diaz Tag Marketing
Author URI: https://www.linkedin.com/in/danielsdiaz35/
Plugin URI: https://tagmarketingdigital.com/
Version: 1.3
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
        56
    );
}

function ddtm_pagina_filtro_pedidos() {
    // Si se pidió exportar, procesamos antes de cualquier salida
    if (isset($_GET['exportar']) && $_GET['exportar'] === '1') {
        $args = [
            'limit' => -1,
            'status' => $_GET['estado'] ?: array_keys(wc_get_order_statuses()),
        ];
        if (!empty($_GET['desde']) && !empty($_GET['hasta'])) {
            $args['date_created'] = $_GET['desde'] . '...' . $_GET['hasta'];
        }
        $orders = wc_get_orders($args);
        ddtm_exportar_csv($orders); // Exporta a CSV y detiene la ejecución
        return;
    }
    ?>
    <div class="wrap">
        <h1>Filtro de Pedidos</h1>
        <!-- Se agregó el siguiente título visual -->
        <h2 style="text-align:center;">Daniel Diaz Tag Marketing Digital</h2>
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
                echo '<form method="GET">';
                echo '<input type="hidden" name="page" value="filtro-pedidos">';
                echo '<input type="hidden" name="desde" value="' . esc_attr($_GET['desde']) . '">';
                echo '<input type="hidden" name="hasta" value="' . esc_attr($_GET['hasta']) . '">';
                echo '<input type="hidden" name="estado" value="' . esc_attr($_GET['estado']) . '">';
                echo '<input type="hidden" name="exportar" value="1">';
                echo '<p><input type="submit" class="button" value="Exportar a CSV"></p>';
                echo '</form>';

                echo "<table class='widefat fixed striped'>";
                echo "<thead><tr><th>ID</th><th>Fecha</th><th>Estado</th><th>Total</th><th>Cliente</th><th>Correo</th><th>Teléfono</th><th>Tipo Doc</th><th>Doc</th></tr></thead><tbody>";
                foreach ($orders as $order) {
                    $tipo_doc = $order->get_meta('billing_tipodocumento');
                    $num_doc = $order->get_meta('billing_numerodocumento');
                    echo "<tr>
                        <td><a href='" . esc_url(get_edit_post_link($order->get_id())) . "'>#{$order->get_id()}</a></td>
                        <td>" . $order->get_date_created()->date('Y-m-d') . "</td>
                        <td>" . wc_get_order_status_name($order->get_status()) . "</td>
                        <td>" . wc_price($order->get_total()) . "</td>
                        <td>" . $order->get_formatted_billing_full_name() . "</td>
                        <td>" . $order->get_billing_email() . "</td>
                        <td>" . $order->get_billing_phone() . "</td>
                        <td>" . esc_html($tipo_doc) . "</td>
                        <td>" . esc_html($num_doc) . "</td>
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

function ddtm_exportar_csv($orders) {
    $fecha = date('Y-m-d_H-i-s');

    // Limpia cualquier contenido que se haya enviado previamente.
    if (ob_get_length()) {
        ob_clean();
        flush();
    }

    header('Content-Type: text/csv');
    header("Content-Disposition: attachment;filename=pedidos_filtrados_{$fecha}.csv");
    $salida = fopen('php://output', 'w');

    // Agrega BOM para asegurar que Excel interprete correctamente el UTF-8
    fputs($salida, "\xEF\xBB\xBF");

    // Escribe la cabecera del CSV
    fputcsv($salida, [
        'ID', 'Fecha', 'Estado', 'Total', 'Cliente', 'Correo', 'Teléfono', 'Tipo Documento', 'Número Documento'
    ]);

    // Recorre y escribe cada pedido
    foreach ($orders as $order) {
        fputcsv($salida, [
            $order->get_id(),
            $order->get_date_created()->date('Y-m-d H:i:s'),
            wc_get_order_status_name($order->get_status()),
            $order->get_total(),
            $order->get_formatted_billing_full_name(),
            $order->get_billing_email(),
            $order->get_billing_phone(),
            $order->get_meta('billing_tipodocumento'),
            $order->get_meta('billing_numerodocumento'),
        ]);
    }

    fclose($salida);
    exit;
}

