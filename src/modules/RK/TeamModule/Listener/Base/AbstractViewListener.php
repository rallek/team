<?php
/**
 * Team.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://k62.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (http://modulestudio.de).
 */

namespace RK\TeamModule\Listener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zikula\Core\Event\GenericEvent;

/**
 * Event handler base class for view-related events.
 */
abstract class AbstractViewListener implements EventSubscriberInterface
{
    /**
     * Makes our handlers known to the event system.
     */
    public static function getSubscribedEvents()
    {
        return [
            'view.init'      => ['init', 5],
            'view.postfetch' => ['postFetch', 5]
        ];
    }
    
    /**
     * Listener for the `view.init` event.
     *
     * Occurs just before `Zikula_View#__construct()` finishes.
     * The subject is the Zikula_View instance.
     *
     * Note that Zikula_View is deprecated and being replaced by Twig.
     *
     * @param GenericEvent $event The event instance
     */
    public function init(GenericEvent $event)
    {
    }
    
    /**
     * Listener for the `view.postfetch` event.
     *
     * Filter of result of a fetch.
     * Receives `Zikula_View` instance as subject,
     * args are `['template' => $template]`,
     * $data was the result of the fetch to be filtered.
     *
     * Note that Zikula_View is deprecated and being replaced by Twig.
     *
     * @param GenericEvent $event The event instance
     */
    public function postFetch(GenericEvent $event)
    {
    }
}
