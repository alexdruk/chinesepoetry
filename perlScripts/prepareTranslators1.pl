#!/Perl/bin/perl -w
###################################################################
### you can get description OR html without descriptions
### to get description file uncomment
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = './translators.html';
my $out_file = './final/translators.sql';
my $tr_file = './translators.tsv';
my %translators = ();
open(IN, "< $tr_file") || die "Cannot open $tr_file.Code: $!";
	while (my $line=<IN>) {
		chomp($line);
		my ($id, $name) = split("\t", $line);
		$name =~ s/ //g;
		$translators{$name} = $id;
	}
close(IN);

my $content ='';
my $head = <<'HEAD';
<!DOCTYPE html>
<html lang="ru-RU">
	<head>
		<meta charset="utf-8" />
		<title>Anthology</title>
		<link href="chinesepoetry.css" type="text/css" rel="stylesheet" />
		<link href="chinesepoetry2.css" type="text/css" rel="stylesheet" />
	</head>
	<body style="background:#ffffff">
HEAD
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
#	print OUT $head;
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
#change Non-breaking space to a regular one See extract_biblio.pl
my %classes = ();
$content =~ s|^(.+?)<body>||gs;
$content =~ s/ / /gs; #first space is  Non-breaking space
$content =~ s|<h(\d+).*?>\s*(.+?)\s*</h(\d+)>|<h$1>$2</h$3>|gs; #to make all h to be in 1 line
$content =~ s|<p(.*?)>\s*(.+?)\s*</p>|<p$1>$2</p>|gs; #to make all p to be in 1 line
$content =~ s|&quot;|"|gs;#
$content =~ s/&laquo;|&raquo;/"/gs; # change russian quotes
$content =~ s/&ldquo;|&rdquo;/"/gs; # change russian quotes
$content =~ s/&#xa0; / /gs; ##double spaces
$content =~ s|&#xa0;| |gs; # to white space
$content =~ s/&#xad;/&nbsp;/gs;#SOFT HYPHEN
$content =~ s|&mdash;|-|gs;# defis
$content =~ s| style="background-color:transparent"||gs;# delete
$content =~ s|<span style="background-color:#ffffff">(.*?)</span>|$1|gs;# blue font
$content =~ s|<p><span style="display:none"> </span></p>|<p>&nbsp;</p>|gs;# blue font
$content =~ s|<p style="margin-bottom:6pt; border-top:0.75pt solid #000000; padding-top:1pt"> </p>||gs;# hr
$content =~ s|<img (.+?)float:left" />|<img $1" class="imageleft" />|gs;#
$content =~ s|<img (.+?)float:right" />|<img $1" class="imageright" />|gs;#
$content =~ s| style="(.+?)"||gs;# eliminate styles within p
$content =~ s|<p class="a5"> </p>||gs;# hr
$content =~ s| style="(.+?)"||gs;# eliminate styles within p
$content =~ s|<span (.+?)>(.*?)</span>|$2|gs;# eliminate all spans
$content =~ s|<span>(.*?)</span>|$1|gs;# eliminate all spans
$content =~ s|<p class="a(\d+?)"> </p>|<p>&nbsp;</p>|gs;#
$content =~ s|<p> </p>|<p>&nbsp;</p>|gs;#
$content =~ s|<p><em> </em></p>|<p>&nbsp;</p>|gs;#
$content =~ s|<a href=(.+?)>(.+?)</a>|$2|gs;#
$content =~ s|alt="http(.+?)"||gs;#
$content =~ s|<p class="a10">Неизвестно</p>||gs;# unknown DOB
$content =~ s|<p class="a22">Нет данных.</p>||gs;# empty record
$content =~ s|<p><em>(.+?)</em></p>|<p class="a16">$1</p>|gs;# Kravzova
$content =~ s|<div>||gs;#
$content =~ s|</div>||gs;#
$content =~ s|<br />||gs;#
$content =~ s|class="a28">\s*По|class="a14">По|gs;#
$content =~ s|<p class="a\d+">Основные работы:</p>|<p class="a26">Основные работы:</p>|gs;#
$content =~ s|<p class="a31">(.+?)</p>|<p><em>$1</em></p>|gs;#
$content =~ s|<p class="a|<p class="t|gs;# CHANGE ALL a CLASSES TO t
$content =~ s|images/translators|/images/translators|gs;#
$content =~ s|<p>&nbsp;</p>||gs;#
$content =~ s|<p class="t2"></p>||gs;#
$content =~ s|<p></p>||gs;#

my @cl = $content =~ /class="t(\d+?)"/gs;
foreach my $t (@cl) {
		if(exists($classes{$t})) {
			$classes{$t} += 1;
		} else {
			$classes{$t} = 1;
		}
}

my @blocks = $content =~/(<p class="t20">.+?)(?=<p class="t20">|$)/gs;
my $n = scalar @blocks;
print  "Total matches $n\n";
foreach my $block (@blocks) {
	my $tr_short = '';
	my $tr_full = '';
	my $dob = 'NULL';
	my $sum = 'NULL';
	my $img = 'NULL';
	my $source = 'NULL';
	if ($block =~ /<img(.+?)>/) {
		$img = $1;
		$img = '<img'.$img.'>';
		$block =~ s/\Q$img\E//gms; #cut off first image
	}

	if ($block =~ /<p class="t20">(.+?)<\/p>/) {
		$tr_short = $1;
		$tr_short =~ s/ //g;
		if (exists($translators{$tr_short})) {
			$tr_short = $translators{$tr_short};
		}
		else {
			print "does not match $tr_short\n";
		}
	}
	if ($block =~ /<p class="t19">(.+?)<\/p>/) {
		$tr_full = $1;
	}
	if ($block =~ /<p class="t10">(.+?)<\/p>/) {
		$dob = $1;
	}
	if ($block =~ /<p class="t16">(.+?)<\/p>/) {
		$sum = $1;
	}
	my $var = "$tr_short#!#$tr_full#!#$dob#!#$sum#!#$img#!#";
	$var =~ s|NULL||gs;
	print OUT $var;
	$block =~ s/<p class="t20">(.+?)<\/p>//gms; #cut off
	$block =~ s/<p class="t19">(.+?)<\/p>//gms; #cut off
	$block =~ s/<p class="t10">(.+?)<\/p>//gms; #cut off
	$block =~ s/<p class="t16">(.+?)<\/p>//gms; #cut off
	$block =~ s|<p class="t14">\s*По материалам сайтов:(.+?)</p>|<div class="d-flex align-items-end"><cite>Источник: $1</cite></div>|gms; #cut off
	$block =~ s/\Q$img\E//gms; #cut off first image
	$block =~ s/\t\t\t//gms; #
	$block =~ s/\n\s*\n/\n/gms; #cut off empty lines
	print OUT $block."##!!##";
}

close(OUT);
my $count=0;
foreach my $key (sort keys %classes) {
	$count++;
	print "$count\t$key\t$classes{$key}\n";
}

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
