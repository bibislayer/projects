# lib/task/reIndexArticle.sh
#! /bin/bash

# on vide l'index d'abord
directory=$PWD
directory=${directory%/pl*sk}

result=0
i=850
while [ $result -eq 0 ] ; do
    let offset=$i*5
    cd $directory
    php app/console scrap:dpstream $offset $i
    result=$?
    let i=$i+1
done