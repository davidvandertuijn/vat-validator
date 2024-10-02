# VAT Validator

<a href="https://packagist.org/packages/davidvandertuijn/vat-validator"><img src="https://poser.pugx.org/davidvandertuijn/vat-validator/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/davidvandertuijn/vat-validator"><img src="https://poser.pugx.org/davidvandertuijn/vat-validator/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/davidvandertuijn/vat-validator"><img src="https://poser.pugx.org/davidvandertuijn/vat-validator/license.svg" alt="License"></a>

![VAT Validator](https://cdn.davidvandertuijn.nl/github/vat-validator.png)

This VAT Number Validation library allows users to verify VAT numbers with high accuracy by leveraging both regular expressions and the VIES (VAT Information Exchange System) service. This dual-layer validation ensures that VAT numbers are correctly formatted and legally valid within the European Union.

[!["Buy Me A Coffee"](https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png)](https://www.buymeacoffee.com/davidvandertuijn)

## Install

```
composer require davidvandertuijn/vat-validator
```

## Usage

```php
use Davidvandertuijn\VatValildator\Validator as VatValidator;
```

```
$vatValidator = new VatValidator;

$vatValidator->validate('NL821783981B01'); // true

if ($vatValidator->isValid()) {
    $aMetaData = $vatValidator->getMetaData();
    
    /*
    [
        "name" => "FLORO WEBDEVELOPMENT B.V.",
        "address" => "WESTBLAAK 00180 3012KN ROTTERDAM"
    ]
    */
}
```

## Strict

In this VAT Number Validation library, when strict mode is set to FALSE, the validation process is more flexible, especially in scenarios where the VIES (VAT Information Exchange System) service is temporarily unavailable. In such cases, the validation will return TRUE, allowing the workflow to continue without disruption due to service timeouts or SOAP errors.

```
$vatValidator->setStrict(false); // default = true
```

## Cache

To optimize the validation process and reduce the dependency on the VIES (VAT Information Exchange System) service, we recommend caching valid VAT numbers within your application. This approach minimizes repeated requests to the VIES service and improves overall performance.
