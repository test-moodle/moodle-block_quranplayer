<?php
defined('MOODLE_INTERNAL') || die();

class block_audioplayer_renderer extends plugin_renderer_base {

    /**
     * Render the audio player UI with the dropdown.
     *
     * @param array $files List of MP3 files.
     * @return string HTML output for the audio player.
     */
    public function render_audio_player($files) {
        $options = '';

        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $options .= "<option value='$file'>$filename</option>";
        }

        $html = <<<HTML
<div class="audioplayer-container">
    <label for="audioplayer-select">Select a chapter to play:</label>
    <select id="audioplayer-select">
        $options
    </select>
    <audio id="audioplayer" controls>
        <source id="audioplayer-source" src="" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
</div>
<script>
    const select = document.getElementById('audioplayer-select');
    const audio = document.getElementById('audioplayer');
    const source = document.getElementById('audioplayer-source');
    select.addEventListener('change', function() {
        source.src = 'block_audioplayer/mp3/' + this.value;
        audio.load();
    });
</script>
HTML;

        return $html;
    }
}
