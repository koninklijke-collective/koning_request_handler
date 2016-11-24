<?php
namespace Keizer\KoningRequestHandler\Handler;

use Keizer\KoningRequestHandler\Domain\Model\Request;
use Keizer\KoningRequestHandler\Service\RequestService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Trait: Request Handler
 *
 * @package Keizer\KoningRequestHandler\Trait
 */
trait RequestHandler
{

    /**
     * @var RequestService
     */
    protected $requestService;

    /**
     * Returns the request data
     *
     * @param Request $request
     * @return array|object
     */
    public function getRequestHandlerResult(Request $request)
    {
        return $this->getRequestService()->get($request);
    }

    /**
     * @return RequestService
     */
    protected function getRequestService()
    {
        if ($this->requestService === null) {
            $this->requestService = GeneralUtility::makeInstance(ObjectManager::class)->get(RequestService::class, $this->getRequestInterface());
        }
        return $this->requestService;
    }

    /**
     * Override this getter to use your own interface
     *
     * @return string
     */
    protected function getRequestInterface()
    {
        return null;
    }

}
