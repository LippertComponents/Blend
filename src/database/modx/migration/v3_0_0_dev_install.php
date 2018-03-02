<?php

use LCI\Blend\Migrations\Modx\Setup as MODXSetup;

class v3_0_0_dev_install extends MODXSetup
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->loadUserInstallConfig();

        $this->modx->getManager();
        $connected = $this->modx->connect();

        if (!$connected) {
            // connect with xPDO:
            $dsnArray= \xPDO\xPDO :: parseDSN($this->modx->getOption('dsn'));

            // Create the MODX database:
            $created= $this->modx->manager->createSourceContainer($dsnArray, $this->modx->config['username'], $this->modx->config['password']);
            if (!$created) {
                $this->addResultMessage([xPDO::LOG_LEVEL_ERROR => $this->modx->lexicon('db_err_create')]);
            } else {
                $connected = $this->modx->connect();
            }
            if ($connected) {
                $this->addResultMessage([xPDO::LOG_LEVEL_DEBUG + 1 => $this->modx->lexicon('db_created')]);
            }
        }

        if ($connected) {
            $this->modx->loadClass('modAccess');
            $this->modx->loadClass('modAccessibleObject');
            $this->modx->loadClass('modAccessibleSimpleObject');
            $this->modx->loadClass('modResource');
            $this->modx->loadClass('modElement');
            $this->modx->loadClass('modScript');
            $this->modx->loadClass('modPrincipal');
            $this->modx->loadClass('modUser');

            // create MODX tables
            foreach ($this->modx_tables as $class) {
                if (!$dbcreated= $this->modx->manager->createObjectContainer($class)) {
                    $this->addResultMessage([xPDO::LOG_LEVEL_ERROR => $this->modx->lexicon('table_err_create', ['class' => $class])]);
                } else {
                    $this->addResultMessage([xPDO::LOG_LEVEL_DEBUG + 1 => $this->modx->lexicon('table_created', ['class' => $class])]);
                }
            }
        }

        $this->loadCurrentVersionInfo();

        $this->upNamespaceWorkspace();
        // Moved _build/data to database/modx/seeds/transport
        // @TODO refactor to make then OOP classes
        // that can be base for 3.0 and then extended for each release to add/remove/update the contents as needed
        $legacy_transport = [
            // the order in which MODX _build has them:
            'transport.core.menus.php',
            'transport.core.content_types.php',
            'transport.core.classmap.php',
            'transport.core.events.php',
            'transport.core.system_settings.php',
            'transport.core.context_settings.php',
            'transport.core.usergroups.php',
            'transport.core.dashboards.php',
            'transport.core.media_sources.php',
            'transport.core.dashboard_widgets.php',
            // resolver: resolvers/resolve.dashboardwidgets.php
            'transport.core.usergrouproles.php',
            'transport.core.usergrouproles.php',
            'transport.core.accesspolicytemplategroups.php',
            'transport.core.accesspolicytemplates.php',
            // resolvers/resolve.policytemplates.php'
            'transport.core.accesspolicies.php',
            // resolve policies resolvers/resolve.policies.php
            // resolvers/resolve.core.php
            // resolvers/resolve.actionfields.php
        ];

        $path = $this->blender->getSeedsDirectory('transport');
        $xpdo = $this->modx;
        foreach ($legacy_transport as $file) {
            echo $path.$file.PHP_EOL;

            if (file_exists($path.$file)) {
                $collection = [];
                $objects = include $path.$file;

                if (!is_array($objects) && count($collection) > 0) {
                    $objects = $collection;
                }

                /** @var \xPDO\xPDOObject $object */
                foreach ($objects as $count => $object) {
                    // If failed it is logged to the main log file
                    $object->save();
                }
            }
        }

        // Replace resolvers:
        $this->upAttachPolicyTemplates();
        $this->upAttachPolicies();
        $this->upAttachDashboardWidgets();
        // Core ??? - why is this needed?
        $this->upAttachActionFields();

        $this->upSystemSettings();
        $this->upAdminUser();
        $this->upAcl();

        $this->upContexts();
        $this->upInitSiteContent();

        /** @var \modCacheManager $cacheManager */
        $cacheManager = $this->modx->getCacheManager();
        if ($cacheManager instanceof \modCacheManager) {
            $cacheManager->refresh();

            /* create assets/ */
            $assetsPath = $this->getUserInstallConfigValue('assets_path');
            if (!is_dir($assetsPath)) {
                $cacheManager->writeTree($assetsPath, [
                    'new_folder_permissions' => $this->modx->getOption('new_folder_permissions', null, 0775)
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->method = 'down';

        // drop tables
        foreach ($this->modx_tables as $class) {
            if (!$dbcreated= $this->modx->manager->removeObjectContainer($class)) {
                $this->results[]= [xPDO::LOG_LEVEL_ERROR => $this->modx->lexicon('table_err_remove', ['class' => $class])];
            } else {
                $this->results[]= [xPDO::LOG_LEVEL_DEBUG + 1 => $this->modx->lexicon('table_removed', ['class' => $class])];
            }
        }

        if ($this->modx->getCacheManager()) {
            $this->modx->cacheManager->refresh();
        }
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignDescription()
    {
        $this->description = 'Install MODX 3.0 Dev';
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignVersion()
    {
        $this->version = '3.0.0-dev';
    }

    /**
     * Method is called on construct, Child class can override and implement this
     */
    protected function assignSeedsDir()
    {
        $this->seeds_dir = '3_0_0_dev';
    }

    /**
     * Method is called on construct, can change to only run this migration for those types
     */
    protected function assignType()
    {
        $this->type = 'master';
    }

    /**
     * Helpers:
     */

    protected function upSystemSettings()
    {
        // Load MODX System Settings:

        /** @var \LCI\Blend\SystemSetting $systemSetting */
        $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
        $systemSetting
            ->setSeedsDir($this->getSeedsDir())
            ->setCoreSettingsVersion($this->current_version['full_version'])
            ->blend();


        /** @var \LCI\Blend\SystemSetting $systemSetting */
        $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
        $systemSetting
            ->setSeedsDir($this->getSeedsDir())
            ->setCoreSettingsDistro(trim($this->current_version['distro'], '@'))
            ->blend();

        /* set new_folder_permissions/new_file_permissions if specified */
        if ($this->getUserInstallConfigValue('new_folder_permissions')) {
            /** @var \LCI\Blend\SystemSetting $systemSetting */
            $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
            $systemSetting
                ->setName('new_folder_permissions')
                ->setSeedsDir($this->getSeedsDir())
                ->setValue($this->getUserInstallConfigValue('new_folder_permissions'))
                ->setArea('Files')
                ->blend();
        }
        if ($this->getUserInstallConfigValue('new_file_permissions')) {
            /** @var \LCI\Blend\SystemSetting $systemSetting */
            $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
            $systemSetting
                ->setName('new_file_permissions')
                ->setSeedsDir($this->getSeedsDir())
                ->setValue($this->getUserInstallConfigValue('new_file_permissions'))
                ->setArea('Files')
                ->blend();
        }

        /* check for mb extension, set setting accordingly */
        $usemb = function_exists('mb_strlen');
        if ($usemb) {
            /** @var \LCI\Blend\SystemSetting $systemSetting */
            $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
            $systemSetting
                ->setSeedsDir($this->getSeedsDir())
                ->setCoreUseMultibyte(1)
                ->blend();
        }

        /* if language != en, set cultureKey, manager_language, manager_lang_attribute to it */
        $language = $this->getUserInstallConfigValue('language','en');
        if ($language != 'en') {
            /** @var \LCI\Blend\SystemSetting $systemSetting */
            $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
            $systemSetting
                ->setSeedsDir($this->getSeedsDir())
                ->setCoreCultureKey($language)
                ->blend();

            /** @var \LCI\Blend\SystemSetting $systemSetting */
            $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
            $systemSetting
                ->setSeedsDir($this->getSeedsDir())
                ->setCoreManagerLanguage($language)
                ->blend();

            /** @var \LCI\Blend\SystemSetting $systemSetting */
            $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
            $systemSetting
                ->setSeedsDir($this->getSeedsDir())
                ->setCoreManagerLangAttribute($language)
                ->blend();
        }

        /* add ext_debug setting for sdk distro */
        if ('sdk' === trim($this->current_version['distro'], '@')) {
            /** @var \LCI\Blend\SystemSetting $systemSetting */
            $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
            $systemSetting
                ->setSeedsDir($this->getSeedsDir())
                ->setArea('system')
                ->setName('ext_debug')
                ->setNamespace('core')
                ->setType('combo-boolean')
                ->setValue(false)
                ->blend();
        }

        $maxFileSize = ini_get('upload_max_filesize');
        $maxFileSize = trim($maxFileSize);
        $last = strtolower($maxFileSize[strlen($maxFileSize)-1]);
        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $maxFileSize *= 1024;
            //no break;
            case 'm':
                $maxFileSize *= 1024;
            // no break;
            case 'k':
                $maxFileSize *= 1024;
        }

        /** @var \LCI\Blend\SystemSetting $systemSetting */
        $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
        $systemSetting
            ->setSeedsDir($this->getSeedsDir())
            ->setCoreUploadMaxsize($maxFileSize)
            ->blend();
    }

    protected function upNamespaceWorkspace()
    {
        /** @var \modNamespace $namespace */
        $namespace = $this->modx->newObject('modNamespace');
        $namespace->set('name','core');
        $namespace->set('path','{core_path}');
        $namespace->set('assets_path','{assets_path}');

        $namespace->save();

        /** @var \modWorkspace $workspace */
        $workspace = $this->modx->newObject('modWorkspace');
        $workspace->fromArray(array (
            'id' => 1,
            'name' => 'Default MODX workspace',
            'path' => '{core_path}',
            'active' => 1,
        ), '', true, true);
        $workspace->save();

        /* modx.com extras provisioner */
        $transportProvider = $this->modx->newObject('transport.modTransportProvider');
        $transportProvider->fromArray(array (
            'id' => 1,
            'name' => 'modx.com',
            'description' => 'The official MODX transport provider for 3rd party components.',
            'service_url' => 'https://rest.modx.com/extras/',
            'created' => strftime('%Y-%m-%d %H:%M:%S'),
        ), '', true, true);
        $transportProvider->save();
    }
    /**
     *
     */
    protected function upAcl()
    {

        /* setup load only anonymous ACL */
        /** @var modAccessPolicy $loadOnly */
        $loadOnly = $this->modx->getObject('modAccessPolicy',array(
            'name' => 'Load Only',
        ));
        if ($loadOnly) {
            /** @var modAccessContext $access */
            $access= $this->modx->newObject('modAccessContext');
            $access->fromArray(array(
                'target' => 'web',
                'principal_class' => 'modUserGroup',
                'principal' => 0,
                'authority' => 9999,
                'policy' => $loadOnly->get('id'),
            ));
            $access->save();
            unset($access);
        }
        unset($loadOnly);


        /* setup default admin ACLs */
        /** @var modAccessPolicy $adminPolicy */
        $adminPolicy = $this->modx->getObject('modAccessPolicy',array(
            'name' => 'Administrator',
        ));
        /** @var modUserGroup $adminGroup */
        $adminGroup = $this->modx->getObject('modUserGroup',array(
            'name' => 'Administrator',
        ));
        if ($adminPolicy && $adminGroup) {
            /** @var modAccessContext $access */
            $access= $this->modx->newObject('modAccessContext');
            $access->fromArray(array(
                'target' => 'mgr',
                'principal_class' => 'modUserGroup',
                'principal' => $adminGroup->get('id'),
                'authority' => 0,
                'policy' => $adminPolicy->get('id'),
            ));
            $access->save();
            unset($access);

            $access= $this->modx->newObject('modAccessContext');
            $access->fromArray(array(
                'target' => 'web',
                'principal_class' => 'modUserGroup',
                'principal' => $adminGroup->get('id'),
                'authority' => 0,
                'policy' => $adminPolicy->get('id'),
            ));
            $access->save();
            unset($access);
        }
        unset($adminPolicy,$adminGroup);
    }

    /**
     *
     */
    protected function upAdminUser()
    {
        /* add default admin user */
        /** @var modUser $user */
        $user = $this->modx->newObject('modUser');
        $user->set('username', $this->getUserInstallConfigValue('admin_username', 'default_user'));
        $user->set('password', $this->getUserInstallConfigValue('admin_password', 'password'));
        $user->setSudo(true);
        $saved = $user->save();

        if ($saved) {
            /** @var modUserProfile $userProfile */
            $userProfile = $this->modx->newObject('modUserProfile');
            $userProfile->set('internalKey', $user->get('id'));
            $userProfile->set('fullname', (!empty($this->modx->lexicon('default_admin_user')) ? $this->modx->lexicon('default_admin_user') : 'Default Admin'));
            $userProfile->set('email', $this->getUserInstallConfigValue('admin_email', 'need-email@email.com'));
            $saved = $userProfile->save();
            if ($saved) {
                /** @var modUserGroupMember $userGroupMembership */
                $userGroupMembership = $this->modx->newObject('modUserGroupMember');
                $userGroupMembership->set('user_group', 1);
                $userGroupMembership->set('member', $user->get('id'));
                $userGroupMembership->set('role', 2);
                $saved = $userGroupMembership->save();

                $user->set('primary_group',1);
                $user->save();
            }
            if ($saved) {
                /** @var \LCI\Blend\SystemSetting $systemSetting */
                $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
                $systemSetting
                    ->setSeedsDir($this->getSeedsDir())
                    ->setCoreEmailsender($this->getUserInstallConfigValue('admin_email', 'need-email@email.com'))
                    ->blend();
            }
        }
        if (!$saved) {
            $this->addResultMessage([xPDO::LOG_LEVEL_ERROR => $this->modx->lexicon('dau_err_save').'<br />' . print_r($this->modx->errorInfo(), true)]);
        } else {
            $this->addResultMessage([xPDO::LOG_LEVEL_DEBUG + 1 => $this->modx->lexicon('dau_saved')]);
        }

    }


    protected function upAttachPolicyTemplates()
    {
        // replaces: _build/resolvers/resolve.policytemplates.php
        $success= false;

        /* map of Template -> TemplateGroup */
        $map = [
            'ResourceTemplate' => 'Resource',
            'AdministratorTemplate' => 'Admin',
            'ObjectTemplate' => 'Object',
            'ContextTemplate' => 'Object',
            'ElementTemplate' => 'Element',
            'MediaSourceTemplate' => 'MediaSource',
            'NamespaceTemplate' => 'Namespace',
        ];

        $templates = $this->modx->getCollection('modAccessPolicyTemplate');
        foreach ($templates as $template) {
            if (isset($map[$template->get('name')])) {
                $templateGroup = $this->modx->getObject('modAccessPolicyTemplateGroup',array('name' => $map[$template->get('name')]));
                if ($templateGroup) {
                    $template->set('template_group',$templateGroup->get('id'));
                    $success = $template->save();
                } else {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, "Core AccessPolicyTemplateGroup {$map[$template->get('name')]} is missing!");
                }
            } else {
                $success = true;
            }
        }
        return $success;
    }

    protected function upAttachPolicies()
    {
        // replaces: _build/resolvers/resolve.policies.php

        $success= false;

        /* map of Policy -> Template */
        $map = [
            'Resource' => 'ResourceTemplate',
            'Administrator' => 'AdministratorTemplate',
            'Content Editor' => 'AdministratorTemplate',
            'Load Only' => 'ObjectTemplate',
            'Load, List and View' => 'ObjectTemplate',
            'Object' => 'ObjectTemplate',
            'Context' => 'ContextTemplate',
            'Element' => 'ElementTemplate',
            'Media Source Admin' => 'MediaSourceTemplate',
            'Media Source User' => 'MediaSourceTemplate',
            'Hidden Namespace' => 'NamespaceTemplate',
        ];

        $policies = $this->modx->getCollection('modAccessPolicy');
        foreach ($policies as $policy) {
            if (isset($map[$policy->get('name')])) {
                $template = $this->modx->getObject('modAccessPolicyTemplate',array('name' => $map[$policy->get('name')]));
                if ($template) {
                    $policy->set('template',$template->get('id'));
                    $success = $policy->save();
                } else {
                    $this->modx->log(xPDO::LOG_LEVEL_ERROR, "Core AccessPolicyTemplate {$map[$policy->get('name')]} is missing! Could not resolve AccessPolicy {$policy->get('name')}.");
                }
            } else {
                $success = true;
            }
        }
        return $success;
    }

    protected function upAttachDashboardWidgets()
    {
        // replaces: _build/resolvers/resolve.dashboardwidgets.php
        $success = false;

        $map = [
            'w_configcheck',
            'w_newsfeed',
            'w_securityfeed',
            'w_whosonline',
            'w_recentlyeditedresources',
        ];

        /** @var modDashboard $dashboard */
        $dashboard = $this->modx->getObject('modDashboard', 1);
        if (empty($dashboard)) {
            $dashboard = $this->modx->getObject('modDashboard', ['name' => 'Default']);
            if (empty($dashboard)) {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not find default Dashboard!');
                return false;
            }
        }

        $idx = 0;
        //$widgets = $this->modx->getCollection('modDashboardWidget');
        foreach ($map as $widgetName) {
            /** @var \modDashboardWidget $widget */
            $widget = $this->modx->getObject('modDashboardWidget',array(
                'name' => $widgetName,
            ));
            if ($widget) {
                /** @var \modDashboardWidgetPlacement $placement */
                $placement = $this->modx->getObject('modDashboardWidgetPlacement',array(
                    'widget' => $widget->get('id'),
                    'dashboard' => $dashboard->get('id'),
                ));
                if (!$placement) {
                    $placement = $this->modx->newObject('modDashboardWidgetPlacement');
                    $placement->set('widget',$widget->get('id'));
                    $placement->set('dashboard',$dashboard->get('id'));
                    $placement->set('rank',$idx);
                    $success = $placement->save();
                } else {
                    $success = true;
                }
                $idx++;
            }
        }
        return $success;
    }

    protected function upAttachActionFields()
    {
        // replaces: _build/resolvers/resolve.actionfields.php
        $success= true;

        $xmlFile = MODX_CORE_PATH.'model/schema/modx.action.fields.schema.xml';
        if (!file_exists($xmlFile)) return false;

        $xml = @file_get_contents($xmlFile);
        if (empty($xml)) return false;

        $xml = @simplexml_load_string($xml);

        // @TODO why is this in here??
        $actionFields = $this->modx->getCollection('modActionField');
        foreach ($actionFields as $actionField) {
            $actionField->remove();
        }

        foreach ($xml->action as $action) {
            $tabIdx = 0;
            foreach ($action->tab as $tab) {
                $tabName = (string)$tab['name'];
                if ($tabName != 'modx-resource-content') {
                    $tabObj = $this->modx->getObject('modActionField',array(
                        'action' => (string)$action['controller'],
                        'name' => $tabName,
                        'type' => 'tab',
                    ));
                    if (!$tabObj) {
                        $tabObj = $this->modx->newObject('modActionField');
                        $tabObj->fromArray(array(
                            'action' => (string)$action['controller'],
                            'name' => $tabName,
                            'type' => 'tab',
                            'tab' => '',
                            'form' => (string)$action['form'],
                            'other' => !empty($tab['other']) ? (string)$tab['other'] : '',
                            'rank' => $tabIdx,
                        ));
                        $success = $tabObj->save();
                    }
                }

                $fieldIdx = 0;
                foreach ($tab->field as $field) {
                    $fieldObj = $this->modx->getObject('modActionField',array(
                        'action' => (string)$action['controller'],
                        'name' => (string)$field['name'],
                        'type' => 'field',
                    ));
                    if (!$fieldObj) {
                        $fieldObj = $this->modx->newObject('modActionField');
                        $fieldObj->fromArray(array(
                            'action' => (string)$action['controller'],
                            'name' => (string)$field['name'],
                            'type' => 'field',
                            'tab' => (string)$tab['name'],
                            'form' => (string)$action['form'],
                            'other' => !empty($tab['other']) ? (string)$tab['other'] : '',
                            'rank' => $fieldIdx,
                        ));
                        $success = $fieldObj->save();
                    }
                    $fieldIdx++;
                }

                $tabIdx++;
            }
        }

        return $success;
    }

    protected function upContexts()
    {
        /** @var \modContext $webContext */
        $webContext = $this->modx->newObject('modContext');
        $webContext->fromArray(array (
            'key' => 'web',
            'name' => 'Website',
            'description' => 'The default front-end context for your web site.',
        ), '', true, true);
        $webContext->save();


        /** @var \modContext $managerContext */
        $managerContext = $this->modx->newObject('modContext');
        $managerContext->fromArray(array (
            'key' => 'mgr',
            'name' => 'Manager',
            'description' => 'The default manager or administration context for content management activity.',
        ), '', true, true);
        $managerContext->save();
    }

    protected function upInitSiteContent()
    {
        /* add base template and home resource */
        $templateContent = file_get_contents($this->blender->getSeedsDirectory($this->getSeedsDir()) . 'base_template.tpl');

        $template_name = ($this->modx->lexicon('base_template') ? $this->modx->lexicon('base_template') : 'Base Template');
        /** @var \LCI\Blend\Template $baseTemplate */
        $baseTemplate = $this->blender->blendOneRawTemplate($template_name);
        $baseTemplate
            ->setSeedsDir($this->getSeedsDir())
            ->setCode($templateContent);

        if ($baseTemplate->blend()) {

            $template = $baseTemplate->getElementFromName($template_name);

            /** @var \LCI\Blend\SystemSetting $systemSetting */
            $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
            $systemSetting
                ->setSeedsDir($this->getSeedsDir())
                ->setCoreDefaultTemplate($template->get('id'))
                ->blend();

            /** @var \LCI\Blend\Resource $blendResource */
            $blendResource = new \LCI\Blend\Resource($this->modx, $this->blender);
            $blendResource
                ->setContextKey('web')
                ->setSeedsDir($this->getSeedsDir());

            if ($blendResource->blendFromSeed('index')) {
                /** @var modResource $resource */
                $resource = $blendResource->getResourceFromSeedKey('index');
                $resource->set('pagetitle', (!empty($this->modx->lexicon('home')) ? $this->modx->lexicon('home') : 'Home'));
                $resource->set('longtitle', ( !empty($this->modx->lexicon('congratulations')) ? $this->modx->lexicon('congratulations') : 'Congratulations'));
                $resource->set('template', $template->get('id'));

                if ($resource->save()) {
                    /* site_start */
                    /** @var \LCI\Blend\SystemSetting $systemSetting */
                    $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
                    $systemSetting
                        ->setSeedsDir($this->getSeedsDir())
                        ->setCoreSiteStart($resource->get('id'))
                        ->blend();
                }

            } else {
                // @TODO note the error
            }
        }
    }
}
