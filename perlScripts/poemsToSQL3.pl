#!/Perl/bin/perl -w
###################################################################
### extract  poems  from html file
### with subcycles for КНИГА ПЕСЕН only
###################################################################
use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;
use Data::Dumper;

my $in_file = './final/bookOfSongs2.html';
my $out_file = './final/tmp.sql';
#just in case to avoid repeated text in files
unlink $out_file;
my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
#change Non-breaking space to a regular one See extract_biblio.pl
$content =~ s/ / /gs; #first space is  Non-breaking space
my @blocks = $content =~ /(<h2>.*?)(?=<h2>|$)/gs;  #s - to get data from multiline text, use Lookahead Assertions https://www.regular-expressions.info/lookaround.html, |$ - to match last occurrence without <h1> tag
my $author = 95;
my $translator = 122;
my $cycle_name_zh = '';
my $cycle_name_ru  = '';
my $subcycle_name_zh = '';
my $subcycle_name_ru  = '';
foreach my $block (@blocks) {
		if ($block =~ /<h2>(.+?)<\/h2>/) {
			($cycle_name_zh, $cycle_name_ru) = split('###', &getPoemNames($1));
		}
		my @originals_block = $block =~ /(<h3>.*?)(?=<h3>|$)/gs; #subcycles
		foreach my $originals_block (@originals_block) {
			if ($originals_block =~ /<h3>(.+?)<\/h3>/) {
				($subcycle_name_zh, $subcycle_name_ru) = split('###', &getPoemNames($1));
			}
			&makesql($originals_block,$cycle_name_zh, $cycle_name_ru,$subcycle_name_zh, $subcycle_name_ru);
#print "$cycle_name_ru\t$subcycle_name_ru\n";
		}
}#foreach original
#################################################
sub makesql
###
### Accept: originals_block from translator till translator or end
### Return: print to out_file
### Usage: &makesql($originals_block)
#################################################
{
	my ($block, $cycle_name_zh, $cycle_name_ru,$subcycle_name_zh, $subcycle_name_ru) = @_;
	open(OUT, ">> $out_file") || die "Can't open $out_file Code: $!";
	my @poems = ();
	my @topics =();
	my $author = 95;
	my $translator = 55; #Кравцова М.Е
#	if ($block =~ /<h5>/) {
#	}
#	else {
		@poems = $block =~ /(<h4>.*?)(?=<h4>|$)/gs;
#		$poems_total += scalar @poems;
		my $cnt = 0;
		foreach my $poem (@poems) {
			$cnt++;
			my $biblio_id = '';
			my $code = '';
			my $poem_name_zh = '';
			my $poem_name_ru = '';
			my $biblio = '';
			my @poem_text = ();
			my @lines = split("\n", $poem);
			foreach my $line (@lines) {
				if ($line =~ /<p class="a22">/) { #topics
					@topics = $line =~ /record_id=(\d+?)" class="topics/g;
				}
				if ($line =~ /<h2>(.+?)<\/h2>/) { #skip cycles
					next;
				}
				if ($line =~ /<h3>(.+?)<\/h3>/) { #skip cycles
					next;
				}
				if ($line =~ /<h4>(.+?)<\/h4>/) {
					my $poem_name = $1;
					$poem_name =~ s|<a href=.+?>(.+?)</a>|$1|gs;
					($poem_name_zh, $poem_name_ru) = split('###', &getPoemNames($poem_name));
					if ($poem_name_ru =~ /^&nbsp;/) {$poem_name_ru =~ s|&nbsp;||}
	#print "$poem_name_ru\t$translator", join('A', @translators), "\n";
				}
				elsif ($line =~ /<p class="a24">(.+?)<\/p>/) {
					$code = $1;
				}
				elsif ($line =~ /biblio\.php\?record_id=(\d+?)"/) {
					$biblio_id = $1;
				}
				else {
					push(@poem_text, $line);
				}
			}
			my $tr =  '#!#'.$translator;
			my ($topic1, $topic2, $topic3) = @topics;
			$topic1 = ($topic1) ? $topic1 : 'NULL';
			$topic2 = ($topic2) ? $topic2 : 'NULL';
			$topic3 = ($topic3) ? $topic3 : 'NULL';
			my $top = "#!#$topic1#!#$topic2#!#$topic3";
			my $var =  join("\n", @poem_text);
			$var = "\n".$var;
		#	$var = $var."\n$biblio\n" if ($biblio); #list biblio for wich there is no id after text
#			if (exists($namesHash{$poem_name_ru})) {
#				($cycle_name_zh, $cycle_name_ru) = split('#', $namesHash{$poem_name_ru});
#			}
			$cycle_name_zh = ($cycle_name_zh) ? $cycle_name_zh : 'NULL';
			$cycle_name_ru = ($cycle_name_ru) ? $cycle_name_ru : 'NULL';
			$subcycle_name_zh = ($subcycle_name_zh) ? $subcycle_name_zh : 'NULL';
			$subcycle_name_ru = ($subcycle_name_ru) ? $subcycle_name_ru : 'NULL';
			$poem_name_zh = ($poem_name_zh) ? $poem_name_zh : 'NULL';
			$poem_name_ru = ($poem_name_ru) ? $poem_name_ru : 'NULL';
			$code = ($code) ? $code : 'NULL';
			$var = "$author$tr$top#!#$cycle_name_zh#!#$cycle_name_ru#!#$subcycle_name_zh#!#$subcycle_name_ru#!#$poem_name_zh#!#$poem_name_ru#!#$code#!#$biblio_id#!#^$var^##!!##\n";
		#	$var = "$author$tr$top#!#$cycle_name_zh#!#$cycle_name_ru#!#$poem_name_zh#!#$poem_name_ru#!#$code#!#$biblio_id##!!##\n";
			$var =~ s|'NULL'|NULL|gs;
			$var =~ s|\t||gs;
			$var =~ s| +| |gs;
			print OUT $var;
		}
#	}
	return;
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
