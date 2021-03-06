#!/bin/bash

set -e

SCRIPTFILE=$(readlink -f "$0")
SCRIPTDIR=$(dirname "$SCRIPTFILE")

alias phpunit='php -dzend_extension=xdebug.so $(which phpunit)'

phpunit -c "$SCRIPTDIR/../../phpunit.dist.xml" --coverage-clover "$SCRIPTDIR/../../build/logs/clover.xml"

$SCRIPTDIR/phpcs.bash $1


if [ "$PROCESS_CODECLIMAE" = true ] && [ "${TRAVIS_PULL_REQUEST}" = "false" ] && [ "${TRAVIS_BRANCH}" = "master" ]
then
    ./vendor/bin/test-reporter
fi
