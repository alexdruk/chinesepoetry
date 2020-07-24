#!/Perl/bin/perl -w
###################################################################
### extract references to bibliography from html file АНТОЛОГИЯ_1column_utf.htm
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $str = '(У Хань. "Орнителогические образы в русской и китайской поэзии первой трети XX века" (диссертация), Волгоград, 2015)';
print $str, "\n";
$str =~ s|[ ()"'«»]||g;
print $str, "\n";
