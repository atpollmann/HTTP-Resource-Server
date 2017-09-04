#!/bin/bash
#
# Copies the template file into a public directory.
# This template is a web page that can be requested
# by the tester
#
# Params:
#   --count: The number of images that the page will request.
#            (the corresponding images_xxx.php file must exist)
#            Default: 100
#   --width: The width of each image that will be delivered
#            Default: 80
#   --nocaching: 1 = no_caching, 0 nothing
#            Default: 1

SOURCE_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/"
PUB_DIR="/var/www/public/"
PERFORMANCE_TEST_DIR=${PUB_DIR}"/performance/image/"

# Arguments parsing
OPT_COUNT=100
OPT_WIDTH=80
OPT_NOCACHING=0

while [[ $# -gt 1 ]]
do
key="$1"

case ${key} in
    --count)
    OPT_COUNT="$2"
    shift
    ;;
    --width)
    OPT_WIDTH="$2"
    shift
    ;;
    --nocaching)
    OPT_NOCACHING=1
    shift
    ;;
    *)

    ;;
esac
shift
done

# First run the simple deployment script
bash ${SOURCE_DIR}../../../web/deployment.sh --no-vendor --no-composer

# Directories and permissions
mkdir -p ${PUB_DIR}
mkdir -p ${PERFORMANCE_TEST_DIR}

# Replace source file width query string
PLACEHOLDER="__QUERYSTRING__"
REPLACEMENT="width=${OPT_WIDTH}"

# If no caching is set
if [ ${OPT_NOCACHING} = "1" ]; then
    REPLACEMENT="${REPLACEMENT}\&no_caching"
fi

sed "s/${PLACEHOLDER}/${REPLACEMENT}/" <${SOURCE_DIR}images_${OPT_COUNT}.php >${PERFORMANCE_TEST_DIR}"index.php"
cp -rfv ${SOURCE_DIR}".htaccess" ${PUB_DIR}".htaccess"