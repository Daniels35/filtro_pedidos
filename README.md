# 📊 Filtro de Pedidos WooCommerce

**Herramienta administrativa para la generación de reportes y exportación de ventas.**

Este plugin añade un panel dedicado dentro de WooCommerce para filtrar pedidos de manera granular (por fecha y estado). Su función principal es permitir a los administradores visualizar rápidamente la información clave de los clientes y exportar estos datos a archivos CSV compatibles con Excel, incluyendo campos personalizados de identificación.

## 📋 Características Principales

### 🛠️ Gestión Administrativa
* **Integración Nativa:** Añade un submenú llamado "Filtro de Pedidos" directamente bajo la pestaña principal de **WooCommerce** en el panel de administración.
* **Filtros Combinados:** Permite buscar pedidos seleccionando un rango de fechas (Desde/Hasta) y un estado específico del pedido (ej. Completado, Procesando, Fallido) o todos los estados simultáneamente.

### 📉 Exportación y Datos
* **Tabla de Vista Rápida:** Muestra los resultados en una tabla limpia con datos esenciales: ID, Fecha, Estado, Total, Nombre del Cliente, Correo y Teléfono.
* **Campos Personalizados:** Está optimizado para regiones que requieren identificación fiscal, extrayendo automáticamente los metadatos `billing_tipodocumento` y `billing_numerodocumento`.
* **Exportación CSV (Excel Friendly):** Genera archivos `.csv` que incluyen una marca de orden de bytes (BOM) para garantizar que los caracteres especiales (tildes, ñ) se visualicen correctamente al abrir el archivo en Microsoft Excel.

## ⚙️ Instrucciones de Uso

1.  Ve al menú **WooCommerce > Filtro de Pedidos** en el administrador.
2.  **Filtrar:**
    * Selecciona la "Fecha de inicio" y "Fecha de fin".
    * Elige el estado del pedido (opcional).
    * Haz clic en **"Filtrar"** para ver los resultados en pantalla.
3.  **Exportar:**
    * Una vez filtrados los datos, aparecerá un botón **"Exportar a CSV"**.
    * Haz clic para descargar el archivo automáticamente con la fecha y hora en el nombre.

## 📂 Estructura del Plugin

Este es un plugin de archivo único ("Single-file plugin"), lo que lo hace muy ligero y fácil de mantener:

* `filtro-pedidos-woocommerce.php`: Contiene toda la lógica:
    * Registro del menú de administración.
    * Renderizado del formulario y la tabla HTML.
    * Lógica de filtrado con `wc_get_orders()`.
    * Función `ddtm_exportar_csv` para la generación y descarga del archivo.

## 🚀 Instalación

1.  Sube el archivo `filtro-pedidos-woocommerce.php` (o la carpeta que lo contenga) al directorio `/wp-content/plugins/`.
2.  Activa el plugin desde el panel de WordPress.
3.  Accede a la nueva opción en el menú de WooCommerce.

## 💻 Shortcode

*Este plugin funciona exclusivamente en el panel de administración (Backend) y no requiere shortcodes para el Frontend.*

---
**Versión:** 1.3
**Autor:** Daniel Diaz - Tag Marketing Digital
**Tecnología:** PHP, WooCommerce CRUD classes.
