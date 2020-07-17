#!/Perl/bin/perl -w
###################################################################
### make authors table from html file АНТОЛОГИЯ_1column_utf.htm
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = './final/authorsTable_final.tsv';
my $out_file = './final/authorsAtrib.sql';
my %authorsHASH = ();
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	my $present ='';
	my $full_name ='';
	my $dates ='';
	my $proper_name ='';
	my $epoch ='';
	my $cnt = 0;
	while (my $line=<IN>) {
		chomp($line);
			$cnt++;
		my(@data) = split("\t", $line);
		$present = $data[0];
		if ($present) {
			$full_name = $data[1];
			if ($full_name =~ /\((.+?)\)$/) {
				$dates = '('.$1.')';
			}
			else {print $full_name, $cnt,"\n";}
			$proper_name = $data[2];
			$epoch = $data[34];
			$authorsHASH{$proper_name.dates} = 1;
		}
	}
close(IN);
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
#print OUT 'INSERT INTO `authors` (`full_name`, `proper_name`, `dates`, `epoch`, `present`) VALUES '."\n";
my $count =0;
foreach my $key (sort keys %authorsHASH) {
	$count++;
#	print OUT "('".$key."',".$authorsHASH{$key}."),\n";
	print  $key,"\n";
}
print "total: $count\n";
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
