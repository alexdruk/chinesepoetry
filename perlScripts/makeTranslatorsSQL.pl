#!/Perl/bin/perl -w
###################################################################
### make translators sql from table on drive
###################################################################

use strict;
use warnings;

my $in_file = './translators_final.tsv';
my $out_file = './translators.sql';
my %translators = ();
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	while (my $line=<IN>) {
		chomp($line);
		$line =~ s/\'/\\'/g;
		my ($full_name, $lit_name, $real_name, $first_name, $father_name, $pseudonyms, $present, $doc_file, $born, $born_place, $died, $died_place, @junk ) = split("\t", $line);
		if ($real_name =~ /та же/) {
			$real_name = $lit_name;
		}
		elsif ($real_name =~ /нет данных/) {
			$real_name = '';
		}
#		print "$full_name\n" if (($present eq '')||($present eq 'NULL'));
		if (!$born) {$born  = 'NULL';}
		if (!$born_place) {$born_place  = 'NULL';}
		if (!$died) {$died  = 'NULL';}
		if (!$died_place) {$died_place  = 'NULL';}

		if ($lit_name =~/нет данных/) {$lit_name  = 'NULL';}
		if ($real_name =~/нет данных/) {$real_name  = 'NULL';}
		if ($first_name =~/нет данных/) {$first_name  = 'NULL';}
		if ($father_name =~/нет данных/) {$father_name  = 'NULL';}
		if ($pseudonyms =~/нет данных/) {$pseudonyms  = 'NULL';}
		if ($born =~/нет данных/) {$born  = 'NULL';}
		if ($born_place =~/нет данных/) {$born_place  = 'NULL';}
		if ($died =~/нет данных/) {$died  = 'NULL';}
		if ($died_place =~/нет данных/) {$died_place  = 'NULL';}

		if ($lit_name eq '') {$lit_name  = 'NULL';}
		if ($real_name eq '') {$real_name  = 'NULL';}
		if ($first_name eq '') {$first_name  = 'NULL';}
		if ($father_name eq '') {$father_name  = 'NULL';}
		if ($pseudonyms eq '') {$pseudonyms  = 'NULL';}
		if ($born eq '') {$born  = 'NULL';}
		if ($born_place eq '') {$born_place  = 'NULL';}
		if ($died eq '') {$died  = 'NULL';}
		if ($died_place eq '') {$died_place  = 'NULL';}

		if ($born =~ /(\d+)\.(\d+)\.(\d+)/) {
			my $month = $2;
			my $day = $1;
			my $year = $3;
			if ($month =~ /^0/) {$month =~ s/0//;}
			if ($day =~ /^0/) {$day =~ s/0//;}
			if ($day > 31) {print $born."\n";}
			if ($month > 12) {print $born."\n";}
			$born = $day.'.'.$month.'.'.$year;
		}
		elsif ($born =~ /(\d+)\/(\d+)\/(\d+)/) {
			my $day = $2;
			my $month = $1;
			my $year = $3;
			if ($month =~ /^0/) {$month =~ s/0//;}
			if ($day =~ /^0/) {$day =~ s/0//;}
			if ($day > 31) {print $born."\n";}
			if ($month > 12) {print $born."\n";}
			$born = $day.'.'.$month.'.'.$year;
		}
		if ($died =~ /(\d+)\.(\d+)\.(\d+)/) {
			my $month = $2;
			my $day = $1;
			my $year = $3;
			if ($month =~ /^0/) {$month =~ s/0//;}
			if ($day =~ /^0/) {$day =~ s/0//;}
			if ($day > 31) {print $died."\n";}
			if ($month > 12) {print $died."\n";}
			$died = $day.'.'.$month.'.'.$year;
		}
		elsif ($died =~ /(\d+)\/(\d+)\/(\d+)/) {
			my $day = $2;
			my $month = $1;
			my $year = $3;
			if ($month =~ /^0/) {$month =~ s/0//;}
			if ($day =~ /^0/) {$day =~ s/0//;}
			if ($day > 31) {print $died."\n";}
			if ($month > 12) {print $died."\n";}
			$died = $day.'.'.$month.'.'.$year;
		}
#		print "$full_name\t$born\t$died\n";
		$translators{$full_name} = "'".$lit_name."','".$real_name."','".$first_name."','".$father_name."','".$pseudonyms."','".$born."','".$born_place."','".$died."','".$died_place."',".$present;
	}
close(IN);

open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
print OUT "INSERT INTO `translators` (`full_name`, `lit_name`, `real_name`, `first_name`, `father_name`, `pseudonyms`, `born`, `born_place`, `died`, `died_place`, `present` ) VALUES ";
foreach my $key (sort keys %translators) {
	$translators{$key} =~ s/\'NULL\'/NULL/g;
	print OUT "('".$key."',".$translators{$key}."),\n";
}
close(OUT);
