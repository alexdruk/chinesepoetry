#!/Perl/bin/perl -w
###################################################################
### check epochs
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = './epochs.txt';
my %unique_epochs = ();
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	while (my $line=<IN>) {
		chomp($line);
		$unique_epochs{$line} = 1;
	}
close(IN);
foreach my $key (sort keys %unique_epochs) {
	print  "$key\n";
}
