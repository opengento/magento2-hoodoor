# Module for Magento 2

[![Latest Stable Version](https://img.shields.io/packagist/v/opengento/module-hoodoor.svg?style=flat-square)](https://packagist.org/packages/opengento/module-hoodoor)
[![License: MIT](https://img.shields.io/github/license/opengento/magento2-hoodoor.svg?style=flat-square)](./LICENSE)
[![Packagist](https://img.shields.io/packagist/dt/opengento/module-hoodoor.svg?style=flat-square)](https://packagist.org/packages/opengento/module-hoodoor/stats)
[![Packagist](https://img.shields.io/packagist/dm/opengento/module-hoodoor.svg?style=flat-square)](https://packagist.org/packages/opengento/module-hoodoor/stats)

This module provides a top-notch security for your customers' accounts by adopting a passwordless approach, effectively removing the vulnerability of weak passwords from your database. This instills a sense of confidence and reliability in your platform among your customers.

- [Setup](#setup)
    - [Composer installation](#composer-installation)
    - [Setup the module](#setup-the-module)
- [Settings](#settings)
- [Documentation](#documentation)
- [Support](#support)
- [Authors](#authors)
- [License](#license)

## Setup

Magento 2 Open Source or Commerce edition is required.

###  Composer installation

Run the following composer command:

```
composer require opengento/module-hoodoor
```

### Setup the module

Run the following magento command:

```
bin/magento setup:upgrade
```

**If you are in production mode, do not forget to recompile and redeploy the static resources.**

## Settings

The configuration for this module is available in `Stores > Configuration > OpenGento > Hoodoor`.

Make sure you have generated a secret key.

## Documentation

### Compatibility and Activation:

This module is compatible with Magento 2 version 2.4.6-p4. 

You have the flexibility to enable its functionality either on the Magento frontend or backend. To activate either option, adjust the corresponding values in the config settings.

### Token Expiration and Customization:

By default, the authentication token remains valid for 15 minutes after the email is sent. However, you have the option to customize this duration according to your requirements. Refer to the PHP documentation on how to modify the datetime value.

### Enhanced Security Measures:

We have implemented a robust security layer to ensure a high level of protection for the data transmitted via the HTTP protocol.

### Private Key Generation:

To process requests securely, it is essential to generate a private key in the settings. This private key serves as a crucial component for decrypting and authenticating requests. Failure to provide this key may hinder the ability to decipher and establish connections effectively.

## Support

Raise a new [request](https://github.com/opengento/magento2-hoodoor-login/issues) to the issue tracker.

## Authors

- **Opengento Community** - *Lead* - [![Twitter Follow](https://img.shields.io/twitter/follow/opengento.svg?style=social)](https://twitter.com/opengento)
- **Ronan Guérin** - *Maintainer* - [![GitHub followers](https://img.shields.io/github/followers/ronangr1.svg?style=social)](https://github.com/ronangr1)
- **Contributors** - *Contributor* - [![GitHub contributors](https://img.shields.io/github/contributors/opengento/magento2-store-path-url.svg?style=flat-square)](https://github.com/opengento/magento2-store-path-url/graphs/contributors)

## License

This project is licensed under the MIT License - see the [LICENSE](./LICENSE) details.

***That's all folks!***
