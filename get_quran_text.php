<?php
require_once('../../config.php');
defined('MOODLE_INTERNAL') || die();

$file = optional_param('file', '', PARAM_TEXT);
$quranfile = __DIR__ . '/quran.txt';

if (!file_exists($quranfile)) {
    die('Quran text file not found.');
}

$qurantext = file_get_contents($quranfile);
$lines = explode("\n", $qurantext);

$selectedText = '';
foreach ($lines as $line) {
    if (strpos($line, $file) !== false) {
        $selectedText .= $line . "\n";
    }
}

echo $selectedText;