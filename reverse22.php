<?php

$base = $argv[1];
if(!is_dir($base)) 
    {
    mkdir($base);
    }
$input=$argv[2];
$arg3 = $argv[3];
$pratyayas = file($arg3);
include 'conf.php';
error_reporting(0);
/* Code written by Dr. Dhaval Patel, www.sanskritworld.in.
 * Version 1.0, Date: 2nd October, 2013
 * email: drdhaval2785@gmail.com
 * This code is free to be used, modified or altered for any purpose.
 * Please make sure to keep these lines unaltered to credit the author of the code.
 * The purpose of this code is to create a reverse dictionary of devanagari i.e. the dictionary sorted by the last letter of the word instead of the first letter.
 * The machine has been converted to a Command line tool now. (12 Dec 2014).
 * The syntax is php reverse22.php outputfolder input morphologicends.
 * The actual code is run like php reverse22.php d:\!sorting d:\!sorting\input\input.txt d:\!sorting\input\morphologicends.txt
 */

// set execution time to an hour
ini_set('max_execution_time', 360000);
// set memory limit to 1000 MB
ini_set("memory_limit","100000M");
// hides error reports.
error_reporting(0);
// include files for conversion SLP and devanagari.
include "dev-slp.php";
include "slp-dev.php";
// H6069: consolidated Devanagari sorting/normalization library (single home
// for the sort-key pipeline shared with the multi* family).
require_once __DIR__ . "/lib/SanskritSorting.php";

$test = file($input);
$orig = $test;
$test=array_map('removeaccent',$test);
$test = array_map('convert',$test);

// $count counts the number of members in the array $test
$count = count($test);
// (varga/khar/shar/special tables and the sort-key normalization now live in lib/SanskritSorting.php -- H6069)

// input the text file into an array.
// Make sure to hit an enter at the last of your file. Otherwise the last word may be missed in sorting. 
// In other words add "\r\n" at the end of your data.


// defining certain arrays for removing the GRETIL abbreviations
$capital = 'AĀIĪUŪRṚEOKGṄCJÑṬḌṆTDNPBMYLVŚṢSH';
$small = 'abcdefghijklmnopqrstuv){}';
$ns = '0123456789_.,){}:\/-';
$numerals = '0123456789';



// specifying the arrays of different datatypes
$ch['unicode'] = array(
1 => "ā", // _a
2 => "ī", // _i
3 => "ū", // _u
4 => "ṛ", // .r
5 => "ṝ", // _.r
6 => "ṅ", // 'n
7 => "ñ", // ~n
8 => "ṭ", // .t
9 => "ḍ", // .d 
10 => "ṇ", // .n
11 => "ś", // 's
//11 => "ç", // 's
12 => "ṣ", // .s
13 => "ṁ", // 'm (anusvara)
14 => "ḥ", // .h (visarga)
15 => "ḷ", // .l
16 => "ḹ", // _.l

30 => "Ā", // _a
31 => "Ī", // _i
32 => "Ū", // _u
33 => "Ṛ", // .r
34 => "Ṝ", // _.r
35 => "Ṅ", // 'n
36 => "Ñ", // ~n
37 => "Ṭ", // .t
38 => "Ḍ", // .d
39 => "Ṇ", // .n
40 => "Ś", // 's
41 => "Ṣ", // .s
42 => "ṃ", // 'm (anusvara)
43 => "Ḥ", // .h (visarga)
44 => "Ḷ", // .l
45 => "Ḹ", // _.l

50 => "Ḏ", // _D
51 => "Ẏ", // .Y

20 => "ḏ", // _d
21 => "ẏ", // .y

60 => "ɱ", // \-/ (candrabindu)
61 => "̮", // _ (ha_uk)
62 => "^", // ^ (ext. sandhi)
63 => "'", // avagraha
64 => "ɱ", // \_/ (candra e)
65 => "/x", // \ (virama)
66 => "…", // abbreviation
67 => "’", // Latin apostrophe

101 => "kh", // _a
102 => "gh", // _i
103 => "ch", // _u
104 => "jh", // .r
105 => "ṭh", // _.r
106 => "ḍh", // 'n
107 => "th", // ~n
108 => "dh", // .t
109 => "ph", // .d 
110 => "bh", // .n
111 => "ai", // .d 
112 => "au", // .n
113 => "ḻ", // .n
114 => "ḻh", // .n
);

$ch['slp'] = array(
1 => "A", // _a
2 => "I", // _i
3 => "U", // _u
4 => "f", // .r
5 => "F", // _.r
6 => "N", // 'n
7 => "Y", // ~n
8 => "w", // .t
9 => "q", // .d 
10 => "R", // .n
11 => "S", // 's
//11 => "ç", // 's
12 => "z", // .s
13 => "M", // 'm (anusvara)
14 => "H", // .h (visarga)
15 => "x", // .l
16 => "X", // _.l

30 => "A", // _a
31 => "I", // _i
32 => "U", // _u
33 => "f", // .r
34 => "F", // _.r
35 => "N", // 'n
36 => "Y", // ~n
37 => "w", // .t
38 => "q", // .d
39 => "R", // .n
40 => "S", // 's
41 => "z", // .s
42 => "M", // 'm (anusvara)
43 => "H", // .h (visarga)
44 => "x", // .l
45 => "X", // _.l

50 => "Ḏ", // _D
51 => "Ẏ", // .Y

20 => "ḏ", // _d
21 => "ẏ", // .y

60 => "ɱ", // \-/ (candrabindu)
61 => "̮", // _ (ha_uk)
62 => "^", // ^ (ext. sandhi)
63 => "'", // avagraha
64 => "ɱ", // \_/ (candra e)
65 => "/x", // \ (virama)
66 => "…", // abbreviation
67 => "’", // Latin apostrophe

101 => "K", // _a
102 => "G", // _i
103 => "C", // _u
104 => "J", // .r
105 => "W", // _.r
106 => "Q", // 'n
107 => "T", // ~n
108 => "D", // .t
109 => "P", // .d 
110 => "B", // .n
111 => "E", // .d 
112 => "O", // .n
113 => "L", // .n
113 => "L", // .n
);

$ch['balaram'] = array(

30 => "Ä", // _a
31 => "É", // _i
32 => "Ü", // _u
33 => "Å", // .r
34 => "È", // _.r
35 => "Ì", // 'n
36 => "Ï", // ~n
37 => "Ö", // .t
38 => "Ò", // .d
39 => "Ë", // .n
40 => "Ç", // 's
41 => "Ñ", // .s
42 => "À", // 'm (anusvara)
43 => "Ù", // .h (visarga)
44 => "ß", // .l
45 => "ß", // _.l

50 => ".Ò", // _d
51 => "Ý", // .y

1 => "ä", // _a
2 => "é", // _i
3 => "ü", // _u
4 => "å", // .r
5 => "è", // _.r
6 => "ì", // 'n
7 => "ï", // ~n
8 => "ö", // .t
9 => "ò", // .d 
10 => "ë", // .n
11 => "ç", // 's
12 => "ñ", // .s
13 => "à", // 'm (anusvara)
14 => "ù", // .h (visarga)
15 => "ÿ", // .l
16 => "û", // _.l

20 => ".ò", // _d
21 => "ý", // .y

60 => "~", // \-/ (candrabindu)
61 => "_", // _ (ha_uk)62 => "^", // ^ (ext. sandhi)63 => "'", // avagraha
64 => "~", // \_/ (candra e)
65 => "/x", // \ (virama)
66 => "…", // abbreviation
67 => "’", // Latin apostrophe


);


$ch['csx'] = array(

30 => "â", // _a
31 => "ä", // _i
32 => "æ", // _u
33 => "è", // .r
34 => "é", // _.r
35 => "ð", // 'n
36 => "¥", // ~n
37 => "ò", // .t
38 => "ô", // .d
39 => "ö", // .n
40 => "ø", // 's
41 => "ú", // .s
42 => "ý", // 'm (anusvara)
43 => "ş", // .h (visarga)
44 => "ë", // .l
45 => "í", // _.l

50 => "ô", // _D
51 => "Ÿ", // .Y

1 => "à", // _a
2 => "ã", // _i
3 => "å", // _u
4 => "ç", // .r
5 => "é", // _.r
6 => "ï", // 'n
7 => "¤", // ~n
8 => "ñ", // .t
9 => "ó", // .d 
10 => "õ", // .n
11 => "÷", // 's
12 => "ù", // .s
13 => "ü", // 'm (anusvara)
14 => "þ", // .h (visarga)
15 => "í", // .l
16 => "û", // _.l

20 => "ó", // _d
21 => "ÿ", // .y

60 => "~", // \-/ (candrabindu)
61 => "_", // _ (ha_uk)
62 => "^", // ^ (ext. sandhi)
63 => "'", // avagraha
64 => "~", // \_/ (candra e)
65 => "/x", // \ (virama)
66 => "…", // abbreviation
67 => "’", // Latin apostrophe

);
$ch['itrans'] = array(

30 => "%A", // _a
31 => "%I", // _i
32 => "%U", // _u
33 => "%R^i", // .r
34 => "%R^I", // _.r
35 => "%~N", // 'n
36 => "%~n", // ~n
37 => "%T", // .t
38 => "%D", // .d
39 => "%N", // .n
40 => "%sh", // 's
41 => "%Sh", // .s
42 => "%M", // 'm (anusvara)
43 => "%H", // .h (visarga)
44 => "%L^i", // .l
45 => "%L^I", // _.l

50 => "%.D", // _D
51 => "%Y", // .Y

1 => "A", // _a
2 => "I", // _i
3 => "U", // _u
4 => "R^i", // .r
5 => "R^I", // _.r
6 => "~N", // 'n
7 => "~n", // ~n
8 => "T", // .t
9 => "D", // .d 
10 => "N", // .n
11 => "sh", // 's
12 => "Sh", // .s
13 => "M", // 'm (anusvara)
14 => "aH", // .h (visarga) (visarga)
15 => "L^i", // .l
16 => "L^I", // _.l

20 => ".D", // _d
21 => "Y", // .y

60 => ".N", // \-/ (candrabindu)
61 => "_", // _ (ha_uk)
62 => "^", // ^ (ext. sandhi)
63 => ".a", // avagraha
64 => ".c", // \_/ (candra e)
65 => ".h", // \ (virama)
66 => "@", // abbreviation
67 => "`", // Latin apostrophe

);


$ch['xsanskrit'] = array(

30 => "š", // _a
31 => "Ÿ", // _i
32 => "¶", // _u
33 => "", // .r
34 => "­", // _.r
35 => "¥", // 'n
36 => "Ñ", // ~n
37 => "µ", // .t
38 => "", // .d
39 => "¦", // .n
40 => "®", // 's
41 => "±", // .s
42 => "¤", // 'm (anusvara)
43 => "ž", // .h (visarga)
44 => "¢", // .l
45 => "¢", // _.l

50 => ".P", // _D
51 => ".Y", // .Y

1 => "€", // _a
2 => "…", // _i
3 => "™", // _u
4 => "", // .r
5 => "Ž", // _.r
6 => "‰", // 'n
7 => "ñ", // ~n
8 => "˜", // .t
9 => "", // .d 
10 => "Š", // .n
11 => "", // 's
12 => "", // .s
13 => "ˆ", // 'm (anusvara)
14 => "ƒ", // .h (visarga)
15 => "†", // .l
16 => "‡", // _.l

20 => ".p", // _d
21 => ".y", // .y


60 => "~", // \-/ (candrabindu)
61 => "_", // _ (ha_uk)
62 => "^", // ^ (ext. sandhi)
63 => "'", // avagraha
64 => "~", // \_/ (candra e)
65 => "/x", // \ (virama)
66 => "...", // abbreviation
67 => "’", // Latin apostrophe

);


$ch['shakti'] = array(

30 => "", // _a
31 => "é", // _i 
32 => "¬", // _u // uncertain
33 => "ä", // .r
34 => ".¨", // _.r // missing
35 => "", // 'n // missing
36 => "", // ~n // missing
37 => "æ", // .t
38 => "¶", // .d // missing
39 => "º", // .n // missing
40 => "", // 's
41 => "ê", // .s
42 => "µ", // 'm (anusvara) // missing
43 => "ú", // .h (visarga) // missing
44 => ".l", // .l // missing
45 => "_.l", // _.l // missing

50 => ".¶", // _D // missing
51 => ".Y", // .Y // missing

1 => "", // _a
2 => "", // _i
3 => "", // _u
4 => "¨", // .r
5 => ".¨", // _.r
6 => "", // 'n
7 => "", // ~n
8 => " ", // .t // not working
9 => "¶", // .d 
10 => "º", // .n
11 => "½", // 's
12 => "§", // .s
13 => "µ", // 'm (anusvara)
14 => "ú", // .h (visarga)
15 => ".L", // .l // missing
16 => "_.L", // _.l // missing

20 => ".¶", // _d // missing
21 => ".y", // .y // missing


60 => "~", // \-/ (candrabindu)
61 => "_", // _ (ha_uk)
62 => "^", // ^ (ext. sandhi)
63 => "'", // avagraha
64 => "~", // \_/ (candra e)
65 => "/x", // \ (virama)
66 => "@", // abbreviation
67 => "Õ", // Latin apostrophe

);

$ch['velthuis'] = array(

30 => "%aa", // _a
31 => "%ii", // _i
32 => "%uu", // _u
33 => "%.r", // .r
34 => "%.R", // _.r
35 => "%\"n", // 'n
36 => "%~n", // ~n
37 => "%.t", // .t
38 => "%.d", // .d
39 => "%.n", // .n
40 => "%\"s", // 's
41 => "%.s", // .s
42 => "%.m", // 'm (anusvara)
43 => "%.h", // .h (visarga)
44 => "%.l", // .l
45 => "%.L", // _.l

50 => "%.d", // _D
51 => "%.y", // .Y

1 => "aa", // _a
2 => "ii", // _i
3 => "uu", // _u
4 => ".r", // .r
5 => ".R", // _.r
6 => "\"n", // 'n
7 => "~n", // ~n
8 => ".t", // .t
9 => ".d", // .d 
10 => ".n", // .n
11 => "\"s", // 's
12 => ".s", // .s
13 => ".m", // 'm (anusvara)
14 => ".h", // .h (visarga)
15 => ".l", // .l
16 => ".L", // _.l

20 => ".d", // _d
21 => ".y", // .y

60 => "k.N", // \-/ (candrabindu)
61 => "_", // _ (ha_uk)
62 => "^", // ^ (ext. sandhi)
63 => ".a", // avagraha
64 => "k.c", // \_/ (candra e)
65 => "/x", // \ (virama)
66 => "@", // abbreviation
67 => "`", // Latin apostrophe

);

$ch['hk'] = array(

30 => "%A", // _a
31 => "%I", // _i
32 => "%U", // _u
33 => "%R", // .r
34 => "%q", // _.r
35 => "%G", // 'n
36 => "%J", // ~n
37 => "%T", // .t
38 => "%D", // .d
39 => "%N", // .n
40 => "%z", // 's
41 => "%S", // .s
42 => "%M", // 'm (anusvara)
43 => "%H", // .h (visarga)
44 => "%L", // .l
45 => "%W", // _.l

50 => "%P", // _D
51 => "%Y", // .Y

1 => "A", // _a
2 => "I", // _i
3 => "U", // _u
4 => "R", // .r
5 => "q", // _.r
6 => "G", // 'n
7 => "J", // ~n
8 => "T", // .t
9 => "D", // .d 
10 => "N", // .n
11 => "z", // 's
12 => "S", // .s
13 => "M", // 'm (anusvara)
14 => "H", // .h (visarga)
15 => "L", // .l
16 => "W", // _.l

20 => "P", // _d
21 => "Y", // .y


60 => "~", // \-/ (candrabindu)
61 => "_", // _ (ha_uk)
62 => "^", // ^ (ext. sandhi)
63 => "'", // avagraha
64 => "~", // \_/ (candra e)
65 => "/x", // \ (virama)
66 => "@", // abbreviation
67 => "`", // Latin apostrophe

);
$yukt = array(
	
	307 => "ृ", // R joint
	308 => "ॄ", // q joint
	309 => "ॢ",
	310 => "ॣ", // W  joint
	
	311 => "े", // e joint
	312 => "ै", // ai joint
	313 => "ो", // o joint
	314 => "ौ", // au joint
	
	301 => "&#8205;", // a joint
	302 => "ा", // A joint
	303 => "ि", // i joint
	304 => "ी", // I joint
	305 => "ु", // u joint
	306 => "ू", // U joint
	320 => "ुँ",
       
);
$yukt1 = array(

	307 => "ṛ", // joint
	308 => "ṝ", // joint
	309 => "ḷ",
	310 => "ḹ", // joint
	
	311 => "e", // joint
	312 => "ai", // joint
	313 => "o", // joint
	314 => "au", // joint
	
	301 => "a", // joint
	302 => "ā", // joint
	303 => "i", // joint
	304 => "ī", // joint
	305 => "u", // joint
	306 => "ū", // joint
	320 => "@",

);

// Specify the input data type
$type = $ch['unicode'];
// Now starts the main coding part. We will run this as long as the whole array $test is exhausted

                /* Coding for sorting */

$i=0;
while ($i<$count)
{ // reads the 'i'th member of the array. $test[0] would mean the first member of the array.
   $text = $test[$i];
   $text = str_replace("\t"," ",$text);
   $text = trim($text);

                /* Finding out the $pre (numbers preceding devanagari, if any),
                 * $post (numbers suffixed to devanagari, if any),
                 * $original (the original devanagari data, to be called back after sorting)
                 * $c (the devanagari data for manipulation, for doing intermediate work of sorting),
                 */

// The following preg_split function splits the $text into parts where this particular delimiter occurs. e.g. "12 ??? ??" would be split into this array
   // Array ( [0] => ?12 [1] => [2] => ??? [3] => [4] => ?? ). Here the even numbers are the main data and the odd numbers are the delimiters - in this case " ".
   
$delimiters = " \-*()@\ ";

$a = preg_split('/([' . $delimiters . '])/m', $text, null,PREG_SPLIT_DELIM_CAPTURE );

// Now we find out four parameters. 
    //  $pre is the data which precedes the actual word. In our example, it is 12. 
    // $c is the actual data which is to be sorted. In our example it is ???
    // $post is the data which is suffixed to the data. In our example it is ??
    // $original is the whole data. In our example it is 12 ??? ??.
    // Bear in mind that the $pre and $post are not mandatory. If they occur, they will be stored. Otherwise we define them as "".

//  finds out whether the first component is a number series.
if (preg_match('/[0-9]+/',$a[0]))
        
{   // if yes
    $pre[$i] = $a[0];
    $q=2;$c[$i] = "";
        // find out whether the last component is a number series
        if (preg_match('/[0-9].\r\n$/',$a[count($a)-1]))
        {//if yes, we define the four parameters accordingly.
        while($q < count($a)-1)
        {
            $c[$i] = $c[$i].$a[$q];
            $q=$q+2;
        }
        $post[$i] = $a[count($a)-1];
        $original[$i] = ltrim((chop($text,$post[$i])),$pre[$i]);
        }
    else {//if not, we define the four parameters accordingly.
        while($q <count($a))
        {
            $c[$i] = $c[$i].$a[$q];
            $q=$q+2;
        }
        $post[$i] = "";
        $original[$i] = ltrim((chop($text,$post[$i])),$pre[$i]);
        }
}
// if the first component is not a number series
 else 
{    
    $pre[$i] = "";
    $q=0;$c[$i] = "";$original[$i]="";
        // if the last component is a number series
        if (preg_match('/[0-9].\r\n$/',$a[count($a)-1]))
        {//if yes, we define the four parameters accordingly.
        while($q <count($a)-1)
        {
            $c[$i] = $c[$i].$a[$q];
            $q=$q+2;
        }
        $post[$i] = $a[count($a)-1];
        $original[$i] = ltrim((chop($text,$post[$i])),$pre[$i]);
        }
    else {// if not, we define the four parameters accordingly.
        //echo "the last component is not a number series"."</br>";
        while($q <count($a))
        {
            $c[$i] = $c[$i].$a[$q];
            $q=$q+2;
        }
        $post[$i] = "";
        $original[$i] = ltrim((chop($text,$post[$i])),$pre[$i]);
        }
        
}
// Now our four parameters are defined for all four possibility. First number- last number, first number - last no number, first no number - last number, first no  number - last no number.

// Now we will sort our array according to the $c parameter, because we have removed the (,*,) and such other characters from the $c.
// Therefore it will sort properly. Original data may not sort properly.
// So now onwards we will work on the $c. Once we are good to sort it properly, we will store $c,$original,$pre and $post in an array and thereafter sort the array.
// Thereafter we will echo only the $original, so that the original data is displayed on browser.


/* Sort-key normalization now consolidated in lib/SanskritSorting.php (H6069):
 * accented marks, anusvara->varga, terminal halant marker, visarga sandhi
 * (Siddhanta Kaumudi 8.3.34-36), RU/lR placement, anusvara/visarga position,
 * L/lh (ळ/ळ्ह), OM, tilde, ignored special characters. */
$c[$i] = sanskrit_sorting_key($c[$i], array('halant_loop' => true, 'tilde_to_anusvara' => true));
$original[$i] = str_replace("ंं","ँ",$original[$i]);
// word-endings sort key: reverse the codepoint groups (was inline).
$c[$i] = sanskrit_sorting_reverse_key($c[$i]);
$i++;
}


// creating a multidimentional array $araay which contains $c,$original, $pre and $post.
$i=0;
while ($i<count($test))
{
//$array[$i] = array('$c' => $c[$i], '$original' => $original[$i], '$pre' => $pre[$i] , '$post' => $post[$i]);
// for accents we take $orig. Otherwise for numbered series etc we take $original.
$array[$i] = array('$c' => $c[$i], '$original' => $orig[$i], '$pre' => $pre[$i] , '$post' => $post[$i]);
$i++;
}



// Sorting $array by $c.
usort($array, sanskrit_sorting_build_sorter('$c'));



$i=0;
while($i<count($test))
{               /* Coding for interchanging between panchama letter and anusvara (Optional) */
    
    
// If you want to keep anusvaras and don't want to convert the panchama letters back to anusvaras, uncomment this section.    
// $panchama is 0 by default. If you want you can change it to 1 in conf.php.
if ($panchama===1)
{
$array[$i]['$original']= json_encode($array[$i]['$original']);
    $k=0;
While($k<5)
{
$array[$i]['$original']= str_replace("\u0919\u094d".$kavarga[$k],"\u0902".$kavarga[$k],$array[$i]['$original']);
$array[$i]['$original']= str_replace("\u091e\u094d".$cavarga[$k],"\u0902".$cavarga[$k],$array[$i]['$original']);
$array[$i]['$original']= str_replace("\u0923\u094d".$Tavarga[$k],"\u0902".$Tavarga[$k],$array[$i]['$original']);
$array[$i]['$original']= str_replace("\u0928\u094d".$tavarga[$k],"\u0902".$tavarga[$k],$array[$i]['$original']);
$array[$i]['$original']= str_replace("\u092e\u094d".$pavarga[$k],"\u0902".$pavarga[$k],$array[$i]['$original']);
$k++;
}
$array[$i]['$original'] = json_decode($array[$i]['$original']);
}
  

    /* Coding for displaying the output in .txt file or showing in the browser. */
 
// this will show $pre, $original and $post separated by a space. ltrim removes the left white spaces and chop removes the right white spaces if any.
$outputtext[$i] = ltrim(chop($array[$i]['$pre']." ".$array[$i]['$original']." ".$array[$i]['$post']));
$outputtext[$i] = trim($outputtext[$i]);
$i++;
}

/* displaying a file with simple listing. No headers, no bookmarks. */
$out4=fopen($outfile4,"w+");
for($i=0;$i<count($outputtext);$i++)
{
    fputs($out4,$outputtext[$i]."\r\n");
}    
fclose ($out4);

/* preparation for headers and bookmarks */
$text2= array_map('removeaccent',$outputtext);
$outputtext = array_map('convert',$outputtext);
$text = array_map('json_encode',$outputtext);
for($i=0;$i<count($outputtext);$i++)
{
    $text1[$i]=str_replace(array("१","२","३","४","५","६","७","८","९","०"),array("","","","","","","","","","",""),$outputtext[$i]);
    $text2[$i]=str_replace(array("1","2","3","4","5","6","7","8","9","0"),array("","","","","","","","","","",""),$text2[$i]);
}
$text2= array_map('convert',$text2);
$text2=array_map('json_encode',$text2);
$out1=fopen($outfile,"w+");

/* If you want code for header + counter for different headers + separate identity for 'kA',"khA' etc, keep this section open. */
if ($display===1)
{
    for($i=0;$i<count($text2);$i++)
    {

        $a[$i]=substr($text2[$i],-7);
        $b[$i]=substr($text2[$i],-13);
        $x[$i]=substr($text2[$i],-19);
        if ($x[$i]==='\u0933\u094d\u0939"' && $xx!==1)
        {
            $xx=1;
            if ($i!==0)
            {
            $counter=$i;
            }
        fputs($out1,"| ".json_decode('"'.$x[$i])." |"."\r\n");
        }
        elseif ($a[$i]==='\u094d"' && $b[$i]!==$b[$i-1])
        {
            if ($i!==0)
            {
            $counter=$i;
            }
        fputs($out1,"| ".json_decode('"'.$b[$i])." |"."\r\n");
        }
        elseif (in_array($a[$i],array('\u093e"','\u093f"','\u0940"','\u0941"','\u0942"','\u0943"','\u0944"','\u0945"','\u0946"','\u0947"','\u0948"','\u0949"','\u094a"','\u094b"','\u094c"',) ) && $b[$i]!==$b[$i-1])
        {
            if ($i!==0)
            {
            $counter=$i;
            } 
        fputs($out1,"| ".json_decode('"'.$b[$i])." |"."\r\n");
        }
        elseif ($a[$i]!==$a[$i-1] && !in_array($a[$i],array("\u002d")))
        {

            if ($i!==0)
            {
            $counter=$i;
            }
        fputs($out1,"| ".json_decode('"'.$a[$i])." |"."\r\n");
        }
        fputs($out1,json_decode($text[$i])."\r\n");
    }
}
/* If you want code for header + counter for different headers (without 'kA','khA' etc), keep this section open. */
if ($display===2)
{
    for($i=0;$i<count($text);$i++)
    {
        $a[$i]=substr($text[$i],-7);
        $b[$i]=substr($text[$i],-13);        
        if ($a[$i]==='\u094d"' && $b[$i]!==$b[$i-1])
        {
            if ($i!==0)
            {
            $counter=$i;
            }
        fputs($out1,"| ".json_decode('"'.$b[$i])." |"."\r\n");
        }
        elseif ($a[$i]!==$a[$i-1])
        {
            if ($i!==0)
            {
            $counter=$i;
            }
        fputs($out1,"| ".json_decode('"'.$a[$i])." |"."\r\n");
        }
        $text[$i]=  json_decode($text[$i]);
        fputs($out1,$text[$i]."\r\n");
    }
}
/* If you want only list and no header, keep this section open. */
if ($display===3)
{
    for($i=0;$i<count($text);$i++)
    {
        fputs($out1,$outputtext[$i]."\r\n");
    }    
}


/* code for counter of pratyayas */
$counter=0;
$pratyayas=array_map('trim',$pratyayas);
$outputtext=array_map('trim',$outputtext);
$pratyayasslp=array_map('convert1',$pratyayas);
$lengthpratyayas=array_map('strlen',$pratyayasslp);
$pratyayastatistics_reverse=fopen($pratyayareverse,'w+');
for($i=0;$i<count($pratyayas);$i++)
{
    $array1[$i] = array('$pratyayas' => $pratyayas[$i], '$pratyayasslp' => $pratyayasslp[$i], '$lengthpratyayas' => $lengthpratyayas[$i] );
}
usort($array1, sanskrit_sorting_build_sorter1('$lengthpratyayas'));
    foreach ($outputtext as $val1)
    {
        if (strpos($val1,'ळ्ह')!==false )
        {            
             $outputtext=array_diff($outputtext,array($val1));
        }
    }
for ($i=0;$i<count($array1);$i++)
{
    foreach ($outputtext as $val1)
    {
        if ((substr($val1,-strlen($array1[$i]['$pratyayas']))===$array1[$i]['$pratyayas']) )
        {            
            $e[]=$val1;
        }
    }
    if (count($e)>0)
    {
            fputs($pratyayastatistics_reverse,"-".slptoiast(convert1($array1[$i]['$pratyayas']))." ".count($e)."\r\n");
            $outputtext=array_diff($outputtext,$e);
    }
    $e=array();
}
fclose($pratyayastatistics_reverse);

/* The code for sorting pratyayawise with numbers of words ending with pratyayas. */
if ($display===4)
{
    foreach ($pratyayas as $value)
    {
        foreach ($outputtext as $val1)
        {
            if (substr($val1,-strlen($value))===$value)
            {            
                $e[]=$val1;
            }
        }
        if (count($e)>0)
        {
                echo "। ".$value." ।";
                foreach ($e as $val2)
                {
                    echo "<br>".$val2;
                }
                    echo " (".count($e).")<br>";
        }
        $e=array();
    }
}

fclose($out1);


/* Highlighting first occurrence of the pratyaya */
$out2=fopen($outfile2,"w+");
$fileopen=file($outfile);
$fileopen=array_map('convert1',$fileopen);
$fileopen=array_map('trim',$fileopen);
for ($i=0;$i<count($pratyayas);$i++)
{
    $pra=preg_quote($pratyayas[$i]);
            $count=0;
    for ($j=0;$j<count($fileopen);$j++)
    {
        if(substr($fileopen[$j],-strlen($pratyayasslp[$i]))===$pratyayasslp[$i] && $count===0 && $fileopen[$j]!==$pratyayasslp[$i] && substr($fileopen[$j],-3)!=="Lha")
        {
            $fileopen[$j]=substr($fileopen[$j],0,(strlen($fileopen[$j])-strlen($pratyayasslp[$i])))."<b>".$pratyayasslp[$i]."</b>";
            $count++;
        }
    }
}
$fileopen=array_map('slptoiast',$fileopen);
// if you want to add '\' at the begining and the end of the word
if ($slashdef===1) {$fileopen=array_map('slash',$fileopen); }
// if you want to add # at the beginning and end of the word
if ($slashdef===2) {$fileopen=array_map('addhash',$fileopen);}
$senttext=implode("<br>",$fileopen);
fputs($out2,$htmlhead);
fputs($out2,$senttext);
fputs($out2,'<br></body></html>');
fclose($out2);

/* creating an array */
$a=array("a","A","i","I","u","U","f","F","x","X","e","o","E","O",);
$b=array("k","K","g","G","c","C","j","J","w","W","q","Q","R","t","T","d","D","n","p","P","b","B","m","y","r","l","v","S","z","s","L","|",);
foreach ($a as $val1)
{
    foreach ($b as $val2)
    {
        $c[]=$val2.$val1;
    }
}
$d=array_map('slptoiast',$c);
$vowcon=array("k","kh","g","gh","ṅ","c","ch","j","jh","ñ","ṭ","ṭh","ḍ","ḍh","ṇ","t","th","d","dh","n","p","ph","b","bh","m","y","r","l","v","ś","ṣ","s","|","ḻ","a","ā","i","ī","u","ū","ṛ","ṝ","ḷ","ḹ","e","ai","o","au","ṁ","ḥ");
/* code for creating an index like that of Oliver's */
$in=file_get_contents($outfile2); 
$out3=fopen($outfile3,"w+");
$bookmarks=preg_split('/[|][ ]([^|]*)[ ][|]/',$in,null,PREG_SPLIT_DELIM_CAPTURE);
fputs($out3,'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <META HTTP-EQUIV="Content-Language" CONTENT="HI">
  <!--<meta name="language" content="hi"> -->
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  </meta>
  
</head>
    <body>');
for($i=1;$i<count($bookmarks)/2;$i++)
{ 
    $p=explode("<br>",$bookmarks[2*$i]);
    if (in_array($bookmarks[2*$i-1],$vowcon) && in_array($bookmarks[2*$i+1],$d))
    {
    $bookmarks[(2*$i)-1]=str_replace($bookmarks[2*$i-1],'-<a href="#'.$bookmarks[2*$i-1].'">'.$bookmarks[2*$i-1]."</a>",$bookmarks[(2*$i)-1]);
        fwrite($out3,$bookmarks[(2*$i)-1]." (".(count($p)-2).");<br/> ");                
    }
    elseif ((substr($bookmarks[2*$i-1],-1)===substr($bookmarks[2*$i+1],-1)) || in_array($bookmarks[2*$i-1],$vowcon))
    {
    $bookmarks[(2*$i)-1]=str_replace($bookmarks[2*$i-1],'-<a href="#'.$bookmarks[2*$i-1].'">'.$bookmarks[2*$i-1]."</a>",$bookmarks[(2*$i)-1]);
        fwrite($out3,$bookmarks[(2*$i)-1]." (".(count($p)-2)."), ");
    }
    else
    {
    $bookmarks[(2*$i)-1]=str_replace($bookmarks[2*$i-1],'-<a href="#'.$bookmarks[2*$i-1].'">'.$bookmarks[2*$i-1]."</a>",$bookmarks[(2*$i)-1]);
        fwrite($out3,$bookmarks[(2*$i)-1]." (".(count($p)-2).");<br/> ");        
    }
    $bookmarks[(2*$i)-1]=str_replace('-<a href="#','| <a id="',$bookmarks[(2*$i)-1]);
    $bookmarks[(2*$i)-1]=str_replace('</a>','</a> |',$bookmarks[(2*$i)-1]);
    $bookmarks[(2*$i)]=$bookmarks[(2*$i)]."<br/>";
    $bookmarks[0]="";
}
fputs($out3,"<br/>");
$finaldisplay=implode("",$bookmarks);
$finaldisplay=addaccent($finaldisplay);
fputs($out3,$finaldisplay);
fclose($out3);

$in4=  file_get_contents($outfile);
$in4=str_replace(array("\\","/","^",),array("॒","॔","॑",),$in4);
file_put_contents($outfile,$in4);

function slptoiast($text)
{
    global $ch; global $yukt; global $yukt1;
    $text=str_replace($ch['slp'],$ch['unicode'],$text);
    $text=str_replace($yukt,$yukt1,$text);
    return $text;
}
function slash($text)
{
$text = "/".$text."/";
$text = str_replace("/|","|",$text);
$text = str_replace("|/","|",$text);
return $text;
}
function addhash($text)
{
$text = "#".$text."#";
$text = str_replace("#|","|",$text);
$text = str_replace("|#","|",$text);
return $text;
}
?>