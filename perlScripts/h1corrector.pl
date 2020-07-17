#!/Perl/bin/perl -w
###################################################################
### change <h1 .+? tags to <h1> in file produced by https://docconverter.pro/
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = './anthology/anthology.html';
my $out_file = './anthology/anthology1.html';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
	while (my $line=<IN>) {
		if ($line =~ /<h1\s+style/) {
#		print $line."\n";
			$line =~ s/<h1\s+style.+?>/<h1>/;
#		print $line."\n\n";
		}
		print OUT $line;
	}
close(IN);
close(OUT);
