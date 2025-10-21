# vim: set tabstop=8 softtabstop=8 noexpandtab:
.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: cs
cs: vendor ## Normalizes composer.json with ergebnis/composer-normalize and fixes code style issues with friendsofphp/php-cs-fixer
	mkdir -p .build/php-cs-fixer
	vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --diff --verbose

phpdoc: clean ## Generate PHPDoc
	mkdir -p docs
	phpdoc --defaultpackagename=MainPackage
	mv .phpdoc/build/* docs

apigen: ## Build Apigen documentation
	rm -rfv docs ; mkdir docs
	VERSION=`cat debian/composer.json | grep version | awk -F'"' '{print $4}'`; \
	apigen generate --destination=docs --title "FluentPDO ${VERSION}" --charset UTF-8 --access-levels public --access-levels protected --php --tree -- src/
