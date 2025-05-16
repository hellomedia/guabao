<?php

namespace App\Controller;

use App\Helper\BreadcrumbsHelper;
use App\Translation\TranslatableHtmlMessage;
use App\Translation\TranslatableMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    /**
     * $this->container only contains a subset of services defined in
     * AbstractController::getSubscribedServices().
     * Additional services must be injected here.
     * NB: We try to keep those to a minimum
     * 
     * This parent constructor is called when the child is instantiated 
     * EXCEPT IF the child has its own constructor.
     * If that is the case and we need this constructor to be called,
     * it must be called explicitely with parent::__construct()
     * https://www.php.net/manual/en/language.oop5.decon.php
     * NB: The parent of this class has no constructor
     * 
     * ==> If controller needs to access the BaseController properties
     * ie: $this->breadcrumb and $this->siteProvider,
     * call parent::__constuct() from the child constructor -- when defined.
     * Otherwise, an error is thrown when trying to access a property 
     * that should have been initialized in the constructor.
     */
    public function __construct(
        protected BreadcrumbsHelper $breadcrumbsHelper,
    ) {}

    protected function addBreadcrumb(mixed $item, $route = null, $routeParams = [], ?bool $isLarge = null, ?bool $isAdmin = null)
    {
        return $this->breadcrumbsHelper->addBreadcrumb($item, $route, $routeParams, $isLarge, $isAdmin);
    }

    /**
     * Override parent addFlash() method
     * Allowed under these conditions :
     * - parent parameters must be present and have same signature
     * (thus we have to keep $message typehinted as mixed)
     * - additional parameters must be placed at the end and must have a default value
     * (ie $parameters, $html and $domain)
     */
    protected function addFlash(string $type, mixed $message, array $parameters = [], bool $html = false, string $domain = 'feedback'): void
    {
        if ($html) {
            // if flash message contains html, let's pass a TranslatableHtmlMessage
            // object, which might or might not be needed (depending on if there are
            // parameters to pass to the translation), but which will act as a
            // marquor class to chose the escaping strategy in the template
            $message = new TranslatableHtmlMessage($message, $parameters, $domain);
        } else {
            // pass a TranslatableMessage object,
            // which allows to pass the translation parameters and domain.
            // and use a generic  {{ flashMessage|trans }} to display all flash messages
            $message = new TranslatableMessage($message, $parameters, $domain);
        }

        parent::addFlash($type, $message);
    }
}
