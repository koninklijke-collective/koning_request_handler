<?php
namespace Keizer\KoningRequestHandler\Scheduler;

/**
 * Koning Request Handler: Handle queued requests
 *
 * @package Keizer\KoningRequestHandler\Scheduler
 */
class QueueTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask
{

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Keizer\KoningRequestHandler\Domain\Repository\RequestRepository
     */
    protected $requestRepository;

    /**
     * Function executed from the Scheduler: Update API calls in database
     *
     * @throws \TYPO3\CMS\Core\Exception
     * @return boolean
     */
    public function execute()
    {
        $success = false;
        $this->logMessage('Job initiated');

        if ($this->isExecutable()) {
            $runnerUniqueId = uniqid();

            // set running parameter to queue for all empty queries
            $this->getRequestRepository()->lockAll($runnerUniqueId);
            foreach ($this->getRequestRepository()->findAllLocked($runnerUniqueId) as $request) {
                /** @var \Keizer\KoningRequestHandler\Domain\Model\Request $request */
                if ($this->getRequestService()->download($request)) {
                    if ($request->getPersistent()) {
                        $this->getRequestRepository()->update($request);
                    } else {
                        $this->getRequestRepository()->remove($request);
                    }
                }
            }
            $this->getRequestRepository()->unlockAll($runnerUniqueId);
            $success = true;
        }

        return $success;
    }

    /**
     * Log error message with devLog function
     *
     * @param string $message
     * @param boolean $includeVariables
     * @return void
     */
    protected function logMessage($message, $includeVariables = false)
    {
        if (TYPO3_DLOG) {
            $variables = false;
            if ($includeVariables) {
                // Get execution information
                $exec = $this->getExecution();

                // Get call method
                if (basename(PATH_thisScript) == 'cli_dispatch.phpsh') {
                    $calledBy = 'CLI module dispatcher';
                    $site = '-';
                } else {
                    $calledBy = 'TYPO3 backend';
                    $site = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
                }

                $start = $exec->getStart();
                $end = $exec->getEnd();
                $interval = $exec->getInterval();
                $multiple = $exec->getMultiple();
                $cronCmd = $exec->getCronCmd();

                $variables = [
                    'uid' => $this->taskUid,
                    'sitename' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'],
                    'site' => $site,
                    'calledBy' => $calledBy,
                    'tstamp' => date('Y-m-d H:i:s') . ' [' . time() . ']',
                    'maxLifetime' => $this->scheduler->extConf['maxLifetime'],
                    'start' => date('Y-m-d H:i:s', $start) . ' [' . $start . ']',
                    'end' => ((empty($end)) ? '-' : (date('Y-m-d H:i:s', $end) . ' [' . $end . ']')),
                    'interval' => $interval,
                    'multiple' => ($multiple ? 'yes' : 'no'),
                    'cronCmd' => ($cronCmd ? $cronCmd : 'not used'),
                ];
            }

            \TYPO3\CMS\Core\Utility\GeneralUtility::devLog(
                '[Koning Request Handler: Cache]: ' . $message,
                'scheduler',
                0,
                $variables
            );
        }
    }

    /**
     * Check if scheduler is ready for execute
     *
     * @return boolean
     */
    protected function isExecutable()
    {
        if ($this->getObjectManager() !== null) {
            if (class_exists('\Keizer\KoningRequestHandler\Library\ApiDataHandler')) { // @TODO
                if ($this->getRequestRepository() !== null) {
                    return true;
                } else {
                    $this->logMessage('No repository layer found');
                }
            } else {
                $this->logMessage('Extensions Data Handler unknown', true);
            }
        } else {
            $this->logMessage('Object manager can not be initiated', true);
        }

        return false;
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
     * @return \Keizer\KoningRequestHandler\Domain\Repository\RequestRepository
     */
    protected function getRequestRepository()
    {
        return $this->getObjectManager()->get(\Keizer\KoningRequestHandler\Domain\Repository\RequestRepository::class);
    }

    /**
     * @return \Keizer\KoningRequestHandler\Service\RequestService
     */
    protected function getRequestService()
    {
        return $this->getObjectManager()->get(\Keizer\KoningRequestHandler\Service\RequestService::class);
    }
}
