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
 * Permission check for workflow schema 'none'.
 * This function allows to calculate complex permission checks.
 * It receives the object the workflow engine is being asked to process and the permission level the action requires.
 *
 * @param array  $obj         The currently treated object
 * @param int    $permLevel   The required workflow permission level
 * @param int    $currentUser Id of current user
 * @param string $actionId    Id of the workflow action to be executed
 *
 * @return bool Whether the current user is allowed to execute the action or not
 */
function RKTeamModule_workflow_none_permissioncheck($obj, $permLevel, $currentUser, $actionId)
{
    // calculate the permission component
    $objectType = $obj['_objectType'];
    $component = 'RKTeamModule:' . ucfirst($objectType) . ':';

    // calculate the permission instance
    $instance = $obj->createCompositeIdentifier() . '::';

    // now perform the permission check
    $result = SecurityUtil::checkPermission($component, $instance, $permLevel, $currentUser);

    // check whether the current user is the owner
    if (!$result && isset($obj['createdBy']) && $obj['createdBy']->getUid() == $currentUser) {
        // allow author update operations for all states which occur before 'approved' in the object's life cycle.
        $result = in_array($actionId, ['initial', 'deferred', 'accepted']);
    }

    return $result;
}

/**
 * This helper functions cares for including the strings used in the workflow into translation.
 */
function RKTeamModule_workflow_none_gettextstrings()
{
    $translator = \ServiceUtil::get('translator.default');

    return [
        'title' => $translator->__('None workflow (no approval)'),
        'description' => $translator->__('This is like a non-existing workflow. Everything is online immediately after creation.'),

        // state titles
        'states' => [
            $translator->__('Initial') => $translator->__('Pseudo-state for content which is just created and not persisted yet.'),
            $translator->__('Deferred') => $translator->__('Content has not been submitted yet or has been waiting, but was rejected.'),
            $translator->__('Approved') => $translator->__('Content has been approved and is available online.'),
            $translator->__('Deleted') => $translator->__('Pseudo-state for content which has been deleted from the database.')
        ],

        // action titles and descriptions for each state
        'actions' => [
            'initial' => [
                $translator->__('Submit') => $translator->__('Submit content.'),
                $translator->__('Defer') => $translator->__('Defer content for later submission.'),
            ]
            ,
            'deferred' => [
                $translator->__('Submit') => $translator->__('Submit content.'),
                $translator->__('Update') => $translator->__('Update content.'),
                $translator->__('Delete') => $translator->__('Delete content permanently.')
            ]
            ,
            'approved' => [
                $translator->__('Update') => $translator->__('Update content.'),
                $translator->__('Delete') => $translator->__('Delete content permanently.')
            ]
            ,
            'deleted' => [
            ]
        ]
    ];
}
