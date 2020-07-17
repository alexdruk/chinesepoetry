#!/Perl/bin/perl -w
###################################################################
### convert topics txt to sql
###
###################################################################

use strict;
use warnings;
use Encode;

my $in_file = 'topics.txt';
my $out_file = 'topics.sql';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
my %topics = ();
while (my $line=<IN>) {
	chomp($line);
	my ($topic, $synonym, $present) = split(',', $line);
	$synonym = 'NULL' if ($synonym eq '');
	$topics{$topic} = $synonym;
}
close(IN);

open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
print OUT "INSERT INTO topics (topic_name, topic_synonym, present) VALUES ";
foreach my $key (sort keys %topics) {
	my $val =  "('".$key."','".$topics{$key}."',1),\n";
	$val =~ s/'NULL'/NULL/;
	print OUT $val;
}
close(OUT);
