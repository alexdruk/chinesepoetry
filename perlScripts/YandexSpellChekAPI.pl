### Usage: spellchecker.pl
### Author;: Alex Druk
##################################################

use strict;
use LWP::UserAgent;
use Encode;

my $in_file = 'poem_names.tsv';
my $out_file = 'poem_names_errors.tsv';
my $ua = new LWP::UserAgent;
$ua->timeout(100);
my $API_URL = 'https://speller.yandex.net/services/spellservice/checkText?';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	my $cnt = 0;
	while (my $line=<IN>) {
		$cnt++;
		print "doing $cnt\n";
		chomp($line);
		my ($a_id, $p_id, $p_name, $a_name) = split("\t", $line);
		my $res = $ua->post($API_URL,
				 Content_Type => 'application/x-www-form-urlencoded',
	#       Accept_encoding => 'gzip',
				 Content      => [
								'text' => $p_name,
								'lang	'	 => 'ru',
								'format' => 'plain',
								'sourceText'	 => 'cleaned',
								'options'	 => 526
												 ]);
		die "Error: ", $res->status_line, $res->code unless $res->is_success;
		my $content = $res->decoded_content();
		if ($content =~ /error/g) {
			open(OUT, ">> $out_file") || die "Can't open $out_file Code: $!";
			my @errs = $content =~ m|<word>(.+?)</word><s>(.+?)</s>|gs;
			print OUT "poems.php?action=show&record_id=$a_id&poem_id=$p_id errors and suggestions: ".join(" - ", @errs)."\n";
			print join(" - ", @errs)."\n";
			close(OUT);
		}
#		print $content."\n";
    my $random = int( rand(5)) + 5;
    sleep ($random);
	}
close(IN);

