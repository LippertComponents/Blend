<?php

/**
 * Auto Generated from Blender
 * Date: 2018/03/26 at 18:10:35 UTC +00:00
 */

use \LCI\Blend\Migrations;

class SiteExample extends Migrations
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * mediaSources'
         * contexts
         * templates
         * resources
         * chunks
         * plugins
         * snippets
         * systemSettings
         */

        /** @var \LCI\Blend\Blendable\MediaSource $mediaSource */
        $mediaSource = $this->blender->getBlendableLoader()->getBlendableMediaSource('mediaSourceSiteExample');
        $mediaSource
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldDescription('Site Example media source test')
            ->setPropertyBasePath('/assets/path/site/')
            ->setPropertyBaseUrl('/assets/url/site/');
        if ($mediaSource->blend(true)) {
            $this->blender->out($mediaSource->getFieldName().' was saved correctly');

        } else {
            //error
            $this->blender->outError($mediaSource->getFieldName().' did not save correctly ');
            $this->blender->outError(print_r($mediaSource->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }


        /** @var \LCI\Blend\Blendable\Context $contextSiteExample */
        $contextSiteExample = $this->blender->getBlendableLoader()->getBlendableContext('site');
        $contextSiteExample
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldName('Site Example')
            ->setFieldDescription('Sub domain')
            ->addSetting('cultureKey', 'en')
            ->addSetting('http_host', 'sub.mysite.com')
            ->addSetting('base_url', '/')
            ->addSetting('site_url', 'https://sub.mysite.com/');

        if ($contextSiteExample->blend(true)) {
            $this->blender->out($contextSiteExample->getFieldKey().' was saved correctly');

        } else {
            //error
            $this->blender->outError($contextSiteExample->getFieldKey().' did not save correctly ');
            $this->blender->outError(print_r($contextSiteExample->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }

        /** @var \LCI\Blend\Blendable\Template $templateSiteExample */
        $templateSiteExample = $this->blender->getBlendableLoader()->getBlendableTemplate('templateSiteExample');
        $templateSiteExample
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldDescription('Site Example template test')
            ->setFieldCategory('Parent Template Cat=>Child Template Cat')
            ->setFieldCode('<!DOCTYPE html><html lang="en"><head><title>[[*pagetitle]]</title></head><body><!-- Site Example -->[[*content]]</body></html>');

        if ($templateSiteExample->blend(true)) {
            $this->blender->out($templateSiteExample->getFieldName().' was saved correctly');

        } else {
            //error
            $this->blender->outError($templateSiteExample->getFieldName().' did not save correctly ');
            $this->blender->outError(print_r($templateSiteExample->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }

        $alias = 'index';
        $content = 'Index page code...';
        $description = 'Site Example...';
        $long_title = 'Site Index Example, Long title';
        $page_title = 'Site Index Example, Page Title';
        /** @var \LCI\Blend\Blendable\Resource $resourceSiteExample */
        $resourceSiteExample = $this->blender->getBlendableLoader()->getBlendableResource($alias);
        $resourceSiteExample
            ->setSeedsDir(BLEND_TEST_SEEDS_DIR)
            ->setFieldTemplate('templateSiteExample')
            ->setFieldContent($content)
            ->setFieldDescription($description)
            ->setFieldLongtitle($long_title)
            ->setFieldPagetitle($page_title)
            ->setFieldPublished(true);

        if ($resourceSiteExample->blend(true)) {
            $this->blender->out($resourceSiteExample->getFieldAlias().' was saved correctly');

        } else {
            //error
            $this->blender->outError($resourceSiteExample->getFieldAlias().' did not save correctly ');
            $this->blender->outError(print_r($templateSiteExample->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }

        $alias = 'site-example-resource';
        $content = 'Site Example content, can put in HTML here';
        $description = 'Site Example...';
        $long_title = 'Site Example, Long title';
        $page_title = 'Site Example, Page Title';
        /** @var \LCI\Blend\Blendable\Resource $resourceSiteExample */
        $resourceSiteExample = $this->blender->getBlendableLoader()->getBlendableResource($alias);
        $resourceSiteExample
            ->setSeedsDir(BLEND_TEST_SEEDS_DIR)
            ->setFieldContent($content)
            ->setFieldDescription($description)
            ->setFieldLongtitle($long_title)
            ->setFieldPagetitle($page_title);

        if ($resourceSiteExample->blend(true)) {
            $this->blender->out($resourceSiteExample->getFieldAlias().' was saved correctly');

        } else {
            //error
            $this->blender->outError($resourceSiteExample->getFieldAlias().' did not save correctly ');
            $this->blender->outError(print_r($templateSiteExample->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }

        /** @var \LCI\Blend\Blendable\Chunk $chunk */
        $chunkSiteExample = $this->blender->getBlendableLoader()->getBlendableChunk('chunkSiteExample');
        $chunkSiteExample
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldDescription('Site Example chunk test')
            ->setFieldCategory('Parent Cat=>Child Cat')
            ->setFieldCode('Hi [[+siteExamplePlaceholder]], ...');

        if ($chunkSiteExample->blend(true)) {
            $this->blender->out($chunkSiteExample->getFieldName().' was saved correctly');

        } else {
            //error
            $this->blender->outError($chunkSiteExample->getFieldName().' did not save correctly ');
            $this->blender->outError(print_r($chunkSiteExample->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }

        $plugin_name = 'pluginSiteExample';
        $plugin_description = 'Site Example plugin test';
        $plugin_code = '<?php $eventName = $modx->event->name; // Site Example';
        $plugin_event = 'OnUserActivate';

        /** @var \LCI\Blend\Blendable\Plugin $pluginSiteExample */
        $pluginSiteExample = $this->blender->getBlendableLoader()->getBlendablePlugin($plugin_name);
        $pluginSiteExample
            ->setSeedsDir($plugin_name)
            ->setFieldDescription($plugin_description)
            ->setFieldCategory('Parent Plugin Cat=>Child Plugin Cat')
            ->setFieldCode($plugin_code, true)
            ->setAsStatic('core/components/mysite/elements/plugins/myPlugin.tpl')
            ->attachOnEvent($plugin_event);

        if ($pluginSiteExample->blend(true)) {
            $this->blender->out($pluginSiteExample->getFieldName().' was saved correctly');

        } else {
            //error
            $this->blender->outError($pluginSiteExample->getFieldName().' did not save correctly ');
            $this->blender->outError(print_r($pluginSiteExample->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }


        /** @var \LCI\Blend\Blendable\Snippet $snippetSiteExample */
        $snippetSiteExample = $this->blender->getBlendableLoader()->getBlendableSnippet('snippetSiteExample');
        $snippetSiteExample
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldDescription('Site Example snippet test')
            ->setFieldCategory('Parent Snippet Cat=>Child Snippet Cat')
            ->setFieldCode('<?php return \'Site Example test Snippet!\'; ');

        if ($snippetSiteExample->blend(true)) {
            $this->blender->out($snippetSiteExample->getFieldName().' was saved correctly');

        } else {
            //error
            $this->blender->outError($snippetSiteExample->getFieldName().' did not save correctly ');
            $this->blender->outError(print_r($snippetSiteExample->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }

        /** @var \LCI\Blend\Blendable\SystemSetting $systemSettingSiteExample */
        $systemSettingSiteExample = $this->blender->getBlendableLoader()->getBlendableSystemSetting('systemSettingSiteExample');
        // Can use lots of helper methods for core system settings, see all setCore* methods
        $systemSettingSiteExample
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldValue('Blend Site Example');

        if ($systemSettingSiteExample->blend(true)) {
            $this->blender->out($systemSettingSiteExample->getFieldName().' was saved correctly');

        } else {
            //error
            $this->blender->outError($systemSettingSiteExample->getFieldName().' did not save correctly ');
            $this->blender->outError(print_r($systemSettingSiteExample->getErrorMessages(), true), \LCI\Blend\Blender::VERBOSITY_DEBUG);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /** @var \LCI\Blend\Blendable\MediaSource $mediaSource */
        $mediaSource = $this->blender->getBlendableLoader()->getBlendableMediaSource('mediaSourceSiteExample');
        if ( $mediaSource->setSeedsDir($this->getSeedsDir())->revertBlend() ) {
            $this->blender->out($mediaSource->getFieldName().' setting has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->outError($mediaSource->getFieldName().' setting was not reverted');
        }

        $contextSiteExample = $this->blender->getBlendableLoader()->getBlendableContext('site');
        $contextSiteExample->setSeedsDir($this->getSeedsDir());
        if ( $contextSiteExample->revertBlend() ) {
            $this->blender->out($contextSiteExample->getFieldKey().' setting has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->outError($contextSiteExample->getFieldKey().' setting was not reverted');
        }

        $blendTemplate = $this->blender->getBlendableLoader()->getBlendableTemplate('templateSiteExample');
        $blendTemplate->setSeedsDir($this->getSeedsDir());
        if ( $blendTemplate->revertBlend() ) {
            $this->blender->out($blendTemplate->getFieldName().' template has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->outError($blendTemplate->getFieldName().' template was not reverted');
        }

        $resourceSiteExample = $this->blender->getBlendableLoader()->getBlendableResource('index');
        $resourceSiteExample->setSeedsDir($this->getSeedsDir());
        if ( $resourceSiteExample->revertBlend() ) {
            $this->blender->out($resourceSiteExample->getFieldAlias().' resource has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->outError($resourceSiteExample->getFieldAlias().' resource was not reverted');
        }

        $resourceSiteExample = $this->blender->getBlendableLoader()->getBlendableResource('site-example-resource');
        $resourceSiteExample->setSeedsDir($this->getSeedsDir());
        if ( $resourceSiteExample->revertBlend() ) {
            $this->blender->out($resourceSiteExample->getFieldAlias().' resource has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->outError($resourceSiteExample->getFieldAlias().' resource was not reverted');
        }


        $blendChunk = $this->blender->getBlendableLoader()->getBlendableChunk('chunkSiteExample');
        $blendChunk->setSeedsDir($this->getSeedsDir());
        if ( $blendChunk->revertBlend() ) {
            $this->blender->out($blendChunk->getFieldName().' setting has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->outError($blendChunk->getFieldName().' setting was not reverted');
        }

        /** @var \LCI\Blend\Blendable\Plugin $pluginSiteExample */
        $pluginSiteExample = $this->blender->getBlendableLoader()->getBlendablePlugin('pluginSiteExample');
        $pluginSiteExample->setSeedsDir($this->getSeedsDir());

        if ( $pluginSiteExample->revertBlend() ) {
            $this->blender->out($pluginSiteExample->getFieldName().' plugin has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->outError($pluginSiteExample->getFieldName().' plugin was not reverted');
        }

        /** @var \LCI\Blend\Blendable\Snippet $snippetSiteExample */
        $snippetSiteExample = $this->blender->getBlendableLoader()->getBlendableSnippet('snippetSiteExample');
        $snippetSiteExample->setSeedsDir($this->getSeedsDir());

        if ( $snippetSiteExample->revertBlend() ) {
            $this->blender->out($snippetSiteExample->getFieldName().' snippet has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->outError($snippetSiteExample->getFieldName().' snippet was not reverted');
        }


        /** @var \LCI\Blend\Blendable\SystemSetting $systemSettingSiteExample */
        $systemSettingSiteExample = $this->blender->getBlendableLoader()->getBlendableSystemSetting('systemSettingSiteExample');
        // Can use lots of helper methods for core system settings, see all setCore* methods
        $systemSettingSiteExample
            ->setSeedsDir($this->getSeedsDir())
            ->setFieldValue('Blend Site Example');

        if ( $systemSettingSiteExample->revertBlend() ) {
            $this->blender->out($systemSettingSiteExample->getFieldName().' system setting has been reverted to '.$this->getSeedsDir());

        } else {
            $this->blender->outError($systemSettingSiteExample->getFieldName().' system setting was not reverted');
        }
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignDescription()
    {
        $this->description = '';
    }

    /**
     * Method is called on construct, please fill me in
     */
    protected function assignVersion()
    {

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
        $this->seeds_dir = 'm'.BLEND_TEST_SEEDS_DIR.'_SiteExample';
    }
}