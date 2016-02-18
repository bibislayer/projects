Symfony command utile
========================


1) Installing the Standard Edition
----------------------------------

Install vendor

  	php composer.phar update

Generate entities

  	php app/console doctrine:generate:entities FAC

Executes (or dumps) the SQL needed to update the database schema to match the current mapping metadata

  	php app/console doctrine:schema:update --force


2) Generate all public assets
----------------------------------

Dumps all assets to the filesystem
  
  	php app/console assetic:dump 

Installs bundles web assets under a public web directory

  	php app/console assets:install 

Clears the cache
  
  	php app/console cache:clear --env=dev