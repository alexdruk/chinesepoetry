#!/Perl/bin/perl -w
###################################################################
### extract Originals of the poems  from html file
### WITHOUT cycles
###################################################################
use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;
use Data::Dumper;

my $in_file = './final/anthology2.html';
my $out_file = './final/originals1.sql';
my $out_file1 = './final/manually_add_to_Originals.txt';
my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
#change Non-breaking space to a regular one See extract_biblio.pl
$content =~ s/ / /gs; #first space is  Non-breaking space
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
open(OUT1, "> $out_file1") || die "Can't open $out_file1 Code: $!";
my @blocks = $content =~ /(<h1>.*?)(?=<h1>|$)/gs;  #s - to get data from multiline text, use Lookahead Assertions https://www.regular-expressions.info/lookaround.html, |$ - to match last occurrence without <h1> tag
my $poems_total = 0;
foreach my $block (@blocks) {
	my @authors_blocks = $block =~ /<h1>(.*?)<\/h1>/gs;
#	print $authors_blocks[0];
	my $author = '';
	foreach my $a (@authors_blocks) {
		$a = HTML::FormatText->new->format(parse_html(decode_utf8($a)));
#		$a =~ s/\s+\((.+?)\)//i; #delete year
		$a =~ s/^\s+|\s+$//; #trim both ends
		$a = encode_utf8($a);
		$author = $a;
#print $author."\n";
	}
	if ($block =~ /Оригиналы/s) {
#		next if ($author !~ /ДЖИДИ МАЦЗЯ/);
#		print $author."\n";
		my @originals_block = $block =~ /Оригиналы<\/p>(.*?)<p class="a2">/s; #from Оригиналы till translator
		foreach my $originals_block (@originals_block){
			if  ($originals_block !~ /<h4>/) { # with or WITHOUT CYCLES but without subcycles

				my @poems = $originals_block =~ /(<h3>.*?)(?=<h3>|$)/gs;
#				print $author, "\tpoems found: ", scalar @poems, "\n";
				$poems_total += scalar @poems;
				my $biblio_id = 'NULL';
				my $code = '';
				my $poem_name_zh = '';
				my $poem_name_ru = '';
				my $cnt = 0;
				my $biblio = '';
				foreach my $poem (@poems) {
					$cnt++;
					my @poem_text = ();
					my @lines = split("\n", $poem);
					foreach my $line (@lines) {
						next if ($line =~ /<p class="a22">/); #skip topics
						if ($line =~ /<h2>(.+?)<\/h2>/) { #skip cycles
							print OUT1 "$cnt\t$author\t", $1, "\n";
							next;
						}
						if ($line =~ /<h3>(.+?)<\/h3>/) {
							my $poem_name = $1;
							$poem_name =~ s|<a href=.+?>(.+?)</a>|$1|gs;
							($poem_name_zh, $poem_name_ru) = split('###', &getPoemNames($poem_name));
#							print $poem_name, "\n";
#							print $poem_name_zh, "\t", $poem_name_ru,  "\n\n";
						}
						elsif ($line =~ /<p class="a24">(.+?)<\/p>/) {
							$code = $1;
						}
						elsif ($line =~ /biblio\.php\?record_id=(\d+?)"/) {
							$biblio_id = $1;
						}
						elsif ($line =~ /<p class="a">/) { #biblio but without biblio_id
							$biblio = $line;
						}
						else {
							push(@poem_text, $line);
						}
					}
				my $var =  join("\n", @poem_text);
				$var = "\n".$var;
				$var = $var."\n$biblio\n" if ($biblio);
				$var = "^$author^#!#^$poem_name_zh^#!#^$poem_name_ru^#!#^$code^#!#$biblio_id#!#^$var^##!!##\n";
				$var =~ s|'NULL'|NULL|gs;
				$var =~ s|\t||gs;
				$var =~ s| +| |gs;
				print OUT $var;
				}
			}
			elsif (($originals_block =~ /<h4>/)) { #without subcycle
				print "with subcycles: ", $author, "\n";
			}
		}
	}
#last if ($author =~ /ЦАО ЧЖИ/);
}
print "total poems:\t", $poems_total, "\n";
close(OUT);
close(OUT1);

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
sub getPoemText
###
### Accept: poem block
### Return: long string of poem with \n separators
### Usage: &getPoemText($poems_block)
#################################################
{
	my $self = shift;
	my @poem = ();
	my @poem_lines = $self =~ m!<p class="a39">(.+?)</p>!gs;
	foreach my $pline (@poem_lines) {
		my $a = HTML::FormatText->new->format(parse_html(decode_utf8($pline)));
		$pline = encode_utf8($a);
		$pline =~ s/^\s+|\s+$//; #trim both ends
		push (@poem, $pline);
	}
	my $poem_as_string = join('#!#', @poem);
	return $poem_as_string;
}

#################################################
sub getPoemBiblio
###
### Accept: poem block
### Return: biblio string
### Usage: &getPoemBiblio($poems_block)
#################################################
{
	my $self = shift;
	my $biblio = '';
	$biblio =$1 if ($self =~ m!<p class="a">(.+?)</p>!s);
#	my $a = HTML::FormatText->new->format(parse_html(decode_utf8($biblio)));
#	$biblio = encode_utf8($a);
#	$biblio =~ s/[\(\)]//gs;
	return $biblio;
}

#################################################
sub getpoemCode
###
### Accept: poem block
### Return: code string
### Usage: &getpoemCode($poems_block)
#################################################
{
	my $self = shift;
	my $poem_code = '';
	$poem_code =$1 if ($self =~ m!<p class=af8>(.+?)</p>!s);
	my $a = HTML::FormatText->new->format(parse_html(decode_utf8($poem_code)));
	$poem_code = encode_utf8($a);
	#check just in case
	if($poem_code !~ /^.+?\-\d+?$/) {
#	    print "WRONG CODE!";
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
