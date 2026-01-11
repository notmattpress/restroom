# PHP Unit Tests

This directory contains PHP unit tests for the Restroom theme.

## Setup

Install dependencies:

```bash
npm install
composer install
```

## Running Tests

Run tests inside wp-env:

```bash
npm run wp-env start
npm run test:php
```

## Test Coverage

Currently, the following classes are tested:

-   `Restroom_PooCommerce_Adjacent_Products` - Integration tests verifying adjacent product selection skips hidden products on identical publish dates.
