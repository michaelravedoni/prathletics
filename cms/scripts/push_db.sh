#!/bin/bash

# Push Database
#
# Push local database to a remote
#
# @author    michaelravedoni
# @copyright Copyright (c) 2019 michaelravedoni
# @link      https://michael.ravedoni.com/
# @license   MIT

# Get the directory of the currently executing script
DIR="$(dirname "${BASH_SOURCE[0]}")"

# Include files
INCLUDE_FILES=(
            "common/defaults.sh"
            ".env.sh"
            "common/common_env.sh"
            "common/common_db.sh"
            )
for INCLUDE_FILE in "${INCLUDE_FILES[@]}"
do
    if [[ ! -f "${DIR}/${INCLUDE_FILE}" ]] ; then
        echo "File ${DIR}/${INCLUDE_FILE} is missing, aborting."
        exit 1
    fi
    source "${DIR}/${INCLUDE_FILE}"
done

# Source the correct file for the database driver
case "$GLOBAL_DB_DRIVER" in
    ( 'mysql' )
        source "${DIR}/common/common_mysql.sh"
        if [[ "${REMOTE_DB_USING_SSH}" == "yes" ]] ; then
            ssh $REMOTE_SSH_LOGIN -p $REMOTE_SSH_PORT "$REMOTE_MYSQLDUMP_CMD --verbose -u $REMOTE_DB_USER -p${REMOTE_DB_PASSWORD} -h $REMOTE_DB_HOST $REMOTE_DB_NAME < ${REMOTE_ROOT_PATH}${REMOTE_BACKUPS_PATH}backup-database.sql"
        else
            echo "error"
        fi
        echo "Push db done !"
        ;;
    ( * )
        echo "Environment variable GLOBAL_DB_DRIVER was neither 'mysql' nor 'pgsql'. Aborting."
        exit 1 ;;
esac

# Normal exit
exit 0
