###################################################################
### play with content table
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = './contenttable2.html';
my $out_file = './contenttable3.html';
my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
my @blocks = $content =~ /(<h1>.*?)(?=<h1>|$)/gs;  #s - to get data from multiline text, use Lookahead Assertions https://www.regular-expressions.info/lookaround.html, |$ - to match last occurrence without <h1> tag
my $cnt=0;
foreach my $b (@blocks) {
	$cnt++;
	my $author = '';
	if ( $b =~ /<h1>(.+?)<\/h1>/) {
		$author = HTML::FormatText->new->format(parse_html(decode_utf8($1)));
		$author = encode_utf8($author);
		$author =~ s/^\s+|\s+$//;
	}
	next if !($author =~ /ФАНЬ ЧЭНДА/);
	print $author, "\n";
	my $array_elems_num = 0;
	my @authorArray = ();
	if ($b =~ /TOC4/s) {
		$array_elems_num = 4;
		@authorArray = (0,0,0,0);
	}
	elsif ($b =~ /TOC3/s) {
		$array_elems_num = 3;
		@authorArray = (0,0,0);
	}
	my @lines = split("\n", $b);
	my $currentToc =0;
	my $prevToc = 0;
	foreach my $line (@lines) {
#	print $line, "\n";
		if ($line =~ /TOC1/) {
			@authorArray = map {$_= 0} @authorArray;
#			print "Array:", join(',', @authorArray),"\n";
			$authorArray[0] = 1;
			$currentToc = 1;
#				$prevToc = 1;
		}
		elsif ($line =~ /TOC(\d)/) {
			$currentToc = $1;
			if ($currentToc > $authorArray[$currentToc]) {
				$authorArray[$currentToc-1] = $currentToc;
			}
			else {
				@authorArray = map { $_ = 0 if ($_ > $currentToc) } @authorArray;
				$authorArray[$currentToc-1] = $currentToc;
			}
		}
		print $line, join(',', @authorArray), "\n";
	}
#	print "\n\n\n";
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
