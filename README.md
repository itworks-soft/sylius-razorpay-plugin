![ITWorks](https://itworks-soft.com/static/ITWorks-logo.png)

# ITWorks Razorpay Plugin [![Build Status](https://travis-ci.org/ITWorksCommerce/SyliusRazorpayPlugin.svg?branch=master)](https://travis-ci.org/ITWorksCommerce/SyliusRazorpayPlugin)

## Overview

This plugin integrated [Razorpay payments](https://www.razorpay.com/) with Sylius based applications. After installing it you should be able to create a payment method for Razorpay gateway and enable its payments in your web store.

## Support

We work on amazing eCommerce projects on top of Sylius and Pimcore. Need some help or additional resources for a project?
Write us an email on mikolaj.krol@ITWorks.pl or visit [our website](https://ITWorks.shop/)! :rocket:

## Demo

We created a demo app with some useful use-cases of the plugin! Visit [demo.ITWorks.shop](https://demo.ITWorks.shop) to take a look at it. 
The admin can be accessed under [demo.ITWorks.shop/admin](https://demo.ITWorks.shop/admin) link and `sylius: sylius` credentials.

## Installation

```bash
$ composer require itworks-soft/sylius-razorpay-plugin

```
    
Add plugin dependencies to your AppKernel.php file:

```php
public function registerBundles()
{
    return array_merge(parent::registerBundles(), [
        ...
        
        new \ITWorks\SyliusRazorpayPlugin\ITWorksSyliusRazorpayPlugin(),
    ]);
}
```

## Contribution

Learn more about our contribution workflow on http://docs.sylius.org/en/latest/contributing/.
