<?php declare(strict_types=1);

use GeneralForm\IEvent;
use GeneralForm\IEventContainer;
use Nette\SmartObject;
use Nette\Utils\Callback;


/**
 * Class CheckValueEvent
 *
 * @deprecated
 * @author  geniv
 */
class CheckValueEvent implements IEvent
{
    use SmartObject;

    /** @var callable */
    private $callback;


    /**
     * Set callback.
     *
     * @param callable $callback
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }


    /**
     * Update.
     *
     * @param IEventContainer $eventContainer
     * @param array           $values
     */
    public function update(IEventContainer $eventContainer, array $values)
    {
        // function (IEventContainer $eventContainer, array $value) { return $value; }
        $eventContainer->setValues(Callback::invokeSafe($this->callback, [$eventContainer, $values], null));
    }
}
