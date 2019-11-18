<?php

namespace PhilKra\Events;

use PhilKra\Helper\Timer;
use PhilKra\Helper\Encoding;

/**
 *
 * Spans
 *
 * @link https://www.elastic.co/guide/en/apm/server/master/span-api.html
 *
 */
class Span extends TraceableEvent implements \JsonSerializable
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var \PhilKra\Helper\Timer
     */
    private $timer;

    /**
     * @var int
     */
    private $duration = 0;

    /**
     * @var string
     */
    private $action = null;

    /**
     * @var string
     */
    private $type = 'request';

    /**
     * @var mixed array|null
     */
    private $context = null;

    /**
     * @var mixed array|null
     */
    private $stacktrace = [];

    /**
     * @param string $name
     * @param EventBean $parent
     */
    public function __construct(string $name, EventBean $parent)
    {
        parent::__construct([]);
        $this->name  = trim($name);
        $this->timer = new Timer();
        $this->setParent($parent);
    }

    /**
     * Start the Timer
     *
     * @return void
     */
    public function start()
    {
        $this->timer->start();
    }

    /**
     * Stop the Timer
     *
     * @param integer|null $duration
     *
     * @return void
     */
    public function stop(int $duration = null)
    {
        $this->timer->stop();
        $this->duration = $duration ?? round($this->timer->getDurationInMilliseconds(), 3);
    }

    /**
     * Get the Event Name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get the Spans' Duration
     *
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * Set the Spans' Duration
     *
     * @param int $duration
     */
    public function setDuration(int $duration)
    {
        return $this->duration = $duration;
    }

    /**
     * Get the Spans' Action
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Set the Span's Action
     *
     * @param string $action
     */
    public function setAction(string $action)
    {
        $this->action = trim($action);
    }

    /**
     * Get the Spans' Type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set the Spans' Type
     *
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = trim($type);
    }

    /**
     * Provide additional Context to the Span
     *
     * @link https://www.elastic.co/guide/en/apm/server/master/span-api.html
     *
     * @param array $context
     */
    public function setContext(array $context)
    {
        $this->context = $context;
    }

    /**
     * Get the Spans' Stack Trace
     *
     * @link https://www.elastic.co/guide/en/apm/server/master/span-api.html
     *
     * @return array
     */
    public function getStackTrace(): array
    {
        return $this->stacktrace;
    }

    /**
     * Set a complimentary Stacktrace for the Span
     *
     * @link https://www.elastic.co/guide/en/apm/server/master/span-api.html
     *
     * @param array $stacktrace
     */
    public function setStacktrace(array $stacktrace)
    {
        $this->stacktrace = $stacktrace;
    }

    /**
     * Serialize Span Event
     *
     * @link https://www.elastic.co/guide/en/apm/server/master/span-api.html
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return [
            'span' => [
                'id'             => $this->getId(),
                'transaction_id' => $this->getParentId(),
                'trace_id'       => $this->getTraceId(),
                'parent_id'      => $this->getParentId(),
                'type'           => Encoding::keywordField($this->getType()),
                'action'         => Encoding::keywordField($this->getAction()),
                'context'        => $this->getContext(),
                'duration'       => $this->getDuration(),
                'name'           => Encoding::keywordField($this->getName()),
                'stacktrace'     => $this->getStacktrace(),
                'sync'           => false,
                'timestamp'      => $this->getTimestamp(),
            ]
        ];
    }
}
