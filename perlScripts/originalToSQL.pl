#!/Perl/bin/perl -w
###################################################################
### extract originals, pinyin and Подстрочники from html file
### АНТОЛОГИЯ_1column_utf.htm
### into SQL file for import
###################################################################

use strict;
use warnings;

my $in_file = '../orig/АНТОЛОГИЯ_1column_utf.htm';
my $out_file = 'originals.sql';
my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);

my @paragraphs = ( $content =~ m!<h1>(.+?)</h1>!gs );  #s - to get data from multiline text
my $n = scalar @paragraphs;
print "Total matches $n\n";


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
