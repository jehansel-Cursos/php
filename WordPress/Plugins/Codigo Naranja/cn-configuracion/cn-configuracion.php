<?php
/*
Plugin Name: Plugin con parámetros
Plugin URI: https://www.paginaswebrr.com
Description: Pantalla de parámetros
Version: 1.0
Author: Juan Roldán
Author URI: https://www.paginaswebrr.com
License: GPLv2
*/

# Agregar información predeterminada al activar el plugin
/*
Este Script se correra en 3 momentos:
1. Al activar por primera vez
2. Al actualizar
3. Al reactivar
*/
/*
https://developer.wordpress.org/reference/functions/register_activation_hook/
register_activation_hook( string $file, callable $callback )
Set the activation hook for a plugin.
*/
register_activation_hook(__FILE__, 'cn_set_default_options');

function cn_set_default_options()
{
    /*
https://developer.wordpress.org/reference/functions/get_option/
get_option( string $option, mixed $default = false )
Retrieves an option value based on an option name.

https://developer.wordpress.org/reference/functions/add_option/
add_option( string $option, mixed $value = '', string $deprecated = '', string|bool $autoload = 'yes' )
Adds a new option.
*/

    // Revisar si ya se había activado antes
    if (get_option('cn_ga_cuenta') === false) {
        add_option('cn_ga_cuenta', 'UA-0000000-0');
    }
}

# Agregar esta configuración al menú
function cn_menu_ajustes()
{
    /*
    https://developer.wordpress.org/reference/functions/add_options_page/
    add_options_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $callback = '', int $position = null )
    Adds a submenu page to the Settings main menu.
    */
    $pagina_opciones = add_options_page(
        'Configuración Google Analytics', // Título de la página
        'Código de Google Analytics', // Nombre del menú
        'manage_options', // Nivel de acceso, solo usuarios (https://wordpress.org/support/article/roles-and-capabilities/)
        'cn-conf-ga', // slug
        'cn_genera_pagina' // Función que procesará todo
    );
}

/*
https://developer.wordpress.org/reference/hooks/admin_menu/
do_action( 'admin_menu', string $context )
Fires before the administration menu loads in the admin.
*/
add_action('admin_menu', 'cn_menu_ajustes');

// Generar el código de la pagina de actualización
function cn_genera_pagina()
{
    // Conseguir el valor de GA
    $codigo_ga = get_option('cn_ga_cuenta');
?>
    <div class="wrap">
        <h2>Google Analytics</h2>

        <form method="post" action="admin-post.php">
            <input type="hidden" name="action" value="guardar_ga" />

            <!-- 
                Mejorar la seguridad 
                https://developer.wordpress.org/reference/functions/wp_nonce_field/
                wp_nonce_field( int|string $action = -1, string $name = '_wpnonce', bool $referer = true, bool $echo = true )
                Retrieve or display nonce hidden field for forms.
            -->
            <?php wp_nonce_field('token_ga'); ?>

            Código de Google Analytics:
            <!-- 
                https://developer.wordpress.org/reference/functions/esc_html/
                esc_html( string $text )
                Escaping for HTML blocks.
            -->
            <input type="text" name="codigo_ga" value="<?php echo esc_html($codigo_ga) ?>" />
            <br />
            <input type="submit" value="Guardar" class="button-primary" />
        </form>
    </div>
<?php
}

add_action('admin_post_guardar_ga', 'cn_guardar_ga');

function cn_guardar_ga()
{
    // Revisar el permios de autorización
    if (!current_user_can('manage_options')) {
        wp_die('Not allowed');
    }

    // Revisar el token que creamos antes
    check_admin_referer('token_ga');

    // Limpiar valor, para prevenir problemas de seguridad
    $codigo_ga = sanitize_text_field($_POST['codigo_ga']);

    // Guardar en la base de datos
    update_option('cn_ga_cuenta', $codigo_ga);

    // Regresamos a la página de ajustes
    wp_redirect(add_query_arg(
        'page',
        'cn_conf_ga',
        admin_url('options-general.php')
    ));
    exit;
}
