#!/Perl/bin/perl -w
###################################################################
### extract topics from html file АНТОЛОГИЯ_1column_utf.htm
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = './anthology/anthology1.html';
my $out_file = 'topics.sql';
my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$content = ${&readSlurp($in_file)};
close(IN);
my @paragraphs = ( $content =~ m!<p class="a22">(.+?)</p>!gs );
my %unique_topics =();
my $max_num_topics = 0;
foreach my $key (@paragraphs) {
	my @topics = ($key =~ m!\[(.+?)\]!gs); #s - to get data from multiline text
#	print join(',', @topics), "\n";
	foreach my $t (@topics ) {
		if (scalar @topics > $max_num_topics) {
			$max_num_topics = scalar @topics;
		}
#		$t = HTML::FormatText->new->format(parse_html(decode_utf8($t)));
		$t =~ s/^\s+|\s+$//;
		$t =~ s/\n$//;
		$t =~s/<(.*?)>//gs; #delete tags
#		$t = encode_utf8($t);
		if(exists($unique_topics{$t})) {
			$unique_topics{$t} += 1;
		} else {
			$unique_topics{$t} = 1;
		}
#		print $t."\n\n";
	}
}
my $count =0;
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
print OUT "INSERT INTO  `topics` (`topic_name`, `presentAntology`) VALUES\n";
print "max $max_num_topics\n";
foreach my $key (sort keys %unique_topics) {
	$count++;
	print "#$count\t$key\t$unique_topics{$key}\n";
	$key = "'".$key."'";
	print OUT "($key,1),\n";
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
