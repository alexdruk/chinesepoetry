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
my $content ='';
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
		my $pattern = '<p class="a18">.*?</p>\n';
#		my $pattern = '<p class="a18">.*?&#xa0;</p>\n';
		my @descriptions_blocks = $block =~ /$pattern(.+?)(?=$pattern|$)/gs;
		my $blockNew = $block;
		foreach my $d (@descriptions_blocks) {
			$blockNew =~ s/\Q$d\E//gms;
		}
		$blockNew =~ s/\Q$pattern\E//gms;
#		print OUT1 $blockNew;
		my @img_blocks = $block =~ /<img(.+?)>/gs;
		my @imgs = map {'<img'.$_.'>'} @img_blocks;
		@imgs = map {local $_ = $_; $_ =~ s/ style(.+?)>/>/; $_ } @imgs;
		@imgs = map {local $_ = $_; $_ =~ s/ alt(.+?)>/>/; $_ } @imgs;
		@imgs = map {local $_ = $_; $_ =~ s/>/ class="centerimage">/; $_ } @imgs;
		my $images = join(' ', @imgs);
		foreach my $d (@descriptions_blocks) {
#			$d =~ s/\Q$pattern\E//gms;
			$d =~ s/<img(.+?)>//gms; #cut off images from descriptions
		}
		if ($author) {
			print OUT "<h1>$author</h1>\n";
			if ($images) {
				print OUT '<div id="images" class="images">'.$images.'</div>', "\n";
			}
#			print OUT "$block\n";
			my $divider = '<p class="a18">&nbsp;</p>'."\n";
			print OUT join($divider, @descriptions_blocks), "\n";
		}
		#now delete authors blocks from text
	}
}
close(OUT);
#close(OUT1);

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
