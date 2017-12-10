<?php


namespace Paloma\ClientBundle\Security;


use GuzzleHttp\Exception\BadResponseException;
use Paloma\ClientBundle\Model\Customer;
use Paloma\ClientBundle\Model\User;
use Paloma\Shop\PalomaClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class PalomaUserAuthProvider implements AuthenticationProviderInterface
{

    /** @var  PalomaClient */
    private $palomaClient;
    /** @var  LoggerInterface */
    private $logger;

    public function __construct(PalomaClient $palomaClient, LoggerInterface $logger)
    {
        $this->palomaClient = $palomaClient;
        $this->logger = $logger;
    }

    public function authenticate(TokenInterface $token)
    {
        if (!($token instanceof PalomaUserToken)) {
            throw new \RuntimeException('PalomaUserAuthProvider: Token not supported: ' . get_class($token));
        }

        try {
            $response = $this->palomaClient->customers()->authenticateUser($token->getUsername(),
                $token->getPassword());
        } catch (BadResponseException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == 403 || $statusCode == 404) {
                throw new CustomUserMessageAuthenticationException('Invalid credentials.');
            }
            if ($statusCode >= 500) {
                throw new CustomUserMessageAuthenticationException(
                    'Authentication request could not be processed due to a system problem.');
            }
            throw $e;
        }

        // Some data checks
        if (!isset($response['user']['username']) || !isset($response['user']['id'])
            || !isset($response['customer']['id'])) {
            throw new \RuntimeException('PalomaUserAuthProvider: got invalid auth response from Paloma: ' .
                print_r($response, true));
        }

        $this->logger->debug('PalomaUserAuthProvider: auth successful, creating authenticated token');

        $customer = new Customer();
        $customer->setId($response['customer']['id']);
        $customer->setCustomerNumber(isset($response['customer']['customerNumber']) ?
            $response['customer']['customerNumber'] : null);
        $customer->setEmailAddress(isset($response['customer']['emailAddress']) ?
            $response['customer']['emailAddress'] : null);
        $customer->setLocale(isset($response['customer']['locale']) ?
            $response['customer']['locale'] : null);
        $customer->setFirstName(isset($response['customer']['firstName']) ?
            $response['customer']['firstName'] : null);
        $customer->setLastName(isset($response['customer']['lastName']) ?
            $response['customer']['lastName'] : null);
        $customer->setGender(isset($response['customer']['gender']) ?
            $response['customer']['gender'] : null);

        $user = new User();
        $user->setUsername($response['user']['username']);
        $user->setPassword($token->getPassword());
        $user->setId($response['user']['id']);
        $user->setCustomer($customer);
        $user->setRoles(['ROLE_CUSTOMER']);

        $newToken = new PalomaUserToken($user->getRoles());
        $newToken->setAuthenticated(true);
        $newToken->setUser($user);

        return $newToken;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof PalomaUserToken;
    }

}
