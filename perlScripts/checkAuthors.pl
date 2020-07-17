#!/Perl/bin/perl -w
###################################################################
### make authors table from html file АНТОЛОГИЯ_1column_utf.htm
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = '././anthology56-15d-2.html';
my $out_file = './anthology/authorstable.tsv';
my $authors_file = './authorsFromTable.txt';
my %authorsHASH = ();
my %authorsNOtInTable = ();
open(IN, "< $authors_file") || die "Cannot open $in_file.Code: $!";
	while (my $line=<IN>) {
		chomp($line);
		$authorsHASH{$line} = 1;
	}
close(IN);
my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
#change Non-breaking space to a regular one See extract_biblio.pl
$content =~ s/ / /gs; #first space is  Non-breaking space
my @authors_blocks = $content =~ /<h1>(.*?)<\/h1>/gs;
my $author = '';
foreach my $a (@authors_blocks) {
	$a = HTML::FormatText->new->format(parse_html(decode_utf8($a)));
	$a =~ s/^\s+|\s+$//; #trim both ends
	$a = encode_utf8($a);
	$a =~ s|\n||;
	$author = $a;
	foreach my $key (keys %authorsHASH) {
		if (!exists($authorsHASH{$author})) {
			$authorsNOtInTable{$author} =1;
		}
	}
}
foreach my $key (keys %authorsNOtInTable) {
	print "$key\n";
}

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
