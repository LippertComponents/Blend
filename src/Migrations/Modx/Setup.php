<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 3/1/18
 * Time: 10:21 AM
 */

namespace LCI\Blend\Migrations\Modx;

use LCI\Blend\Blender;
use LCI\Blend\Migrations;
use LCI\Blend\Helpers\SimpleCache;

class Setup extends Migrations
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

    /** @var array  */
    protected $results = [];

    /** @var array */
    protected $current_version = [];

    /** @var array  */
    protected $install_config = [];

    public function __construct(\modX $modx, Blender $blender)
    {
        parent::__construct($modx, $blender);

        $this->cacheOptions = [
            \xPDO::OPT_CACHE_KEY => 'modx'
        ];
    }

    /**
     * @param array|string $message
     */
    protected function addResultMessage($message)
    {
        $this->results[] = $message;
        // now cache it:

        $simpleCache = new SimpleCache(BLEND_CACHE_DIR . 'modx/');
        $simpleCache->set($this->getSeedsDir().'-'.$this->method, $this->results);
    }

    /**
     *
     */
    protected function loadUserInstallConfig()
    {
        $key = 'install-config-v'.str_replace(['.', '-'], '_', $this->version).'_install';

        $simpleCache = new SimpleCache(BLEND_CACHE_DIR . 'modx/');
        $this->install_config = $simpleCache->get($key);
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

    /**
     *
     */
    protected function loadCurrentVersionInfo()
    {
        $this->current_version = include MODX_CORE_PATH . 'docs/version.inc.php';
    }
}