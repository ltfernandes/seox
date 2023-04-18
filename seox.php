<?php
/**
 * Plugin Name: Seox
 * Plugin URI: https://www.google.com/
 * Description: Plugin que adiciona regiao e subregiao a taxonomia e altera a slug do post de acordo com as opcoes escolhidas
 * Version: 1.0
 * Author: Lucas Teles Fernandes
 * Author URI: https://www.linkedin.com/in/lucas-tf/
 */
require_once(dirname(__FILE__) . '/functions.php');

add_action( 'init', 'registra_taxonomia_regioes' );
add_filter( 'post_link', 'change_link', 1, 3 );
add_action( 'rest_api_init', 'register_route_regioes');
