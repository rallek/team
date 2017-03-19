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

namespace RK\TeamModule\Base;

use Doctrine\DBAL\Connection;
use RuntimeException;
use Zikula\Core\AbstractExtensionInstaller;
use Zikula_Workflow_Util;

/**
 * Installer base class.
 */
abstract class AbstractTeamModuleInstaller extends AbstractExtensionInstaller
{
    /**
     * Install the RKTeamModule application.
     *
     * @return boolean True on success, or false
     *
     * @throws RuntimeException Thrown if database tables can not be created or another error occurs
     */
    public function install()
    {
        $logger = $this->container->get('logger');
        $userName = $this->container->get('zikula_users_module.current_user')->get('uname');
    
        // Check if upload directories exist and if needed create them
        try {
            $container = $this->container;
            $uploadHelper = new \RK\TeamModule\Helper\UploadHelper($container->get('translator.default'), $container->get('session'), $container->get('logger'), $container->get('zikula_users_module.current_user'), $container->get('zikula_extensions_module.api.variable'), $container->getParameter('datadir'));
            $uploadHelper->checkAndCreateAllUploadFolders();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            $logger->error('{app}: User {user} could not create upload folders during installation. Error details: {errorMessage}.', ['app' => 'RKTeamModule', 'user' => $userName, 'errorMessage' => $e->getMessage()]);
        
            return false;
        }
        // create all tables from according entity definitions
        try {
            $this->schemaTool->create($this->listEntityClasses());
        } catch (\Exception $e) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $e->getMessage());
            $logger->error('{app}: Could not create the database tables during installation. Error details: {errorMessage}.', ['app' => 'RKTeamModule', 'errorMessage' => $e->getMessage()]);
    
            return false;
        }
    
        // set up all our vars with initial values
        $this->setVar('personEntriesPerPage', '10');
        $this->setVar('linkOwnPersonsOnAccountPage', true);
        $this->setVar('enableShrinkingForPersonTeamMemberImage', false);
        $this->setVar('shrinkWidthPersonTeamMemberImage', '800');
        $this->setVar('shrinkHeightPersonTeamMemberImage', '600');
        $this->setVar('thumbnailModePersonTeamMemberImage',  'inset' );
        $this->setVar('thumbnailWidthPersonTeamMemberImageView', '32');
        $this->setVar('thumbnailHeightPersonTeamMemberImageView', '24');
        $this->setVar('thumbnailWidthPersonTeamMemberImageDisplay', '240');
        $this->setVar('thumbnailHeightPersonTeamMemberImageDisplay', '180');
        $this->setVar('thumbnailWidthPersonTeamMemberImageEdit', '240');
        $this->setVar('thumbnailHeightPersonTeamMemberImageEdit', '180');
    
        // create the default data
        $this->createDefaultData();
    
        // install subscriber hooks
        $this->hookApi->installSubscriberHooks($this->bundle->getMetaData());
        
    
        // initialisation successful
        return true;
    }
    
    /**
     * Upgrade the RKTeamModule application from an older version.
     *
     * If the upgrade fails at some point, it returns the last upgraded version.
     *
     * @param integer $oldVersion Version to upgrade from
     *
     * @return boolean True on success, false otherwise
     *
     * @throws RuntimeException Thrown if database tables can not be updated
     */
    public function upgrade($oldVersion)
    {
    /*
        $logger = $this->container->get('logger');
    
        // Upgrade dependent on old version number
        switch ($oldVersion) {
            case '1.0.0':
                // do something
                // ...
                // update the database schema
                try {
                    $this->schemaTool->update($this->listEntityClasses());
                } catch (\Exception $e) {
                    $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $e->getMessage());
                    $logger->error('{app}: Could not update the database tables during the upgrade. Error details: {errorMessage}.', ['app' => 'RKTeamModule', 'errorMessage' => $e->getMessage()]);
    
                    return false;
                }
        }
    
        // Note there are several helpers available for making migrating your extension from Zikula 1.3 to 1.4 easier.
        // The following convenience methods are each responsible for a single aspect of upgrading to Zikula 1.4.x.
    
        // here is a possible usage example
        // of course 1.2.3 should match the number you used for the last stable 1.3.x module version.
        /* if ($oldVersion = '1.2.3') {
            // rename module for all modvars
            $this->updateModVarsTo14();
            
            // update extension information about this app
            $this->updateExtensionInfoFor14();
            
            // rename existing permission rules
            $this->renamePermissionsFor14();
            
            // rename all tables
            $this->renameTablesFor14();
            
            // remove event handler definitions from database
            $this->dropEventHandlersFromDatabase();
            
            // update module name in the hook tables
            $this->updateHookNamesFor14();
            
            // update module name in the workflows table
            $this->updateWorkflowsFor14();
        } * /
    */
    
        // update successful
        return true;
    }
    
    /**
     * Renames the module name for variables in the module_vars table.
     */
    protected function updateModVarsTo14()
    {
        $dbName = $this->getDbName();
        $conn = $this->getConnection();
    
        $conn->executeQuery("
            UPDATE $dbName.module_vars
            SET modname = 'RKTeamModule'
            WHERE modname = 'Team';
        ");
    }
    
    /**
     * Renames this application in the core's extensions table.
     */
    protected function updateExtensionInfoFor14()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();
    
        $conn->executeQuery("
            UPDATE $dbName.modules
            SET name = 'RKTeamModule',
                directory = 'RK/TeamModule'
            WHERE name = 'Team';
        ");
    }
    
    /**
     * Renames all permission rules stored for this app.
     */
    protected function renamePermissionsFor14()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();
    
        $componentLength = strlen('Team') + 1;
    
        $conn->executeQuery("
            UPDATE $dbName.group_perms
            SET component = CONCAT('RKTeamModule', SUBSTRING(component, $componentLength))
            WHERE component LIKE 'Team%';
        ");
    }
    
    /**
     * Renames all (existing) tables of this app.
     */
    protected function renameTablesFor14()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();
    
        $oldPrefix = 'team_';
        $oldPrefixLength = strlen($oldPrefix);
        $newPrefix = 'rk_team_';
    
        $sm = $conn->getSchemaManager();
        $tables = $sm->listTables();
        foreach ($tables as $table) {
            $tableName = $table->getName();
            if (substr($tableName, 0, $oldPrefixLength) != $oldPrefix) {
                continue;
            }
    
            $newTableName = str_replace($oldPrefix, $newPrefix, $tableName);
    
            $conn->executeQuery("
                RENAME TABLE $dbName.$tableName
                TO $dbName.$newTableName;
            ");
        }
    }
    
    /**
     * Removes event handlers from database as they are now described by service definitions and managed by dependency injection.
     */
    protected function dropEventHandlersFromDatabase()
    {
        \EventUtil::unregisterPersistentModuleHandlers('Team');
    }
    
    /**
     * Updates the module name in the hook tables.
     */
    protected function updateHookNamesFor14()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();
    
        $conn->executeQuery("
            UPDATE $dbName.hook_area
            SET owner = 'RKTeamModule'
            WHERE owner = 'Team';
        ");
    
        $componentLength = strlen('subscriber.team') + 1;
        $conn->executeQuery("
            UPDATE $dbName.hook_area
            SET areaname = CONCAT('subscriber.rkteammodule', SUBSTRING(areaname, $componentLength))
            WHERE areaname LIKE 'subscriber.team%';
        ");
    
        $conn->executeQuery("
            UPDATE $dbName.hook_binding
            SET sowner = 'RKTeamModule'
            WHERE sowner = 'Team';
        ");
    
        $conn->executeQuery("
            UPDATE $dbName.hook_runtime
            SET sowner = 'RKTeamModule'
            WHERE sowner = 'Team';
        ");
    
        $componentLength = strlen('team') + 1;
        $conn->executeQuery("
            UPDATE $dbName.hook_runtime
            SET eventname = CONCAT('rkteammodule', SUBSTRING(eventname, $componentLength))
            WHERE eventname LIKE 'team%';
        ");
    
        $conn->executeQuery("
            UPDATE $dbName.hook_subscriber
            SET owner = 'RKTeamModule'
            WHERE owner = 'Team';
        ");
    
        $componentLength = strlen('team') + 1;
        $conn->executeQuery("
            UPDATE $dbName.hook_subscriber
            SET eventname = CONCAT('rkteammodule', SUBSTRING(eventname, $componentLength))
            WHERE eventname LIKE 'team%';
        ");
    }
    
    /**
     * Updates the module name in the workflows table.
     */
    protected function updateWorkflowsFor14()
    {
        $conn = $this->getConnection();
        $dbName = $this->getDbName();
    
        $conn->executeQuery("
            UPDATE $dbName.workflows
            SET module = 'RKTeamModule'
            WHERE module = 'Team';
        ");
    }
    
    /**
     * Returns connection to the database.
     *
     * @return Connection the current connection
     */
    protected function getConnection()
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $connection = $entityManager->getConnection();
    
        return $connection;
    }
    
    /**
     * Returns the name of the default system database.
     *
     * @return string the database name
     */
    protected function getDbName()
    {
        return $this->container->getParameter('database_name');
    }
    
    /**
     * Uninstall RKTeamModule.
     *
     * @return boolean True on success, false otherwise
     *
     * @throws RuntimeException Thrown if database tables or stored workflows can not be removed
     */
    public function uninstall()
    {
        $logger = $this->container->get('logger');
    
        // delete stored object workflows
        $result = Zikula_Workflow_Util::deleteWorkflowsForModule('RKTeamModule');
        if (false === $result) {
            $this->addFlash('error', $this->__f('An error was encountered while removing stored object workflows for the %extension% extension.', ['%extension%' => 'RKTeamModule']));
            $logger->error('{app}: Could not remove stored object workflows during uninstallation.', ['app' => 'RKTeamModule']);
    
            return false;
        }
    
        try {
            $this->schemaTool->drop($this->listEntityClasses());
        } catch (\Exception $e) {
            $this->addFlash('error', $this->__('Doctrine Exception') . ': ' . $e->getMessage());
            $logger->error('{app}: Could not remove the database tables during uninstallation. Error details: {errorMessage}.', ['app' => 'RKTeamModule', 'errorMessage' => $e->getMessage()]);
    
            return false;
        }
    
        // uninstall subscriber hooks
        $this->hookApi->uninstallSubscriberHooks($this->bundle->getMetaData());
        
    
        // remove all module vars
        $this->delVars();
    
        // remove all thumbnails
        $manager = $this->container->get('systemplugin.imagine.manager');
        $manager->setModule('RKTeamModule');
        $manager->cleanupModuleThumbs();
    
        // remind user about upload folders not being deleted
        $uploadPath = $this->container->getParameter('datadir') . '/RKTeamModule/';
        $this->addFlash('status', $this->__f('The upload directories at "%path%" can be removed manually.', ['%path%' => $uploadPath]));
    
        // uninstallation successful
        return true;
    }
    
    /**
     * Build array with all entity classes for RKTeamModule.
     *
     * @return array list of class names
     */
    protected function listEntityClasses()
    {
        $classNames = [];
        $classNames[] = 'RK\TeamModule\Entity\PersonEntity';
    
        return $classNames;
    }
    
    /**
     * Create the default data for RKTeamModule.
     *
     * @return void
     */
    protected function createDefaultData()
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $logger = $this->container->get('logger');
        $request = $this->container->get('request_stack')->getCurrentRequest();
        
        $entityClass = 'RK\TeamModule\Entity\PersonEntity';
        $entityManager->getRepository($entityClass)->truncateTable($logger);
    }
}
