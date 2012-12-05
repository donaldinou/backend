#! /usr/bin/perl -w

use strict;
use JSON;

use Encode::Guess;

my $src_file = $ARGV[0];

if ( ! ( defined $src_file && -f $src_file ) ) {
    die "Unable to find source file";
}

my $json_data = do { local($/); <STDIN> };

open FH, "<$src_file"
  or die "Unable to load source file $src_file";
my $source = do { local($/); <FH> };
close FH;


my $guess = guess_encoding( $source, qw/iso-8859-15 UTF-8/ );
if ( ref($guess) and ( $guess->name ne 'UTF-8' ) ) {
    $source = $guess->decode( $source );
}

#print STDERR $json_data;

my $data = JSON->new->decode( $json_data );

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


print JSON->new->utf8->encode( $results );

