<?php
namespace Keizer\KoningRequestHandler\Handler;

use Keizer\KoningRequestHandler\Domain\Model\Request;
use Keizer\KoningRequestHandler\Service\RequestService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Trait: RequestData trait
 *
 * @package Keizer\KoningRequestHandler\Handler
 */
trait RequestResult
{

    /**
     * Returns the request data
     *
     * @param Request $request
     * @return array|object
     */
    public function getRequestResult(Request $request)
    {
        $requestService = GeneralUtility::makeInstance(ObjectManager::class)
            ->get(RequestService::class);
        return $requestService->get($request);
    }

}