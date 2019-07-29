<?php

namespace LCI\Blend\Transport;

use LCI\Blend\Exception\TransportException;
use LCI\Blend\Exception\TransportNotFoundException;
use LCI\MODX\Console\Helpers\UserInteractionHandler;
use modTransportPackage;
use modTransportProvider;
use modX;
use xPDO;
use xPDOTransport;

class MODXPackages
{
    /** @var string  */
    protected $date_format = '';

    /** @var \modX  */
    protected $modx;

    /** @var array $providerCache */
    protected $providerCache = array();

    protected $provider_map = [];

    /** @var int $updates_cache_expire */
    protected $updates_cache_expire = 300;

    /** @var array  */
    protected $possible_package_signatures = [];

    /** @var \LCI\MODX\Console\Helpers\UserInteractionHandler */
    protected $userInteractionHandler;

    /**
     * MODXPackages constructor.
     * @param modX $modx
     * @param UserInteractionHandler $userInteractionHandler
     */
    public function __construct(modX $modx, UserInteractionHandler $userInteractionHandler)
    {
        $this->modx = $modx;

        $this->userInteractionHandler = $userInteractionHandler;

        $this->date_format = $this->modx->getOption('manager_date_format') .', '. $this->modx->getOption('manager_time_format');
    }

    // Code ideas for MODX packages from: core/model/modx/processors/workspace/packages/getlist.class.php and
    // https://github.com/modmore/SiteDashClient/blob/master/core/components/sitedashclient/src/Package/Update.php

    /**
     * Get basic version information about the package
     *
     * @param string $signature
     * @return array
     */
    public static function getVersionInfo($signature)
    {
        $parts = explode('-', $signature);
        return [
            'base' => $parts[0],
            'version' => $parts[1],
            'release' => (isset($parts[2]) ? $parts[2] : '')
        ];
    }

    /**
     * @param int $limit
     * @param int $start
     * @param string $search
     * @return array
     */
    public function getList($limit=25, $start=0, $search='') {
        $data = [
            'limit' => $limit,
            'packages' => [],
            'start' => $start,
            'total' => 0
        ];

        /** @var array $package_list */
        $package_list = $this->modx->call(
            'transport.modTransportPackage',
            'listPackages',
            [
                &$this->modx,
                1,
                $limit > 0 ? $limit : 0,
                $start,
                $search
            ]
        );


        if ($package_list > 0) {
            /** @var modTransportPackage $package */
            foreach ($package_list['collection'] as $package) {
                if ($package->get('installed') == '0000-00-00 00:00:00') {
                    $package->set('installed', null);
                }

                $package_array = $package->toArray();

                $package_array['name'] = $package_array['package_name'];

                $version_info = self::getVersionInfo($package_array['signature']);
                $package_array['version'] = $version_info['version'];
                $package_array['release'] = $version_info['release'];

                $package_array = $this->formatDates($package_array);
                $package_array['updates'] = $this->getAvailableUpdateInfo($package);

                $data['packages'][] = $package_array;
            }
        }

        $data['total'] = $package_list['total'];
        return $data;
    }

    /**
     * This is only needed if TransportNotFoundException is caught
     * @return array
     */
    public function getPossiblePackageSignatures(): array
    {
        return $this->possible_package_signatures;
    }

    /**
     * @param string $signature - ex: ace-1.8.0-pl
     * @param bool $latest_version
     * @param string $provider_name
     * @return bool
     * @throws TransportException
     * @throws TransportNotFoundException
     */
    public function requirePackage($signature, $latest_version=true, $provider_name='modx.com')
    {
        // partial signature like fred sent, so is this package installed?
        $package_info = static::getVersionInfo($signature);

        // get the latest installed, might sort for the release column
        $query = $this->modx->newQuery('transport.modTransportPackage');
        $query->where(['signature:LIKE' => $package_info['base'].'-%']);
        $query->sortby('version_major', 'DESC');
        $query->sortby('version_minor', 'DESC');
        $query->sortby('version_patch', 'DESC');
        $query->sortby('release_index', 'ASC');
        $query->limit(1);

        /** @var modTransportPackage $package */
        $package = $this->modx->getObject('transport.modTransportPackage', $query);

        $type = 'install';

        if ($package instanceof \modTransportPackage) {
            $signature = $package->get('signature');
            $provider = $this->getPackageProvider($package);
            $type = 'update';

        } else {
            $provider = $this->getProvider($provider_name);
        }

        $transfer_options = [];

        // this only returns the
        $options = $this->getPackageLatestVersions($provider, $signature);

        if (isset($options[$signature])) {
            $transfer_options['location'] = $options[$signature]['location'];
        }

        if ($latest_version && count($options) > 0) {
            $opt = reset($options);

            $signature = $opt['signature'];
            $transfer_options['location'] = $opt['location'];

        } elseif ($type == 'update' && $package instanceof modTransportPackage && !empty($package->get('installed'))) {
            MODXPackagesConfig::addPackageConfig($signature, $latest_version, $provider_name);
            $this->userInteractionHandler->tellUser('Extra '.$signature.' is already installed, skipping!', userInteractionHandler::MASSAGE_ERROR);
            return true;
        }

        if (!$package instanceof modTransportPackage || ($package instanceof modTransportPackage && $package->get('signature') != $signature)) {
            $package = $this->downloadPackageFiles($signature, $provider, $latest_version);
        }

        if (!$package instanceof \modTransportPackage) {
            $this->userInteractionHandler->tellUser('Extra '.$signature.' not found', userInteractionHandler::MASSAGE_ERROR);
            return false;
        }

        if ($success = $this->runPackageInstallUpdate($package)) {
            MODXPackagesConfig::addPackageConfig($signature, $latest_version, $provider_name);
        }

        return $success;
    }

    /**
     * @param string $signature - ex: ace-1.8.0-pl
     * @param bool $force
     * @return bool
     * @throws TransportException
     */
    public function removePackage($signature, $force=true)
    {
        /** @var modTransportPackage $package */
        $package = $this->modx->getObject('transport.modTransportPackage', [
            'signature' => $signature,
        ]);

        if ($package instanceof \modTransportPackage) {
            $package->getTransport();

            if (!$success = $package->removePackage($force)) {
                throw new TransportException('Error Package did not uninstall.');
            }

            $this->userInteractionHandler->tellUser('Extra '.$signature.' has been removed', userInteractionHandler::MASSAGE_SUCCESS);

            $this->modx->cacheManager->refresh([
                $this->modx->getOption('cache_packages_key', null, 'packages') => []
            ]);
            $this->modx->cacheManager->refresh();

        } else {
            throw new TransportException('Package with the signature: '.$signature.' does not seem to be installed. '.
                'Run the extra command to see list of installed extras with proper signatures. You can only remove packages that are installed.');
        }

        return $success;
    }

    /**
     * @param string $signature - ex: ace-1.8.0-pl
     * @param int $preexisting_mode, see xPDOTransport
     *  xPDOTransport::PRESERVE_PREEXISTING = 0;
    xPDOTransport::REMOVE_PREEXISTING = 1;
    xPDOTransport::RESTORE_PREEXISTING = 2;
     * @return bool
     * @throws TransportException
     */
    public function unInstallPackage($signature, $preexisting_mode=null)
    {
        /** @var modTransportPackage $package */
        $package = $this->modx->getObject('transport.modTransportPackage', [
            'signature' => $signature,
        ]);

        if ($package instanceof \modTransportPackage) {
            $package->getTransport();
            /* uninstall package */
            $options = array(
                xPDOTransport::PREEXISTING_MODE => is_null($preexisting_mode) ? xPDOTransport::REMOVE_PREEXISTING : $preexisting_mode,
            );

            if (!$success = $package->uninstall($options)) {
                throw new TransportException('Error Package did not uninstall.');
            }

            $this->userInteractionHandler->tellUser('Extra '.$signature.' has been uninstalled', userInteractionHandler::MASSAGE_SUCCESS);

            $this->modx->cacheManager->refresh([
                $this->modx->getOption('cache_packages_key', null, 'packages') => []
            ]);
            $this->modx->cacheManager->refresh();

        } else {
            throw new TransportException('Package with the signature: '.$signature.' does not seem to be installed. '.
                'Run the extra command to see list of installed extras with proper signatures. You can only remove packages that are installed.');
        }

        return $success;
    }

    // @TODO local packages

    /**
     * @param string $signature - ex: ace-1.8.0-pl
     * @param modTransportProvider $provider
     * @param bool $latest
     * @throws TransportNotFoundException
     * @return bool|modTransportPackage
     */
    protected function downloadPackageFiles($signature, modTransportProvider $provider, $latest=true)
    {
        $transfer_options = [];

        $options = $this->getPackageLatestVersions($provider, $signature);

        if (isset($options[$signature])) {
            $transfer_options['location'] = $options[$signature]['location'];
        }

        if ($latest && count($options) > 1) {
            // @TODO review:
            $opt = reset($options);

            $signature = $opt['signature'];
            $transfer_options['location'] = $opt['location'];
        }

        /** @var modTransportPackage|bool $package */
        $package = $provider->transfer($signature, null, $transfer_options);
        if (!$package) {
            $parts = self::getVersionInfo($signature);

            $this->possible_package_signatures = $provider->find(['query' => $parts['base']]);

            throw new TransportNotFoundException('Failed to download package ' . $signature.
                ' from the '.$provider->get('name').' transport provider. Verify the signature and provider');
        }

        return $package;
    }

    /**
     * @param modTransportPackage $package
     * @return bool
     * @throws TransportException
     */
    protected function runPackageInstallUpdate(modTransportPackage $package)
    {
        $installed = $package->install([]);
        $this->modx->cacheManager->refresh([
            $this->modx->getOption('cache_packages_key', null, 'packages') => []
        ]);
        $this->modx->cacheManager->refresh();

        if (!$installed) {
            throw new TransportException('Failed to install package ' . $package->signature);
        }

        $this->userInteractionHandler->tellUser('Extra '.$package->get('signature').' has been installed!', userInteractionHandler::MASSAGE_SUCCESS);

        $this->modx->invokeEvent('OnPackageInstall', array(
            'package' => $package,
            'action' => $package->previousVersionInstalled() ? \xPDOTransport::ACTION_UPGRADE : \xPDOTransport::ACTION_INSTALL
        ));

        return $installed;
    }

    /**
     * Format installed, created and updated dates
     * @param array $packageArray
     * @return array
     */
    protected function formatDates(array $packageArray)
    {
        if ($packageArray['updated'] != '0000-00-00 00:00:00' && $packageArray['updated'] != null) {
            $packageArray['updated'] = utf8_encode(date($this->date_format, strtotime($packageArray['updated'])));
        } else {
            $packageArray['updated'] = '';
        }

        $packageArray['created']= utf8_encode(date($this->date_format, strtotime($packageArray['created'])));

        if ($packageArray['installed'] == null || $packageArray['installed'] == '0000-00-00 00:00:00') {
            $packageArray['installed'] = null;
        } else {
            $packageArray['installed'] = utf8_encode(date($this->date_format, strtotime($packageArray['installed'])));
        }
        return $packageArray;
    }

    /**
     * @param modTransportPackage $package
     * @return array
     */
    protected function getAvailableUpdateInfo(modTransportPackage $package)
    {
        $updates = [
            'count' => 0,
            'versions' => []
        ];
        if ($package->get('provider') > 0 && $this->modx->getOption('auto_check_pkg_updates',null,false)) {
            $updateCacheKey = 'mgr/providers/updates/'.$package->get('provider').'/'.$package->get('signature');
            $updateCacheOptions = array(
                xPDO::OPT_CACHE_KEY => $this->modx->cacheManager->getOption('cache_packages_key', null, 'packages'),
                xPDO::OPT_CACHE_HANDLER => $this->modx->cacheManager->getOption('cache_packages_handler', null, $this->modx->cacheManager->getOption(xPDO::OPT_CACHE_HANDLER)),
            );
            $updates = $this->modx->cacheManager->get($updateCacheKey, $updateCacheOptions);

            if (empty($updates)) {
                /* cache providers to speed up load time */
                /** @var modTransportProvider $provider */
                $provider = $this->getPackageProvider($package);

                if ($provider) {
                    $options = $this->getPackageLatestVersions($provider, $package->get('signature'));

                    $updates['count'] = count($options);
                    $updates['versions'] = $options;

                    $this->modx->cacheManager->set($updateCacheKey, $updates, $this->updates_cache_expire, $updateCacheOptions);
                }
            }
        }

        return $updates;
    }

    /**
     * @param modTransportProvider $provider
     * @param string $signature
     * @return array - only returns values if the extra has a version that is more recent then the signature
     */
    protected function getPackageLatestVersions(modTransportProvider $provider, $signature)
    {
        $package_versions = $provider->latest($signature);

        $options = [];
        /** @var \SimpleXMLElement $package */
        foreach ($package_versions as $package_version) {
            $version_info = array_merge([
                'location' => (string)$package_version['location'],
                'signature' => (string)$package_version['signature'],
                'package_name' => (string)$package_version['package_name'],
                'release' => '',
                'version' => ''
            ],
                self::getVersionInfo((string)$package_version['signature'])
            );

            $options[$version_info['signature']] = $version_info;
        }

        return $options;
    }

    /**
     * @param modTransportPackage $package
     * @return modTransportProvider|bool
     */
    protected function getPackageProvider(modTransportPackage $package)
    {
        /* cache providers to speed up load time */
        /** @var modTransportProvider|bool $provider */
        if (!empty($this->providerCache[$package->get('provider')])) {
            return $this->providerCache[$package->get('provider')];
        }

        /** @var modTransportProvider|bool $provider */
        if ($provider = $package->getOne('Provider')) {
            $this->providerCache[$provider->get('id')] = $provider;
        }

        return $provider;
    }

    /**
     * @param string $name
     * @return bool|modTransportProvider
     */
    protected function getProvider($name='modx.com')
    {
        if (isset($this->provider_map[$name]) && $this->providerCache[$this->provider_map[$name]]) {
            return $this->providerCache[$this->provider_map[$name]];
        }

        /** @var modTransportProvider|bool $provider */
        $provider = $this->modx->getObject('transport.modTransportProvider', ['name' => $name]);

        if ($provider) {
            $this->provider_map[$name] = $provider->get('id');
            $this->providerCache[$this->provider_map[$name]] = $provider;
        }

        return $provider;
    }
}
