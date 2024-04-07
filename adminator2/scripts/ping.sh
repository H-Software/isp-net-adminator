#!/bin/bash

#		    
ping -c1 $1 -w 1 -A |grep rtt | awk '{ $c=split($4,i,"/"); printf i[1] }';
