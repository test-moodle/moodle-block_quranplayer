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
        global $CFG;

        $mp3path = $CFG->dirroot . '/blocks/audioplayer/mp3/';
        if (!is_dir($mp3path)) {
            return '<div class="alert alert-error">' . get_string('nodirectory', 'block_audioplayer') . '</div>';
        }

        $files = array_diff(scandir($mp3path), ['..', '.']);
        if (empty($files)) {
            return '<div class="alert alert-warning">' . get_string('noaudiofiles', 'block_audioplayer') . '</div>';
        }

        $options = '';
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'mp3') {
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $options .= "<option value='$file'>$filename</option>";
            }
        }

        $html = <<<HTML
<div>
    <label for="audioplayer-select">{$this->title}</label>
    <select id="audioplayer-select">
        $options
    </select>
    <audio id="audioplayer" controls>
        <source id="audioplayer-source" src="" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <div id="quran-text">
        <h3>{$this->title}</h3>
        <pre id="quran-content"></pre>
    </div>
</div>
<script>
    const select = document.getElementById('audioplayer-select');
    const audio = document.getElementById('audioplayer');
    const source = document.getElementById('audioplayer-source');
    const quranContent = document.getElementById('quran-content');

    select.addEventListener('change', function() {
        const selectedFile = this.value;
        source.src = '{$CFG->wwwroot}/blocks/audioplayer/mp3/' + selectedFile;
        audio.load();

        // Fetch Quran text for the selected file
        fetch('{$CFG->wwwroot}/blocks/audioplayer/get_quran_text.php?file=' + selectedFile)
            .then(response => response.text())
            .then(text => {
                quranContent.textContent = text;
            })
            .catch(error => {
                quranContent.textContent = 'Failed to load Quran text.';
            });
    });

    // Trigger change event to load the first file's text on page load
    select.dispatchEvent(new Event('change'));
</script>
HTML;

        return $html;
    }
}