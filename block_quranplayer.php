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

/**
 * Quran Player block.
 *
 * @package    block_quranplayer
 * @copyright  2024 Maysara Mohamed
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Quran Player block class.
 *
 * @package    block_quranplayer
 * @copyright  2024 Maysara Mohamed 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_quranplayer extends block_base {

    /**
     * Initializes the block.
     */
    public function init() {
        $this->title = get_string('quranplayer', 'block_quranplayer');
    }

    /**
     * Gets the block content.
     *
     * @return stdClass
     */
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = $this->render_audio_player();
        $this->content->footer = '';

        return $this->content;
    }

    /**
     * Renders the audio player.
     *
     * @return string
     */
    private function render_audio_player() {
        global $CFG;

        $mp3path = $CFG->dirroot . '/blocks/quranplayer/mp3/';
        if (!is_dir($mp3path)) {
            return '<div class="alert alert-error">' . get_string('nodirectory', 'block_quranplayer') . '</div>';
        }

        $files = array_diff(scandir($mp3path), ['..', '.']);
        if (empty($files)) {
            return '<div class="alert alert-warning">' . get_string('noaudiofiles', 'block_quranplayer') . '</div>';
        }

        // List of Quran chapter names.
        $quranchapters = [
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
            "المسد", "الإخلاص", "الفلق", "الناس",
        ];

        $options = '';
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'mp3') {
                $surahnumber = intval(pathinfo($file, PATHINFO_FILENAME)); // Extract surah number from file name.
                if ($surahnumber >= 1 && $surahnumber <= 114) {
                    $surahname = $quranchapters[$surahnumber - 1]; // Get surah name from the list.
                    $options .= "<option value='" . s($file) . "'>$surahnumber. $surahname</option>";
                }
            }
        }

        $html = <<<HTML
<div>
    <label for="quranplayer-select">{$this->title}</label>
    <select id="quranplayer-select">
        $options
    </select>
    <div id="quran-text">
        <h3>{$this->title}</h3>
        <pre id="quran-content"></pre>
    </div>
    <audio id="quranplayer" controls>
        <source id="quranplayer-source" src="" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
</div>
<script>
    const select = document.getElementById('quranplayer-select');
    const audio = document.getElementById('quranplayer');
    const source = document.getElementById('quranplayer-source');
    const quranContent = document.getElementById('quran-content');

    select.addEventListener('change', function() {
        const selectedFile = this.value;
        source.src = '{$CFG->wwwroot}/blocks/quranplayer/mp3/' + encodeURIComponent(selectedFile);
        audio.load();

        fetch('{$CFG->wwwroot}/blocks/quranplayer/get_quran_text.php?file=' + encodeURIComponent(selectedFile))
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