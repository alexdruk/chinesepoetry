#!/Perl/bin/perl -w
###################################################################
### extract names of the poems  from html file АНТОЛОГИЯ_1column_utf.htm
###################################################################




######## NOT FINISHED YET
use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Data::Dumper;
use Encode;
#use String::CamelCase qw(camelize decamelize wordsplit);
#use utf8;

my $in_file = '../orig/АНТОЛОГИЯ_1column_utf.htm';
#my $in_file = 'tmp.htm';

my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
#change Non-breaking space to a regular one See extract_biblio.pl
$content =~ s/ / /gs; #first space is  Non-breaking space
my @blocks = $content =~ /(<h1>.*?)(?=<h1>|$)/gs;  #s - to get data from multiline text, use Lookahead Assertions https://www.regular-expressions.info/lookaround.html, |$ - to match last occurrence without <h1> tag
#@blocks = map {'<h1>'.$_} @blocks; #reinsert <h1> tag which was lost in previous matching
my $n = scalar @blocks;
print  "Total matches $n\n";
foreach my $block (@blocks) {
	my @authors_blocks = $block =~ /<h1>(.*?)<\/h1>/gs;
	my $author = '';
	foreach my $a (@authors_blocks) {
		$a = HTML::FormatText->new->format(parse_html(decode_utf8($a)));
		$a =~ s/\s+\((.+?)\)//i; #delete year
		$a =~ s/^\s+|\s+$//; #trim both ends
		$a = encode_utf8($a);
		$author = $a;
		print $author."\n";
	}
	my @translators_block = $block =~ /<p class=a2>(.*?)<\/p>/gs;
#my $m = scalar @translators_block;
#print  "Total matches $m\n";
	my $translator = '';
	foreach my $t (@translators_block) {
		$t = HTML::FormatText->new->format(parse_html(decode_utf8($t)));
		$t = encode_utf8($t);
		print "\t$t\n";
	}
}

#open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
#	print OUT $content;
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
