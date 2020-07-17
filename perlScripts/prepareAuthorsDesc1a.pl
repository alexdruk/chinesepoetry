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

my $in_file = './final/authors_desc.html';
my $out_file = './final/authors_desc2.html';
#my $out_file1 = './final/anthology1.html';
my $biblio_file = './final/biblio.tsv';
my %unique_biblio = ();
my $content ='';
open(IN, "< $biblio_file") || die "Cannot open $biblio_file.Code: $!";
	while (my $line=<IN>) {
		chomp($line);
		my ($a,$b) = split('\t', $line); #number\tstring
		$b =~ s|["' «»]||g;
		$unique_biblio{$b} = $a;
	}
close(IN);

my $head = <<'HEAD';
<!DOCTYPE html>
<html lang="ru-RU">
	<head>
		<meta charset="utf-8" />
		<title>Anthology</title>
		<link href="chinesepoetry.css" type="text/css" rel="stylesheet" />
	</head>
	<body style="background:#ffffff">
HEAD
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
	print OUT $head;
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	my $imagedir = 0;
	while (my $line=<IN>) {
	next if ($line =~ /^\s*$/); #skip empty lines
#images
	if ($line =~/centralimage/) {
			$imagedir = 1;
	}
	if ($line =~ /<img/) {
		if ($line !~/centralimage/) {
			if ($imagedir) {
				$line =~ s| style(.+?)>| class="imageleft"/>|;
				$imagedir = 0;
			}
			else {
				$line =~ s| style(.+?)>| class="imageright"/>|;
				$imagedir = 1;
			}
		}
	}
# end of images
	if ($line =~ /<p class="a"><\/p>/) {next;}
	if ($line =~ /<p><\/p>/) {next;}
	if ($line =~ /<p>\.<\/p>/) {next;}
	if ($line =~ /<p class="a">(.+?)<\/p>/) {
		my $bibref = $1;
		my $orig = $1;
		$bibref =~ s|[()"' «»]||g;
		$bibref =~ s|<strong>||g;
		$bibref =~ s|</strong>||g;
		$bibref =~ s|<span.+?>(.+?)</span>|$1|g;
		$bibref =~ s|&nbsp;||g;
		$orig =~s/<(.*?)>//gs; #delete tags
		$orig =~ s/^&nbsp;//gs;
		$orig =~ s/^ //gs;
		$orig =~ s/^\(//gs;
		$orig =~ s/\)$//gs;
		$orig =~ s/из Википедии/Википедия/gs;
		$orig =~ s/с сайта/сайт/igs;
		$orig =~ s/из //gs;
		foreach my $bibl (keys %unique_biblio) {
			if ($bibref =~ /\Q$bibl\E/) {
				$orig =~ s|\Q$orig\E|<a href=\"\.\/biblio.php\?record_id=$unique_biblio{$bibl}\" class="biblio ref">$orig<\/a>|gs;
#			print "match: $orig\n";
			}
		}
		#exceptions
		if ($orig =~ /Дальнее эхо/ ) {
			$orig = '<a href="./biblio.php?record_id=71" class="biblio ref">"Дальнее эхо. Антология китайской лирики (VII-IX вв) в переводах Ю.К. Шуцкого", 2000</a>';
		}
		elsif ($orig =~ /Федоренко/ ) {
			$orig = '<a href="./biblio.php?record_id=118" class="biblio ref">"Китайская классическая поэзия (Эпоха Тан). Сост. Н.Т. Федоренко", 1956</a>';
		}
		elsif ($orig =~ /Бхагавад/ ) {
			$orig = '<a href="./biblio.php?record_id=74" class="biblio ref">"Дао дэ цзин с параллелями из Библии и Бхагавад Гиты (перевод с англ.)", 1998</a>';
		}
		elsif ($orig =~ /лирика\. \(М\. Басманов\)/ ) {
			$orig = '<a href="./biblio.php?record_id=120" class="biblio ref">"Китайская лирика. (М. Басманов)", 2003</a>';
		}
		elsif ($orig =~ /Китайская пейзажная/ ) {
			$orig = '<a href="./biblio.php?record_id=127" class="biblio ref">"Китайская пейзажная лирика III-XIV вв", 1984</a>';
		}
		elsif ($orig =~ /ссическая поэзия \(М\. Басманов\)/ ) {
			$orig = '<a href="./biblio.php?record_id=114" class="biblio ref">"Китайская классическая поэзия (М. Басманов", 2005)</a>';
		}
		elsif ($orig =~ /Л\. Черкасский/ ) {
			$orig = '<a href="./biblio.php?record_id=129" class="biblio ref">"Китайская поэзия (Л. Черкасский)", 1982<\/a>';
		}
		elsif ($orig =~ /Резной дракон/ ) {
			$orig = '<a href="./biblio.php?record_id=213" class="biblio ref">"Резной дракон. Поэзия эпохи Шести династий (III-VI вв.) в переводах М. Кравцовой", 2004</a>';
		}
		elsif ($orig =~ /"Сон в красном тереме" Том 1, 2014/ ) {
			$orig = '<a href="./biblio.php?record_id=271" class="biblio ref">"Сон в красном тереме" Том 1, 2014</a>';
		}
		elsif ($orig =~ /образы в русской и китайской поэзии первой трети XX века/ ) {
			$orig = '<a href="./biblio.php?record_id=259" class="biblio ref">У Хань. "Орнителогические образы в русской и китайской поэзии первой трети XX века", 2015</a>';
		}
		elsif ($orig =~ /"Сорок поэтов", 197я8/ ) {
			$orig = '<a href="./biblio.php?record_id=229" class="biblio ref">"Сорок поэтов", 1978</a>';
		}
		elsif ($orig =~ /Китайская поэзия в переводах Л.Е. Черкасского/ ) {
			$orig = '<a href="./biblio.php?record_id=129" class="biblio ref">"Китайская поэзия (Л. Черкасский", 1982)</a>';
		}
		elsif ($orig =~ /Духовная культура Китая/ ) {
			$orig = '<a href="./biblio.php?record_id=90" class="biblio ref">"Духовная культура Китая. Энциклопедия. Том 3. Литература. Язык и письменность., 2008"</a>';
		}
		$line = '<div class="d-flex align-items-end"><cite>Источник: '.$orig."</cite></div>\n";
	}
	print OUT $line;
	}
close(IN);
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
