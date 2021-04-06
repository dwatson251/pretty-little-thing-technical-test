# Pretty Little Thing Technical Test
## Software Dependencies
- PHP 7.x
    - ext-json
    - ext-mysql
- MySQL 5.x/ MariaDB

## Installation
### Config
Copy the .env.dist file in to .env `cp .env.dist .env` and modify the database connection URL to your needs.
By default, the application will conntect to a MySQL database named `plt` hosted on `127.0.0.1:3306` with the user and pass
`root` and `root`.

### Application install
Ensure required composer dependencies are installed by running

`composer install`

### Database install
`$php bin/console doctrine:database:create`

`$php bin/console doctrine:migrations:migrate --no-interaction`

## Usage
Create a file `my-products-file.csv` with the following format.

```CSV
sku           | description    | normal_price | special_price
5ee39a2442ad7 | the description| 276          | 275.50
```

Whitespace has been added here to make the dataset look prettier, but you'll need to remove extraneous whitespace.
Please ensure you stick to the CSV format but instead using (|) as a delimiter.

Then run
`$php bin/console  plt:product:import --filename my-products-file.csv --no-debug`

## Debugging

The console can output additional information such as products that have 
errored, memory usage and time taken

To show these, use the command with the `--debug` option

`$php bin/console  plt:product:import --filename my-products-file.csv --debug --no-debug`

## Functional Test Cases

Additional functional test cases can be found within `tests/Integration/Provider/ProductImport`

## Tools
### Unit tests
For unit tests, run:

`$php vendor/phpunit/phpunit/phpunit`