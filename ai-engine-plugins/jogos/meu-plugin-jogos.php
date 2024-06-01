<?php
/*
Plugin Name: Jogos
Description: Um plugin para criar, editar e excluir custom posts chamados 'jogos' no front-end do WordPress.
Version: 1.4
Author: Lucas Moura
*/

// Registrando o custom post type 'jogos'
function meu_plugin_jogos_post_type() {
    $args = array(
        'public' => true,
        'label' => 'Jogos',
        'supports' => array('title', 'editor', 'custom-fields'),
        'show_in_rest' => true,
    );

    register_post_type('jogos', $args);
}
add_action('init', 'meu_plugin_jogos_post_type');

// Registrando os campos personalizados
function meu_plugin_jogos_register_meta() {
    $fields = array(
        'memorias_da_aventura',
        'nome_jogador',
        'genero_do_jogo',
        'descricao_do_personagem',
        'checkpoint',
        'raca',
        'classe',
        'nivel',
        'pontos_de_vida',
        'forca',
        'destreza',
        'constituicao',
        'inteligencia',
        'sabedoria',
        'carisma',
        'equipamento',
        'caracteristicas_especiais',
        'missoes_realizadas',
        'missao_atual',
        'local_atual',
        'estilo_da_imagem',
        'modo_de_jogo',
    );

    foreach ($fields as $field) {
        register_post_meta('jogos', $field, array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ));
    }
}
add_action('init', 'meu_plugin_jogos_register_meta');

// Registrando os shortcodes
function meu_plugin_jogos_shortcode_editor() {
    ob_start();
    include 'formulario-jogos.php';
    return ob_get_clean();
}
add_shortcode('meu_plugin_jogos_editor', 'meu_plugin_jogos_shortcode_editor');

function meu_plugin_jogos_shortcode_lista() {
    ob_start();
    include 'lista-jogos.php';
    return ob_get_clean();
}
add_shortcode('meu_plugin_jogos_lista', 'meu_plugin_jogos_shortcode_lista');

// Excluindo o jogo
function meu_plugin_jogos_excluir() {
    if (isset($_POST['meu_plugin_jogos_excluir'])) {
        $jogo_id = intval($_POST['meu_plugin_jogos_excluir']);
        $jogo = get_post($jogo_id);
        $user_id = get_current_user_id();

        if ($jogo && $jogo->post_author == $user_id) {
            wp_delete_post($jogo_id, true);
            wp_send_json_success('Jogo excluído com sucesso!');
        } else {
            wp_send_json_error('Você não tem permissão para excluir este jogo.');
        }
        die();
    }
}
add_action('init', 'meu_plugin_jogos_excluir');
?>