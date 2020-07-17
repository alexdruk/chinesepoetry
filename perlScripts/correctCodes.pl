#!/Perl/bin/perl -w
###################################################################
### correct code
###################################################################
use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;
use Data::Dumper;

my $in_file = './final/anthology2.html';
my $out_file = './final/correction.sql';
my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
#change Non-breaking space to a regular one See extract_biblio.pl
$content =~ s/ / /gs; #first space is  Non-breaking space
my %namesHash = ();
my @blocks = $content =~ /(<h3>.*?)(?=<h3>|$)/gs;  #s - to get data from multiline text, use Lookahead Assertions https://www.regular-expressions.info/lookaround.html, |$ - to match last occurrence without <h1> tag
my $cnt =0;
foreach my $block (@blocks) {
	next if ($block =~ /<p class="a2">Оригиналы/); #exclude originals
	my $code = '';
	my $poem_name_zh = '';
	my $poem_name_ru = '';
	if ($block =~ /<h3>(.+?)<\/h3>/s) {
		my $poem_name = $1;
		$poem_name =~ s|<a href=.+?>(.+?)</a>|$1|gs;
		($poem_name_zh, $poem_name_ru) = split('###', &getPoemNames($poem_name));
		if ($poem_name_ru =~ /^&nbsp;/) {$poem_name_ru =~ s|&nbsp;||}
	}
	if ($block =~ /<p class="a24">(.+?)<\/p>/s) {
		$code = $1;
		if($code !~ /^.+?\-\d+?$/) {
	  print "$code\tWRONG CODE!\n";
	}

	}
	$namesHash{$poem_name_ru} = $code;
}
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
foreach my $key (keys %namesHash) {
	$cnt++;
	print OUT "$key#!#$namesHash{$key}\n";
}
close(OUT);
#################################################
sub getPoemNames
### NEEDS TO BE MODIFIED TO MATCH NAMES OF NOT ORIGINALS
### Accept: poem block
### Return:  string zh_name and  poem full name separated by '###'
### Usage: &getPoemNames($poems_block)
#################################################
{
	my $self = shift;
	my $zh ='';
	my $ru ='';
		my $names = $self;
		my @strs = $names =~ /(<span.+?SimSun.+?>.+?<\/span>)/gs;
		$zh = join('', @strs);
		$names =~ s/\Q$zh\E//gs; #delete all zh from poem name
		$zh =~ s/<[^>]*>//g; #strip all html tags
		$zh =~ s/^\s+|\s+$//; #trim
		$names =~ s/<[^>]*>//g; #strip all html tags
		$ru = $names;
		$ru =~ s/^\s+|\s+$//; #trim
	return $zh.'###'.$ru;
}
#################################################
sub getpoemCode
###
### Accept: poem block
### Return: code string
### Usage: &getpoemCode($poems_block)
#################################################
{
	my $poem_code = shift;
#	my $a = HTML::FormatText->new->format(parse_html(decode_utf8($self)));
#	$poem_code = encode_utf8($a);
	#check just in case
	if($poem_code !~ /^.+?\-\d+?$/) {
	    print "$poem_code\tWRONG CODE!\n";
	}
#	print "$poem_code\n";
	return $poem_code;
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
