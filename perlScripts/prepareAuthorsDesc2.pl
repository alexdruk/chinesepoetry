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

my $in_file = './final/authors_desc2.html';
my $out_file = './final/authors_desc.sql';
#my $out_file1 = './final/anthology1.html';
my $content ='';
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
#	print OUT $head;
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
#$content =~ s|<h(\d+).*?>\s*(.+?)\s*</h(\d+)>|<h$1>$2</h$3>|gs; #to make all h to be in 1 line
#$content =~ s|<p(.*?)>\s*(.+?)\s*</p>|<p$1>$2</p>|gs; #to make all p to be in 1 line
#$content =~ s|<p class="a14">|<p>|gs; # a14 can be simple p
#$content =~ s| class="NormalWeb"||gs; #  can be simple p
#$content =~ s/&laquo;|&raquo;/"/gs; # change russian quotes
#$content =~ s/&#xa0; / /gs; ##double spaces
#$content =~ s/&#xa0;/&nbsp;/gs; #
#$content =~ s/&#xad;/&nbsp;/gs;#SOFT HYPHEN
#$content =~ s|<u><span style="color:#0000ff">(.*?)</span></u>|$1|gs;# blue font
#$content =~ s|<p class="a">&nbsp;</p>||gs;#
#$content =~ s|<span style="color:#0000ff">(.*?)</span>|$1|gs;# blue font
#$content =~ s|<p class="a"><span style="font-family:Calibri">\(\)</span></p>||gs;#
#$content =~ s|<span style="font-family:Calibri">\(\)</span>||gs;#
#$content =~ s|<p >\.</p>||gs;#
#$content =~ s|<span class="iwtooltip"(.+?)</span>||gs;#
$content =~ s|<p class="a"></p>||gs;#
$content =~ s|<p>.</p>||gs;#
$content =~ s|<p></p>||gs;#

my @blocks = $content =~ /(<h1.*?)(?=<h1>|$)/gs;  #s - to get data from multiline text, use Lookahead Assertions https://www.regular-expressions.info/lookaround.html, |$ - to match last occurrence without <h1> tag
my $n = scalar @blocks;
print  "Total matches $n\n";
foreach my $block (@blocks) {
	my @authors_blocks = $block =~ /<h1>(.*?)<\/h1>/gs;
	my $author = '';
	foreach my $a (@authors_blocks) {
		$author = $a;

		if ($author) {
			print OUT "$author#!#";
		}
		$block =~ s/<h1>(.*?)<\/h1>//gs;
	}
	if ($block =~ /<hr>$/) { #delete last hr
		$block =~ s/<hr>$//s;
	}
	if ($block =~ /\r/) {
		$block =~ s/\r/\n/gs;
	}
	if ($block =~ /\t/) {
		$block =~ s/\t//gs;
	}

	print OUT "^$block^##!!##\n";
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
