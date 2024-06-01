<?php
if (!is_user_logged_in()) {
    echo "Você precisa estar logado para acessar esta página.";
    return;
}

$user_id = get_current_user_id();
$args = array(
    'post_type' => 'jogos',
    'author' => $user_id,
    'posts_per_page' => -1,
    'post_status' => 'publish',
);

$jogos = new WP_Query($args);
?>

<table id="meu-plugin-jogos-tabela">
    <thead>
        <tr>
            <th>Título</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($jogos->have_posts()) : ?>
            <?php while ($jogos->have_posts()) : $jogos->the_post(); ?>
                <tr>
                    <td><?php the_title(); ?></td>
                    <td>
                        <a href="<?php echo get_permalink(); ?>">Jogar</a> | <a href="/editar-jogo?jogo_id=<?php the_ID(); ?>">Editar</a> |
                        <a href="#" class="meu-plugin-jogos-excluir" data-jogo-id="<?php the_ID(); ?>">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="2">Nenhum jogo encontrado.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
function atualizarListaDeJogos() {
    const tabelaJogos = document.getElementById('meu-plugin-jogos-tabela');
    fetch(window.location.href)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const novaTabela = doc.getElementById('meu-plugin-jogos-tabela');
            tabelaJogos.replaceWith(novaTabela);
            adicionarEventosExcluir();
        })
        .catch(error => console.error('Erro ao atualizar a lista de jogos:', error));
}

function adicionarEventosExcluir() {
    const deleteLinks = document.querySelectorAll('.meu-plugin-jogos-excluir');

    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Tem certeza de que deseja excluir este jogo?')) {
                const jogoId = e.target.getAttribute('data-jogo-id');
                const formData = new FormData();
                formData.append('meu_plugin_jogos_excluir', jogoId);

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                    },
                })
                .then(() => atualizarListaDeJogos())
                .catch(error => console.error('Erro ao excluir o jogo:', error));
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    adicionarEventosExcluir();
});
</script>