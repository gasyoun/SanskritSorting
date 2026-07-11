# SanskritSorting

_Created: 04-10-2014 · Last updated: 11-07-2026_

A PHP command-line toolkit that sorts Sanskrit word lists into the traditional
dictionary alphabetical order — the order used by the classical printed lexica,
which ordinary spreadsheet/`sort` collation gets wrong.

Originally written by Dr. Dhaval Patel (upstream:
[drdhaval2785/SanskritSorting](https://github.com/drdhaval2785/SanskritSorting),
first published 2014) for Sanskrit natural-language programming. This is the
`gasyoun` maintenance fork.

## Why this exists

The default collation of MS Excel (and most generic sorting tools) does not
follow the alphabet order known from the classical Sanskrit dictionaries — for
example the order described in
[MW 1899, p. XIV](http://www.sanskrit-lexicon.uni-koeln.de/scans/csldoc/dictionaries/prefaces/mdpref/mdpref10.html)
("the usual Sanskrit Dictionary order of the alphabet").

Concretely, the tool handles cases such as:

```
saMkalpa (संकल्प) should sort as saNkalpa (सङ्कल्प)   — the major case
RR (ॠ) and lRR (लॄ) must sort in their correct places
ळ and ळ्ह must sort correctly
hyphens, apostrophes, and similar marks should be ignored while sorting
```

## Tools

Two families of sorters are provided, each with several numbered revisions —
**use the highest-numbered file in each family**:

- **Forward (normal) sorting** — the `multi*.php` series. Current:
  [`multi13.php`](https://github.com/gasyoun/SanskritSorting/blob/master/multi13.php).
- **Reverse sorting** (by word endings) — the `reverse*.php` series. Current:
  [`reverse22.php`](https://github.com/gasyoun/SanskritSorting/blob/master/reverse22.php).

Word-by-word variants
([`multi_wordbyword.php`](https://github.com/gasyoun/SanskritSorting/blob/master/multi_wordbyword.php),
[`multi_wordbyword2.php`](https://github.com/gasyoun/SanskritSorting/blob/master/multi_wordbyword2.php))
and folder-oriented variants
([`foldersorter.php`](https://github.com/gasyoun/SanskritSorting/blob/master/foldersorter.php),
[`reversefoldersorter.php`](https://github.com/gasyoun/SanskritSorting/blob/master/reversefoldersorter.php))
are also present for batch use.

## Usage (command line)

```
php multi13.php   outputfolder inputtext morphologicends
php reverse22.php outputfolder inputtext morphologicends
```

Worked examples:

```
php multi13.php   d:\sorting d:\sorting\input\input.txt d:\sorting\input\morphologicends.txt
php reverse22.php d:\sorting d:\sorting\input\input.txt d:\sorting\input\morphologicends.txt
```

`morphologicends` is the list of morphological endings used by the reverse
sorter — a sample lives at
[`morphologicends.txt`](https://github.com/gasyoun/SanskritSorting/blob/master/morphologicends.txt).

## Output

**Forward sorting** (`multi13.php`):

```
normalsorted1.txt  - sorted list with headers
normalsorted2.html - HTML of sorted list with headers
normalsorted3.html - HTML with headers + bookmarks
normalsorted4.txt  - plain sorted list, no headers / no bookmarks
```

**Reverse sorting** (`reverse22.php`):

```
reversesorted1.txt  - sorted list with headers
reversesorted2.html - HTML of sorted list with headers
reversesorted3.html - HTML with headers + bookmarks
reversesorted4.txt  - plain sorted list, no headers / no bookmarks
reverse_pratyaya_stats.txt - statistics about the word endings
```

## Credits

Code by Dr. Dhaval Patel
([drdhaval2785/SanskritSorting](https://github.com/drdhaval2785/SanskritSorting)).
Fork maintenance and documentation by Dr. Mārcis Gasūns. Part of the wider
[Cologne Digital Sanskrit Dictionaries](https://github.com/sanskrit-lexicon)
tooling ecosystem.

_Dr. Mārcis Gasūns_
