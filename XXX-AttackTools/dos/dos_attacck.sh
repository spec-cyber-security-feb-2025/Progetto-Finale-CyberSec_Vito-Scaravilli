#!/bin/bash

URL="http://localhost:8000"
NUM_REQUESTS=1000

for ((i=1; i<=NUM_REQUESTS; i++))
do
   curl -s "$URL" > /dev/null &
   echo "Richiesta $i inviata"
done

wait
echo "Attacco completato: $NUM_REQUESTS richieste inviate"