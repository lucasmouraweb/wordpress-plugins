(function ($) {
    $(document).ready(function () {
        function updateBackgroundImage() {
            var $imageGen = $('#image-gen');
            var $chatCode = $('#chat-code');

            if ($imageGen.length && $chatCode.length) {
                var $chatCodeContent = $chatCode.find('.mwai-content');

                if ($chatCodeContent.length) {
                    var $lastImage = $imageGen.find('img').not('.mwai-open-button img').last();

                    if ($lastImage.length) {
                        var imageUrl = $lastImage.attr('src');
                        $chatCodeContent.css('background-image', 'url(' + imageUrl + ')');
                    } else {
                        // Remover o background-image se não houver imagens
                        $chatCodeContent.css('background-image', 'none');
                    }
                }
            }
        }

        // Chama a função inicialmente
        updateBackgroundImage();

        // Observa mudanças no DOM para atualizar o background-image quando necessário
        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.type === 'childList') {
                    updateBackgroundImage();
                }
            });
        });

        var observerConfig = {
            childList: true,
            subtree: true
        };

        var targetNode = document.getElementById('image-gen');
        observer.observe(targetNode, observerConfig);
    });
})(jQuery);