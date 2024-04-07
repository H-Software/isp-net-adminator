#!/usr/bin/perl

########################################################################
##                                                                    ##
##  vypis informaci z dhcp serveru				      ##
##								      ##
########################################################################

$ip_klienta = $ARGV[1];

if( $#ARGV != 1 )
{
   usage();
   exit 1;
}
      


$output = `ssh -l admin-ssh-key -i /root/.ssh/id_dsa $ARGV[0] "/ip dhcp-server lease print value where address =$ip_klienta"`;

print $output;

sub usage 
{
    print "vypis informaci z dhcp serveru pro optiku \n\n";
    print " Pouziti: $0 <ip_adresa_dhcp_serveru> <ip_adresa_klienta> \n";
		    
}
	    