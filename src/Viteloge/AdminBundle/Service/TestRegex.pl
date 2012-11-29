#! /usr/bin/perl -w

use strict;
use JSON;

my $src_file = $ARGV[0];

if ( ! ( defined $src_file && -f $src_file ) ) {
    die "Unable to find source file";
}

my $json_data = do { local($/); <STDIN> };

open FH, "<$src_file"
  or die "Unable to load source file $src_file";
my $source = do { local($/); <FH> };
close FH;

#print STDERR $json_data;

my $data = JSON->new->decode( $json_data );

my $results = {};

foreach my $expression_name ( keys %{$data} ) {
    my $expr = $data->{$expression_name};
	my($result) = $source =~ /$expr/sig;
    $results->{$expression_name} = $result;
}


print JSON->new->indent->encode( $results );

