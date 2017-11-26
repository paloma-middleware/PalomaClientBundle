# Paloma Client Symfony Bundle

A Symfony bundle for easy integration of the
[paloma/shop-client](https://github.com/paloma-middleware/shop-client-php)
package into a Symfony application.

Features:
- Configure your Paloma shop client within a Symfony app configuration
- Paloma client factory and convenience service definition
- Integration into the Symfony web profiler


## Usage

To get the default Paloma client (read below what that means):

```php
$client = $container->get('paloma_client.default_client');
```

To get the client factory in order to get other clients than the default one:

```php
$factory = $container->get('paloma_client.client_factory');
$defaultClient = $factory->getDefaultClient();
$myOtherChannelClient = $factory->getClient('my-other-channel', 'my-locale');
```


## Installation and Configuration

Add the bundle to your application:

```bash
composer require paloma/client-bundle
```

Then add add it to your `AppKernel.php`:

```php
$bundles = [
    [...]
    new Paloma\ClientBundle\PalomaClientBundle(),
    [...]
];
```

The following configuration is mandatory:

```yaml
paloma_client:
    base_url: 'https://my-api-endpoint'  # Probably get this from parameters
    api_key: MyApiKey  # Probably get this from parameters
```


## Channels and Locales

Paloma is built around the notion of channels. One Paloma setup may consist of
many different channels. Good examples of channels are "country" or "tenant" (in
a multi tenant system). Each Paloma API endpoint is prefixed by the channel (see 
[Paloma API docs](https://docs.paloma.one/) for more information). In addition
to channels most API endpoints also take a prefix for the locale of the request.
Paloma itself cannot determine the correct channel or locale and
it is the frontend application's job to define that.

This job is normally done by applying some URL scheme to the frontend 
application, like for example
`https://<tenant>.myclient.com/<country>/<language>/<path>`. In this example the
resulting Paloma channel might be `<tenant>_<country>` and locale `<language>`.
The important part is that channels are project specific and can change in 
syntax and semantics.
For that reason it has to be solved again in every application.

The standard way to solve this is to install a request listener which
analyzes the incoming URL and determines the channel and locale from that URL.
It then sets the default channel using:

```php
$factory = $container->get('paloma_client.client_factory');
$factory->setDefaultChannel('<my determined channel>');
$factory->setDefaultLocale('<my determined locale>');
```

After this no other code in the application needs to care about channels or
locales. This should handle most common use cases as the need to address 
multiple channels or locales within the same request is rare.
