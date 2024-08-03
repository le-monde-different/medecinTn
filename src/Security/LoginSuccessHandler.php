<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();
        if ($user instanceof \App\Entity\Utilisateur) {
            $profileUrl = $this->router->generate('user_profile', ['id' => $user->getId()]);
            return new RedirectResponse($profileUrl);
        }

        // Si l'utilisateur n'est pas une instance de Utilisateur, redirigez vers une route par défaut
        return new RedirectResponse($this->router->generate('default_route'));
    }
}
