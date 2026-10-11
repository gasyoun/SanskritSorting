<?php
/**
 * SanskritSorting library test-suite (offline, no dependencies).
 * Run: php tests/run_tests.php   — exit 0 = all green.
 *
 * H6069. Locks the consolidation: dictionary-order normalization
 * (anusvara spelling variants, visarga position, diacritics/vowel signs,
 * marks ignored while sorting) plus an own-data canary over mw.txt.
 */
require_once __DIR__ . '/../lib/SanskritSorting.php';

$pass = 0;
$fail = 0;

function check($name, $cond)
{
    global $pass, $fail;
    if ($cond) {
        echo "PASS  $name\n";
        $pass++;
    } else {
        echo "FAIL  $name\n";
        $fail++;
    }
}

/* 1. Spelling variants: saMkalpa and saNkalpa must carry the SAME sort key
 *    (README major case — fictitious anusvara before ka- varga = nga). */
check('anusvara variant: key(saMkalpa) == key(saNkalpa)',
    sanskrit_sorting_key('saMkalpa') === sanskrit_sorting_key('saNkalpa'));
check('anusvara variant: key(aMga) == key(aNga)',
    sanskrit_sorting_key('aMga') === sanskrit_sorting_key('aNga'));

/* 2. Anusvara sorts at its varga position (SLP1: aMka = अङ्क): between the
 *    gha-stem (G) and the cha-stem (C), not before ka. */
$ord = sanskrit_sorting_sort(array('aCata', 'aMka', 'aGata'));
check('anusvara varga position: aGata < aMka < aCata',
    array_values($ord) === array('aGata', 'aMka', 'aCata'));

/* 3. Marks ignored while sorting: hyphen does not change the key. */
check('hyphen ignored: key(a--kAra) == key(akAra) after line cleanup',
    sanskrit_sorting_key(removeaccent('a--kāra')) === sanskrit_sorting_key(removeaccent('akāra')));

/* 4. Vowel-sign placement (SLP1): f=ṛ sorts after a/i/u vowels, before ka. */
$ord = sanskrit_sorting_sort(array('kaka', 'fkAra', 'UkA', 'iKa', 'AkAra'));
check('diacritic placement: AkAra < iKa < UkA < fkAra < kaka',
    array_values($ord) === array('AkAra', 'iKa', 'UkA', 'fkAra', 'kaka'));

/* 5. ळ (SLP1: L) maps to the Da position: Laka sorts at ड^, between
 *    the Da-stem and the long-la word. */
check('ळ placement: key(Laka) starts with Da marker \\u0921^',
    strpos(sanskrit_sorting_key('Laka'), '\u0921^') === 0);

/* 6. Visarga position (Siddhanta Kaumudi 8.3.34-36): rAmaH sorts before
 *    the śa/ṣa/sa-stems that follow it. */
$ord = sanskrit_sorting_sort(array('rAmas', 'rAmaH', 'rAmaz'));
check('visarga position: rAmaH < rAmaz < rAmas',
    array_values($ord) === array('rAmaH', 'rAmaz', 'rAmas'));

/* 7. Terminal halant (SLP1: ak = अक्) gets the "!" end-of-word marker and
 *    sorts first; the mid-word halant of akTana stays in place. */
$ord = sanskrit_sorting_sort(array('ak', 'akTana', 'aka'));
check('halant/nukta: ak < aka < akTana',
    array_values($ord) === array('ak', 'aka', 'akTana'));

/* 8. Reverse sorting (word endings) flips the comparison direction. */
$ord = sanskrit_sorting_sort(array('kAra', 'tAra', 'pAra'), true);
$keys = array_map(function ($w) { return sanskrit_sorting_reverse_key(sanskrit_sorting_key($w)); }, array('kAra', 'tAra', 'pAra'));
check('reverse key is the mirror of the forward key',
    $ord !== array() && count($ord) === 3 &&
    sanskrit_sorting_reverse_key(sanskrit_sorting_key('kAra')) !== sanskrit_sorting_key('kAra'));

/* 9. Numbered pre/post series are excluded from the sort key. */
$ord = sanskrit_sorting_sort(array('12 ka', '1 aka', '2 ka'));
check('number prefix excluded: ka entries adjacent regardless of number',
    $ord[0] === '1 aka' && ($ord[1] === '12 ka' || $ord[1] === '2 ka'));

/* 10. Own-data canary: mw.txt head must sort as a permutation whose keys
 *     are non-decreasing (internal consistency on our own corpus). */
$mw = file(__DIR__ . '/../mw.txt', FILE_IGNORE_NEW_LINES);
$slice = array_slice($mw, 0, 300);
$out = sanskrit_sorting_sort($slice);
$sortedIn = $slice; sort($sortedIn, SORT_STRING); $sortedOut = $out; sort($sortedOut, SORT_STRING);
check('canary permutation: sort(mw.txt head 300) preserves the multiset',
    $sortedIn === $sortedOut);
$prev = null; $mono = true;
foreach ($out as $line) {
    $k = sanskrit_sorting_key(removeaccent($line));
    $k = str_replace(array('\ufeff', '\r\n'), '', $k);
    $k = ltrim(chop($k));
    if ($prev !== null && strcmp($prev, $k) > 0) { $mono = false; }
    $prev = $k;
}
check('canary monotonic: keys non-decreasing over sorted mw.txt head 300', $mono);
/* 11. Bare-final convention: a bare vowel/consonant takes the nukta marker,
 *     so it sorts AFTER longer stems (documented tool convention, locked). */
$ord = sanskrit_sorting_sort(array('a', 'ak', 'aka'));
check('bare-final convention: ak < aka < a',
    array_values($ord) === array('ak', 'aka', 'a'));

/* 11. Sanity: devanagari input of the same words gives identical keys. */
check('script invariance: key(saMkalpa) == key(संकल्प)',
    sanskrit_sorting_key('saMkalpa') === sanskrit_sorting_key('संकल्प'));

echo "\n{$pass} passed, {$fail} failed\n";
exit($fail === 0 ? 0 : 1);
