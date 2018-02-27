<?php

/**
 * Auto Generated from Blender
 * Date: 2017/11/10 at 15:29:37 EST -05:00
 */

use \LCI\Blend\Migrations;

class v3_0_dev_install extends Migrations
{
    /** @var array ~ xPDO class names */
    protected $modx_tables = [
        'modAccessAction',
        'modAccessActionDom',
        'modAccessCategory',
        'modAccessContext',
        'modAccessElement',
        'modAccessMenu',
        'modAccessPermission',
        'modAccessPolicy',
        'modAccessPolicyTemplate',
        'modAccessPolicyTemplateGroup',
        'modAccessResource',
        'modAccessResourceGroup',
        'modAccessTemplateVar',
        'modAccessNamespace',
        'modAction',
        'modActionDom',
        'modActionField',
        'modActiveUser',
        'modCategory',
        'modCategoryClosure',
        'modChunk',
        'modClassMap',
        'modContentType',
        'modContext',
        'modContextResource',
        'modContextSetting',
        'modDashboard',
        'modDashboardWidget',
        'modDashboardWidgetPlacement',
        'modElementPropertySet',
        'modEvent',
        'modExtensionPackage',
        'modFormCustomizationProfile',
        'modFormCustomizationProfileUserGroup',
        'modFormCustomizationSet',
        'modLexiconEntry',
        'modManagerLog',
        'modMenu',
        'modNamespace',
        'modPlugin',
        'modPluginEvent',
        'modPropertySet',
        'modResource',
        'modResourceGroup',
        'modResourceGroupResource',
        'modSession',
        'modSnippet',
        'modSystemSetting',
        'modTemplate',
        'modTemplateVar',
        'modTemplateVarResource',
        'modTemplateVarResourceGroup',
        'modTemplateVarTemplate',
        'modUser',
        'modUserProfile',
        'modUserGroup',
        'modUserGroupMember',
        'modUserGroupRole',
        'modUserGroupSetting',
        'modUserMessage',
        'modUserSetting',
        'modWorkspace',
        'registry.db.modDbRegisterMessage',
        'registry.db.modDbRegisterTopic',
        'registry.db.modDbRegisterQueue',
        'transport.modTransportPackage',
        'transport.modTransportProvider',
        'sources.modAccessMediaSource',
        'sources.modMediaSource',
        'sources.modMediaSourceElement',
        'sources.modMediaSourceContext'
    ];

    /** @var array  */
    protected $cacheOptions = [];

    protected $results = [];

    /** @var array */
    protected $current_version = [];

    /** @var array  */
    protected $install_config = [];

    protected $method = 'up';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->cacheOptions = [
            \xPDO::OPT_CACHE_KEY => 'modx'
        ];

        $this->loadUserInstallConfig();

        // MySQL/DB connection

        // does DB exist?
        $this->modx->getManager();
        $connected= $this->modx->connect();

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

        $this->upSystemSettings();

        $this->upAdminUser();

        $this->upAcl();

        /* add base template and home resource */
        $templateContent = file_get_contents($this->blender->getSeedsDir() . 'base_template.tpl');

        /** @var \LCI\Blend\Template $baseTemplate */
        $baseTemplate = $this->blender->blendOneRawTemplate($this->modx->lexicon('base_template'));
        $baseTemplate->setCode($templateContent);

        if ($baseTemplate->blend()) {

            $template = $baseTemplate->getElementFromName($this->modx->lexicon('base_template'));

            /** @var \LCI\Blend\SystemSetting $systemSetting */
            $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
            $systemSetting
                ->setCoreDefaultTemplate($template->get('id'))
                ->blend();

            /** @var \LCI\Blend\Resource $blendResource */
            $blendResource = new \LCI\Blend\Resource($this->modx, $this->blender);
            $blendResource->setContextKey('web');

            if (!empty($seeds_dir)) {
                $blendResource->setSeedsDir($this->getSeedsDir());
            }

            if ($blendResource->blendFromSeed('index')) {
                /** @var modResource $resource */
                $resource = $blendResource->getResourceFromSeedKey('index');
                $resource->set('pagetitle', $this->modx->lexicon('home'));
                $resource->set('longtitle', $this->modx->lexicon('congratulations'));
                $resource->set('template', $template->get('id'));

                if ($resource->save()) {
                    /* site_start */
                    /** @var \LCI\Blend\SystemSetting $systemSetting */
                    $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
                    $systemSetting
                        ->setCoreSiteStart($resource->get('id'))
                        ->blend();
                }

            } else {
                // @TODO note the error
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

        $this->modx->cacheManager->refresh();
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
     * Method is called on construct, can change to only run this migration for those types
     */
    protected function assignType()
    {
        $this->type = 'master';
    }

    /**
     * Method is called on construct, Child class can override and implement this
     */
    protected function assignSeedsDir()
    {
        $this->seeds_dir = 'v3_0_0_dev';
    }

    /**
     * Helpers:
     */
    /**
     * Creates the database connection for the installation process.
     *
     * @access private
     * @return xPDO The xPDO instance to be used by the installation.
     */
    protected function connect($dsn, $user = '', $password = '', $prefix = '', array $options = array()) {
        if (class_exists('\xPDO\xPDO')) {
            $this->xpdo = new xPDO($dsn, $user, $password, array_merge(array(
                xPDO::OPT_CACHE_PATH => MODX_CORE_PATH . 'cache/',
                xPDO::OPT_TABLE_PREFIX => $prefix,
                xPDO::OPT_SETUP => true,
            ), $options),
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT)
            );
            $this->xpdo->setLogTarget(array(
                'target' => 'FILE',
                'options' => array(
                    'filename' => 'install.' . MODX_CONFIG_KEY . '.' . strftime('%Y%m%dT%H%M%S') . '.log'
                )
            ));
            $this->xpdo->setLogLevel(xPDO::LOG_LEVEL_ERROR);
            return $this->xpdo;
        } else {
            return $this->lexicon('xpdo_err_nf', array('path' => MODX_CORE_PATH.'vendor/xpdo/xpdo/src/xPDO/xPDO.php'));
        }
    }

    protected function loadCurrentVersionInfo()
    {
        $this->current_version = include MODX_CORE_PATH . 'docs/version.inc.php';
    }

    protected function upSystemSettings()
    {
        // Load MODX System Settings:

        /** @var \LCI\Blend\SystemSetting $systemSetting */
        $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
        $systemSetting
            ->setCoreSettingsVersion($this->current_version['full_version'])
            ->blend();


        /** @var \LCI\Blend\SystemSetting $systemSetting */
        $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
        $systemSetting
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
                ->setCoreUseMultibyte(1)
                ->blend();
        }

        /* if language != en, set cultureKey, manager_language, manager_lang_attribute to it */
        $language = $this->getUserInstallConfigValue('language','en');
        if ($language != 'en') {
            /** @var \LCI\Blend\SystemSetting $systemSetting */
            $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
            $systemSetting
                ->setCoreCultureKey($language)
                ->blend();

            /** @var \LCI\Blend\SystemSetting $systemSetting */
            $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
            $systemSetting
                ->setCoreManagerLanguage($language)
                ->blend();

            /** @var \LCI\Blend\SystemSetting $systemSetting */
            $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
            $systemSetting
                ->setCoreManagerLangAttribute($language)
                ->blend();
        }

        /* add ext_debug setting for sdk distro */
        if ('sdk' === trim($this->current_version['distro'], '@')) {
            /** @var \LCI\Blend\SystemSetting $systemSetting */
            $systemSetting = new \LCI\Blend\SystemSetting($this->modx, $this->blender);
            $systemSetting
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
            ->setCoreUploadMaxsize($maxFileSize)
            ->blend();
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
        $user->set('username', $this->getUserInstallConfigValue('admin_username'));
        $user->set('password', $this->getUserInstallConfigValue('admin_password'));
        $user->setSudo(true);
        $saved = $user->save();

        if ($saved) {
            /** @var modUserProfile $userProfile */
            $userProfile = $this->modx->newObject('modUserProfile');
            $userProfile->set('internalKey', $user->get('id'));
            $userProfile->set('fullname', $this->modx->lexicon('default_admin_user'));
            $userProfile->set('email', $this->getUserInstallConfigValue('admin_email'));
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
                    ->setCoreEmailsender($this->getUserInstallConfigValue('admin_email'))
                    ->blend();
            }
        }
        if (!$saved) {
            $this->addResultMessage([xPDO::LOG_LEVEL_ERROR => $this->modx->lexicon('dau_err_save').'<br />' . print_r($this->modx->errorInfo(), true)]);
        } else {
            $this->addResultMessage([xPDO::LOG_LEVEL_DEBUG + 1 => $this->modx->lexicon('dau_saved')]);
        }

    }

    /**
     * @param array|string $message
     */
    protected function addResultMessage($message)
    {
        $this->results[] = $message;
        // now cache it:
        $this->modx->cacheManager->set(
            $this->getSeedsDir().'-'.$this->method,
            $this->results,
            0,
            $this->cacheOptions
        );
    }

    /**
     *
     */
    protected function loadUserInstallConfig()
    {
        $this->install_config = $this->modx->cacheManager->get('install-config-'.$this->getSeedsDir(), $this->cacheOptions);
    }

    /**
     * @param string $key
     * @param mixed
     * @return bool|mixed
     */
    protected function getUserInstallConfigValue($key, $default=false)
    {
        if (isset($this->install_config[$key])) {
            return $this->install_config[$key];
        }

        return $default;
    }
}
