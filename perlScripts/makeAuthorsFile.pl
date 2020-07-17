#!/Perl/bin/perl -w
###################################################################
### make html word-like file with authors descriptions from html file АНТОЛОГИЯ_1column_utf.htm
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = './АНТОЛОГИЯ_copy/copy1.html';
my $out_file = './АНТОЛОГИЯ_copy/allAuthors.html';
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
open(OUT, ">> $out_file") || die "Can't open $out_file Code: $!";
	print OUT $head;
close(OUT);
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
#change Non-breaking space to a regular one See extract_biblio.pl
$content =~ s/ / /gs; #first space is  Non-breaking space
my @blocks = $content =~ /(<h1.*?)(?=<h1>|$)/gs;  #s - to get data from multiline text, use Lookahead Assertions https://www.regular-expressions.info/lookaround.html, |$ - to match last occurrence without <h1> tag
my $n = scalar @blocks;
print  "Total matches $n\n";
open(OUT, ">> $out_file") || die "Can't open $out_file Code: $!";
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

		my $pattern = <<'END_MESSAGE';
			<p class="a18">
				&#xa0;
			</p>
END_MESSAGE
		my @descriptions_blocks = $block =~ /$pattern(.+?)$pattern/gs;
#	print  "\tTotal matches:", scalar  @descriptions_blocks, "\n";
		print OUT "<h1>$author</h1>", "\n";
		print OUT join('',@descriptions_blocks), "\n";
	}
}
print OUT "</body>\n</html>\n";
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
