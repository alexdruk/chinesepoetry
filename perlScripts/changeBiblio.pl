#!/Perl/bin/perl -w
###################################################################
### include links for biblio ref
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = './anthology56-15d.html';
my $out_file = './anthology56-15d-2.html';
#my $biblio_file = './biblio.csv';
#my %unique_biblio = ();
#my %empty_images = &getEmptyImagesHash();
#open(IN, "< $biblio_file") || die "Cannot open $biblio_file.Code: $!";
#	while (my $line=<IN>) {
#		chomp($line);
#		my ($a,$b) = split('\t', $line);
#		$a =~ s/\s+/ /gs;
#		$unique_biblio{$a} = $b;
#	}
#close(IN);
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
open(OUT, "> $out_file") || die "Cannot open $out_file.Code: $!";
my $matches =0;
while (my $line=<IN>) {
#	if ($line =~ /<a id="_Toc\d+?">/) {
#		$line =~ s/<a id="_Toc\d+?">)/<a>/;
#	}
	if ($line =~ /<h\d\s+style/) {
		$line =~ s/<h(\d)\s+style.+?>/<h$1>/;
	}
	if ($line =~ /«|»/) {
		$line =~ s/«|»/\"/g;
	}
	if ($line =~ /&#xa0;\(/ ) {#space before (
		$line =~ s/&#xa0;\(/\(/g;
	}
	if ($line =~ /&#xad;/ ) {#SOFT HYPHEN
		$line =~ s/&#xad;//g;
	}
	if ($line =~ /&#xa0; / ) { #double spaces
		$line =~ s/&#xa0; / /g;
	}
	if ($line =~ /&#xa0;$/ ) { #empty paragraphs
		$line =~ s/&#xa0;/&nbsp;/g;
	}
	if ($line =~ /&#xa0;/ ) { #all others
		$line =~ s/&#xa0;/&nbsp;/g;
	}
	if ($line =~ /&#x200e;/ ) { #'LEFT-TO-RIGHT MARK'
		$line =~ s/&#x200e;//g;
	}
	if ($line =~ /<a id="_Toc\d+"><\/a>/) {
		$line =~ s/<a id="_Toc\d+"><\/a>//gs;
	}
	if ($line =~ /<a id="_Toc\d+">(.+?)<\/a>/) {
		$line =~ s/<a id="_Toc\d+">(.+?)<\/a>/$1/gs;
	}
	if ($line =~ /<span style="height:0pt; display:block; position:absolute; z-index:\d+">/) { #empty images
		$line =~ s|<span style="height:0pt; display:block; position:absolute; z-index:\d+">(.+?)<\/span>|$1|gs;
	}

#	foreach my $bibl (keys %unique_biblio) {
#		if ($line =~ /\Q$bibl\E/) {
##			print $bibl."\n" if ($bibl =~ /Дальнее эхо/gs);
#			$matches += $line =~ s|\Q$bibl\E|<a href=\"\.\/biblio.php\?biblio_id=$unique_biblio{$bibl}\" class="biblio ref">$bibl<\/a>|gs;
##			print $matches, $bibl, $line, "\n" if ($matches && $bibl =~ /Дальнее эхо/);
#		}
#	}
#	if (($line =~ /\.png/) || ($line =~ /\.jpg/)){
#		foreach my $key (keys %empty_images) {
#			if ($line =~ /$key/) {
#				$line =~ s|<img src="images/$key" .+?\/>||gs;
#			}
#		}
#	}
	print OUT $line;
}
print "matches: $matches\n";
close(IN);
close(OUT);
#################################################
sub getEmptyImagesHash
###
### Accept:
### Return:
### Usage:
#################################################
{
	my $self = shift;
	my %empty_images =();
	$empty_images{"anthology.005.png"}=1;
	$empty_images{"anthology.006.png"}=1;
	$empty_images{"anthology.007.png"}=1;
	$empty_images{"anthology.039.png"}=1;
	$empty_images{"anthology.040.png"}=1;
	$empty_images{"anthology.041.png"}=1;
	$empty_images{"anthology.042.png"}=1;
	$empty_images{"anthology.050.png"}=1;
	$empty_images{"anthology.051.png"}=1;
	$empty_images{"anthology.058.png"}=1;
	$empty_images{"anthology.098.png"}=1;
	$empty_images{"anthology.128.png"}=1;
	$empty_images{"anthology.129.png"}=1;
	$empty_images{"anthology.130.png"}=1;
	$empty_images{"anthology.165.png"}=1;
	$empty_images{"anthology.166.png"}=1;
	$empty_images{"anthology.176.png"}=1;
	$empty_images{"anthology.177.png"}=1;
	$empty_images{"anthology.188.png"}=1;
	$empty_images{"anthology.203.png"}=1;
	$empty_images{"anthology.213.png"}=1;
	$empty_images{"anthology.275.png"}=1;
	$empty_images{"anthology.276.png"}=1;
	$empty_images{"anthology.323.png"}=1;
	$empty_images{"anthology.324.png"}=1;
	$empty_images{"anthology.325.png"}=1;
	$empty_images{"anthology.334.png"}=1;
	$empty_images{"anthology.344.png"}=1;
	$empty_images{"anthology.363.png"}=1;
	return %empty_images;
}
