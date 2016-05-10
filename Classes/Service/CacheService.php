<?php
namespace Keizer\KoningRequestHandler\Service;

class CacheService
{

    const CACHE_INTERFACE = 'koningrequesthandler_requests';

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
     */
    protected $cacheInstance;

    /**
     * @param \Keizer\KoningRequestHandler\Domain\Model\Request $request
     * @return string
     */
    public function get($request)
    {
    }

    /**
     * @param \Keizer\KoningRequestHandler\Domain\Model\Request $request
     * @param string $results
     * @return void
     */
    public function set($request, $results, $tags)
    {
    }

    /**
     * @param \Keizer\KoningRequestHandler\Domain\Model\Request $request
     * @return boolean
     */
    public function has($request)
    {
    }

    /**
     * @param \Keizer\KoningRequestHandler\Domain\Model\Request $request
     * @return boolean
     */
    public function queued($request)
    {
        return $this->getRequestRepository()->existsByIdentifier($request->getIdentifier());
    }

    /**
     * @param \Keizer\KoningRequestHandler\Domain\Model\Request $request
     * @return void
     */
    public function enqueue($request)
    {
        $this->getRequestRepository()->add($request);
    }

    /**
     * @param \Keizer\KoningRequestHandler\Domain\Model\Request $request
     * @return boolean
     */
    public function dequeue($request)
    {
        $this->getRequestRepository()->removeByIdentifier($request->getIdentifier());
    }

    /**
     * @return \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected function getObjectManager()
    {
        if ($this->objectManager === null) {
            $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        }
        return $this->objectManager;
    }

    /**
     * @return \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface
     */
    protected function getCacheInterface()
    {
        if ($this->cacheInstance === null) {
            $this->cacheInstance = $this->getObjectManager()->get(\TYPO3\CMS\Core\Cache\CacheManager::class)->getCache(static::CACHE_INTERFACE);
        }
        return $this->cacheInstance;
    }

    /**
     * @return \Keizer\KoningRequestHandler\Domain\Repository\RequestRepository
     */
    protected function getRequestRepository()
    {
        return $this->getObjectManager()->get(\Keizer\KoningRequestHandler\Domain\Repository\RequestRepository::class);
    }
}