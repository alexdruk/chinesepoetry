#!/Perl/bin/perl -w
###################################################################
### final script for prepare authors description
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = './authors.html';
my $out_file = './final/authors_desc.sql';
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
#$content =~ s|<p class="a"(.*?)>(.*?)&#xa0;(.*?)<p>|<p>|gs;# empty paragraph
#$content =~ s|<p class="a14"(.*?)>(.*?)&#xa0;(.*?)<p>|<p>|gs;# empty paragraph
$content =~ s|&quot;|"|gs;#
$content =~ s|<div>||gs;#
$content =~ s|</div>||gs;#
$content =~ s|<a id="_Toc\d+"><\/a>||gs;#
$content =~ s|<a id="_Toc\d+">(\s*)(.+?)(\s*)<\/a>|$1$2$3|gs;#
$content =~ s|<a id="_Toc\d+">(.+?)<\/a>|$1|gs;#
$content =~ s|<br style="page-break-before:always; clear:both; mso-break-type:section-break" />||gs;#
$content =~ s|<h6>(.*?)<\/h6>\n<p>&#xa0;</p>||gs;# h6 and line after it
$content =~ s|<h6>(.*?)<\/h6>||gs;#
$content =~ s|<em>\.</em>|\.|gs;#
#$content =~ s|<p style(.*?)>|<p>|gs;#
$content =~ s|<h1 style=(.*?)>|<h1>|gs;#
$content =~ s|<h(\d+).*?>\s*(.+?)\s*</h(\d+)>|<h$1>$2</h$3>|gs; #to make all h to be in 1 line
$content =~ s|<p(.*?)>\s*(.+?)\s*</p>|<p$1>$2</p>|gs; #to make all p to be in 1 line
$content =~ s|<h6>(.*?)<\/h6>||gs;#

open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
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
		my @img_blocks = $block =~ /<img(.+?)>/gs;
		my $original_images = scalar @img_blocks;
		my @imgs = map {'<img'.$_.'>'} @img_blocks;
		if ($imgs[0]) {
			$block =~ s|\Q<p>$imgs[0]<\/p>\E||gs; #cut off first image if it is the only content of paragraph (to avoid <p></p>)
			$block =~ s|\Q$imgs[0]\E||gs; #cut off first image
			$imgs[0] =~ s/ style(.+?)>/>/;
			$imgs[0] =~ s/alt=""/alt="$author"/;
			$imgs[0] =~ s/>/ class="centerimage">/;
		}
		#to deal with images inside <p class="a18"></p>
		$block =~ s|<p class="a18"></p>|<p class="a18">&#xa0;</p>|gs;

		my $pattern = '<p class="a18">&#xa0;</p>\n';
		my @descriptions_blocks = $block =~ /$pattern(.+?)(?=$pattern|$)/gs;
		my %biblioHASH = ();

		foreach my $d (@descriptions_blocks) {
#do biblio
#			@biblio_blocks =~ $d =~ /<p class="a40">(.+?)<\/p>/gs;
#		foreach my $b (@biblio_blocks) {
			if ($d =~ m|<p class="a40">(.+?)</p>|) {
				my $biblio = $1;
				$biblio =~ s| style="text-decoration:none"||gs;
				$biblio =~ s|<span class="Hyperlink" style="font-style:italic">(.+?)</span>|$1|gs;
				$biblio =~ s|<span class="Hyperlink">(.+?)</span>|$1|gs;
				$biblio =~ s|%22%20id=%22biblio_poems"|"|gs;
				if ($biblio =~ /biblio_id=(\d+?)/) {
#					$biblio =~ s|Источник:\s*<a href="file:(.+?)biblio_id=(\d+?)">(.+?)</a>|Источник: <a href="./biblio.php?biblio_id=$2">$3</a>|gs;
#					$biblio =~ s|Источник:(.+?)<a href="file:(.+?)biblio_id=(\d+?)">(.+?)</a>|Источник: <a href="./biblio.php?biblio_id=$3">$4</a>|gs;
					$biblio =~ s|Источник:(.+?)<a href="file:(.+?)biblio_id=(\d+?)">(.+?)</a>|Источник: $1<a href="\.\/biblio\.php?biblio_id=$3">$4</a>|gs;
#					$biblio =~ s|Источник:(.+?)<a href="file:(.+?)biblio_id=(\d+?)%22(.*?)">(.+?)</a>|Источник: $1<a href="./biblio.php?biblio_id=$3">$4</a>|gs;
				}
				$biblio = '<div class="d-flex align-items-end"><cite>'.$biblio.'</cite></div>';
				$d =~ s/<p class="a40">(.+?)<\/p>/$biblio/gs;
#if ($author =~ /ДУ ФУ/) {
#	print " ДУ ФУ $biblio\n";
#}
				if (exists($biblioHASH{$biblio})) {
					print "$author $biblio\n";
					$biblioHASH{$biblio} += 1;
				}
				else {
					$biblioHASH{$biblio} = 1;
				}
			}
			$d =~ s/\Q$pattern\E//gms; #Delete ?
#			$d =~ s/<img(.+?)>//gms; #cut off images from descriptions
			if ($d =~ /<img src="(.+?)" alt="(.*?)" style="(.+?)float:left" \/>/gs) {
				$d =~ s|<img src="(.+?)" alt="(.*?)" style="(.+?)float:left"(\s*?)/>|<img src="$1" alt="$author" class="imageleft" />|g;
			}
			if ($d =~ /<img src="(.+?)" alt="(.*?)" style="(.+?)float:right" \/>/gs) {
				$d =~ s|<img src="(.+?)" alt="(.*?)" style="(.+?)float:right"(\s*?)/>|<img src="$1" alt="$author" class="imageright" />|g;
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
			$d =~ s|<p class="a14">&nbsp;</p>||gs; #
			$d =~ s|<p class="a14" style="text-align:right"><em>&nbsp;</em></p>||gs;
			if ($d =~ /<p>&nbsp;<\/p>$/) { #delete last hr
				$d =~ s/<p>&nbsp;<\/p>$//s;
			}
#			$d =~ s/<p>&nbsp;<\/p>//s;
		}
		my $biblio_before = keys %biblioHASH;
#		print "$author $biblio_before\n";
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
		my @img = $final_block =~ /<img(.+?)>/gs;
		my $img_now = scalar @img;
		if ($original_images != $img_now) {
			print "$author $original_images $img_now\n";
		}
		my %biblioHASH1 =();
		my @biblio_data = $final_block =~ /Источник: (.*?)>.+?<\/a>/gs;
		my @biblio_data2 = $final_block =~ /Источник: (.+?) \(ссылка недоступна\)/gs;
		push(@biblio_data, @biblio_data2);
		foreach my $bibl (@biblio_data) {
				if (exists($biblioHASH1{$bibl})) {
#					print "$author $bibl\n";
					$biblioHASH1{$bibl} += 1;
				}
				else {
					$biblioHASH1{$bibl} = 1;
				}
		}
		my $biblio_after = keys %biblioHASH1;
		if ($biblio_before != $biblio_after) {
#			print "not equal biblio $author $biblio_before $biblio_after\n";
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
