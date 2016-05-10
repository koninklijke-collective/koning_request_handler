<?php
namespace Keizer\KoningRequestHandler\Domain\Model;

/**
 * Cache: Request
 *
 * @package Keizer\KoningRequestHandler\Domain\Model
 */
class Request extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    const TABLE = 'tx_koningrequesthandler_domain_model_request';

    /**
     * @var boolean
     */
    protected $persistent;

    /**
     * @var \DateTime
     */
    protected $crdate;

    /**
     * @var \DateTime
     */
    protected $tstamp;

    /**
     * @var integer
     */
    protected $feUser;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $running;

    /**
     * @var string
     */
    protected $target;

    /**
     * @var string
     */
    protected $parameters;

    /**
     * @var string
     */
    protected $caller;

    /**
     * @var boolean
     */
    protected $noCache = false;

    /**
     * Create Request based on array parameters
     *
     * @param array $parameters
     * @return Request
     */
    public static function create(array $parameters)
    {
        $request = new static();
        foreach ($parameters as $key => $value) {
            $methodName = 'set' . ucfirst($key);
            if (method_exists($request, $methodName)) {
                $request->{$methodName}($value);
            } elseif ($request->_hasProperty($key)) {
                $request->{$key} = $value;
            }
        }
        return $request->generateIdentifier();
    }

    /**
     * Generate unique id used for cache mappings
     *
     * @return string
     */
    public function generateIdentifier()
    {
        $identifier = sha1($this->getTarget() . '+' . http_build_query($this->getParameters()));
        $this->setIdentifier($identifier);
        return $this;
    }

    /**
     * @return boolean
     */
    public function useCache()
    {
        return ($this->getNoCache() === false);
    }

    /**
     * Generate GET request for curl
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->getTarget() . '?' . http_build_query($this->getParameters());
    }

    /**
     * Returns the NoCache
     *
     * @return boolean
     */
    public function getNoCache()
    {
        return $this->noCache;
    }

    /**
     * Sets the NoCache
     *
     * @param boolean $noCache
     * @return Request
     */
    public function setNoCache($noCache)
    {
        $this->noCache = $noCache;
        return $this;
    }

    /**
     * Returns the persistent
     *
     * @return boolean
     */
    public function getPersistent()
    {
        return $this->persistent;
    }

    /**
     * Sets the persistent
     *
     * @param boolean $persistent
     * @return Request
     */
    public function setPersistent($persistent)
    {
        $this->persistent = $persistent;
        return $this;
    }

    /**
     * Returns the Crdate
     *
     * @return \DateTime
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * Sets the Crdate
     *
     * @param \DateTime $date
     * @return Request
     */
    public function setCrdate(\DateTime $date)
    {
        $this->crdate = $date;
        return $this;
    }

    /**
     * Returns the Tstamp
     *
     * @return \DateTime
     */
    public function getTstamp()
    {
        return $this->tstamp;
    }

    /**
     * Sets the Tstamp
     *
     * @param \DateTime $date
     * @return Request
     */
    public function setTstamp(\DateTime $date)
    {
        $this->tstamp = $date;
        return $this;
    }

    /**
     * Returns the FeUser
     *
     * @return int
     */
    public function getFeUser()
    {
        return $this->feUser;
    }

    /**
     * Sets the FeUser
     *
     * @param int $feUser
     * @return Request
     */
    public function setFeUser($feUser)
    {
        $this->feUser = $feUser;
        return $this;
    }

    /**
     * Returns the Identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Sets the Identifier
     *
     * @param string $identifier
     * @return Request
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * Returns the Running
     *
     * @return string
     */
    public function getRunning()
    {
        return $this->running;
    }

    /**
     * Sets the Running
     *
     * @param string $running
     * @return Request
     */
    public function setRunning($running)
    {
        $this->running = $running;
        return $this;
    }

    /**
     * Returns the Target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Sets the Target
     *
     * @param string $target
     * @return Request
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Returns the Parameters
     *
     * @return string
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets the Parameters
     *
     * @param string $parameters
     * @return Request
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }
}
