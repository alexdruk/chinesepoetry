#!/Perl/bin/perl -w
###################################################################
### to final prepare only poems file
### to find empty image files run "find . -type f -size -1k > ../emptyimages.txt" in image dir
### biblio.tsv, translator.tsv, topics.tsv - are prepared from DB
###################################################################

use strict;
use warnings;
use HTML::Parse;
use HTML::FormatText;
use Encode;

my $in_file = './final/anthology1.html';
my $out_file = './final/anthology2.html';
my $empty_images_file = './final/emptyimages.txt';
my $biblio_file = './final/biblio.tsv';
my $translators_file = './final/translators.tsv';
my $topics_file = './final/topics.tsv';
my %unique_biblio = ();
my %unique_translators = ();
my %unique_topics = ();
my %empty_images = ();
open(IN, "< $biblio_file") || die "Cannot open $biblio_file.Code: $!";
	while (my $line=<IN>) {
		chomp($line);
		my ($a,$b) = split('\t', $line); #number\tstring
		$b =~ s|["' «»]||g;
		$unique_biblio{$b} = $a;
	}
close(IN);

open(IN, "< $translators_file") || die "Cannot open $translators_file.Code: $!";
	while (my $line=<IN>) {
		chomp($line);
		my ($a,$b) = split('\t', $line);
		$unique_translators{$b} = $a;
#		print $unique_translators{"Шуцкий Ю.К.\n"};
	}
close(IN);

open(IN, "< $topics_file") || die "Cannot open $topics_file.Code: $!";
	while (my $line=<IN>) {
		chomp($line);
		my ($a,$b) = split('\t', $line); #number\tstring
		$unique_topics{$b} = $a;
	}
close(IN);

open(IN, "< $empty_images_file") || die "Cannot open $empty_images_file.Code: $!";
	while (my $line=<IN>) {
		chomp($line);
		$empty_images{$line} = 1;
	}
close(IN);

open(IN, "< $in_file") || die "Cannot open $in_file.Code: $!";
open(OUT, "> $out_file") || die "Cannot open $out_file.Code: $!";
my $matches =0;
while (my $line=<IN>) {
	if ($line =~ / /) { #Non-breaking space
		$line=~ s/ / /g;
	}
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
	if ($line =~ /<p class="a">&nbsp;/) {
		$line =~ s/<p class="a">&nbsp;/<p class="a">/;
	}
	if ($line =~ /<p class="a2">&nbsp;/) {
		$line =~ s/<p class="a2">&nbsp;/<p class="a2">/;
	}
	if ($line =~ /<p class="a18">&nbsp;<\/p>\n/) { #underlines
		$line =~ s/<p class="a18">&nbsp;<\/p>\n//;
	}
	if ($line =~ /<p class="a2">Оригиналы.<\/p>/) { #Оригиналы with dot
		$line =~ s/<p class="a2">Оригиналы.<\/p>/<p class="a2">Оригиналы<\/p>/;
	}
	$line =~ s/> </></g;
	$line =~ s/<span [^>]*><\/span>//g;
#	$line =~ s/\\r//g;

	if ($line =~ /<p class="a">(.+?)<\/p>/) {
		my $bibref = $1;
		my $orig = $1;
		$bibref =~ s|[()"' «»]||g;
		$bibref =~ s|<strong>||g;
		$bibref =~ s|</strong>||g;
		$bibref =~ s|<span.+?>(.+?)</span>|$1|g;
		$bibref =~ s|&nbsp;||g;
		foreach my $bibl (keys %unique_biblio) {
			if ($bibref =~ /\Q$bibl\E/) {
				$orig =~ s|\Q$orig\E|<a href=\"\.\/biblio.php\?record_id=$unique_biblio{$bibl}\" class="biblio ref">$orig<\/a>|gs;
			}
		}
		#exceptions
		if ($orig =~ /Дальнее эхо/ ) {
			$orig = '<a href="./biblio.php?record_id=71" class="biblio ref">("Дальнее эхо. Антология китайской лирики (VII-IX вв) в переводах Ю.К. Шуцкого", 2000)<\/a>';
		}
		elsif ($orig =~ /Федоренко/ ) {
			$orig = '<a href="./biblio.php?record_id=118" class="biblio ref">("Китайская классическая поэзия (Эпоха Тан). Сост. Н.Т. Федоренко", 1956)<\/a>';
		}
		elsif ($orig =~ /Бхагавад/ ) {
			$orig = '<a href="./biblio.php?record_id=74" class="biblio ref">("Дао дэ цзин с параллелями из Библии и Бхагавад Гиты (перевод с англ.)", 1998)<\/a>';
		}
		elsif ($orig =~ /лирика\. \(М\. Басманов\)/ ) {
			$orig = '<a href="./biblio.php?record_id=120" class="biblio ref">("Китайская лирика. (М. Басманов)", 2003)<\/a>';
		}
		elsif ($orig =~ /Китайская пейзажная/ ) {
			$orig = '<a href="./biblio.php?record_id=127" class="biblio ref">("Китайская пейзажная лирика III-XIV вв", 1984)<\/a>';
		}
		elsif ($orig =~ /ссическая поэзия \(М\. Басманов\)/ ) {
			$orig = '<a href="./biblio.php?record_id=114" class="biblio ref">("Китайская классическая поэзия (М. Басманов)", 2005)<\/a>';
		}
		elsif ($orig =~ /Л\. Черкасский/ ) {
			$orig = '<a href="./biblio.php?record_id=129" class="biblio ref">("Китайская поэзия (Л. Черкасский)", 1982)<\/a>';
		}
		elsif ($orig =~ /Резной дракон/ ) {
			$orig = '<a href="./biblio.php?record_id=213" class="biblio ref">("Резной дракон. Поэзия эпохи Шести династий (III-VI вв.) в переводах М. Кравцовой", 2004)<\/a>';
		}
		elsif ($orig =~ /"Сон в красном тереме" Том 1, 2014/ ) {
			$orig = '<a href="./biblio.php?record_id=271" class="biblio ref">("Сон в красном тереме" Том 1, 2014)<\/a>';
		}
		elsif ($orig =~ /образы в русской и китайской поэзии первой трети XX века/ ) {
			$orig = '<a href="./biblio.php?record_id=259" class="biblio ref">(У Хань. "Орнителогические образы в русской и китайской поэзии первой трети XX века", 2015)<\/a>';
		}
		elsif ($orig =~ /"Сорок поэтов", 197я8/ ) {
			$orig = '<a href="./biblio.php?record_id=229" class="biblio ref">("Сорок поэтов", 1978)</a>';
		}
		elsif ($orig =~ /Китайская поэзия в переводах Л.Е. Черкасского/ ) {
			$orig = '<a href="./biblio.php?record_id=129" class="biblio ref">("Китайская поэзия (Л. Черкасский)", 1982)</a>';
		}
		$line = '<p class="a">'.$orig.'</p>'."\n";
	}

	if ($line =~ /<p class="a22">(.+?)<\/p>/) {
		my $ln = HTML::FormatText->new->format(parse_html(decode_utf8($line)));
		$ln = encode_utf8($ln);
		my @topics = $ln =~ m!\[(.+?)\]!g;
		my @new_topics = ();
		foreach my $topic (@topics) {
			if (exists($unique_topics{$topic})) {
				$topic = '[<a href="./topics.php\?record_id='.$unique_topics{$topic}.'" class="topics ref">'.$topic.'</a>]';
				push(@new_topics, $topic);
			}
		}
		my $new_line = join(' ', @new_topics);
		$new_line = '<p class="a22">'.$new_line.'</p>'."\n";
		$line = $new_line;
	}

	if ($line =~ /<p class="a2">(.+?)<\/p>/) {
		my $ln = HTML::FormatText->new->format(parse_html(decode_utf8($line)));
		$ln = encode_utf8($ln);
		$ln =~ s/^\s+|\s+$//;
		$ln =~ s/\n//g;
		my @translators =();
		if ($ln =~ /Оригиналы|Пиньинь|Подстрочники|Не установлен/) {
			$line = $ln;
		}
		elsif ($ln =~ m|Переводчик|) {
			$ln =~ s| и |, |g;
			$ln =~ s/Переводчик //;
			@translators = split(',', $ln);
#			print join(':', @translators);
			my @new_translators = ();
			foreach my $translator (@translators) {
				$translator =~ s/\n//g;
				$translator =~ s/^\s+|\s+$//g;
				if (exists($unique_translators{$translator})) {
					$translator = '<a href="./translators.php\?record_id='.$unique_translators{$translator}.'" class="translators ref">'.$translator.'</a>';
					push(@new_translators, $translator);
				}
			}
			my $new_line = join(', ', @new_translators);
			$ln = 'Перевод: '.$new_line;

		}
		elsif ($ln =~ m|Литературная обработка Балин А.И.|) {
			$ln = 'Литературная обработка <a href="./translators.php\?record_id=10" class="translators ref">Балин А.И.</a>';
		}
		elsif ($ln =~ m|Обработка Миримский И.В.|) {
			$ln = 'Обработка <a href="./translators.php\?record_id=72" class="translators ref">Миримский И.В.</a>';
		}
		elsif ($ln =~ m|Ма Хайпэн|) {
			$ln = 'Перевод: <a href="./translators.php\?record_id=62" class="translators ref">Ма Хайпэн</a>, <a href="./translators.php\?record_id=88" class="translators ref">Пэн Сюэ</a>, <a href="./translators.php\?record_id=114" class="translators ref">Цяо Жуйлин</a>';
		}
		elsif ($ln =~ m|Бичурин Н.Я.|) {
			$ln = 'Перевод: <a href="./translators.php\?record_id=17" class="translators ref">Бичурин Н.Я. (о. Иакинф)</a>';
		}
		elsif ($ln =~ m|Перевод Басманов М.И.|) {
			$ln = 'Перевод: <a href="./translators.php\?record_id=13" class="translators ref">Басманов М.И.</a>';
		}
		elsif ($ln =~ m|Азарова Н.М.|) {
			$ln = 'Перевод: <a href="./translators.php\?record_id=2" class="translators ref">Азарова Н.М.</a>';
		}
		elsif ($ln =~ m|неизвестен|) {
			$ln = 'Переводчик <a href="./translators.php\?record_id=128" class="translators ref">неизвестен</a>';
		}
		else {
			print $ln."\n";
		}
		$line = '<p class="a2">'.$ln.'</p>'."\n";
	}

	if (($line =~ /\.png/) || ($line =~ /\.jpg/)){
		foreach my $key (keys %empty_images) {
			if ($line =~ /$key/) {
				$line =~ s|<img src="images/$key" .+?\/>||gs;
			}
		}
	}
	print OUT $line;
}
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
