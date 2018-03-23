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

Instead of requesting the default client "at runtime" over the 
`$cotainer->get()` method you can also make use of Symfony's auto wiring feature
by using the `DefaultPalomaClient` class:

```php
public function mySymfonyAction(DefaultPalomaClient $paloma) {
  $categories = $paloma->catalog()->categories();
}
```

To get the client factory in order to get other clients than the default one:

```php
$factory = $container->get('paloma_client.client_factory');
$defaultClient = $factory->getDefaultClient();
$myOtherChannelClient = $factory->getClient('my-other-channel', 'my-locale');
```


## Installation

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


## Configuration

```yaml
paloma_client:
    base_url: 'https://my-api-endpoint'  # Probably get this from parameters
    api_key: MyApiKey  # Probably get this from parameters
    # The log format to use for Paloma requests which are deemed successful.
    # If not set the default specified in paloma/shop-client will be used.
    log_format_success: ~
    # The log format to use for Paloma requests which are deemed failed.
    # If not set the default specified in paloma/shop-client will be used.
    log_format_failure:  ~
    # The cache provider to use as the caching backend. Has to be a provider
    # which implements the PSR-6 CacheItemPoolInterface. Ideally one uses
    # the php-cache/adapter-bundle to define and configure a provider service. 
    cache_provider: ~
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


## Paloma trace ID

Paloma allows for the specification of a trace ID alongside every request. This
helps to correlate requests observed on Paloma and the client application with
each other.

The Paloma trace ID has to be a string which is exactly 8 characters long and
consists of only lower case characters a-z and digits 0-9.

It is possible to set this trace ID within the `ClientFactory` such that it will
be added to every request to Paloma which is sent through a client created by
that factory. A convenient location to set the trace ID is probably in the same
area as the default channel and locale is set.

It can be done like this:

```php
$factory = $container->get('paloma_client.client_factory');
# This is just an example of how to specify a trace ID, one might use more
# elaborate approaches which include session and request information.
$factory->setPalomaTraceId(substr(uniqid(), 0, 8));
```
