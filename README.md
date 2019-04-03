# get-address-io-php
Repo for using GetAddress.io within PHP

Currently only implements find.

Requires an API key!

# How to Use
```PHP
<?php
    $apiKey = '';

    $getAddress = new GetAddress($apiKey);

    // Get a list of addresses in a postcode
    $getAddress->find('SR51NA');

    // Get detailed information about an address
    $getAddress->find('SR51NA', 132, false, true, true);
```

# Unit Testing
To setup PHPUnit to test everything you'll need to add your API key.

You can configure your suite by adding a global var of GETADDRESS_API_KEY and calling test/GetAddressTest.php

```xml
<php>
    <var name="GETADDRESS_API_KEY" value="" />
</php>
<testsuites>
    <testsuite name="get-address-io-php test">
        <file>test/GetAddressTest.php</file>
    </testsuite>
</testsuites>
```

# To implement
* Parse addresses in to objects before returning
* Add admin functionality
* Long & Lat functionaility
