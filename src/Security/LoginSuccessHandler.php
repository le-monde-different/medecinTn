<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();

        if ($user instanceof \App\Entity\Utilisateur) {
            // Vérifier si l'utilisateur a le rôle ROLE_ADMIN
            if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                // Rediriger vers la page d'administration
                $url = $this->router->generate('app_admin'); 
            } else {
                // Rediriger vers la page de profil utilisateur
                $url = $this->router->generate('user_profile', ['id' => $user->getId()]);
            }

            return new RedirectResponse($url);
        }

        // Si l'utilisateur n'est pas une instance de Utilisateur, rediriger vers une route par défaut
        return new RedirectResponse($this->router->generate('default_route')); // Assurez-vous que la route 'default_route' existe
    }
}
