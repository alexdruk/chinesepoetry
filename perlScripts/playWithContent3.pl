###################################################################
### play with content table
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;
use Data::Dumper;

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
	# next if !($author =~ /ФАНЬ ЧЭНДА/);

	my @translators = $b =~ /(<p class="TOC1">.*?)(?=<p class="TOC1">|$)/gs;  #s - to get data from multiline text, use Lookahead Assertions https://www.regular-expressions.info/lookaround.html, |$ - to match last occurrence without <h1> tag
	my @poems = ();

	foreach my $t (@translators) {
		my @lines = split("\n", $t);

		my $translator = shift @lines;

		my $prevToc = 0;
		my $currentToc = 0;
		my $inCycle = 0;
		my $cycleName;
		my $notRealPoem;
		my $subCycleName;

		foreach my $line (@lines) {
			$line =~ /TOC(\d)/;
			$currentToc = $1;

			# TOC2 is always a cycle
			if ($currentToc == 2) {
				$inCycle = 1;
				$cycleName = $line;
				next;
			}

			my $poem = {
				"poem" => $line,
				"translator" => $translator,
				"author" => $author
			};

			if ($cycleName) {
				$poem->{"cycle"} = $cycleName;
			}

			# when we find TOC4, it means previous TOC3 is not a poem, but a subcycle
			if ($currentToc == 4) {
				if ($prevToc != 4) {
					$notRealPoem = pop @poems;
					$subCycleName = $notRealPoem->{"poem"};
				}
				$poem->{"subcycle"} = $subCycleName;
			}

			push(@poems, $poem);

			$prevToc = $currentToc;
		}
	}

	print Dumper(\@poems);
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
