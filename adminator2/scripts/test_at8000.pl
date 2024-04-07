#!/usr/bin/perl

########################################################################
##                                                                    ##
##  test spojeni v at8000					      ##
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
    case "show_dot1x" 
    {
        show_dot1x();
    }
    case "show_portstatus" 
    {
        show_portstatus();
    }
    else
    {
        usage();
    }
}

sub show_dot1x()
{

    $telnet = new Net::Telnet( Timeout=>2, Errmode=>'die' ); 
    @open = $telnet->open($ip_adr_sw); 

    if( $debug == 1)
    { print "vysledek open: ".@open."\n"; }

    $telnet->waitfor('/User Name:/'); #or die "no User Name prompt: ", $telnet->lastline;;
    @login = $telnet->print($login_sw);

    if( $debug == 1)
    { print "vysledek loginu: ".@login."\n"; }
    
    $telnet->waitfor('/Password:/');
    $telnet->print($passwd_sw);

    $telnet->waitfor('/# $/');
    $telnet->print("show dot1x users");

    #$telnet->waitfor('/More: $/');

    @out = $telnet->waitfor('/# $/');	
    print $out[0];

}

sub show_portstatus()
{

    $telnet = new Net::Telnet( Timeout=>2, Errmode=>'die' ); 
    @open = $telnet->open($ip_adr_sw); 

    if( $debug == 1)
    { print "vysledek open: ".@open."\n"; }

    $telnet->waitfor('/User Name:/'); #or die "no User Name prompt: ", $telnet->lastline;;
    @login = $telnet->print($login_sw);

    if( $debug == 1)
    { print "vysledek loginu: ".@login."\n"; }
    
    $telnet->waitfor('/Password:/');
    $telnet->print($passwd_sw);

    $telnet->waitfor('/# $/');
    $telnet->print("show interfaces status ethernet e".$port_id);

    @out = $telnet->waitfor('/# $/');
    print $out[0];

}

sub usage {
    print "testovani at8000 vuci urcite ip adrese \n\n";
    print " Pouziti: $0 <fce> <ip_adresa_sw> <cislo_portu >\n";
    print "  <fce>       - show_dot1x - zjisti a vypise data ohledne overeni \n";
    print "              - show_portstatus - zjisti a vypise data ohledne portu \n";
    print "  <ip_adresa_sw> - IP adresa switche \n";
    
}
