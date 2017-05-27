#! /bin/bash
#by geniv
# instalace sandboxu

# stazeni composeru pokud neni na disku
if [ ! -f composer.phar ]; then
    wget https://getcomposer.org/composer.phar
fi

# zpracovani sekce jako 1 parametur
NAME="sandbox"
if [ "${1}" != "" ]; then
	NAME=${1} 
fi

php composer.phar create-project nette/sandbox "${NAME}"

#php composer.phar require latte/latte
