#!/bin/bash
#
# Creates the structure and copies files in the
# $BASEPATH and $PUBDIR directories.
# Downloads composer and updates the packages
#
# Params:
#   --no-resource: The resource directory is not created
#   --no-composer: Composer is not downloaded nor the packages updated
#   --no-vendor: The vendor directory is not created nor copied


SOURCEDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/../"
BASEPATH="/var/www/resource_server/"
PUBDIR="/var/www/public/"
VARDIR=${BASEPATH}"var/"
COMPOSERDIR=${SOURCEDIR}"composer/"
COMPOSERCOMMAND=${COMPOSERDIR}"composer.phar"
RESOURCESTORE="/var/www/static/"
PRODUCTIMGSTORE=${RESOURCESTORE}"img/product/"
RESOURCECACHE=${RESOURCESTORE}"cache/"
WATERMARKSTORE=${RESOURCESTORE}"img/watermark/"

# Arguments parsing
OPT_RESOURCE=1
OPT_COMPOSER=1
OPT_VENDOR=1

while [[ $# -gt 0 ]]
do
key="$1"

case ${key} in
    --no-resource)
    OPT_RESOURCE=0
    shift
    ;;
    --no-composer)
    OPT_COMPOSER=0
    shift
    ;;
    --no-vendor)
    OPT_VENDOR=0
    shift
    ;;
    *)

    ;;
esac
done

# Directories and permissions
mkdir -p ${BASEPATH}
chown -R apache:apache ${BASEPATH}
mkdir -p ${PUBDIR}
mkdir -p ${VARDIR}
chmod 777 ${VARDIR}

if [ ${OPT_RESOURCE} = "1" ]; then
    mkdir -p ${PRODUCTIMGSTORE}
    mkdir -p ${RESOURCECACHE}
    mkdir -p ${WATERMARKSTORE}
    chmod -R 775 ${RESOURCESTORE}
    chown -R apache:apache ${RESOURCESTORE}
fi

if [ ${OPT_COMPOSER} = "1" -a ! -f ${COMPOSERCOMMAND} ]; then
    # Install composer
    mkdir -p ${COMPOSERDIR}
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php -r "if (hash_file('SHA384', 'composer-setup.php') === 'e115a8dc7871f15d853148a7fbac7da27d6c0030b848d9b3dc09e2a0388afed865e6a3d6b3c0fad45c48e2b5fc1196ae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    php composer-setup.php --install-dir=${COMPOSERDIR}
    php -r "unlink('composer-setup.php');"

    # Update packages
    cd ${SOURCEDIR}
    php ${COMPOSERCOMMAND} update --no-dev
fi

if [ ${OPT_VENDOR} = "1" ]; then
    cp -rfv ${SOURCEDIR}"vendor" ${BASEPATH}
fi

cp -rfv ${SOURCEDIR}"src" ${BASEPATH}
cp -rfv ${SOURCEDIR}"config" ${BASEPATH}
cp -rfv ${SOURCEDIR}"web/index.php" ${PUBDIR}"index.php"
cp -rfv ${SOURCEDIR}"web/.htaccess" ${PUBDIR}".htaccess"
cp -rfv ${SOURCEDIR}"web/favicon.ico" ${PUBDIR}