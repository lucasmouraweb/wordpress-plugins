<?php

if (!is_user_logged_in()) {
    echo "Você precisa estar logado para acessar esta página.";
    return;
}

$user_id = get_current_user_id();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $memorias_da_aventura = sanitize_text_field($_POST['memorias_da_aventura']);
    $nome_jogador = sanitize_text_field($_POST['nome_jogador']);
    $genero_do_jogo = sanitize_text_field($_POST['genero_do_jogo']);
    $descricao_do_personagem = sanitize_text_field($_POST['descricao_do_personagem']);
    $raca = sanitize_text_field($_POST['raca']);
    $classe = sanitize_text_field($_POST['classe']);
    $nivel = sanitize_text_field($_POST['nivel']);
    $pontos_de_vida = sanitize_text_field($_POST['pontos_de_vida']);
    $forca = sanitize_text_field($_POST['forca']);
    $destreza = sanitize_text_field($_POST['destreza']);
    $constituicao = sanitize_text_field($_POST['constituicao']);
    $inteligencia = sanitize_text_field($_POST['inteligencia']);
    $sabedoria = sanitize_text_field($_POST['sabedoria']);
    $carisma = sanitize_text_field($_POST['carisma']);
    $equipamento = sanitize_text_field($_POST['equipamento']);
    $caracteristicas_especiais = sanitize_text_field($_POST['caracteristicas_especiais']);
    $missoes_realizadas = sanitize_text_field($_POST['missoes_realizadas']);
    $missao_atual = sanitize_text_field($_POST['missao_atual']);
    $local_atual = sanitize_text_field($_POST['local_atual']);
    $estilo_da_imagem = sanitize_text_field($_POST['estilo_da_imagem']);
    $modo_de_jogo = sanitize_text_field($_POST['modo_de_jogo']);
    
    if (isset($_POST['jogo_id'])) {
        $jogo_id = intval($_POST['jogo_id']);
        $jogo = array(
            'ID' => $jogo_id,
            'post_title' => sanitize_text_field($_POST['jogo_titulo']),
            'post_type' => 'jogos',
            'post_status' => 'publish',
            'post_author' => $user_id,
        );
        $post_id = wp_update_post($jogo);
        if ($post_id) {
            update_post_meta($post_id, 'memorias_da_aventura', $memorias_da_aventura);
            update_post_meta($post_id, 'nome_jogador', $nome_jogador);
            update_post_meta($post_id, 'genero_do_jogo', $genero_do_jogo);
            update_post_meta($post_id, 'descricao_do_personagem', $descricao_do_personagem);
            update_post_meta($post_id, 'raca', $raca);
            update_post_meta($post_id, 'classe', $classe);
            update_post_meta($post_id, 'nivel', $nivel);
            update_post_meta($post_id, 'pontos_de_vida', $pontos_de_vida);
            update_post_meta($post_id, 'forca', $forca);
            update_post_meta($post_id, 'destreza', $destreza);
            update_post_meta($post_id, 'constituicao', $constituicao);
            update_post_meta($post_id, 'inteligencia', $inteligencia);
            update_post_meta($post_id, 'sabedoria', $sabedoria);
            update_post_meta($post_id, 'carisma', $carisma);
            update_post_meta($post_id, 'equipamento', $equipamento);
            update_post_meta($post_id, 'caracteristicas_especiais', $caracteristicas_especiais);
            update_post_meta($post_id, 'missoes_realizadas', $missoes_realizadas);
            update_post_meta($post_id, 'missao_atual', $missao_atual);
            update_post_meta($post_id, 'local_atual', $local_atual);
            update_post_meta($post_id, 'estilo_da_imagem', $estilo_da_imagem);
            update_post_meta($post_id, 'modo_de_jogo', $modo_de_jogo);

            echo '<script>window.location.href = "/jogos/";</script>';
            exit;
        } else {
            echo "Erro ao atualizar o jogo.";
            header("Refresh:0");
            return;
        }
    } else {
        $jogo_titulo = sanitize_text_field($_POST['jogo_titulo']);
        
        if (!empty($jogo_titulo)) {
            $jogo = array(
                'post_title' => $jogo_titulo,
                'post_type' => 'jogos',
                'post_status' => 'publish',
                'post_author' => $user_id,
            );
            $post_id = wp_insert_post($jogo);
            if ($post_id) {
                update_post_meta($post_id, 'memorias_da_aventura', $memorias_da_aventura);
                update_post_meta($post_id, 'nome_jogador', $nome_jogador);
                update_post_meta($post_id, 'genero_do_jogo', $genero_do_jogo);
                update_post_meta($post_id, 'descricao_do_personagem', $descricao_do_personagem);
                update_post_meta($post_id, 'raca', $raca);
                update_post_meta($post_id, 'classe', $classe);
                update_post_meta($post_id, 'nivel', $nivel);
                update_post_meta($post_id, 'pontos_de_vida', $pontos_de_vida);
                update_post_meta($post_id, 'forca', $forca);
                update_post_meta($post_id, 'destreza', $destreza);
                update_post_meta($post_id, 'constituicao', $constituicao);
                update_post_meta($post_id, 'inteligencia', $inteligencia);
                update_post_meta($post_id, 'sabedoria', $sabedoria);
                update_post_meta($post_id, 'carisma', $carisma);
                update_post_meta($post_id, 'equipamento', $equipamento);
                update_post_meta($post_id, 'caracteristicas_especiais', $caracteristicas_especiais);
                update_post_meta($post_id, 'missoes_realizadas', $missoes_realizadas);
                update_post_meta($post_id, 'missao_atual', $missao_atual);
                update_post_meta($post_id, 'local_atual', $local_atual);
                update_post_meta($post_id, 'estilo_da_imagem', $estilo_da_imagem);
                update_post_meta($post_id, 'modo_de_jogo', $modo_de_jogo);

                echo '<script>window.location.href = "/jogos/";</script>';
                exit;
            } else {
                echo "Erro ao salvar o jogo.";
                header("Refresh:0");
                return;
            }
        }
    }
}

if (isset($_GET['jogo_id'])) {
    $jogo_id = intval($_GET['jogo_id']);
    $jogo = get_post($jogo_id);
    if ($jogo && $jogo->post_author == $user_id) {
        $jogo_titulo = $jogo->post_title;

        $memorias_da_aventura = get_post_meta($jogo_id, 'memorias_da_aventura', true);
        $nome_jogador = get_post_meta($jogo_id, 'nome_jogador', true);
        $genero_do_jogo = get_post_meta($jogo_id, 'genero_do_jogo', true);
        $descricao_do_personagem = get_post_meta($jogo_id, 'descricao_do_personagem', true);
        $raca = get_post_meta($jogo_id, 'raca', true);
        $classe = get_post_meta($jogo_id, 'classe', true);
        $nivel = get_post_meta($jogo_id, 'nivel', true);
        $pontos_de_vida = get_post_meta($jogo_id, 'pontos_de_vida', true);
        $forca = get_post_meta($jogo_id, 'forca', true);
        $destreza = get_post_meta($jogo_id, 'destreza', true);
        $constituicao = get_post_meta($jogo_id, 'constituicao', true);
        $inteligencia = get_post_meta($jogo_id, 'inteligencia', true);
        $sabedoria = get_post_meta($jogo_id, 'sabedoria', true);
        $carisma = get_post_meta($jogo_id, 'carisma', true);
        $equipamento = get_post_meta($jogo_id, 'equipamento', true);
        $caracteristicas_especiais = get_post_meta($jogo_id, 'caracteristicas_especiais', true);
        $missoes_realizadas = get_post_meta($jogo_id, 'missoes_realizadas', true);
        $missao_atual = get_post_meta($jogo_id, 'missao_atual', true);
        $local_atual = get_post_meta($jogo_id, 'local_atual', true);
        $estilo_da_imagem = get_post_meta($jogo_id, 'estilo_da_imagem', true);
        $modo_de_jogo = get_post_meta($jogo_id, 'modo_de_jogo', true);
    } else {
        echo "Você não tem permissão para editar este jogo.";
        return;
    }
} else {
    $jogo_id = 0;
    $jogo_titulo = '';
    
    // Inicializar todas as variáveis com valores vazios
    $memorias_da_aventura = '';
    $nome_jogador = '';
    $genero_do_jogo = '';
    $descricao_do_personagem = '';
    $raca = '';
    $classe = '';
    $nivel = '';
    $pontos_de_vida = '';
    $forca = '';
    $destreza = '';
    $constituicao = '';
    $inteligencia = '';
    $sabedoria = '';
    $carisma = '';
    $equipamento = '';
    $caracteristicas_especiais = '';
    $missoes_realizadas = '';
    $missao_atual = '';
    $local_atual = '';
    $estilo_da_imagem = '';
    $modo_de_jogo = '';
}
?>

<p style="padding:8px;border-radius:4px;background:#278770;margin-bottom: 25px;">
    O <b>Título, Gênero e Nome</b> são obrigatórios por serem essenciais para o jogo. Os demais podem ser adicionado editando o jogo a qualquer momento.</p>

<form method="post">
    <input type="hidden" name="jogo_id" value="<?php echo $jogo_id; ?>">
    <label for="jogo_titulo">Título do jogo:</label>
    <p>Insira o título do seu jogo. Exemplo: 'A Jornada do Dragão'. O título não interfere no jogo.</p>
    <input type="text" name="jogo_titulo" id="jogo_titulo" placeholder="Escreva o título do jogo aqui...(máximo de 80 caracteres)" value="<?php echo esc_attr($jogo_titulo); ?>" maxlength="80" required>
    
    <label for="genero_do_jogo">Gênero do Jogo:</label>
     <p>Digite o gênero, tema ou assunto do jogo. Exemplo: 'Fantasia Medieval' ou uma série ou filme favorito. As possibilidades são infinitas.</p>
    <input type="text" name="genero_do_jogo" id="genero_do_jogo" placeholder="Digite o gênero, tema ou assunto do jogo...(máximo de 200 caracteres)" value="<?php echo esc_attr($genero_do_jogo); ?>" maxlength="200" required>

    <label for="nome_jogador">Nome do Personagem:</label>
     <p>Digite o nome do seu personagem. Exemplo: 'Sirius, o Valente'.</p>
    <input type="text" name="nome_jogador" id="nome_jogador" placeholder="Digite o nome do personagem...(máximo de 40 caracteres)" value="<?php echo esc_attr($nome_jogador); ?>" maxlength="40" required>
    
    
    <label for="modo_de_jogo">Estilo do RPG:</label>
    <p>Escolha o estilo do jogo, no modo RPG de Mesa você interpreta e escreve todas as suas ações. No modo Livro jogo você tem sempre 4 escolhas predefinidas para escolher como suas ações.</p>
<select name="modo_de_jogo" id="modo_de_jogo" required>
    <option value="mesa" <?php echo ($modo_de_jogo === 'mesa') ? 'selected' : ''; ?>>RPG de mesa</option>
    <option value="livro" <?php echo ($modo_de_jogo === 'livro') ? 'selected' : ''; ?>>Livro jogo</option>
</select>
    
    
   
<label for="descricao_do_personagem">Descrição do Personagem:</label>
 <p>Descreva seu personagem. Exemplo: 'Um cavaleiro destemido com um coração de ouro'.</p>
<textarea name="descricao_do_personagem" id="descricao_do_personagem" placeholder="Descreva o personagem...(máximo de 400 caracteres)"  maxlength="400"  ><?php echo esc_textarea($descricao_do_personagem); ?></textarea>

<div style="display:flex;width: 100%;">

    <div style="padding-right:25px;width: 100%;">
            <label for="raca">Raça:</label>
            <p>Informe a raça do personagem.</p>
            <input type="text" id="raca" name="raca" placeholder="Insira raça" value="<?php echo esc_attr($raca); ?>">
            <label for="classe">Classe:</label>
            <p>Informe a classe do personagem.</p> 
            <input type="text" id="classe" name="classe" placeholder="Insira classe"  value="<?php echo esc_attr($classe); ?>">
    </div>

    <div style="width: 100%;">
            <label for="nivel">Nível:</label>
            <p>Informe o nível atual do personagem.</p> 
            <input type="text" id="nivel" name="nivel" placeholder="Insira nível"  value="<?php echo esc_attr($nivel); ?>">
            <label for="pontos_de_vida">Pontos de Vida:</label>
            <p>Informe os pontos de vida total do personagem.</p>
            <input type="text" id="pontos_de_vida" name="pontos_de_vida" placeholder="Insira pontos de vida"  value="<?php echo esc_attr($pontos_de_vida); ?>">
    </div>

</div>

<label for="nome_jogador">Atributos</label>

<div style="display:flex;width: 100%;padding: 35px;
border: 1px solid #424242;
border-radius: 8px;
margin-bottom: 30px;
margin-top: 10px;">
   
    <div style="padding-right:25px;width: 100%;">
        <label for="forca">Força:</label>
        <p>Informe o atributo de força do personagem.</p>
        <input type="text" id="forca" name="forca" placeholder="Insira força"  value="<?php echo esc_attr($forca); ?>">
        <label for="destreza">Destreza:</label>
        <p>Informe o atributo de destreza do personagem.</p> 
        <input type="text" id="destreza" name="destreza" placeholder="Insira destreza"  value="<?php echo esc_attr($destreza); ?>">
        <label for="constituicao">Constituição:</label>
        <p>Informe o atributo de constituição do personagem.</p>
        <input type="text" id="constituicao" name="constituicao" placeholder="Insira constituição"  value="<?php echo esc_attr($constituicao); ?>">
    </div>
    
    <div style="width: 100%;">
        <label for="inteligencia">Inteligência:</label>
        <p>Informe o atributo de inteligência do personagem.</p>
        <input type="text" id="inteligencia" name="inteligencia" placeholder="Insira inteligência"  value="<?php echo esc_attr($inteligencia); ?>">
        <label for="sabedoria">Sabedoria:</label>
        <p>Informe o atributo de sabedoria do personagem.</p>
        <input type="text" id="sabedoria" name="sabedoria" placeholder="Insira sabedoria"  value="<?php echo esc_attr($sabedoria); ?>">
        <label for="carisma">Carisma:</label>
        <p>Informe o atributo de carisma do personagem.</p>
        <input type="text" id="carisma" name="carisma" placeholder="Insira carisma"  value="<?php echo esc_attr($carisma); ?>">
    </div>

</div>

<label for="equipamento">Equipamento:</label>
<p>Informe o equipamento atual do personagem.</p>
<input type="text" id="equipamento" name="equipamento" placeholder="Insira equipamento"  value="<?php echo esc_attr($equipamento); ?>">
<label for="caracteristicas_especiais">Características Especiais:</label>
<p>Descreva as características especiais do personagem.</p>
<input type="text" id="caracteristicas_especiais" name="caracteristicas_especiais" placeholder="Insira características especiais"  value="<?php echo esc_attr($caracteristicas_especiais); ?>">
<label for="missoes_realizadas">Missões Realizadas:</label>
<p>Liste as missões já realizadas pelo personagem.</p>
<input type="text" id="missoes_realizadas" name="missoes_realizadas" placeholder="Insira missões realizadas"  value="<?php echo esc_attr($missoes_realizadas); ?>">
<label for="missao_atual">Missão Atual:</label>
<p>Informe a missão atual do personagem.</p>
<input type="text" id="missao_atual" name="missao_atual" placeholder="Insira missão atual"  value="<?php echo esc_attr($missao_atual); ?>">

<label for="missao_atual">Local atual:</label>
<p>Informe o local atual do personagem.</p>
<input type="text" id="local_atual" name="local_atual" placeholder="Insira o local atual"  value="<?php echo esc_attr($local_atual); ?>">

<label for="memorias_da_aventura">Memórias da Aventura:</label>
<p style="margin-bottom: 20px">Digite as memórias da aventura, momentos importantes da história que precisam sempre ser lembrados. Exemplo: 'Sirius encontrou a espada mágica na floresta'.</p>
<textarea name="memorias_da_aventura" id="memorias_da_aventura" placeholder="Digite a memória da IA...(máximo de 2000 caracteres)" maxlength="2000"><?php echo esc_textarea($memorias_da_aventura); ?></textarea>
    
<button type="submit"><?php echo $jogo_id ? 'Atualizar Jogo' : 'Criar Jogo'; ?></button>
<a href="/jogos/" class="cancel-button">Cancelar</a>
</form> 