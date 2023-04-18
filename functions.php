<?php
require_once(dirname(__FILE__) . '/vendor/autoload.php');

/**
 * Função que cria a taxonomia personalizada Regioes, que permite adicionar categorias filhas
 * 
 */
function registra_taxonomia_regioes()
{
    $labels = array(
        'name'              => ('Regioes'),
        'singular_name'     => ('Regioes'),
        'search_items'      => ('Buscar regiões'),
        'all_items'         => ('Todas regioes'),
        'parent_item'       => ('Regiões pai'),
        'parent_item_colon' => ('Regiões pai:'),
        'edit_item'         => ('Editar região'),
        'update_item'       => ('Atualizar região'),
        'add_new_item'      => ('Criar região'),
        'new_item_name'     => ('Criar região'),
        'menu_name'         => ('Regiões'),
    );
    $args   = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields')
    );
    register_taxonomy('regioes', ['post'], $args);
}

/**
 * Altera a slug do post para /regiao/ ou regiao/subregiao/, caso se aplique
 * 
 * @param mixed $post_link
 * @param int $id
 * 
 */
function change_link($post_link, $id = 0)
{
    $post = get_post($id);
    $childSlug = $parentSlug = '';

    if ($post->post_type == 'post') {

        if (is_object($post)) {
            $terms = wp_get_post_terms($post->ID, array('regioes'));

            if ($terms) {
                foreach ($terms as $term) {

                    if ($term->parent == 0) {
                        $parentSlug = $term->slug;
                    } else {
                        $childSlug = $term->slug;
                    }
                }
                $slug = $childSlug ? $parentSlug . "/" . $childSlug : $parentSlug;
                return str_replace('%regioes%', $slug, $post_link);
            }
        }
    }
    return str_replace('%regioes%', 'regiao', $post_link);
}

/**
 * Registra a rota wp/v2/posts/nome-da-regiao para realizar a consulta dos posts com a regiao definida
 */
function register_route_regioes()
{
    register_rest_route('wp/v2', '/getposts=(?P<regiao_slug>[a-zA-Z0-9-]+)', array(
        'methods' => 'GET',
        'callback' => 'get_posts_by_regioes',
    ));
}

/**
 * @param mixed $request
 * 
 * retorna informacoes de posts com a regiao pesquisada
 */
function get_posts_by_regioes($request)
{
    $regiao_slug = $request->get_param('regiao_slug');
    $posts = get_posts(array(
        'post_type' => 'post',
        'tax_query' => array(
            array(
                'taxonomy' => 'regioes',
                'field' => 'slug',
                'terms' => $regiao_slug,
            ),
        ),
    ));
    $data = array();
    foreach ($posts as $post) {
        $regioes = wp_get_post_terms( $post->ID, array('regioes'));
        $data[] = [
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'regioes' => $regioes
        ];
    }
    return json_decode(json_encode($data));
}

