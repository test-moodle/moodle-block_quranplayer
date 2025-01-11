<?php
defined('MOODLE_INTERNAL') || die();

class block_audioplayer extends block_base {

    public function init() {
        $this->title = get_string('audioplayer', 'block_audioplayer');
    }

    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = $this->render_audio_player();
        $this->content->footer = '';

        return $this->content;
    }

    private function render_audio_player() {
        $mp3path = __DIR__ . '/mp3/';
        $files = array_diff(scandir($mp3path), ['..', '.']); // Fetch MP3 files
        $options = '';

        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $options .= "<option value='$file'>$filename</option>";
        }

        // Render HTML for the audio player
        $html = <<<HTML
<div>
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
