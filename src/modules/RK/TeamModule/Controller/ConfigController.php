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

namespace RK\TeamModule\Controller;

use RK\TeamModule\Controller\Base\AbstractConfigController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * Config controller implementation class.
 *
 * @Route("/config")
 */
class ConfigController extends AbstractConfigController
{
    /**
     * This method takes care of the application configuration.
     *
     * @Route("/config",
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     *
     * @param Request $request Current request instance
     *
     * @return string Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function configAction(Request $request)
    {
        return parent::configAction($request);
    }

    // feel free to add your own config controller methods here
}
