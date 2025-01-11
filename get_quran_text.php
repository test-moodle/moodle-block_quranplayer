<?php
require_once('../../config.php');
defined('MOODLE_INTERNAL') || die();

$file = optional_param('file', '', PARAM_TEXT);
$quranfile = __DIR__ . '/quran.txt';

if (!file_exists($quranfile)) {
    echo get_string('noqurantext', 'block_audioplayer');
    exit;
}

// Extract surah number from the file name (e.g., 001.mp3 -> 1)
$surahNumber = intval(pathinfo($file, PATHINFO_FILENAME));

$qurantext = file_get_contents($quranfile);
$lines = explode("\n", $qurantext);

$selectedText = '';
foreach ($lines as $line) {
    // Each line is in the format "surah|verse|text"
    list($lineSurah, $lineVerse, $text) = explode('|', $line, 3);
    if ($lineSurah == $surahNumber) {
        $selectedText .= "$lineVerse. $text\n";
    }
}

if (empty($selectedText)) {
    echo get_string('noqurantext', 'block_audioplayer');
} else {
    echo $selectedText;
}