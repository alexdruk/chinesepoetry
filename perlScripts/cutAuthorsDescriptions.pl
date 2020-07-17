#!/Perl/bin/perl -w
###################################################################
### cut off authors descriptions from html file АНТОЛОГИЯ_1column_utf.htm
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = './anthology/anthology2.html';
#my $out_file = './anthology/anthology3.html';
my $out_file = './author_docs.sql';
my $authors_file = './authors.csv';
my %unique_authors = ();
open(IN, "< $authors_file") || die "Cannot open $authors_file.Code: $!";
	while (my $line=<IN>) {
		chomp($line);
		my ($a,$b) = split(',', $line);
		$a =~ s/^\"//;
		$a =~ s/\"$//;
		$unique_authors{$a} = $b;
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
	</head>
	<body style="background:#ffffff">
HEAD
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
#	print OUT $head;
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
#change Non-breaking space to a regular one See extract_biblio.pl
$content =~ s/ / /gs; #first space is  Non-breaking space
my @blocks = $content =~ /(<h1.*?)(?=<h1>|$)/gs;  #s - to get data from multiline text, use Lookahead Assertions https://www.regular-expressions.info/lookaround.html, |$ - to match last occurrence without <h1> tag
my $n = scalar @blocks;
print  "Total matches $n\n";
foreach my $block (@blocks) {
	my @authors_blocks = $block =~ /<h1>(.*?)<\/h1>/gs;
	my $author = '';
	foreach my $a (@authors_blocks) {
		$a = HTML::FormatText->new->format(parse_html(decode_utf8($a)));
		$a =~ s/^\s+|\s+$//; #trim both ends
		$a = encode_utf8($a);
		$a =~ s|\n||;
		$author = $a;
#		print $author."\n";
#		if (!exists($unique_authors{$author})) {
#			print $author, "\n";
#		}

		my $pattern = <<'END_MESSAGE';
			<p class="a18">
				&nbsp;
			</p>
END_MESSAGE
		my @descriptions_blocks = $block =~ /$pattern(.+?)(?=$pattern)/gs;
		$block =~ s/\Q$pattern\E//gms;
		my @img_blocks = $block =~ /<img(.+?)>/gs;
		my @imgs = map {'<img'.$_.'>'} @img_blocks;
		@imgs = map {local $_ = $_; $_ =~ s/ style(.+?)>/>/; $_ } @imgs;
		@imgs = map {local $_ = $_; $_ =~ s/ alt(.+?)>/>/; $_ } @imgs;
		@imgs = map {local $_ = $_; $_ =~ s/>/ class="centerimage">/; $_ } @imgs;
		my $images = join(' ', @imgs);
		foreach my $d (@descriptions_blocks) {
			$d =~ s/\Q$pattern\E//gms;
			$d =~ s/<img(.+?)>//gms; #cut off images from descriptions
		}
#		print join(',', @descriptions_blocks), "\n";
#		print join(',', @img_blocks), "\n";
		if ($unique_authors{$author}) {
			print OUT "$unique_authors{$author}#!#^";
			if ($images) {
				print OUT '<div id="images" class="images">'.$images.'</div>', "\n";
			}
			print OUT join("\n",@descriptions_blocks), "^##!!##";
		}
	}
}
#print OUT "</body>\n</html>\n";
#close(OUT);

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
