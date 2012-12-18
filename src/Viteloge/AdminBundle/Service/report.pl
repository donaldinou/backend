#! /usr/bin/perl -w

use strict;

my ($ID_AGENCE) = @ARGV;

use viteloge;
require 'config.pl';

use DBI();
use DateTime;
use DateTime::Locale;


use Report;


my $dbh = &connect_bdd("viteloge",config_MYSQL_CREDS());
$dbh->do("SET CHARACTER SET DEFAULT");

DateTime->DefaultLocale( 'fr_FR' );
my $dt = DateTime->now( time_zone =>'Europe/Paris');
$dt->subtract( DateTime::Duration->new(months  => 1) );


my $req = "SELECT
        agence.idAgence,
        agence.nomAgence,
        agence.urlAgence,
        agence.mailAgence,
        privilege.specifAgence
    FROM agence
    LEFT JOIN privilege ON agence.idAgence = privilege.idAgence
    WHERE agence.idAgence = ?";

my $sth = $dbh->prepare( $req );
$sth->execute( $ID_AGENCE );
my $agence = $sth->fetchrow_hashref;
if ( $agence ) {
    my $idAgence = $agence->{idAgence};
    my $specifAgence = $agence->{specifAgence};
    my $html_output = Report::agence( $dbh, $dt, $agence, $specifAgence, 1 );
    print $html_output;
    $sth->finish;
} else {
    print "<b>Agence inconnue</b>\n";
}
