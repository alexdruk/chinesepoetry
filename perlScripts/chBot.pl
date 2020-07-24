#!/Perl/bin/perl -w
use strict;
use WWW::Mechanize ();
use HTTP::Cookies;


my $mech = WWW::Mechanize->new( agent => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36',
        stack_depth     => 10,
        timeout         => 100,
        autocheck => 0
 );# 0 ignore errors
$mech->add_header( 'Content-Type' => 'application/x-www-form-urlencoded',
 'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
 'Accept-Language' => 'en-US,en;q=0.9,ru;q=0.8');
$mech->max_redirect(2);
$mech->cookie_jar(HTTP::Cookies->new(  file => "./cookies.dat"));

my $in_file = 'poem_names.tsv';
my %poem_names =();
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
	while (my $line=<IN>) {
		chomp($line);
		my ($a_id, $p_id, $p_name, $a_name) = split("\t", $line);
		$p_name =~ s/…//g;
		$p_name =~ s/\.//g;
		$p_name =~ s/^\d+//g;
		$p_name =~ s/^[MDCLXVI]+//g;
		$p_name =~ s/[\(\)]/ /g;
		$p_name = $p_name.' "'.$a_name.'"';
		$p_name =~ s/[\;\/\?\:\@\=\&]/ /;
		$p_name =~ s/[\<\>\#\%\{\}\|\\\^\~\[\]\`]/ /;
		$p_name =~ s/\s+/ /;
		$p_name =~ s/ "/"/g;
		$p_name =~ s/ /\+/g;

#do google
		my $uri = "http://google.com/search?q=$p_name";
		&doSearch($uri);
#do bing
#do yandex
		$p_name =~ s/\+/\%20/g;
		$uri = "http://yandex.ru/search/?text=$p_name";
		&doSearch($uri);
# do baidu
#		$uri = "http://www.baidu.com/s?ie=utf-8&wd=$p_name"; #should be encoded like to Yandex in %20
#		&doSearch($uri);

    my $random = int( rand(500)) + 3600;
    sleep ($random);
	}
close(IN);

#################################################
sub doSearch
###
### Accept:
### Return:
### Usage:
#################################################
{
	my $uri = shift;
		print &getTime(), " quering $uri\n";
		$mech->get($uri);
			print  $mech->response->status_line  unless $mech->success;
			my @links = $mech->find_all_links();
			foreach my $link ($mech->links) {
    		my $url   = $link->url;
    		if ($url =~ /captcha/) {
    			print "No access again\n";
    			print "$url\n";
    			return;
    		}
 			}
		if ($mech->follow_link( url_regex => qr/chinese-poetry/i )) {
			print "Link found\n";
		}
		else {
    			my $random = int( rand(20)) + 10;
			    sleep ($random);
					my $uri = $uri." site:chinese-poetry.ru";
					print "quering $uri\n";
					$mech->get($uri);
						print $mech->response->status_line
    					unless $mech->success;
					if ($mech->follow_link( url_regex => qr/chinese-poetry/i )) {
						print "Link found 2 attempt\n";
					}
		}
		return;
#		print $content;
}
#################################################
sub getTime
###
### Accept:
### Return:
### Usage:
#################################################
{
	my $tm = localtime();
	return $tm;
}
