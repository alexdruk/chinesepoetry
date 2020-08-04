#!/Perl/bin/perl -w
###################################################################
### final script1 for prepare authors description
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = './authors_desc02.html';
my $out_file = './final/authors_desc_sample.html';
my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
#change Non-breaking space to a regular one See extract_biblio.pl
$content =~ s/ / /gs; #first space is  Non-breaking space
$content =~ s/&laquo;|&raquo;/"/gs; # change russian quotes
$content =~ s/&#xad;//gs;#SOFT HYPHEN
$content =~ s|<span style="font-family:'Times New Roman'">(.*?)</span>|$1|gs;#
$content =~ s|<p class="NormalWeb"(.*?)>|<p>|gs;#
$content =~ s|<p class="a"(.*?)>|<p>|gs;#
$content =~ s|<p class="a">|<p>|gs; # a14 can be simple p
$content =~ s|&quot;|"|gs;#
$content =~ s|<div>||gs;#
$content =~ s|</div>||gs;#
$content =~ s|<a id="_Toc\d+"><\/a>||gs;#
$content =~ s|<a id="_Toc\d+">(.+?)<\/a>|$1>|gs;#
$content =~ s|<br style="page-break-before:always; clear:both; mso-break-type:section-break" />||gs;#
$content =~ s|<h6>(.*?)<\/h6>\n<p>&#xa0;</p>||gs;# h6 and line after it
$content =~ s|<h6>(.*?)<\/h6>||gs;#
$content =~ s|<em>\.</em>|\.|gs;#
$content =~ s|<span class="highlight"(.*?)>(.*?)</span>|$2|gs;#
$content =~ s|<span class="st"(.*?)>(.*?)</span>|$2|gs;#
$content =~ s|<span class="Emphasis"(.*?)>(.*?)</span>|$2|gs;#
$content =~ s|<span class="Strong"(.*?)>(.*?)</span>|$2|gs;#
$content =~ s|<span class="notranslate"(.*?)>(.*?)</span>|$2|gs;#
$content =~ s|<span class="subber"(.*?)>(.*?)</span>|$2|gs;#
$content =~ s|<span class="Hyperlink"(.*?)>(.*?)</span>|$2|gs;#

$content =~ s|<h(\d+).*?>\s*(.+?)\s*</h(\d+)>|<h$1>$2</h$3>|gs; #to make all h to be in 1 line
$content =~ s|<p(.*?)>\s*(.+?)\s*</p>|<p$1>$2</p>|gs; #to make all p to be in 1 line

$content =~ s|<p class="a21">&#xa0;</p>|<p>&#xa0;</p>|gs;#
$content =~ s|<p class="a25">&#xa0;</p>|<p>&#xa0;</p>|gs;#
$content =~ s|<p class="a49">&#xa0;(.*?)</p>||gs;#
$content =~ s|<p class="a21"(.*?)>|<p>|gs;#
$content =~ s|<p class="a25"(.*?)>|<p>|gs;#

#$content =~ s|<p>&#xa0;(.*?)</p>|<p>&#xa0;</p>|gs;#
$content =~ s|<p><em>&#xa0;</em></p>|<p>&#xa0;</p>|gs;#  line
$content =~ s|<p>&#xa0;</p>\n(\t+?)<p>&#xa0;</p>\n(\t+?)<p>&#xa0;</p>|<p>&#xa0;</p>|gs;# triple line
$content =~ s|<p>&#xa0;</p>\n(\t+?)<p>&#xa0;</p>|<p>&#xa0;</p>|gs;# double line
$content =~ s|<em><span style="font-size:0.79em; color:#000000">- </span></em>|- |gs;#
$content =~ s|<em> </em>| |gs;#

open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";

my %classes =();
my @class = $content =~ /class="(.+?)"/gs;
foreach my $cl (@class) {
	$classes{$cl} =1;
}
my @blocks = $content =~ /(<h1.*?)(?=<h1>|$)/gs;  #s - to get data from multiline text, use Lookahead Assertions https://www.regular-expressions.info/lookaround.html, |$ - to match last occurrence without <h1> tag
#print $blocks[0];
my $n = scalar @blocks;
print  "Total matches $n\n";
foreach my $block (@blocks) {

	my @authors_blocks = $block =~ /<h1>(.*?)<\/h1>/gs;
	my $author = '';
	foreach my $a (@authors_blocks) {
		$a = HTML::FormatText->new->format(parse_html(decode_utf8($a)));
		$a =~ s/^\s+|\s+$//; #trim both ends
		$a = encode_utf8($a);
		$a =~ s|\n||;
		$author = $a;
	}
#print $descriptions_blocks[0];
#		my $blockNew = $block;
#		foreach my $d (@descriptions_blocks) {
#			$blockNew =~ s/\Q$d\E//gms;
#		}
#		$blockNew =~ s/\Q$pattern\E//gms;
#		print OUT1 $blockNew;
		my @img_blocks = $block =~ /<img(.+?)>/gs;
		my @imgs = map {'<img'.$_.'>'} @img_blocks;
#print join(',', @imgs)."\n";
#		@imgs = map {local $_ = $_; $_ =~ s/ style(.+?)>/>/; $_ } @imgs;
#		@imgs = map {local $_ = $_; $_ =~ s/ alt(.+?)>/>/; $_ } @imgs;
#		@imgs = map {local $_ = $_; $_ =~ s/>/ class="centerimage">/; $_ } @imgs;
#		my $images = join(' ', @imgs);
		if ($imgs[0]) {
			$block =~ s|\Q<p>$imgs[0]<\/p>\E||gs; #cut off first image if it is the only content of paragraph (to avoid <p></p>)
			$block =~ s|\Q$imgs[0]\E||gs; #cut off first image
			$imgs[0] =~ s/ style(.+?)>/>/;
			$imgs[0] =~ s/alt=""/alt="$author"/;
			$imgs[0] =~ s/>/ class="centerimage">/;
		}
		my $pattern = '<p class="a26">&#xa0;</p>\n';
		my @descriptions_blocks = $block =~ /$pattern(.+?)(?=$pattern|$)/gs;
		foreach my $d (@descriptions_blocks) {
#do biblio
			if ($d =~ m|<p class="a49">(.+?)</p>|) {
				my $biblio = $1;
				$biblio = '<div class="d-flex align-items-end"><cite>'.$biblio.'</cite></div>';
				$d =~ s/<p class="a49">(.+?)<\/p>/$biblio/gs;
			}
# do Примечания
			if ($d =~ m|<p><em>Примечания</em></p>|) {
				$d =~ s|<p><em>Примечания</em></p>|<p class"-2">Примечания</p>|gs;
			}

			$d =~ s/\Q$pattern\E//gms; #Delete ?
#			$d =~ s/<img(.+?)>//gms; #cut off images from descriptions
			if ($d =~ /<img src="(.+?)" alt="" style="(.+?)float:right" \/>/gs) {
				$d =~ s/<img src="(.+?)" alt="" style="(.+?)float:right" \/>/<img src="$1" alt="$author" class="imageright" \/>/gs
			}
			if ($d =~ /<img src="(.+?)" alt="" style="(.+?)float:left" \/>/gs) {
				$d =~ s/<img src="(.+?)" alt="" style="(.+?)float:left" \/>/<img src="$1" alt="$author" class="imageleft" \/>/gs
			}
			if ($d =~ /<p>&#xa0;<\/p>$/) { #delete last hr
				$d =~ s/<p>&#xa0;<\/p>$//s;
			}
			if ($d =~ /\r/) {
				$d =~ s/\r/\n/gs;
			}
			if ($d =~ /\t/) {
				$d =~ s/\t//gs;
			}
			if ($d =~ /<p><\/p>\n/) {
				$d =~ s/<p><\/p>\n//gs;
			}
			$d =~ s|&#xa0; |&#xa0;|gs; ##double spaces
			$d =~ s|&#xa0;|&nbsp;|gs; #
			if ($d =~ /<p>&nbsp;<\/p>$/) { #delete last hr
				$d =~ s/<p>&nbsp;<\/p>$//s;
			}

		}
		my @b = grep($_, @descriptions_blocks); #delete empty blocks
		my $final_block = '';
		if ($author) {
			$final_block = "$author#!#";
			if ($imgs[0]) {
				$final_block = $final_block.'<div id="images" class="images">'.$imgs[0]."</div>\n";
			}
			$final_block = $final_block. join("<hr>\n", @b);
		}
		else {
			print $block; #just in case
		}

		$final_block =~ s/(^|\n)[\n\s]*/$1/gs;#delete empty lines
		$final_block =~ s/\n$//gs;#delete last \n
		if ($final_block =~ /<hr>$/) {
			$final_block =~ s/<hr>$//gs;#delete last hr
		}
		if ($final_block =~ /<p>&nbsp;<\/p>$/) {
			$final_block =~ s/<p>&nbsp;<\/p>$//gs;#delete last space
		}
		print OUT $final_block."##!!##\n";
}
close(OUT);
print "Classes:\n";
foreach my $class (keys %classes) {
	print "$class\n"
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
