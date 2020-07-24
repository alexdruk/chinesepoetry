#!/Perl/bin/perl -w
###################################################################
### to prepare topics html
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = './topics.html';
my $out_file = './final/topics1.html';
my $content ='';
my $head = <<'HEAD';
<!DOCTYPE html>
<html lang="ru-RU">
	<head>
		<meta charset="utf-8" />
		<title>topics</title>
		<link href="chinesepoetry.css" type="text/css" rel="stylesheet" />
		<link href="chinesepoetry2.css" type="text/css" rel="stylesheet" />
	</head>
	<body style="background:#ffffff">
HEAD
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
	print OUT $head;
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
$content =~ s|^(.+?)<body>||gs;
#change Non-breaking space to a regular one See extract_biblio.pl
$content =~ s/ / /gs; #first space is  Non-breaking space
#$content =~ s|<p class="a2">\s*(.+?)\s*</p>|<p class="a2">$1</p>|gs;
#$content =~ s|<p class="a22">\s*(.+?)\s*</p>|<p class="a22">$1</p>|gs;
$content =~ s|<h(\d+).*?>\s*(.+?)\s*</h(\d+)>|<h$1>$2</h$3>|gs; #to make all h to be in 1 line
$content =~ s|<p(.*?)>\s*(.+?)\s*</p>|<p$1>$2</p>|gs; #to make all p to be in 1 line
$content =~ s|&quot;|"|gs;#
$content =~ s/&laquo;|&raquo;/"/gs; # change russian quotes
$content =~ s/&ldquo;|&rdquo;/"/gs; # change russian quotes
$content =~ s/&#xa0; / /gs; ##double spaces
$content =~ s|&#xa0;| |gs; # to white space
$content =~ s/&#xad;/&nbsp;/gs;#SOFT HYPHEN
$content =~ s|&mdash;|-|gs;# defis
$content =~ s| style="(.+?)"||gs;# eliminate styles within p
$content =~ s|<span (.+?)>(.*?)</span>|$2|gs;# eliminate all spans
##$content =~ s|<p class="a"><span style="font-family:Calibri">\(\)</span></p>||gs;#
##$content =~ s|<span style="font-family:Calibri">\(\)</span>||gs;#
##$content =~ s|<p >\.</p>||gs;#
##$content =~ s|<span class="iwtooltip"(.+?)</span>||gs;#
##$content =~ s|<p class="a"(.*?)>|<p class="a">|gs;#
$content =~ s|<p>\s+</p>|<p>&nbsp;</p>|gs; # empty paragraphs
$content =~ s|<p><br /></p>|<p>&nbsp;</p>|gs; # empty paragraphs
$content =~ s|<p class="NormalWeb"|<p class="t22"|gs;
$content =~ s|<p class="-1" style="font-size:1.1em">\s*Примечания|<p class="-2">Примечания|gs; #Примечания
$content =~ s|<p class="-2">|<p class="-7">|gs; #Примечания
$content =~ s|<p class="12">|<p class="t1">|gs; #Примечания
$content =~ s|<p class="a15"(.+?)</p>|<hr>|gs; #hr
$content =~ s|<p class="-3"\s*style="font-size:0.9em">|<p class="t1">|gs; #examples
$content =~ s|<p class="-3">|<p class="t1">|gs; #examples
$content =~ s|<p class="-4">|<p class="t1">|gs; #examples
#$content =~ s|<p class="a2"> </p>||gs;
#$content =~ s|<p class="(.+?)"> </p>|<p>&nbsp;</p>|gs;
#$content =~ s|<p class="a2">\s*\("Резной дракон(.+?)\s*<\p>|<div class="d-flex align-items-end"><cite><a href="./biblio.php?biblio_id=213" id="biblio_poems">Источник: "Резной дракон. Поэзия эпохи Шести династий (III-VI вв.) в переводах М. Кравцовой", 2004</a></cite></div>|gs; #sources
$content =~ s|<p class="-1">\s*Примечания\s*</p>|<p class="-2">Примечания</p>|gs; #examples
$content =~ s|<p>(.+?)</p>|<p class="t22">$1</p>|gs; #
$content =~ s|\t\t\t<p|<p|gs; #


print OUT $content;
close(OUT);

#################################################
#to read file content in slurp mode
sub readSlurp
###
### Accetp: file name
### Return: ref to file content in slurp mode
### Usage: &readSlurp($file)
#################################################
{
	my $file = shift;
	my $file_content = '';
	open(IN, "< $file") || die  "Can't open $file Code: $!";
	{# need to be sure we do not change global $\
		local $/;
		$file_content = <IN>;
	}# need to be sure we do not change global $\
	close(IN);
	return \$file_content;
}
