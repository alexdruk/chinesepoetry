#!/Perl/bin/perl -w
###################################################################
### make authors table from html file АНТОЛОГИЯ_1column_utf.htm
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = './anthology/anthology1.html';
my $authors_file = './authors_epoch.csv';
my $out_file = './anthology/authorstable.tsv';
my $out_file2 = './anthology/authorstable.sql';
my %authorsHASH = ();
my %up_authorsHASH = ();
open(IN, "< $authors_file") || die "Cannot open $in_file.Code: $!";
	while (my $line=<IN>) {
		chomp($line);
		my($name, $up_name, $epoch) = split(",", $line);
		$epoch =~ s/[\n|\r]$//;
		$authorsHASH{$name} = $epoch;
		$up_authorsHASH{$up_name} = $name;
	}
close(IN);
my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
#change Non-breaking space to a regular one See extract_biblio.pl
$content =~ s/ / /gs; #first space is  Non-breaking space
my @blocks = $content =~ /(<h1.*?)(?=<h1>|$)/gs;  #s - to get data from multiline text, use Lookahead Assertions https://www.regular-expressions.info/lookaround.html, |$ - to match last occurrence without <h1> tag
my $n = scalar @blocks;
print  "Total matches $n\n";
my %unique_authors = ();
my %full_authors = ();
my %comment_authors = ();
foreach my $block (@blocks) {
	my @authors_blocks = $block =~ /<h1>(.*?)<\/h1>/gs;
	my $author = '';
	foreach my $a (@authors_blocks) {
		$a = HTML::FormatText->new->format(parse_html(decode_utf8($a)));
		$a =~ s/^\s+|\s+$//; #trim both ends
		$a = encode_utf8($a);
		$a =~ s|\n||;
		$author = $a;
		my $full_author = $author;
#		print "$author", "\n";
		my $dates = '';
		if ($author =~ /\((.+?)\)/) {
			$dates = '('.$1.')';
			$author =~ s/\Q$dates\E//;
		}
		$author =~ s/^\s+|\s+$//;
		$author =~ s/\n$//;
		$full_authors{$author} = $full_author;
		$unique_authors{$author} = $dates;
		my $comment = ''; #
		my @author_comment_blocks  = $block =~ /<p class="a15">(.*?)<\/p>/gs;
		if(scalar @author_comment_blocks) {
			$comment = HTML::FormatText->new->format(parse_html(decode_utf8($author_comment_blocks[0])));
			$comment = encode_utf8($comment);
#			print $author, "\t", $comment, "\n";

		}
		$comment =~ s/^\s+|\s+$//;
		$comment =~ s/ +/ /g; #Non-breaking space
		$comment =~ s/ +/ /g; #multiple spaces to one space
		$comment =~ s/[\n|\r]$//;
		$comment =~ s/(['"])/\\$1/g, #to escape single and double quotes
		$comment_authors{$author} = $comment;
#		print "$author\t\t$dates", "\n";
#		print "$author", "\n";
	}
}
my $count = 0;
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
print OUT "full_name\tcapital_name\tdates\tname_details\tproper_name\tepoch\n";
foreach my $key (sort keys %unique_authors) {
	$count++;
	my $val = "$full_authors{$key}\t$key\t$unique_authors{$key}\t$comment_authors{$key}";
	if (exists($up_authorsHASH{$key})) {
		$val = $val."\t$up_authorsHASH{$key}\t$authorsHASH{$up_authorsHASH{$key}}";
	}
	$val =~ s/[\n|\r]//g;
	print OUT "$val\n";
}
close(OUT);
open(OUT, "> $out_file2") || die "Can't open $out_file2 Code: $!";
print OUT 'INSERT INTO `authors` (`full_name`, `capital_name`, `dates`, `name_details`, `proper_name`, `epoch`, `present`) VALUES '."\n";
foreach my $key (sort keys %unique_authors) {
	$count++;
	my $present = 1;
	my $val = "'$full_authors{$key}', '$key', '$unique_authors{$key}', '$comment_authors{$key}'";
	if (exists($up_authorsHASH{$key})) {
		$val = $val.", '$up_authorsHASH{$key}', '$authorsHASH{$up_authorsHASH{$key}}',$present";
	}
	else {
		$val = $val.", NULL, NULL, $present";
	}
	$val =~ s/[\n|\r]//g;
	print OUT "(".$val."),\n";
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
