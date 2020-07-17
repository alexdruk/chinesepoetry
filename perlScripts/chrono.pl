#!/Perl/bin/perl -w
###################################################################
### analyze chronology
###################################################################
use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;
use Data::Dumper;

my $in_file = './chonology.tsv';
my $out_file = './final/chonology.txt';
my $content ='';
open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
open(OUT, "> $out_file") || die "Can't open $out_file Code: $!";
	my $cnt=0;
	while (my $line=<IN>) {
		$cnt++;
#		next if ($cnt <17);
		my $epoch_name = '';
		my $dates = '';
		my $zh_name = '';
		my $pn_name = '';
		my $an_name = '';
		my $capital = '';
		my $clan = '';
		my (@data) = split("\t", $line);
		$epoch_name = $data[10];
		$dates = $data[3];
		$zh_name = $data[6];
		$pn_name = $data[7];
		$an_name = $data[8];
		$capital = $data[4];
		$clan = $data[5];
		print  "$epoch_name\n";
#print OUT '  <div class="card">',"\n";
#print OUT '    <div class="card-header py-0" id="heading'.$cnt.'">',"\n";
#print OUT '      <h5 class="my-0">',"\n";
#print OUT '					<div class="row">',"\n";
#print OUT '		        <div class="col-sm-4 d-flex align-items-center">'.$dates.'</div>',"\n";
#print OUT '		        <div class="col-sm-17  d-flex align-items-center">',"\n";
#print OUT '       			<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse'.$cnt.'" aria-expanded="true" aria-controls="collapse'.$cnt.'">',"\n";
#print OUT "							$epoch_name\n";
#print OUT '       			</button>',"\n";
#print OUT '       			</div>',"\n";
#print OUT '    			</div>',"\n";
#print OUT '      </h5>',"\n";
#print OUT '    </div>',"\n";
#print OUT '    <div id="collapse'.$cnt.'" class="collapse" aria-labelledby="heading'.$cnt.'" data-parent="#accordion">',"\n";
#print OUT '      <div class="card-body">',"\n";
#					if (($zh_name) && ($pn_name) && ($an_name)) {
#print OUT '				<p class="chrono names"><i>Кит.:</i> '.$zh_name.' / 西汉, <i>Пиньинь:</i> '.$pn_name.', <i>Англ.:</i> '.$an_name.'</p>',"\n";
#					}
#					elsif (($zh_name) && ($an_name)) {
#print OUT '				<p class="chrono names"><i>Кит.:</i> '.$zh_name.' <i>Англ.:</i> '.$an_name.'</p>',"\n";
#					}
#					elsif ($zh_name) {
#print OUT '				<p class="chrono names"><i>Кит.:</i> '.$zh_name.'</p>',"\n";
#					}
#					elsif ($an_name) {
#print OUT '				<p class="chrono names"><i>Англ.:</i> '.$an_name.'</p>',"\n";
#					}
#
#					if ($clan) {
#print OUT '				<p class="chrono clan"><i>Правящий дом или клан:</i> '.$clan.' <i>(Пиньинь-Китайский)</i></p>',"\n";
#					}
#					if ($capital) {
#print OUT '				<p class="chrono capital"><i>Столица:</i> '.$capital.'</p>',"\n";
#					}
#print OUT '      </div>',"\n";
#print OUT '    </div>',"\n";
#print OUT '  </div>',"\n";
#print OUT "\n";
	}
close(OUT);
close(IN);
