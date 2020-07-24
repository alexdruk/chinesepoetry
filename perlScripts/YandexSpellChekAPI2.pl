### Usage: spellchecker.pl
### Author;: Alex Druk
##################################################

use strict;
use LWP::UserAgent;
use Encode;

my $in_file = 'poem_text.txt';
my $out_file = 'poem_texts_errors.txt';
my $ua = new LWP::UserAgent;
$ua->timeout(100);
my %data =();
my $API_URL = 'https://speller.yandex.net/services/spellservice/checkText?';
my $file_content = '';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	$file_content = ${&readSlurp($in_file)};
close(IN);
my @blocks = $file_content =~/(##!!##.*?)(?=##!!##|$)/gs;
my $cnt = 0;
foreach my $block (@blocks) {
		$cnt++;
		$block =~ s/##!!##//gs;
		my ($ref, $text) = split('#!#',$block);
		$ref =~ s/\n//;
#		print "$ref - $text";

		print "doing $cnt\n";
		my ($a_id, $p_id, $p_name, $a_name);
		my $res = $ua->post($API_URL,
				 Content_Type => 'application/x-www-form-urlencoded',
	#       Accept_encoding => 'gzip',
				 Content      => [
								'text' => $text,
								'lang	'	 => 'ru',
								'format' => 'html',
								'sourceText'	 => 'cleaned',
								'options'	 => 526
												 ]);
		die "Error: ", $res->status_line, $res->code unless $res->is_success;
		my $content = $res->decoded_content();
		if ($content =~ /error/g) {
			open(OUT, ">> $out_file") || die "Can't open $out_file Code: $!";
			my @errs = $content =~ m|<word>(.+?)</word><s>(.+?)</s>|gs;
			print OUT "admin_poems.php?action=modify&record_id=$ref errors and suggestions: ".join(" - ", @errs)."\n";
			print join(" - ", @errs)."\n";
			close(OUT);
		}
#		print $content."\n";
    my $random = int( rand(5)) + 5;
    sleep ($random);
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
