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

my $in_file = './Authors_desc1/authors_desc1.html';
my $out_file = './final/authors_desc.html';
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
#open(OUT1, "> $out_file1") || die "Can't open $out_file1 Code: $!";
#	print OUT1 $head;
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
#change Non-breaking space to a regular one See extract_biblio.pl
$content =~ s/ / /gs; #first space is  Non-breaking space
#$content =~ s|<p class="a">\s*(.+?)\s*</p>|<p class="a">$1</p>|gs;
#$content =~ s|<p class="a2">\s*(.+?)\s*</p>|<p class="a2">$1</p>|gs;
#$content =~ s|<p class="a22">\s*(.+?)\s*</p>|<p class="a22">$1</p>|gs;
$content =~ s|<h(\d+).*?>\s*(.+?)\s*</h(\d+)>|<h$1>$2</h$3>|gs; #to make all h to be in 1 line
$content =~ s|<p(.*?)>\s*(.+?)\s*</p>|<p$1>$2</p>|gs; #to make all p to be in 1 line

$content =~ s|<p class="a14">|<p>|gs; # a14 can be simple p
$content =~ s| class="NormalWeb"||gs; #  can be simple p
$content =~ s/&laquo;|&raquo;/"/gs; # change russian quotes
$content =~ s/&#xa0; / /gs; ##double spaces
$content =~ s/&#xa0;/&nbsp;/gs; #
$content =~ s/&#xad;/&nbsp;/gs;#SOFT HYPHEN
$content =~ s|<u><span style="color:#0000ff">(.*?)</span></u>|$1|gs;# blue font
$content =~ s|<p class="a">&nbsp;</p>||gs;#
$content =~ s|<span style="color:#0000ff">(.*?)</span>|$1|gs;# blue font
$content =~ s|<p class="a"><span style="font-family:Calibri">\(\)</span></p>||gs;#
$content =~ s|<span style="font-family:Calibri">\(\)</span>||gs;#
$content =~ s|<p >\.</p>||gs;#
$content =~ s|<span class="iwtooltip"(.+?)</span>||gs;#
$content =~ s|&quot;|"|gs;#
$content =~ s|<p class="a"(.*?)>|<p class="a">|gs;#


my @blocks = $content =~ /(<h1.*?)(?=<h1>|$)/gs;  #s - to get data from multiline text, use Lookahead Assertions https://www.regular-expressions.info/lookaround.html, |$ - to match last occurrence without <h1> tag
my $n = scalar @blocks;
print  "Total matches $n\n";
my %biblio =();
foreach my $block (@blocks) {
	my @authors_blocks = $block =~ /<h1>(.*?)<\/h1>/gs;
	my $author = '';
	foreach my $a (@authors_blocks) {
		$a = HTML::FormatText->new->format(parse_html(decode_utf8($a)));
		$a =~ s/^\s+|\s+$//; #trim both ends
		$a = encode_utf8($a);
		$a =~ s|\n||;
		$author = $a;
		$block =~ s|\/><img|\/><\/p>\n<p><img|gms;
		my @img_blocks = $block =~ /<img(.+?)>/gs;
		my @imgs = map {'<img'.$_.'>'} @img_blocks;
		if ($imgs[0]) {
			$block =~ s/\Q$imgs[0]\E//gms; #cut off first image
		}
		my $pattern = '<p class="a18">.*?</p>\n';
		my @descriptions_blocks = $block =~ /$pattern(.+?)(?=$pattern|$)/gs;
		my $biblio = '';
		foreach my $d (@descriptions_blocks) {
			$d =~ s/\Q$pattern\E//gms;
#			$d =~ s/<img(.+?)>//gms; #cut off images from descriptions
			if ($d =~ /<p class="a">(.+?)<\/p>/) {
				$biblio = $1;
				$d =~ s/\Q$biblio\E//gs; #cut of biblio paragraph and insert at the end
				$biblio = '<p class="a">'.$biblio.'</p>';
				$d = "$d$biblio\n";
			}
		}
		if ($author) {
			print OUT "<h1>$author</h1>\n";
			if ($imgs[0]) {
				$imgs[0] =~ s| style(.+?)>| />|;
				$imgs[0] =~ s| alt(.+?)>|/>|;
				$imgs[0] =~ s|/>| class="centralimage"/>|;

				print OUT '<div class="text-center">'.$imgs[0].'</div>', "\n";
			}
#			print OUT "$block\n";
#			my $divider = '<p class="a18">&nbsp;</p>'."\n";
			print OUT join("<hr>\n", @descriptions_blocks), "\n";
		}
	}
}

close(OUT);
#close(OUT1);
foreach my $key (sort { $biblio{$a} <=> $biblio{$b} } keys %biblio) {
	print "$key\t$biblio{$key}\n";
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
