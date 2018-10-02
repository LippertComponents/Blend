<?php
/**
 * Created by PhpStorm.
 * User: joshgulledge
 * Date: 10/1/18
 * Time: 12:35 PM
 */

namespace LCI\Blend\Helpers;

use LCI\Blend\Blender;
use LCI\Blend\Blendable\Context;
use LCI\Blend\Blendable\Chunk;
use LCI\Blend\Blendable\MediaSource;
use LCI\Blend\Blendable\Plugin;
use LCI\Blend\Blendable\Resource;
use LCI\Blend\Blendable\Snippet;
use LCI\Blend\Blendable\SystemSetting;
use LCI\Blend\Blendable\Template;
use LCI\Blend\Blendable\TemplateVariable;
use LCI\MODX\Console\Helpers\UserInteractionHandler;

/**
 * Class BlendableLoader
 *  Simple class to help init blendable objects and blend/revert many
 * @package LCI\Blend\Helpers
 */
class BlendableLoader
{
    /** @var \modX */
    protected $modx;

    /** @var Blender */
    protected $blender;

    /** @var \LCI\MODX\Console\Helpers\UserInteractionHandler */
    protected $userInteractionHandler;

    /**
     * BlendableLoader constructor.
     * @param \modX $modx
     * @param Blender $blender
     * @param \LCI\MODX\Console\Helpers\UserInteractionHandler $userInteractionHandler
     */
    public function __construct(Blender $blender, \modX $modx, UserInteractionHandler $userInteractionHandler)
    {
        $this->modx = $modx;
        $this->blender = $blender;
        $this->userInteractionHandler = $userInteractionHandler;
    }

    /**
     * Use this method with your IDE to help manually build a Chunk with PHP
     * @param string $name
     * @return Chunk
     */
    public function getBlendableChunk($name)
    {
        /** @var \LCI\Blend\Blendable\Chunk $chunk */
        $chunk = new Chunk($this->modx, $this->blender, $name);
        return $chunk->setSeedsDir($this->blender->getSeedsDir());
    }
    /**
     * @param array $chunks
     * @param string $seeds_dir
     */
    public function blendManyChunks($chunks = [], $seeds_dir = '')
    {
        // will update if element does exist or create new
        foreach ($chunks as $seed_key) {
            /** @var \LCI\Blend\Blendable\Chunk $blendChunk */
            $blendChunk = new Chunk($this->modx, $this->blender, $this->blender->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendChunk->setSeedsDir($seeds_dir);
            }
            if ($blendChunk->blendFromSeed($seed_key)) {
                $this->blender->out($seed_key.' has been blended into ID: ');

            } elseif ($blendChunk->isExists()) {
                $this->blender->out($seed_key.' chunk already exists', true);
                if ($this->userInteractionHandler->promptConfirm('Would you like to update?', true)) {
                    if ($blendChunk->blendFromSeed($seed_key, true)) {
                        $this->blender->out($seed_key.' has been blended');
                    }
                }
            } else {
                $this->blender->out('There was an error saving '.$seed_key, true);
            }
        }
    }

    /**
     * @param array $chunks
     * @param string $seeds_dir
     */
    public function revertBlendManyChunks($chunks = [], $seeds_dir = '')
    {
        // will update if system setting does exist or create new
        foreach ($chunks as $seed_key) {
            /** @var \LCI\Blend\Blendable\Chunk $blendChunk */
            $blendChunk = new Chunk($this->modx, $this->blender, $this->blender->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendChunk->setSeedsDir($seeds_dir);
            }

            if ($blendChunk->revertBlend()) {
                $this->blender->out($blendChunk->getFieldName().' chunk has been reverted to '.$seeds_dir);

            } else {
                $this->blender->out($blendChunk->getFieldName().' chunk was not reverted', true);
            }
        }
    }

    /**
     * Use this method with your IDE to help manually build a Chunk with PHP
     * @param string $key
     * @return Context
     */
    public function getBlendableContext($key)
    {
        /** @var \LCI\Blend\Blendable\Context $chunk */
        $context = new Context($this->modx, $this->blender, $key);
        return $context->setSeedsDir($this->blender->getSeedsDir());
    }

    /**
     * @param array $contexts
     * @param string $seeds_dir
     */
    public function blendManyContexts($contexts = [], $seeds_dir = '')
    {
        // will update if element does exist or create new
        foreach ($contexts as $seed_key) {
            /** @var \LCI\Blend\Blendable\Context $blendContext */
            $blendContext = new Context($this->modx, $this->blender, $this->blender->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendContext->setSeedsDir($seeds_dir);
            }
            if ($blendContext->blendFromSeed($seed_key)) {
                $this->blender->out($seed_key.' has been blended ');

            } elseif ($blendContext->isExists()) {
                $this->blender->out($seed_key.' context already exists', true);
                if ($this->userInteractionHandler->promptConfirm('Would you like to update?', true)) {
                    if ($blendContext->blendFromSeed($seed_key, true)) {
                        $this->blender->out($seed_key.' has been blended');
                    }
                }
            } else {
                $this->blender->out('There was an error saving '.$seed_key, true);
            }
        }
    }

    /**
     * @param array $contexts
     * @param string $seeds_dir
     */
    public function revertBlendManyContexts($contexts = [], $seeds_dir = '')
    {
        // will update if system setting does exist or create new
        foreach ($contexts as $seed_key) {
            /** @var \LCI\Blend\Blendable\Context $blendContext */
            $blendContext = new Context($this->modx, $this->blender, $this->blender->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendContext->setSeedsDir($seeds_dir);
            }

            if ($blendContext->revertBlend()) {
                $this->blender->out($blendContext->getFieldKey().' context has been reverted to '.$seeds_dir);

            } else {
                $this->blender->out($blendContext->getFieldKey().' context was not reverted', true);
            }
        }
    }


    /**
     * @param string $name
     * @return \LCI\Blend\Blendable\MediaSource
     */
    public function getBlendableMediaSource($name)
    {
        /** @var \LCI\Blend\Blendable\MediaSource $mediaSource */
        $mediaSource = new MediaSource($this->modx, $this->blender, $name);
        return $mediaSource
            ->setFieldName($name)
            ->setSeedsDir($this->blender->getSeedsDir());
    }

    /**
     * @param array $media_sources
     * @param string $seeds_dir
     */
    public function blendManyMediaSources($media_sources = [], $seeds_dir = '')
    {
        // will update if element does exist or create new
        foreach ($media_sources as $seed_key) {
            /** @var \LCI\Blend\Blendable\MediaSource $blendMediaSource */
            $blendMediaSource = new MediaSource($this->modx, $this->blender);
            if (!empty($seeds_dir)) {
                $blendMediaSource->setSeedsDir($seeds_dir);
            }
            if ($blendMediaSource->blendFromSeed($seed_key)) {
                $this->blender->out($seed_key.' has been blended into ID: ');

            } elseif ($blendMediaSource->isExists()) {
                $this->blender->out($seed_key.' media source already exists', true);
                if ($this->userInteractionHandler->promptConfirm('Would you like to update?', true)) {
                    if ($blendMediaSource->blendFromSeed($seed_key, true)) {
                        $this->blender->out($seed_key.' has been blended');
                    }
                }
            } else {
                $this->blender->out('There was an error saving '.$seed_key, true);
            }
        }
    }

    /**
     * @param array $media_sources
     * @param string $seeds_dir
     */
    public function revertBlendManyMediaSources($media_sources = [], $seeds_dir = '')
    {
        // will update if system setting does exist or create new
        foreach ($media_sources as $seed_key) {
            /** @var \LCI\Blend\Blendable\MediaSource $blendMediaSource */
            $blendMediaSource = new MediaSource($this->modx, $this->blender, $this->blender->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendMediaSource->setSeedsDir($seeds_dir);
            }

            if ($blendMediaSource->revertBlend()) {
                $this->blender->out($blendMediaSource->getFieldName().' media source has been reverted to '.$seeds_dir);

            } else {
                $this->blender->out($blendMediaSource->getFieldName().' media source was not reverted', true);
            }
        }
    }

    /**
     * Use this method with your IDE to help manually build a Plugin with PHP
     * @param string $name
     * @return \LCI\Blend\Blendable\Plugin
     */
    public function getBlendablePlugin($name)
    {
        /** @var \LCI\Blend\Blendable\Plugin $plugin */
        $plugin = new Plugin($this->modx, $this->blender, $name);
        return $plugin->setSeedsDir($this->blender->getSeedsDir());
    }

    /**
     * @param array $plugins
     * @param string $seeds_dir
     */
    public function blendManyPlugins($plugins = [], $seeds_dir = '')
    {
        // will update if element does exist or create new
        foreach ($plugins as $seed_key) {
            /** @var \LCI\Blend\Blendable\Plugin $blendPlugin */
            $blendPlugin = new Plugin($this->modx, $this->blender, $this->blender->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendPlugin->setSeedsDir($seeds_dir);
            }
            if ($blendPlugin->blendFromSeed($seed_key)) {
                $this->blender->out($seed_key.' has been blended into ID: ');

            } elseif ($blendPlugin->isExists()) {
                $this->blender->out($seed_key.' plugin already exists', true);
                if ($this->userInteractionHandler->promptConfirm('Would you like to update?', true)) {
                    if ($blendPlugin->blendFromSeed($seed_key, true)) {
                        $this->blender->out($seed_key.' has been blended');
                    }
                }
            } else {
                $this->blender->out('There was an error saving '.$seed_key, true);
            }
        }
    }

    /**
     * @param array $plugins
     * @param string $seeds_dir
     */
    public function revertBlendManyPlugins($plugins = [], $seeds_dir = '')
    {
        // will update if system setting does exist or create new
        foreach ($plugins as $seed_key) {
            /** @var \LCI\Blend\Blendable\Plugin $blendPlugin */
            $blendPlugin = new Plugin($this->modx, $this->blender, $this->blender->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendPlugin->setSeedsDir($seeds_dir);
            }

            if ($blendPlugin->revertBlend()) {
                $this->blender->out($blendPlugin->getFieldName().' plugin has been reverted to '.$seeds_dir);

            } else {
                $this->blender->out($blendPlugin->getFieldName().' plugin was not reverted', true);
            }
        }
    }

    /**
     * Use this method with your IDE to help manually build a Snippet with PHP
     * @param string $name
     * @return \LCI\Blend\Blendable\Snippet
     */
    public function getBlendableSnippet($name)
    {
        /** @var Snippet $snippet */
        $snippet = new Snippet($this->modx, $this->blender, $name);
        return $snippet->setSeedsDir($this->blender->getSeedsDir());
    }

    /**
     * @param array $snippets
     * @param string $seeds_dir
     */
    public function blendManySnippets($snippets = [], $seeds_dir = '')
    {
        // will update if element does exist or create new
        foreach ($snippets as $seed_key) {
            /** @var \LCI\Blend\Blendable\Snippet $blendSnippet */
            $blendSnippet = new Snippet($this->modx, $this->blender, $this->blender->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendSnippet->setSeedsDir($seeds_dir);
            }
            if ($blendSnippet->blendFromSeed($seed_key)) {
                $this->blender->out($seed_key.' has been blended');

            } elseif ($blendSnippet->isExists()) {
                $this->blender->out($seed_key.' snippet already exists', true);
                if ($this->userInteractionHandler->promptConfirm('Would you like to update?', true)) {
                    if ($blendSnippet->blendFromSeed($seed_key, true)) {
                        $this->blender->out($seed_key.' has been blended');
                    }
                }
            } else {
                $this->blender->out('There was an error saving '.$seed_key, true);
            }
        }
    }
    /**
     * @param array $snippets
     * @param string $seeds_dir
     */
    public function revertBlendManySnippets($snippets = [], $seeds_dir = '')
    {
        // will update if system setting does exist or create new
        foreach ($snippets as $seed_key) {
            /** @var Snippet $blendSnippet */
            $blendSnippet = new Snippet($this->modx, $this->blender, $this->blender->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendSnippet->setSeedsDir($seeds_dir);
            }

            if ($blendSnippet->revertBlend()) {
                $this->blender->out($blendSnippet->getFieldName().' snippet has been reverted to '.$seeds_dir);

            } else {
                $this->blender->out($blendSnippet->getFieldName().' snippet was not reverted', true);
            }
        }
    }

    /**
     * Use this method with your IDE to manually build a template
     * @param string $name
     * @return \LCI\Blend\Blendable\Template
     */
    public function getBlendableTemplate($name)
    {
        /** @var \LCI\Blend\Blendable\Template $template */
        $template = new Template($this->modx, $this->blender, $name);
        return $template->setSeedsDir($this->blender->getSeedsDir());
    }

    /**
     * @param array $templates
     * @param string $seeds_dir
     * @param bool $overwrite
     */
    public function blendManyTemplates($templates = [], $seeds_dir = '', $overwrite = false)
    {
        // will update if template does exist or create new
        foreach ($templates as $seed_key) {

            /** @var \LCI\Blend\Blendable\Template $blendTemplate */
            $blendTemplate = new Template($this->modx, $this->blender, $this->blender->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendTemplate->setSeedsDir($seeds_dir);
            }
            if ($blendTemplate->blendFromSeed($seed_key, $overwrite)) {
                $this->blender->out($seed_key.' has been blended');

            } elseif ($blendTemplate->isExists()) {
                $this->blender->out($seed_key.' template already exists', true);
                if ($this->userInteractionHandler->promptConfirm('Would you like to update?', true)) {
                    if ($blendTemplate->blendFromSeed($seed_key, true)) {
                        $this->blender->out($seed_key.' has been blended');
                    }
                }
            } else {
                $this->blender->out('There was an error saving '.$seed_key, true);
            }
        }
    }

    /**
     * @param array $templates
     * @param string $seeds_dir
     */
    public function revertBlendManyTemplates($templates = [], $seeds_dir = '')
    {
        // will update if system setting does exist or create new
        foreach ($templates as $seed_key) {
            /** @var \LCI\Blend\Blendable\Template $blendTemplate */
            $blendTemplate = new Template($this->modx, $this->blender, $this->blender->getNameFromSeedKey($seed_key));
            if (!empty($seeds_dir)) {
                $blendTemplate->setSeedsDir($seeds_dir);
            }

            if ($blendTemplate->revertBlend()) {
                $this->blender->out($blendTemplate->getFieldName().' template has been reverted to '.$seeds_dir);

            } else {
                $this->blender->out($blendTemplate->getFieldName().' template was not reverted', true);
            }
        }
    }

    /**
     * Use this method with your IDE to manually build a template variable
     * @param string $name
     * @return TemplateVariable
     */
    public function getBlendableTemplateVariable($name)
    {
        /** @var \LCI\Blend\Blendable\TemplateVariable $tv */
        $tv = new TemplateVariable($this->modx, $this->blender, $name);
        return $tv->setSeedsDir($this->blender->getSeedsDir());
    }

    /**
     * @param string $alias
     * @param  string $context
     * @return \LCI\Blend\Blendable\Resource
     */
    public function getBlendableResource($alias, $context = 'web')
    {
        /** @var \LCI\Blend\Blendable\Resource $resource */
        $resource = new Resource($this->modx, $this->blender, $alias, $context);
        return $resource
            ->setSeedsDir($this->blender->getSeedsDir());
    }
    /**
     * @param array $resources
     * @param string $seeds_dir
     * @param bool $overwrite
     *
     * @return bool
     */
    public function blendManyResources($resources = [], $seeds_dir = '', $overwrite = false)
    {
        $saved = true;
        // will update if resource does exist or create new
        foreach ($resources as $context => $seeds) {
            foreach ($seeds as $seed_key) {
                /** @var \LCI\Blend\Blendable\Resource $blendResource */
                $blendResource = new Resource($this->modx, $this->blender, $this->blender->getAliasFromSeedKey($seed_key), $context);

                if (!empty($seeds_dir)) {
                    $blendResource->setSeedsDir($seeds_dir);
                }

                if ($blendResource->blendFromSeed($seed_key, $overwrite)) {
                    $this->blender->out($seed_key.' has been blended into ID: ');

                } elseif ($blendResource->isExists()) {
                    $this->blender->out($seed_key.' already exists', true);
                    if ($this->userInteractionHandler->promptConfirm('Would you like to update?', true)) {
                        if ($blendResource->blendFromSeed($seed_key, true)) {
                            $this->blender->out($seed_key.' has been blended into ID: ');
                        }
                    }
                } else {
                    $this->blender->out('There was an error saving '.$seed_key, true);
                    echo 'There was an error saving '.$seed_key; exit();
                    $saved = false;
                }
            }
        }

        return $saved;
    }

    /**
     * @param array $resources
     * @param string $seeds_dir
     * @param bool $overwrite
     *
     * @return bool
     */
    public function revertBlendManyResources($resources = [], $seeds_dir = '', $overwrite = false)
    {
        $saved = true;
        // will update if resource does exist or create new
        foreach ($resources as $context => $seeds) {
            foreach ($seeds as $seed_key) {
                /** @var \LCI\Blend\Blendable\Resource $blendResource */
                $blendResource = new Resource($this->modx, $this->blender, $this->blender->getAliasFromSeedKey($seed_key), $context);

                if (!empty($seeds_dir)) {
                    $blendResource->setSeedsDir($seeds_dir);
                }
                if ($blendResource->revertBlend()) {
                    $this->blender->out($seed_key.' has been reverted ');

                } else {
                    $this->blender->out('There was an error reverting resource '.$seed_key, true);
                    $saved = false;
                }
            }
        }

        return $saved;
    }

    /**
     * @param string $key
     * @return \LCI\Blend\Blendable\SystemSetting
     */
    public function getBlendableSystemSetting($key = '')
    {
        /** @var \LCI\Blend\Blendable\SystemSetting $systemSetting */
        $systemSetting = new SystemSetting($this->modx, $this->blender, $key);
        return $systemSetting->setSeedsDir($this->blender->getSeedsDir());
    }

    /**
     * @param array $settings ~ [ ['name' => 'mySystemSetting', 'value' => 'myValue'], ..]
     * @param string $seeds_dir
     *
     * @return bool
     */
    public function blendManySystemSettings($settings = [], $seeds_dir = '')
    {
        $success = true;
        // will update if system setting does exist or create new
        foreach ($settings as $data) {
            if (isset($data['columns'])) {
                $setting = $data['columns'];
            } else {
                $setting = $data;
                $data['columns'] = $data;
            }

            if (isset($setting['key'])) {
                $key = $setting['key'];

            } elseif (isset($setting['name'])) {
                $key = $setting['name'];

            } else {
                // Error: no name/key
                $success = false;
                continue;
            }

            $systemSetting = $this->getBlendableSystemSetting($key);
            if (!empty($seeds_dir)) {
                $systemSetting->setSeedsDir($seeds_dir);
            }

            if ($systemSetting->blendFromArray($data, true)) {
                $this->blender->out($systemSetting->getFieldName().' setting has been blended');
            } else {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * @param array $settings ~ [ ['name' => 'mySystemSetting', 'value' => 'myValue'], ..]
     * @param string $seeds_dir
     *
     * @return bool
     */
    public function revertBlendManySystemSettings($settings = [], $seeds_dir = '')
    {
        $success = true;
        // will update if system setting does exist or create new
        foreach ($settings as $data) {
            if (isset($data['columns'])) {
                $setting = $data['columns'];
            } else {
                $setting = $data;
                $data['columns'] = $data;
            }

            if (isset($setting['key'])) {
                $key = $setting['key'];

            } elseif (isset($setting['name'])) {
                $key = $setting['name'];

            } else {
                // Error: no name/key
                $success = false;
                continue;
            }

            $systemSetting = $this->getBlendableSystemSetting($key);

            if (!empty($seeds_dir)) {
                $systemSetting->setSeedsDir($seeds_dir);
            }

            if ($systemSetting->revertBlend()) {
                $this->blender->out($systemSetting->getFieldName().' setting has been reverted to '.$seeds_dir);

            } else {
                $this->blender->out($systemSetting->getFieldName().' setting was not reverted', true);
                $success = false;
            }
        }

        return $success;
    }

}