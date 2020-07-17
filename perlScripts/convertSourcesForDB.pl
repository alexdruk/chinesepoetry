#!/Perl/bin/perl -w
###################################################################
### convert ISTOCHNIKI file for DB in json
### NB! change trailing comma to ;
###################################################################

use strict;
use warnings;
use Encode;

my $in_file = 'ИСТОЧНИКИ_final.tsv';
my $out_file = 'sourcesForDB.sql';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
print OUT 'INSERT INTO `biblio` (`author`,`book_name`,`translator`,`ref_name`,`seria`,`publisher`,`year`,`code`,`isbn`,`presence`,`biblio_name`) VALUES '."\n";
my $cnt =0;
while (my $line=<IN>) {
	$cnt++;
	chomp($line);
	$line =~ s/(['"])/\\$1/g, #to escape single and double quotes
	$line =~ s/ / /gs;
	my($author,$book_name,$translator,$ref_name,$seria,$publisher,$year,$code,$isbn,$presence,$junk,$junk1,$junk2,$junk3,$junk4,$junk5,$junk6,$junk7,$biblio_name) = split('\t', $line);
#	if ($prio) {
		if ($author eq '') {$author  = 'NULL';}
		if ($book_name eq '') {$book_name  = 'NULL';}
		if ($translator eq '') {$translator  = 'NULL';}
#		if ($ref_name eq '') {$ref_name  = 'NULL';}
		if ($seria eq '') {$seria  = 'NULL';}
		if ($publisher eq '') {$publisher  = 'NULL';}
		if ($year eq '') {$year  = 'NULL';}
		if ($code eq '') {$code  = 'NULL';}
		if ($isbn eq '') {$isbn  = 'NULL';}
		if ($isbn eq 'отсутствует') {$isbn  = 'NULL';}
		if ($biblio_name eq '') {$biblio_name  = 'NULL';}
		if ($biblio_name =~ /\n$/) {$biblio_name  =~ s/\n$//;}
		if ($biblio_name =~ /\r$/) {$biblio_name  =~ s/\r$//;}
		my $val = '('."'".$author."','".$book_name."','".$translator."','".$ref_name."','".$seria."','".$publisher."',".$year.",'".$code."','".$isbn."',".$presence.",'".$biblio_name."'),";
		$val =~ s/'NULL'/NULL/g;
		print  OUT $val."\n";
#		my @chars = split //, $biblio_name;
#		print join(',', @chars);
#       		print $chars[$#chars]."\n";
#print  sprintf "%#x", $chars[$#chars];
#		print ord($chars[$#chars])."\n";
#		print 'A', $chars[$#chars], "B\n";
#		last if $cnt >4;
#	}
}
close(IN);
close(OUT);
