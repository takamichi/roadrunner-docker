#!/bin/sh
# Composer 実行用Dockerコンテナのエントリーポイント

set -e

if [[ ! -d $COMPOSER_HOME/vendor/hirak/prestissimo ]]; then
    composer global require hirak/prestissimo
fi

composer --version
exec composer "$@"
