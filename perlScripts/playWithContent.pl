#!/Perl/bin/perl -w
###################################################################
### play with content table
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = './contenttable.html';
my $out_file = './contenttable1.html';
my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
my @blocks = $content =~ /(<p class="TOC\d">)(.*?)(<\/p>)/gs;  #s - to get data from multiline text, use Lookahead Assertions https://www.regular-expressions.info/lookaround.html, |$ - to match last occurrence without <h1> tag
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
foreach my $b (@blocks) {
#print $b;
	$b =~ s/\n//gs;
	$b =~ s/\s+/ /g;
	if ($b =~ /<\/p>/) {
		$b = $b."\n";
	}
	$b =~ s|<span style="width:\d+.\d+pt; display:inline-block">&#xa0;</span>||;
	$b =~ s|<span style="width:\d+.\d+pt;.+?display:inline-block">\.+</span>||;
	$b =~ s|\s+<\/p>|<\/p>|;
	print OUT $b;
}
close(OUT);
$in_file = './contenttable1.html';
$out_file = './contenttable2.html';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
	while (my $line=<IN>) {
		if ($line =~ /<p class="TOC1"> <span/) {
			$line =~ s|<p class="TOC1"> <span|<h1> <span|;
			$line =~ s|<\/p>|<\/h1>|;
		}
		print OUT $line;
	}
close(IN);
close(OUT);
exit;

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
