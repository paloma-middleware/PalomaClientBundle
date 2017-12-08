<?php


namespace Paloma\ClientBundle\Security;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class PalomaUserAuthListener implements ListenerInterface
{

    /** @var  string */
    private $formFieldNameUsername;
    /** @var  string */
    private $formFieldNamePassword;
    /** @var  TokenStorageInterface */
    private $tockenStorage;
    /** @var  AuthenticationManagerInterface */
    private $authenticationManager;
    /** @var  LoggerInterface */
    private $logger;

    public function __construct($formFieldNameUsername, $formFieldNamePassword,
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager, LoggerInterface $logger)
    {
        $this->formFieldNameUsername = $formFieldNameUsername;
        $this->formFieldNamePassword = $formFieldNamePassword;
        $this->tockenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->logger = $logger;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->isMethod('POST')) {
            $this->logger->debug('PalomaUserAuthListener: ignoring authentication ' .
                'request not sent in as POST');
            return;
        }

        $username = $request->request->get($this->formFieldNameUsername, '');
        $password = $request->request->get($this->formFieldNamePassword, '');

        if ($username === '' || $password === '') {
            $this->logger->debug('PalomaUserAuthListener: ignoring authentication ' .
                'request does not contain the required data');
            return;
        }

        $token = new PalomaUserToken();
        $token->setUser($username);
        $token->setPassword($password);

        try {
            $authToken = $this->authenticationManager->authenticate($token);
            $this->logger->info("PalomaUserAuthListener: user '$username' successfully authenticated'");
            $this->tockenStorage->setToken($authToken);
        } catch (AuthenticationException $e) {
            $this->logger->debug("PalomaUserAuthListener: authentication of user '$username' failed'");
            $clearToken = $this->tockenStorage->getToken();
            if ($clearToken instanceof PalomaUserToken) {
                $this->logger->debug("PalomaUserAuthListener: clearing current auth token");
                $this->tockenStorage->setToken(null);
            }
        }
    }

}
