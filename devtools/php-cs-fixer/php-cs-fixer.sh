#!/bin/sh
# PHP CS Fixer 実行用Dockerコンテナのエントリーポイント

set -e

if [ ! -x /usr/local/bin/php-cs-fixer ]; then
    curl --silent -L https://cs.symfony.com/download/php-cs-fixer-v2.phar -o /usr/local/bin/php-cs-fixer
    chmod a+x /usr/local/bin/php-cs-fixer
fi

export PHP_CS_FIXER_FUTURE_MODE=1

php-cs-fixer --version
exec php-cs-fixer "$@"
