#!/Perl/bin/perl -w
###################################################################
### extract Originals of the poems  from html file
### NOT FINISHED YET
###################################################################
use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;
use Data::Dumper;

my $in_file = './final/anthology2.html';
my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
#change Non-breaking space to a regular one See extract_biblio.pl
$content =~ s/ / /gs; #first space is  Non-breaking space
my @blocks = $content =~ /(<h1>.*?)(?=<h1>|$)/gs;  #s - to get data from multiline text, use Lookahead Assertions https://www.regular-expressions.info/lookaround.html, |$ - to match last occurrence without <h1> tag
#@blocks = map {'<h1>'.$_} @blocks; #reinsert <h1> tag which was lost in previous matching
print  "Total matches ", scalar @blocks, "\n";
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
#		print $author."\n";
		my @originals_block = $block =~ /Оригиналы<\/p>(.*?)<p class="a2">/s; #from Оригиналы till translator
		my @poems = ();
		foreach my $originals_block (@originals_block){
			my @lines = split("\n", $originals_block);
			my $prevToc = 0;
			my $currentToc = 0;
			my $inCycle = 0;
			my $cycleName;
			my $notRealPoem;
			my $subCycleName;
			my @poem_text = ();
			my $poem = ();
			foreach my $line (@lines) {
				if ($line =~ /<h(\d+?)>/) {
					$currentToc = $1 ;
					# TOC2 is always a cycle
					if ($currentToc == 2) { #cycle
						$inCycle = 1;
						$cycleName = $line;
						$cycleName =~ s/<[^>]*>//g; #strip HTML tags
						$cycleName =~ s/^\s+|\s+$//;
						next;
					}
					$line =~ s/<[^>]*>//g; #strip HTML tags
					$line =~ s/^\s+|\s+$//;
					$poem = {
						"poem_name" => $line,
	#					"translator" => $translator,
						"author" => $author
					};
					if ($cycleName) {
						$poem->{"cycle"} = $cycleName;
					}
					# when we find TOC4, it means previous TOC3 is not a poem, but a subcycle
					if ($currentToc == 4) {
						if ($prevToc != 4) {
							$notRealPoem = pop @poems;
							$subCycleName = $notRealPoem->{"poem_name"};
							$subCycleName =~ s/<[^>]*>//g; #strip HTML tags
							$subCycleName =~ s/^\s+|\s+$//;
						}
						$poem->{"subcycle"} = $subCycleName;
					}
#					print Dumper(\$poem);
					push(@poems, $poem);
					$prevToc = $currentToc;
					print Dumper(\@poems);
				}
				else {
					push(@poem_text, $line);
				}
#				$poem->{"poem_text"} = join('', @poem_text);
			}
		}
	}
last if ($author =~ /ЦАО ЧЖИ/);
}
#################################################
sub getPoemNames
### NEEDS TO BE MODIFIED TO MATCH NAMES OF NOT ORIGINALS
### Accept: poem block
### Return:  string zh_name and  poem full name separated by '###'
### Usage: &getPoemNames($poems_block)
#################################################
{
	my $self = shift;
	my $zh_name ='';
	my $ru_name ='';
	if ($self =~ /<h3>(.+?)<\/h3>/s) {
		my $names_block = $1;
		my @zh_blocks = $names_block =~ m!SimSun; color:#C00000.*?'>(.+?)</span>!gs; #color #C00000 may be followed by ';mso-bidi-font-weight:normal''
		print "\t\t\ttotal chinise names  found: ", scalar @zh_blocks, "\n";
		$zh_name = join(' ', @zh_blocks);
		$zh_name =~ s/^\s+|\s+$//; #trim both ends
		$zh_name =~ s/\s+/ /; #delete extra spaces
		$zh_name =~ s/\n//; #delete \n
#		print 'Z',"$zh_name", 'Z', "\n";
		my @ru_blocks = $names_block =~ m!<span[\s|\n]lang=RU>(.+?)</span>!gs;
		print "\t\t\t\ttotal ru names  found: ", scalar @ru_blocks, "\n";
		$ru_name = join(' ', @ru_blocks);
		$ru_name =~ s/^\s+|\s+$//; #trim both ends
		$ru_name =~ s/\s+/ /; #delete extra spaces
		$ru_name =~ s/\n//; #delete \n
		if (($ru_name =~ /»$/) && ($ru_name !~ /^«/)) { #probably bad patch
			$ru_name = '«'.$ru_name;
		}
#		print 'Z',"$ru_name", 'Z', "\n";
	} else {
		print "NO NAMES FOUND\n";
		return '###';
	}
	return $zh_name.'###'.$ru_name;
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
