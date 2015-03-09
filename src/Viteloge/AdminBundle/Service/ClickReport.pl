#! /usr/bin/perl -w

use strict;

use DBI;
use JSON;

use viteloge;
require 'config.pl';


my $REQ_find_cities = <<EO_REQ_FIND_CITIES;
SELECT codeInsee
FROM insee_communes
WHERE niveauGeo = 'COM'
EO_REQ_FIND_CITIES

my $REQ_count_clicks = <<EO_REQ_COUNT_CLICKS;
SELECT idAgence, count(*) AS nbClicks
FROM statistiques 
WHERE EXTRACT( YEAR_MONTH FROM `date`) = EXTRACT( YEAR_MONTH FROM NOW() - INTERVAL 1 MONTH ) AND codeinsee = ? 
GROUP BY idAgence;
EO_REQ_COUNT_CLICKS

my $REQ_count_properties = <<EO_REQ_COUNT_PROPS;
SELECT idAgence, count(*) AS nbProperties
FROM annonce
WHERE ( DateSuppression IS NULL OR DateSuppression >= ( DATE_SUB(curdate(),INTERVAL (DAY(curdate())-1) DAY) - INTERVAL 1 MONTH ) ) 
  AND codeInsee = ? 
GROUP BY idAgence;
EO_REQ_COUNT_PROPS

my $dbh = &connect_bdd("viteloge",config_MYSQL_CREDS());
$dbh->do("SET CHARACTER SET DEFAULT");

my $sth_cities = $dbh->prepare( $REQ_find_cities );
my $sth_clicks = $dbh->prepare( $REQ_count_clicks );
my $sth_properties = $dbh->prepare( $REQ_count_properties );

$sth_cities->execute
  or die "Unable to get cities";
my %compiled_stats;
my $i = 0;
$| = 1;
while ( my $city = $sth_cities->fetchrow_hashref ) {
    $i++;
    print "." if 0 == $i % 10 ;
    $sth_clicks->execute( $city->{codeInsee} )
      or die "Unable to get stats for " . $city->{codeInsee};
    my %city_compiled_stats;
    while ( my $ag_stats = $sth_clicks->fetchrow_hashref ) {
        $city_compiled_stats{$ag_stats->{idAgence}}{'clicks'} = $ag_stats->{nbClicks};
    }
    $sth_clicks->finish;
    $sth_properties->execute( $city->{codeInsee} )
      or die "Unable to get property counts for " . $city->{codeInsee};
    while ( my $ag_stats = $sth_properties->fetchrow_hashref ) {
        $city_compiled_stats{$ag_stats->{idAgence}}{'properties'} = $ag_stats->{nbProperties};
    }
    $sth_properties->finish;
    $compiled_stats{$city->{codeInsee}} = \%city_compiled_stats;
}

open FH,">stats_cities.json"
  or die "Unable to store stats";
print FH JSON->new->indent->encode( \%compiled_stats );
close FH;
