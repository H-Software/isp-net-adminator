#!/usr/bin/perl

$data=$ARGV[0];

unlink $data or die "Soubor nebyl smazán. $!";

