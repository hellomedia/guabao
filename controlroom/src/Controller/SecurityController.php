<?php

namespace Controlroom\Controller;

use App\Controller\BaseController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends BaseController
{
    #[Route('/login', name: 'controlroom_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@controlroom/login.html.twig', [
            // parameters usually defined in Symfony login forms
            'error' => $error,
            'last_username' => $lastUsername,

            // login_check path that will be intercepted by symfony
            // to process the form
            'action' => '/login-check',

            // OPTIONAL parameters to customize the login form:

            // the translation_domain to use (define this option only if you are
            // rendering the login template in a regular Symfony controller; when
            // rendering it from an EasyAdmin Dashboard this is automatically set to
            // the same domain as the rest of the Dashboard)
            //'translation_domain' => 'admin',

            // by default EasyAdmin displays a black square as its default favicon;
            // use this method to display a custom favicon: the given path is passed
            // "as is" to the Twig asset() function:
            // <link rel="shortcut icon" href="{{ asset('...') }}">
            'favicon_path' => '/favicon-admin.svg',

            // the title visible above the login form (define this option only if you are
            // rendering the login template in a regular Symfony controller; when rendering
            // it from an EasyAdmin Dashboard this is automatically set as the Dashboard title)
            'page_title' => 'Kot Market Control Room',

            // the string used to generate the CSRF token. If you don't define
            // this parameter, the login form won't include a CSRF token
            'csrf_token_intention' => 'authenticate',

            // the URL users are redirected to after the login (default: '/admin')
            'target_path' => $this->generateUrl('controlroom'),

            // the label displayed for the username form field (the |trans filter is applied to it)
            //'username_label' => 'Your username',

            // the label displayed for the password form field (the |trans filter is applied to it)
            //'password_label' => 'Your password',

            // the label displayed for the Sign In form button (the |trans filter is applied to it)
            //'sign_in_label' => 'Log in',

            // the 'name' HTML attribute of the <input> used for the username field (default: '_username')
            // 'username_parameter' => 'my_custom_username_field',

            // the 'name' HTML attribute of the <input> used for the password field (default: '_password')
            // 'password_parameter' => 'my_custom_password_field',

            // whether to enable or not the "forgot password?" link (default: false)
            // 'forgot_password_enabled' => true,

            // the path (i.e. a relative or absolute URL) to visit when clicking the "forgot password?" link (default: '#')
            // 'forgot_password_path' => $this->generateUrl('...', ['...' => '...']),

            // the label displayed for the "forgot password?" link (the |trans filter is applied to it)
            // 'forgot_password_label' => 'Forgot your password?',

            // whether to enable or not the "remember me" checkbox (default: false)
            'remember_me_enabled' => true,

            // remember me name form field (default: '_remember_me')
            // 'remember_me_parameter' => 'custom_remember_me_param',

            // whether to check by default the "remember me" checkbox (default: false)
            'remember_me_checked' => true,

            // the label displayed for the remember me checkbox (the |trans filter is applied to it)
            'remember_me_label' => 'Remember me',
        ]);
    }

    #[Route(path: '/login-check', name: 'controlroom_login_check')]
    public function loginCheck(): never
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the login check of your firewall.');
    }
}
