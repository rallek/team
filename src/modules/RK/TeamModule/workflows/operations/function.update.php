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

/**
 * Update operation.
 *
 * @param object $entity The treated object
 * @param array  $params Additional arguments
 *
 * @return bool False on failure or true if everything worked well
 *
 * @throws RuntimeException Thrown if executing the workflow action fails
 */
function RKTeamModule_operation_update(&$entity, $params)
{

    // get attributes read from the workflow
    if (isset($params['nextstate']) && !empty($params['nextstate'])) {
        // assign value to the data object
        $entity['workflowState'] = $params['nextstate'];
        if ($params['nextstate'] == 'archived') {
            // bypass validator (for example an end date could have lost it's "value in future")
            $entity['_bypassValidation'] = true;
        }
    }
    
    // get entity manager
    $container = \ServiceUtil::get('service_container');
    $entityManager = $container->get('doctrine.orm.default_entity_manager');
    $logger = $container->get('logger');
    $logArgs = ['app' => 'RKTeamModule', 'user' => $container->get('zikula_users_module.current_user')->get('uname')];
    
    // save entity data
    try {
        //$this->entityManager->transactional(function($entityManager) {
        $entityManager->persist($entity);
        $entityManager->flush();
        //});
        $result = true;
        $logger->notice('{app}: User {user} updated an entity.', $logArgs);
    } catch (\Exception $e) {
        $logger->error('{app}: User {user} tried to update an entity, but failed.', $logArgs);
        throw new \RuntimeException($e->getMessage());
    }

    // return result of this operation
    return $result;
}
