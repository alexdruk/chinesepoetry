#!/Perl/bin/perl -w
###################################################################
### extract translators  from html file
###################################################################

use strict;
use warnings;

my $in_file = './anthology56-15d.html';
my $out_file = 'translators.txt';
my $tr_file = './traslatorsFromTable.txt';
my %translatorsFromTable = ();
open(IN, "< $tr_file") || die "Cannot open $tr_file.Code: $!";
	while (my $line=<IN>) {
		chomp($line);
		$translatorsFromTable{$line} = 1;
	}
close(IN);
my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
#change Non-breaking space to a regular one See extract_biblio.pl
$content =~ s/ / /g; #first space is  Non-breaking space
my @paragraphs = ( $content =~ m!<p class="a9">(.*?)</p>!gs );  #s - to get data from multiline text
my $n = scalar @paragraphs;
my %unique_translators =  ();
my $count=0;
foreach my $para (@paragraphs) {
	$count++;
#delete all tags
	$para =~s/<(.*?)>//gs;
	$para =~ s/&#xa0;/ /gs;
	$para =~ s/ / /g;
	$para =~s/\s+/ /gs;
	$para =~ s/^ | $//gs;
	$para =~ s/\n/ /g;
#	$para =~ s/\r/ /g;
#	print "$para\n";
#my $chr = substr( $para, 0, 1 );
#my $ascii = ord( $chr );
#print "$ascii\n";
	next if ($para =~ /Оригинал/s);
	next if ($para =~ /Пиньинь/s);
	next if ($para =~ /Подстрочники/s);
	$para =~ s/Литературная обработка //gs;
	$para =~ s/Обработка //gs;
	$para =~ s/Перевод //gs;
	$para =~ s/Переводчик //gs;
#	$para =~ s/Литературная обработка //gs;
	if ($para =~ / и /) {
		my @pairs1 = split(' и ', $para);
#		print join('#', @pairs1), "\n";
		foreach my $ind1 (@pairs1) {
			$ind1 =~ s/^ | $//gs;
			$unique_translators{$ind1} = 1;
		}
		next;
	}
	if ($para =~ /,/) {
		my @pairs1 = split(',', $para);
#		print join('#', @pairs1), "\n";
		foreach my $ind1 (@pairs1) {
			$ind1 =~ s/^ | $//gs;
			$unique_translators{$ind1} = 1;
		}
		next;
	}
	$unique_translators{$para} = 1;

#last if $count >5;
}


open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
print  "Total matches $n\n";
my $cnt=0;
foreach my $key (sort keys %unique_translators) {
	$cnt++;
	print  "$cnt\t'$key'\n" if (!exists($translatorsFromTable{$key}));
#	print  "$cnt\t'$key'\n";
	print OUT "$key\n";
}
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
