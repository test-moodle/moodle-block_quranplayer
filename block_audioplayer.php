<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

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

        // List of Quran chapter names
        $quranChapters = [
            "الفاتحة", "البقرة", "آل عمران", "النساء", "المائدة", "الأنعام", "الأعراف", "الأنفال", "التوبة", "يونس",
            "هود", "يوسف", "الرعد", "ابراهيم", "الحجر", "النحل", "الإسراء", "الكهف", "مريم", "طه",
            "الأنبياء", "الحج", "المؤمنون", "النور", "الفرقان", "الشعراء", "النمل", "القصص", "العنكبوت", "الروم",
            "لقمان", "السجدة", "الأحزاب", "سبإ", "فاطر", "يس", "الصافات", "ص", "الزمر", "غافر",
            "فصلت", "الشورى", "الزخرف", "الدخان", "الجاثية", "الأحقاف", "محمد", "الفتح", "الحجرات", "ق",
            "الذاريات", "الطور", "النجم", "القمر", "الرحمن", "الواقعة", "الحديد", "المجادلة", "الحشر", "الممتحنة",
            "الصف", "الجمعة", "المنافقون", "التغابن", "الطلاق", "التحريم", "الملك", "القلم", "الحاقة", "المعارج",
            "نوح", "الجن", "المزمل", "المدثر", "القيامة", "الانسان", "المرسلات", "النبإ", "النازعات", "عبس",
            "التكوير", "الإنفطار", "المطففين", "الإنشقاق", "البروج", "الطارق", "الأعلى", "الغاشية", "الفجر", "البلد",
            "الشمس", "الليل", "الضحى", "الشرح", "التين", "العلق", "القدر", "البينة", "الزلزلة", "العاديات",
            "القارعة", "التكاثر", "العصر", "الهمزة", "الفيل", "قريش", "الماعون", "الكوثر", "الكافرون", "النصر",
            "المسد", "الإخلاص", "الفلق", "الناس"
        ];

        $options = '';
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'mp3') {
                $surahNumber = intval(pathinfo($file, PATHINFO_FILENAME)); // Extract surah number from file name (e.g., 001.mp3 -> 1)
                if ($surahNumber >= 1 && $surahNumber <= 114) {
                    $surahName = $quranChapters[$surahNumber - 1]; // Get surah name from the list
                    $options .= "<option value='" . s($file) . "'>$surahNumber. $surahName</option>";
                }
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
        source.src = '{$CFG->wwwroot}/blocks/audioplayer/mp3/' + encodeURIComponent(selectedFile);
        audio.load();

        fetch('{$CFG->wwwroot}/blocks/audioplayer/get_quran_text.php?file=' + encodeURIComponent(selectedFile))
            .then(response => response.text())
            .then(text => {
                quranContent.textContent = text;
            })
            .catch(error => {
                quranContent.textContent = 'Failed to load Quran text.';
            });
    });

    select.dispatchEvent(new Event('change'));
</script>
HTML;

        return $html;
    }
}