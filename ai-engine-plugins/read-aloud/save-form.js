// save-form.js
jQuery(document).ready(function ($) {
    // Listen for form submission
    $('#read-aloud-form').submit(function (e) {
        e.preventDefault();
        
        var apiKey = $('#api-key').val();
        var voiceId = $('#voice-id').val();
        var ttsProvider = $('#tts-provider').val();
        var autoRead = $('#auto-read').is(':checked') ? '1' : '0';

        // Send data to WordPress to save
        $.ajax({
            url: read_aloud.ajax_url,
            type: 'post',
            data: {
                action: 'save_read_aloud_settings',
                api_key: apiKey,
                voice_id: voiceId,
                'tts-provider': ttsProvider,
                'auto-read': autoRead,
            },
            success: function (response) {
                alert('Options saved successfully!');
                location.reload(); // Reload the page after saving settings
            },
            error: function (response) {
                alert('Something went wrong: ' + response.statusText);
            }
        });
    });
});