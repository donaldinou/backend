#! /usr/bin/perl -w

use strict;
use JSON;
use utf8;

use Encode::Guess;
binmode STDIN, ':encoding(UTF-8)'; #binmode STDOUT, ':encoding(UTF-8)';

my $src_file = $ARGV[0];

my $hint = undef;

if ( scalar @ARGV > 1 ) {
    $hint = $ARGV[1];
}

if ( ! ( defined $src_file && -f $src_file ) ) {
    die "Unable to find source file";
}

my $json_data = do { local($/); <STDIN> };

open FH, "<$src_file"
  or die "Unable to load source file $src_file";
if ( defined $hint && uc($hint) eq 'UTF-8' ) {
    binmode(FH, ":utf8");
}
my $source = do { local($/); <FH> };
close FH;


my $guess = guess_encoding( $source, qw/iso-8859-15 UTF-8/ );
if ( ref($guess) and ( $guess->name ne 'utf8' ) ) {
    $source = $guess->decode( $source );
}

#print STDERR $json_data;

my $data = decode_json( $json_data );

my $results = {};

foreach my $expression_name ( keys %{$data} ) {
    my $expr = $data->{$expression_name}{'expr'};
    my $result;
    if ( $data->{$expression_name}{'array'}) {
        my @RESULT = $source =~ /$expr/sig;
        $result = \@RESULT;
    } else {
        ($result) = $source =~ /$expr/sig;
    }
    $results->{$expression_name} = $result;
}


print encode_json( $results );

