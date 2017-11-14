# Blend

This project aims to import/export resources and elements from one MODX instance to another MODX. Working with 
workflows from local to dev to stating to production or master(git). Also attempting to be similar to what Migrate is 
for Laravel. 

**Warning this is a CLI tool only. Most code references are for SSH/command line/terminal.**

## Goals

1. IDE driven, build out a blank migration file and use PHPStorm to help you out, no vague arguments. 
In the up or down methods:
```$this->blender->blend...```
2. To make VCS(git) workflows easily between local, remote, dev, staging and production
3. Abstracting IDs from xPDO relationships for storage. Allow passing of MODX resources(site content) and TVs 
independent of IDs but becoming dependant on alias, the alias must them be unique across servers.
4. Build a MODX dev box/branch that will load in all essential data and create as many users and user groups as 
needed for testing.
5. Allowing smooth rollbacks, if dev to prod failed, roll it back to the latest know working version

## Use case

Example 1: You have a local or dev set up and you make some new resources, snippets, chunks, ect.
But there is a constant creation of resources on the production site from the content creation team. You cannot stop 
content production while you could up the next new feature. Now with Blend it is easy to add in your new feature independent
of the resource IDs. 

Example 2: You are going to do a complete new version of you site and you want to keep a few things from the old site. 
So you export what is needed and then torch the rest. Now you can import into a new MODX install.

## Install

### Step 1
Via git
1. CD into the directory that you want the project to live in. Can be anywhere PHP runs, one place could be outside of 
your public or www directory. Or if you prefer the traditional MODX extras path then in MODX/core/components/blend/
2. ```git clone git@github.com:LippertComponents/Blend.git .```

**OR**

Via composer
1. Add to your projects composer.json file:
```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/LippertComponents/Blend"
    }
]
```
2. Then do ```composer install``` or ```composer update```

### Step 2
Go the bin directory, you may need to copy the sample.config.php file to just config.php and then set the correct path to MODX.

### Step 3
CD to the bin directory and try 

```php BlenderCli.php```

You should see a help menu, then do ```php BlenderCli.php -i``` to install.


## Examples

1. Create a blank migration file: ```php BlenderCli.php -b -n MyChunks```
2. There will now be a file: ```MODX~core/components/blend/database/migrations/MyChunks.php``` open in your IDE
3. You will want to something like below for the up method. The up method creates or updates:

```php
<?php
// Manual set up of a chunk:
/** @var \LCI\Chunk $myChunk */
$myChunk = $this->blender->blendOneRawChunk('myChunk');
$myChunk
    ->setDescription('This is my test chunk, note this is limited to 255 or something')
    ->setCategoryFromNames('My Site=>Chunks')
    ->setCode('[[+testPlaceholder]]')// could do file_get_contents()
    //need the relative to the MODX root path here, or whatever lines up with media source ID: 1
    ->setAsStatic('core/components/mysite/elements/chunks/myChunk.tpl');

if ($myChunk->save()) {
    $this->blender->out($myChunk->getName().' was saved correctly');

} else {
    //error
    $this->blender->out($myChunk->getName().' did not save correctly ', true);
    $this->blender->out(print_r($myChunk->getErrorMessages(), true), true);
}
```

4. To allow for a rollback you will need to code the exact oppisite in the down method. 
The down method is for remove or downgrade.
```php
<?php
// just delete the chunk:
/** @var bool|\modChunk $myChunk */
$name = 'myChunk';
$myChunk = $this->modx->getObject('modChunk', ['name' => $name]);
if ($myChunk instanceof \modChunk) {
    if ($myChunk->remove()) {
        $this->blender->out($name.' has been removed');
    } else {
        $this->blender->out($name.' could not be removed', true);
    }
}
```

5. Now save your file and then you can test is out by running all migrations:

```php BlenderCli.php -m```

And then roll it back:

```php BlenderCli.php -m -x down```

---

In this example you want to export some templates and the related TVs.

1. Run ```php BlenderCli.php -t``` you will then be prompted for enter in a comma separated list of template IDs or names
Yes you can mix and match.

2. Something like ```MODX~core/components/blend/database/migrations/m2017_11_14_160105_Template.php``` will be created along 
with the a new directory:

```MODX~core/components/blend/database/seeds/2017_11_14_160105/elements``` 

This directory holds both the Template and the dependant TV seeds. You could run the migration but not much should happen
since you just exported the data. But if you moved the directories to another MODX install and ran the migration they 
should now show there.

3. If you want to customize the content on export write a 
[plugin](https://docs.modx.com/revolution/2.x/developing-in-modx/basic-development/plugins), these event are available:

   - OnBlendSeedElement
   - OnBlendELementBeforeSave
   - OnBlendElementAfterSave

---

In this example export some resources

1. Run ```php BlenderCli.php -r``` you will then be prompted for enter in a comma separated list of resource IDs to 
include.

2. Something like ```MODX~core/components/blend/database/migrations/m2017_11_14_162358_Resource.php``` will be created along 
with the a new directory:

```MODX~core/components/blend/database/seeds/2017_11_14_162358/resources``` 

This directory holds both the resource seeds. You could run the migration but not much should happen
since you just exported the data. But if you moved the directories to another MODX install and ran the migration they 
should show up there.

3. If you want to customize the content on export write a 
[plugin](https://docs.modx.com/revolution/2.x/developing-in-modx/basic-development/plugins), these event are available:

    - OnBlendResourceBeforeSave
    - OnBlendResourceAfterSave
    - OnBlendSeedResource
    - OnBlendSeedResource


