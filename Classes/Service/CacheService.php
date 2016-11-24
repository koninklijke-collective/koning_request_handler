<?php
namespace Keizer\KoningRequestHandler\Service;

use Keizer\KoningRequestHandler\Domain\Model\Request;
use Keizer\KoningRequestHandler\Domain\Repository\RequestRepository;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Service: Caches
 *
 * @package Keizer\KoningRequestHandler\Service
 */
class CacheService
{

    /**
     * Defined caching interface used in your trait
     */
    const DEFAULT_CACHE_INTERFACE = 'koningrequesthandler_requests';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var FrontendInterface
     */
    protected $cacheInstance;

    /**
     * RequestService constructor.
     *
     * @param string $cacheInterface
     */
    public function __construct($cacheInterface = null)
    {
        $this->setCacheInstance($cacheInterface);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function get($request)
    {
        return $this->getCacheInstance()->get($request->getIdentifier());
    }

    /**
     * @param Request $request
     * @param mixed $data The data to cache - also depends on the concrete cache implementation
     * @param array $tags Tags to associate with this cache entry
     * @param integer $lifetime Lifetime of this cache entry in seconds. If NULL is specified, the default lifetime is used. "0" means unlimited lifetime.
     * @return void
     */
    public function set($request, $data, $tags = [], $lifetime = null)
    {
        $this->getCacheInstance()->set(
            $request->getIdentifier(),
            $data,
            $tags,
            $lifetime
        );
    }

    /**
     * @param Request $request
     * @return boolean
     */
    public function has($request)
    {
        return $this->getCacheInstance()->has($request->getIdentifier());
    }

    /**
     * @param Request $request
     * @return boolean
     */
    public function queued($request)
    {
        return $this->getRequestRepository()->existsByIdentifier($request->getIdentifier());
    }

    /**
     * @param Request $request
     * @return void
     */
    public function enqueue($request)
    {
        $this->getRequestRepository()->add($request);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function dequeue($request)
    {
        $this->getRequestRepository()->removeByIdentifier($request->getIdentifier());
    }

    /**
     * @return ObjectManagerInterface
     */
    protected function getObjectManager()
    {
        if ($this->objectManager === null) {
            $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        }
        return $this->objectManager;
    }

    /**
     * @param string $interface
     */
    protected function setCacheInstance($interface = null)
    {
        $this->cacheInstance = $this->getObjectManager()->get(CacheManager::class)->getCache($interface ?: static::DEFAULT_CACHE_INTERFACE);
    }

    /**
     * @return FrontendInterface
     */
    protected function getCacheInstance()
    {
        return $this->cacheInstance;
    }

    /**
     * @return RequestRepository
     */
    protected function getRequestRepository()
    {
        return $this->getObjectManager()->get(RequestRepository::class);
    }
}
