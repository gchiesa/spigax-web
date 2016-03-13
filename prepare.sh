#!/bin/bash -x

#
# preparo nuovo config con nuova versione
#
if [ -f VERSION ]; then 
	VERSION=$(cat VERSION)
	ROW="define('SITE_VERSION', '${VERSION}');"
	sed -e s/define.*SITE_VERSION.*\'.*\'\).*/"${ROW}"/ lib/config.php > /tmp/spigax-config.php
	mv -f lib/config.php lib/config-dist-example.php 
	cp /tmp/spigax-config.php lib/config-dist.php
fi

#
# pulisco i files di backup
#
find . -name "*~" -exec rm -rf {} \;
find . -name \.\* -not -name \. -not -name ".htaccess" -exec rm -rf {} \; 

#
# preparo i permessi corretti
#
find . -type f -exec chmod go+r {} \;
find . -type d -exec chmod go+rx  {} \; 

#
# elimino i file da non pacchettizzare
RMLIST="pma VERSION"
for ELEM in ${RMLIST}; do
	
	rm -rf $ELEM
	
done
 