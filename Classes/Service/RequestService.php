<?php
namespace Keizer\KoningRequestHandler\Service;

use Keizer\KoningRequestHandler\Domain\Model\Request;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service: Requests
 *
 * @package Keizer\KoningRequestHandler\Service
 */
class RequestService
{

    /**
     * @var CacheService
     */
    protected $cacheService;

    /**
     * RequestService constructor.
     *
     * @param string $cacheInterface
     */
    public function __construct($cacheInterface = null)
    {
        $this->setCacheService($cacheInterface);
    }

    /**
     * Get data for request
     *
     * @param Request $request
     * @return array|object
     */
    public function get(Request $request)
    {
        if ($request->useCache()) {
            return $this->retrieve($request);
        } else {
            return $this->download($request);
        }
    }

    /**
     * Try to retrieve data from cache
     *
     * @param $request
     * @return string
     */
    public function retrieve($request)
    {
        // first check last cached output!
        $results = $this->getCacheService()->get($request);
        if ($results !== false) {
            return $results;
        }

        // if not already queued, try a quick download!
        if ($this->getCacheService()->queued($request) === false) {
            // Try to download with configured max download time
            $results = $this->download($request);
            if ($results !== false) {
                return $results;
            }

            $this->getCacheService()->enqueue($request);
        }

        return null;
    }

    /**
     * Download data
     *
     * @param Request $request
     * @return string
     */
    public function download($request)
    {
        $report = [];
        $url = $request->getUrl();
        $results = GeneralUtility::getUrl($url, 0, false, $report);

        // log error when retrieval gives error
        if ($report['http_code'] !== 200) {
            $log = [
                'url' => $url,
                'output' => $results,
            ];

            GeneralUtility::sysLog(json_encode($log), 'koning_request_handler', GeneralUtility::SYSLOG_SEVERITY_WARNING);
        }

        if ($report['http_code'] > 0 && $report['http_code'] !== 500) {
            if ($results !== null) {
                $tags = [
                    'koning_request_handler',
                    'pageId_' . (int)$GLOBALS['TSFE']->id,
                ];

                // Generate tags based on url
                $info = parse_url($url);
                $tags[] = 'host_' . $info['host'];
                $tags[] = 'action_' . str_replace('/', '_', trim($info['path'], '/'));
                $params = GeneralUtility::explodeUrl2Array($info['query'], true);

                foreach ($params as $key => $value) {
                    if (!is_array($value)) {
                        $tags[] = 'param_' . $key . '_' . $value;
                    }
                }

                // make sure the tags are allowed in zend cache!
                foreach ($tags as $index => $tag) {
                    $tags[$index] = preg_replace('/[^a-zA-Z0-9_]/', '', $tag);
                }

                $this->getCacheService()->set($request, $results, $tags);
            }
            $this->getCacheService()->dequeue($request);
        } else {
            $results = false;
            $this->getCacheService()->enqueue($request);
        }

        return $results;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
    }

    /**
     * @param string $interface
     * @return void
     */
    protected function setCacheService($interface = null)
    {
        $this->cacheService = $this->getObjectManager()->get(CacheService::class, $interface);
    }

    /**
     * @return CacheService
     */
    protected function getCacheService()
    {
        return $this->cacheService;
    }

}
