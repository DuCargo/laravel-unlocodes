# About
Laravel-UNLOCODES is an initiative of the DuCargo rate management and price-finding platform. 

| This package is in a pre-alpha state and should not be used in production. | 
| -------- | 

# Goal
To produce a stable UNLOCode package that can seed a database with the most recent UN/LOCODE data available, regularly checks for changes in the global listing and modifies the (relevant) database(s) to reflect the changes.

We aim to support multiple sources:
- A custom set of CSV files in our own format
- A [datapackage.json dataset](https://github.com/datasets/un-locode.git) file as provided by [DataHub](https://datahub.io/core/un-locode)
- External APIs like [NxtPort](https://github.com/NxtPort/UNLoCodes)

# Installation
Add to `composer.json`

Run on the machine that your database is on:
`php artisan migrate:refresh --seed`
