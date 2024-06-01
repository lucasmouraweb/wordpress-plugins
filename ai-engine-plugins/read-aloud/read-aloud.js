var currentAudio = null;
var disablingAutoRead = false;

function cleanText(text) {
    return text;
}

jQuery(document).ready(function ($) {
    if (!$('body').hasClass('single-jogos')) {
        return;
    }

    var XI_API_KEY = read_aloud.api_key || "fallback_api_key";
    var voice_id = read_aloud.voice_id || "fallback_voice_id";
    var ttsProvider = read_aloud.tts_provider || "elevenlabs";
    var autoRead = read_aloud.auto_read === "1";

    if (autoRead) {
        var disableAutoRead = confirm("A leitura automática está ativada. Para desabilitar clique em OK, para manter habilitada clique em CANCELAR.");
        if (disableAutoRead) {
            disablingAutoRead = true;
            // Enviar uma solicitação AJAX para desabilitar a leitura automática
            $.ajax({
                url: read_aloud.ajax_url,
                type: 'post',
                data: {
                    action: 'disable_auto_read',
                },
                success: function (response) {
                    location.reload(); // Recarregar a página após desabilitar a leitura automática
                },
                error: function (response) {
                    alert('Algo deu errado ao desabilitar a leitura automática: ' + response.statusText);
                }
            });
        }
    }

    function readAloud(text, button) {
        text = cleanText(text);
        var audioURL = button.data('audio-url');

        if (audioURL) {
            playAudio(audioURL, button);
        } else {
            generateAudio(text, button);
        }
    }

    function generateAudio(text, button) {
        if (disablingAutoRead) {
            return;
        }

        if (ttsProvider === 'elevenlabs') {
            // Use ElevenLabs API
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "https://api.elevenlabs.io/v1/text-to-speech/" + voice_id + "/stream", true);
            xhr.responseType = 'blob';
            xhr.setRequestHeader("Accept", "audio/mp3");
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.setRequestHeader("xi-api-key", XI_API_KEY);

            xhr.onload = function () {
                if (this.status === 200) {
                    var blob = new Blob([this.response], { type: 'audio/mp3' });
                    var audioURL = URL.createObjectURL(blob);
                    button.data('audio-url', audioURL);
                    playAudio(audioURL, button);
                } else if (this.status === 429) {
                    alert('You have reached the usage limit of your ElevenLabs account API.');
                    button.find('i').attr('class', 'fas fa-play');
                } else {
                    console.error("Error receiving response: ", this.statusText);
                    alert('Something went wrong: ' + this.statusText + '. Please check your settings by clicking "Read Aloud".');
                    button.find('i').attr('class', 'fas fa-play');
                }
            };

            xhr.onerror = function () {
                console.error("Network or other error occurred.");
                alert('A network error or other problem occurred. Please check your connection and settings.');
                button.find('i').attr('class', 'fas fa-play');
            };

            xhr.send(JSON.stringify({
                text: text,
                model_id: "eleven_multilingual_v1",
                voice_settings: {
                    "stability": 0.5,
                    "style": 1,
                    "similarity_boost": 1
                }
            }));
        } else if (ttsProvider === 'openai') {
            // Use OpenAI API
            var OPENAI_API_KEY = read_aloud.openai_api_key || "fallback_openai_api_key";

            fetch("https://api.openai.com/v1/audio/speech", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": "Bearer " + OPENAI_API_KEY
                },
                body: JSON.stringify({
                    model: "tts-1",
                    voice: "alloy",
                    input: text
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.blob();
            })
            .then(blob => {
                var audioURL = URL.createObjectURL(blob);
                button.data('audio-url', audioURL);
                playAudio(audioURL, button);
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while generating OpenAI audio.");
                button.find('i').attr('class', 'fas fa-play');
            });
        }

        button.find('i').attr('class', 'fas fa-spinner fa-spin');
    }

    function playAudio(audioURL, button) {
        if (currentAudio) {
            currentAudio.pause();
        }

        var audio = new Audio(audioURL);
        currentAudio = audio;

        audio.onplay = function () {
            button.find('i').attr('class', 'fas fa-stop');
        };
        audio.onpause = function () {
            button.find('i').attr('class', 'fas fa-play');
        };
        audio.play();
        button.data('audio', audio);
    }

    function createReadAloudButton() {
        var button = $('<button>', {
            class: 'read-aloud-button',
            html: '<i class="fas fa-play"></i>',
        });

        button.on('click', function () {
            var thisButton = $(this);
            var audio = thisButton.data('audio');
            if (audio && !audio.paused) {
                audio.pause();
            } else {
                var text = thisButton.parent().find('.mwai-text').text();
                text = cleanText(text);
                readAloud(text, thisButton);
            }
        });

        return button;
    }

    function initReadAloudButton(target) {
        if (target.hasClass('mwai-reply') && target.hasClass('mwai-ai') && !target.find('.mwai-gallery').length && !target.data('button-added')) {
            var mwaiText = target.find('.mwai-text');
            var spanChild = mwaiText.find('span');
    
            var intervalId = setInterval(function() {
                if (spanChild.text().trim() !== "") {
                    clearInterval(intervalId);
                    var button = createReadAloudButton();
                    target.append(button);
                    target.data('button-added', true);  // Mark the div as having a button
                    if (autoRead && target.is(':last-child')) {
                        button.click();
                    }
                }
            }, 50);
        }
    }

    var allReplies = $('.mwai-reply.mwai-ai');
    if (allReplies.length) {
        initReadAloudButton(allReplies.last());
    }

    $('body').on('DOMNodeInserted', '.mwai-reply.mwai-ai', function (event) {
        var target = $(event.target);
        initReadAloudButton(target);
    });

    setInterval(function() {
        $('.mwai-reply.mwai-ai').each(function() {
            var target = $(this);
            initReadAloudButton(target);
        });
    }, 500);


});