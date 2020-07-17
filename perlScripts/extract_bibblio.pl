#!/Perl/bin/perl -w
###################################################################
### extract references to bibliography from html file АНТОЛОГИЯ_1column_utf.htm
###################################################################

use strict;
use warnings;

my $in_file = '../orig/АНТОЛОГИЯ_1column_utf.htm';
my $out_file = 'biblio.txt';
my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
my @paragraphs = ( $content =~ m!<p class=a>(.*?)</p>!gs );  #s - to get data from multiline text
my $n = scalar @paragraphs;
my %unique_biblio =();
my $count =0;
foreach my $para (@paragraphs) {
#	$count++;
#delete all tags
	$para =~s/<(.*?)>//gs;
#	$para =~s/<\/span>//gs;
	$para =~ s/<o:p><\/o:p>//g;
#	$para =~ s/\R//; #NB! general way to remove line breaks, but unfortunately also remove russian "x"
	$para =~ s/\n/ /g;
	$para =~ s/\r/ /g;
	$para =~ s/&quot;/"/g;
	$para =~ s/[\(\)]//g;
	$para =~ s/^\s+|\s+$//; #trim both ends, do not work on russian
	$para =~ s/^ +| +$//; #not working
	$para =~ s/^[\s\p{FORMAT}]//g; #not working
	$para =~ s/^[\s\xA0]+//; #not working
	$para =~ s/^\p{Space}//; #not working
					#solution from https://stackoverflow.com/questions/11512264/remove-stubborn-first-space-of-string-in-perl-regex
						if ($para =~ /"Китайская классическая поэзия в переводах М. Басманова", 2004/) {
					#print sprintf( '\x{%x}', ord( $para)); # prints \x{c2}
					#print ord($para)."\n";
						}
					#	$para =~ s/^\x{c2}//;
#NB!!!! for correct solution to change Non-breaking space see:https://en.wikipedia.org/wiki/Non-breaking_space
# to enter Non-breaking space on Mac use ⌥ Opt+Space (option+space)
#1. change all Non-breaking space to regular spaces
	$para =~ s/ / /g;
#2	trim ending and leading spaces $para =~ s/^ +| +$//;
	$para =~ s/^\s+|\s+$//; #trim both ends
	if(exists($unique_biblio{$para})) {
		$unique_biblio{$para} += 1;
	} else {
		$unique_biblio{$para} = 1;
	}

}
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
print OUT "Total matches $n\n";
foreach my $key (sort keys %unique_biblio) {
	$count++;
	print OUT "$count\t'$key'\tfrequency: $unique_biblio{$key}\n";
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
