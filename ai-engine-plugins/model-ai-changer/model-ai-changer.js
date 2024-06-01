jQuery(document).ready(function($) {
    $('#model-ai-select').on('change', function() {
        var model_ai = $(this).val();
        $.ajax({
            url: model_ai_changer_ajax.url,
            type: 'POST',
            data: {
                action: 'model_ai_changer_update',
                model_ai: model_ai
            },
            success: function(response) {
                // Opcional: exibir uma mensagem de sucesso
                console.log('Modelo de IA atualizado com sucesso!');
            }
        });
    });
});