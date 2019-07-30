
PeteNys Laravel Generator
==========================

lets say you build a new table and run the migration (invoices)... you then just run the following command

php artisan petenys:json_api Invoice --fromTable --tableName=invoices --relations

builds the Model, Repository, Policy, Observer, Adapter, Schema, Validator and adds the route, json-api-v1 resource and adds the Observer to the ObserverProvider
adds all the relations it can figure out from foreign keys as well

--include=controller,tests
will also add those objects

or you can call any build by itself... petenys:adapter or petenys:controller or etc
