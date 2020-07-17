#!/Perl/bin/perl -w
###################################################################
### extract head from html file АНТОЛОГИЯ_1column_utf.htm
###################################################################

use strict;
use warnings;

my $in_file = '../orig/АНТОЛОГИЯ_final_UTF.htm';
my $out_file = 'head.html';
my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
	while (my $line=<IN>) {
		print OUT $line;
		last if $line =~ m!^<body bgcolor=white!;
	}

close(IN);
close(OUT);
