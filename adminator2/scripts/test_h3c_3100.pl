#!/usr/bin/perl

########################################################################
##                                                                    ##
##  test spojeni v h3c 3100					      ##
##								      ##	
########################################################################

use Switch;  
use Net::Telnet; 
use RRDs;

## IPcko pro management
$ip_adr_sw = $ARGV[1];

$port_id = $ARGV[2];

## debug mod , 1 pro ON, 0 pro OFF
$debug = 0;

$login_sw = "manager";
$passwd_sw = "Platon1";

if( $#ARGV != 1 && $ARGV[0] eq "show_dot1x" )
{
   usage();
   exit 1;
}

switch ($ARGV[0]) 
{
    case "show_macauth" 
    {
        show_macauth();
    }
    case "show_macaddr" 
    {
        show_macaddr();
    }
    else
    {
        usage();
    }
}

sub show_macauth()
{

    $telnet = new Net::Telnet( Timeout=>2, Errmode=>'die' ); 
    @open = $telnet->open($ip_adr_sw); 

    if( $debug == 1)
    { print "vysledek open: ".@open."\n"; }

    $telnet->waitfor('/Username:/'); #or die "no User Name prompt: ", $telnet->lastline;;
    @login = $telnet->print($login_sw);

    if( $debug == 1)
    { print "vysledek loginu: ".@login."\n"; }
    
    $telnet->waitfor('/Password:/');
    $telnet->print($passwd_sw);

    $telnet->waitfor('/>/');
    $telnet->print();
    $telnet->waitfor('/>/');
    
    $telnet->print("display mac-authentication interface Ethernet 1/0/".$port_id);

    @out = $telnet->waitfor('/>/');	
    print $out[0];

}

sub show_macaddr()
{

    $telnet = new Net::Telnet( Timeout=>2, Errmode=>'die' ); 
    @open = $telnet->open($ip_adr_sw); 

    if( $debug == 1)
    { print "vysledek open: ".@open."\n"; }

    $telnet->waitfor('/Username:/'); #or die "no User Name prompt: ", $telnet->lastline;;
    @login = $telnet->print($login_sw);

    if( $debug == 1)
    { print "vysledek loginu: ".@login."\n"; }
    
    $telnet->waitfor('/Password:/');
    $telnet->print($passwd_sw);

    $telnet->waitfor('/>/');
    $telnet->print();
    
    $telnet->waitfor('/>/');
    $telnet->print("display mac-address interface Ethernet 1/0/".$port_id);

    @out = $telnet->waitfor('/>/');
    print $out[0];

}

sub usage {
    print "testovani h3c 3100 vuci urcite ip adrese \n\n";
    print " Pouziti: $0 <fce> <ip_adresa_sw> <cislo_portu >\n";
    print "  <fce>       - show_macauth - zjisti a vypise data ohledne overeni MAC adresy\n";
    print "              - show_macaddr - zjisti a vypise data ohledne mac adresy \n";
    print "  <ip_adresa_sw> - IP adresa switche \n";
    
}
