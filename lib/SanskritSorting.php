<?php
/**
 * SanskritSorting library — consolidated Devanagari sort-key normalization.
 *
 * H6069. Extracted verbatim from the multi13.php (forward) and reverse22.php
 * (reverse) pipelines of the Dhaval Patel lineage, so that the dictionary-order
 * normalization lives in ONE place instead of being duplicated per numbered
 * script revision.
 *
 * IMPORTANT (quote-style note): every \uXXXX codepoint literal below is
 * SINGLE-quoted on purpose. The sort key is the json_encode() output, which
 * contains literal backslash escape TEXT (e.g. "\u0915" as 6 characters).
 * Brace-less "\u0915" in double quotes is literal text on every PHP version
 * (only the \u{...} brace form is an escape), so tables and searches must
 * denote that literal text; single quotes make the intent explicit and the
 * behavior runtime-stable. (H6069 verifier correction: there was never a
 * PHP 5 vs PHP 7 behavioral split here — this is a clarity fix, not a
 * semantic repair.)
 *
 * This code derives from Dr. Dhaval Patel, www.sanskritworld.in
 * (upstream drdhaval2785/SanskritSorting), free to use, modify or alter for
 * any purpose. Keep the credit lines unaltered.
 */

require_once __DIR__ . '/../dev-slp.php';
require_once __DIR__ . '/../slp-dev.php';

if (!function_exists('removeaccent')) {
/**
 * Strip accents and punctuation marks from an input line before conversion.
 * (verbatim from multi13.php / reverse22.php)
 */
function removeaccent($text)
{
    $text = stripslashes($text);
    $a = array("\\","/","^","-","°","*","(",")","[","]","-","?");
    $b = array("","","","","","","","","","","","");
    $text = str_replace($a, $b, $text);
    return $text;
}
}

if (!function_exists('addaccent')) {
/**
 * Re-apply vowel accents for display output.
 * (verbatim from multi13.php / reverse22.php)
 */
function addaccent($text)
{
    $a = array("a\\","a/","a^","ā\\","ā/","ā^","i\\","i/","i^","ī\\","ī/","ī^","u\\","u/","u^","ū\\","ū/","ū^","ṛ\\","ṛ/","ṛ^","ṝ\\","ṝ/","ṝ^","ḷ\\","ḷ/","ḷ^","e\\","e/","e^","o\\","o/","o^",);
    $b = array("à","á","â","ā̀","ā́","ā̂","ì","í","î","ī̀","ī́","ī̂","ù","ú","û","ū̀","ū́","ū̂","ṛ̀","ṛ́","ṛ̂","ṝ̀","ṝ́","ṝ̂","ḷ̀","ḷ́","ḷ̂","è","é","ê","ò","ó","ô");
    $text = str_replace($a, $b, $text);
    return $text;
}
}

/**
 * Normalization/positioning tables shared by the sort-key pipeline.
 * All codepoint entries are literal escape TEXT, matching the json_encode'd
 * key representation (see file-header note).
 *
 * @return array{name:string,consonants:array,consonantreplace:array,
 *   kavarga:array,cavarga:array,Tavarga:array,tavarga:array,pavarga:array,
 *   khar:array,shar:array,vowel:array,specialcharacters:array}
 */
function sanskrit_sorting_tables()
{
    static $t = null;
    if ($t !== null) {
        return $t;
    }
    // consonants of devanagari as codepoint-escape text
    $t = array();
    $t['name'] = 'SanskritSorting';
    $t['consonants'] = array('\u0915','\u0916','\u0917','\u0918','\u0919','\u091a','\u091b','\u091c','\u091d','\u091e','\u091f','\u0920','\u0921','\u0922','\u0923','\u0924','\u0925','\u0926','\u0927','\u0928','\u092a','\u092b','\u092c','\u092d','\u092e','\u092f','\u0930','\u0932','\u0935','\u0936','\u0937','\u0938','\u0939');
    // $consonantreplace would be used to place the halant characters before akArAnta.
    $t['consonantreplace'] = array('\u0915!','\u0916!','\u0917!','\u0918!','\u0919!','\u091a!','\u091b!','\u091c!','\u091d!','\u091e!','\u091f!','\u0920!','\u0921!','\u0922!','\u0923!','\u0924!','\u0925!','\u0926!','\u0927!','\u0928!','\u092a!','\u092b!','\u092c!','\u092d!','\u092e!','\u092f!','\u0930!','\u0932!','\u0935!','\u0936!','\u0937!','\u0938!','\u0939!');
    // $kavarga to $pavarga are used to convert non-genuine anusvara to their corresponding fifth letter.
    $t['kavarga'] = array('\u0915','\u0916','\u0917','\u0918','\u0919');
    $t['cavarga'] = array('\u091a','\u091b','\u091c','\u091d','\u091e');
    $t['Tavarga'] = array('\u091f','\u0920','\u0921','\u0922','\u0923');
    $t['tavarga'] = array('\u0924','\u0925','\u0926','\u0927','\u0928');
    $t['pavarga'] = array('\u092a','\u092b','\u092c','\u092d','\u092e');
    // $khar and $shar are used for correct placement of visargas.
    $t['khar'] = array('\u0916','\u092b','\u091b','\u0920','\u0925','\u091a','\u091f','\u0924','\u0915','\u092a','\u0936','\u0937','\u0938');
    $t['shar'] = array('\u0936','\u0937','\u0938');
    // $vowel is list of vowel signs in devanagari
    $t['vowel'] = array('\u093e','\u093f','\u0940','\u0941','\u0942','\u0943','\u0944','\u0945','\u0946','\u0947','\u0948','\u0949','\u094a','\u094b','\u094c',' ');
    // $specialcharacters are the codepoints to ignore while sorting,
    // e.g. "°" (\u00b0), "√" (\u221a), "˚" (\u02da), grave/acute/macro (\u0300-\u0331).
    $t['specialcharacters'] = array('\u00b0','\u221a','\u02da','\u0300','\u0301','\u0331');
    return $t;
}

/**
 * Derive the SanskritSorting sort key for ONE word fragment.
 *
 * This is the consolidated normalization pipeline, transcribed verbatim from
 * multi13.php (forward sorting). Options reproduce the two historical
 * divergences of the reverse-sorter family:
 *   - 'halant_loop'  (reverse22) full 33-consonant loop marking terminal
 *     halant positions, instead of multi13's single-consonant prefix strip;
 *   - 'tilde_to_anusvara' (reverse22) ASCII "~" treated as anusvara.
 * Default (both false) = forward multi13 behavior.
 *
 * Input: a word fragment as produced by the pre/post splitting stage
 * (SLP1 or accented-IAST text, accents already collapsed). Output: the
 * json_encode-derived literal-escape sort key used for strcmp().
 *
 * @param string $c    word fragment to key
 * @param array  $opts see above
 * @return string sort key
 */
function sanskrit_sorting_key($c, $opts = array())
{
    $opts = array_merge(array('halant_loop' => false, 'tilde_to_anusvara' => false), $opts);
    $t = sanskrit_sorting_tables();
    $consonants = $t['consonants'];
    $consonantreplace = $t['consonantreplace'];
    $kavarga = $t['kavarga'];
    $cavarga = $t['cavarga'];
    $Tavarga = $t['Tavarga'];
    $tavarga = $t['tavarga'];
    $pavarga = $t['pavarga'];
    $khar = $t['khar'];
    $shar = $t['shar'];
    $specialcharacters = $t['specialcharacters'];

    /* Coding for accented marks */
    $c = str_replace(array("/<170/>","а","à","á","a̱","ā̱","ā́","ā̀","í̱","í","ì","ì̱","ī́","ī̀","u̱","ú","ù","ú̱","ū́","ū̀","è","é","ò","ó","ṛ́","ṛ́","ṝ","ç","१","२","३","४","५","६","७","८","९","०",),array("","a","a","a","a","ā","ā","ā","i","i","i","i","ī","ī","u","u","u","u","ū","ū","e","e","o","o","ṛ","ṛ","ṝ","ś","","","","","","","","","","",),$c);

    // getting the Codepoint for the UTF 8 encoded text
    $c = convert($c);
    $c = json_encode($c);
    // removing the starting code point \ufeff . json_encode adds this \ufeef at the start of the string. Remove it. Otherwise sorting may go wrong.
    $c = str_replace('"','',$c);
    $c = str_replace('\ufeff','',$c);
    $c = str_replace('\r\n','',$c);
    $c = str_replace('\u0902\0902','\u0902^',$c);
    $c = ltrim(chop($c));

    /* Replacing nongenuine anusvaras.
     * When anusvara is followed by a varga letter, the fictitious anusvara is
     * converted to that varga's fifth letter + virama (e.g. saMka -> saNga-ka).
     */
    $k = 0;
    while ($k < 5)
    {
        $u = 0;
        while ($u < 16)
        {
            $c = str_replace('\u0902'.$kavarga[$k],'\u0919\u094d'.$kavarga[$k],$c);
            $c = str_replace('\u0902'.$cavarga[$k],'\u091e\u094d'.$cavarga[$k],$c);
            $c = str_replace('\u0902'.$Tavarga[$k],'\u0923\u094d'.$Tavarga[$k],$c);
            $c = str_replace('\u0902'.$tavarga[$k],'\u0928\u094d'.$tavarga[$k],$c);
            $c = str_replace('\u0902'.$pavarga[$k],'\u092e\u094d'.$pavarga[$k],$c);
            $u++;
        }
        $k++;
    }

    /* code for placing terminal halant before akArAnta */
    if ($opts['halant_loop'])
    {
        // reverse22-style: mark terminal halant at every consonant position.
        $q = 0;
        while ($q < 33)
        {
            $c = str_replace($consonants[$q].'\u094d',$consonantreplace[$q].'\u094d',$c);
            $c = str_replace($consonants[$q].'\u094d\u200d',$consonants[$q].'\u094d',$c);
            $c = str_replace($consonants[$q].'\u094d\u200c',$consonants[$q].'\u094d',$c);
            $q++;
        }
    }
    else
    {
        // multi13-style: strip zero-width joiners after the first consonant.
        $q = 0;
        $c = str_replace($consonants[$q].'\u094d\u200d',$consonants[$q].'\u094d',$c);
        $c = str_replace($consonants[$q].'\u094d\u200c',$consonants[$q].'\u094d',$c);
    }
    // terminal halant -> "!" marker so akArAnta sorts before it; a bare
    // terminal consonant gets a nukta marker instead.
    if (preg_match('/(\\\u[0-9abcdef]{4})(\\\u094d)$/',$c))
    {
        $c = preg_replace('/(\\\u[0-9abcdef]{4})(\\\u094d)$/','$1!$2',$c);
    }
    else
    {
        $c = preg_replace('/(\\\u[0-9abcdef]{4})$/','$1\\u093c',$c);
    }

    /* The code for deciding the position of visarga.
     * This has been done with help of Siddhanta Kaumudi.
     */

    // visarjanIyasya saH (8.3.34) - visarga gets converted to 's' before $khar
    $k = 0;
    while ($k < 13)
    {
        $c = str_replace('\u0903'.$khar[$k],'\u0938\u094d'.$khar[$k],$c);
        $k++;
    }
    // sharpare visarjanIyaH (8.3.35) - visarga-$khar-$shar gets converted to 's'-$khar-$shar
    $k = 0;
    while ($k < 13)
    {
        $j = 0;
        while ($j < 3)
        {
            $c = str_replace('\u0938\u094d'.$khar[$k].'\u094d'.$shar[$j],'\u0903'.$khar[$k].'\u094d'.$shar[$j],$c);
            $j++;
        }
        $k++;
    }
    // vA shari (8.3.36) - visarga-$shar = visarga-$shar. Dictionaries seem to take this as mandatory, even though it is optional
    $k = 0;
    while ($k < 3)
    {
        $c = str_replace('\u0938\u094d'.$shar[$k],'\u0903'.$shar[$k],$c);
        $k++;
    }
    // (kharpare shari vA visargalopo 8.3.36 vArtikA; kupvoH HkHpau ca 8.3.37;
    //  so'padAdau 8.3.38 etc. — sentence-context rules, not coded: dictionaries
    //  do not need them. Comments preserved from the original scripts.)
    $k = 0;
    while ($k < 2) // the logic behind using only 2 instead of 4 is, because 's' would be converted to 'o' before ga,gha,ba,bha. It would no longer remain visarga/'s'
    {
        // iNaH SaH (8.3.39) — i/u before 's' before ka/kha (and pa/pha) -> Sha
        $c = str_replace('\u093f\u0938\u094d'.$kavarga[$k],'\u093f\u0937\u094d'.$kavarga[$k],$c);
        $c = str_replace('\u0941\u0938\u094d'.$kavarga[$k],'\u0941\u0937\u094d'.$kavarga[$k],$c);
        $c = str_replace('\u093f\u0938\u094d'.$pavarga[$k],'\u093f\u0937\u094d'.$pavarga[$k],$c);
        $c = str_replace('\u0941\u0938\u094d'.$pavarga[$k],'\u0941\u0937\u094d'.$pavarga[$k],$c);
        // 'muhusaH pratiSedhaH (vArtika)' - muhuH doesn't get converted to muhuS.
        $c = str_replace('\u092e\u0941\u0939\u0941\u0937\u094d'.$kavarga[$k],'\u092e\u0941\u0939\u0941\u0903'.$kavarga[$k],$c);
        $c = str_replace('\u092e\u0941\u0939\u0941\u0937\u094d'.$pavarga[$k],'\u092e\u0941\u0939\u0941\u0903'.$pavarga[$k],$c);
        // 'adhazzirasI pade' - adhaH + pada -> adhaspada, ziras + pada -> ziraspada
        $c = str_replace('\u0905\u0927\u0903\u092a\u0926','\u0905\u0927\u0938\u094d\u092a\u0926',$c);
        $k++;
    }

    /* coding for correct position of RU,lR lRU */
    // By default these rare vowel signs sort after the whole consonants.
    // We want to position them at their proper place.
    $c = str_replace('\u0960','\u090b^',$c);
    $c = str_replace('\u0961','\u090c^',$c);
    $c = str_replace('\u0962','\u0944^',$c);
    $c = str_replace('\u0963','\u0944_',$c);

    /* Coding for correcting the position of anusvAra and visarga between "au" and "ka" in reverse order. */
    $c = str_replace('\u0902\u0902','\u0914!',$c);
    $c = str_replace('\u0902','\u0914^',$c);
    $c = str_replace('\u0903','\u0914_',$c);

    /* Coding for correcting the position of L and | (ळ and ळ्ह) */
    $c = str_replace('\u0933\u094d\u0939','\u0922^',$c);
    $c = str_replace('\u0933','\u0921^',$c);

    /* coding for OM */
    $c = str_replace('\u0950','\u0913\u0902',$c);

    /* coding for tilde (reverse22 only) */
    if ($opts['tilde_to_anusvara'])
    {
        $c = str_replace('\u007e','\u0902',$c);
    }

    /* Coding for special characters, which are to be ignored while sorting.
     * The original text keeps all characters intact; only the key drops them.
     */
    $l = 0;
    while ($l < count($specialcharacters))
    {
        $c = str_replace($specialcharacters[$l],"",$c);
        $l++;
    }

    return $c;
}

/**
 * Convert a forward sort key into a REVERSE (word-endings) sort key.
 * (verbatim tail of reverse22.php: split the literal-escape key on the
 * backslash separator, reverse the escape groups, drop the leading empty
 * remnant, rejoin.)
 */
function sanskrit_sorting_reverse_key($key)
{
    $a = explode('\\', $key);
    $b = array_reverse($a);
    array_pop($b);
    return implode('\\', $b);
}

/** strcmp-based ascending usort comparator on an array field. */
function sanskrit_sorting_build_sorter($key)
{
    return function ($a, $b) use ($key) {
        return strcmp($a[$key], $b[$key]);
    };
}

/** strcmp-based descending usort comparator on an array field. */
function sanskrit_sorting_build_sorter1($key)
{
    return function ($a, $b) use ($key) {
        return strcmp($b[$key], $a[$key]);
    };
}

/**
 * Sort Sanskrit word lines into traditional dictionary order (one-call API).
 *
 * Reproduces the multi13/reverse22 main flow: strip accents/punctuation,
 * convert to Devanagari, split leading/trailing number series away, derive
 * the normalized key, sort by it, and return the ORIGINAL lines in sorted
 * order. Trailing newline per line is tolerated.
 *
 * @param string[] $lines   input lines (SLP1 or accented IAST)
 * @param bool     $reverse true = sort by word endings (reverse22 semantics)
 * @param array    $opts    forwarded to sanskrit_sorting_key()
 * @return string[] original lines, dictionary-ordered
 */
function sanskrit_sorting_sort($lines, $reverse = false, $opts = array())
{
    $test = array_map('removeaccent', $lines);
    $test = array_map('convert', $test);
    $orig = $lines;

    $array = array();
    $i = 0;
    $count = count($test);
    while ($i < $count)
    {
        $text = str_replace("\t", " ", $test[$i]);
        $text = trim($text);

        // Split leading/trailing number series away from the sortable word.
        // (pre/post-splitting logic verbatim from multi13.php; the debug
        //  echo "last is number" of the original is intentionally dropped)
        $delimiters = " \-*()@\ ";
        $a = preg_split('/([' . $delimiters . '])/m', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        if ($a === false) { $a = array($text); }
        $last = isset($a[count($a) - 1]) ? $a[count($a) - 1] : '';
        if (preg_match('/[0-9]+/', isset($a[0]) ? $a[0] : ''))
        {
            $q = 2;
            $c = "";
            if (preg_match('/[0-9]/', $last))
            {
                while ($q < count($a) - 1) { $c = $c . $a[$q]; $q += 2; }
            }
            else
            {
                while ($q < count($a)) { $c = $c . $a[$q]; $q += 2; }
            }
        }
        else
        {
            $q = 0;
            $c = "";
            if (preg_match('/[0-9, ]+/', $last))
            {
                while ($q < count($a) - 1) { $c = $c . $a[$q]; $q += 2; }
            }
            else
            {
                while ($q < count($a)) { $c = $c . $a[$q]; $q += 2; }
            }
        }

        $c = sanskrit_sorting_key($c, $opts);
        if ($reverse)
        {
            $c = sanskrit_sorting_reverse_key($c);
        }
        $array[$i] = array('$c' => $c, '$original' => isset($orig[$i]) ? $orig[$i] : $test[$i]);
        $i++;
    }

    $array = array_values($array);
    usort($array, sanskrit_sorting_build_sorter('$c'));
    $out = array();
    foreach ($array as $row) { $out[] = $row['$original']; }
    return $out;
}
