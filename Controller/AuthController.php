<?php

declare(strict_types=1);

/*
 * @copyright   2018 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\IntegrationsBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use MauticPlugin\IntegrationsBundle\Exception\IntegrationNotFoundException;
use MauticPlugin\IntegrationsBundle\Exception\UnauthorizedException;
use MauticPlugin\IntegrationsBundle\Helper\AuthIntegrationsHelper;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends CommonController
{
    public function callbackAction(string $integration, Request $request)
    {
        /** @var AuthIntegrationsHelper $authIntegrationsHelper */
        $authIntegrationsHelper = $this->get('mautic.integrations.helper.auth_integrations');
        $template               = 'IntegrationsBundle:Auth:authenticated.html.php';
        $authenticationError    = false;

        try {
            $authIntegration = $authIntegrationsHelper->getIntegration($integration);
            $message         = $authIntegration->authenticateIntegration($request);
        } catch (UnauthorizedException $exception) {
            $message             = $exception->getMessage();
            $authenticationError = true;
        } catch (IntegrationNotFoundException $exception) {
            return $this->notFound();
        }

        return $this->render(
            $template,
            [
                'message'             => $message,
                'authenticationError' => $authenticationError,
            ]
        );
    }
}
