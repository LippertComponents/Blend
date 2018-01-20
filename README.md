# Blend

This project aims to import/export resources and elements from one MODX instance to another MODX. Working with 
workflows from local > dev > staging > production(master). Also attempting to be similar to what Migrate is 
for Laravel. 

**This is a CLI tool. Most code references are for SSH/command line/terminal. To create custom Migrations you 
should be knowledgeable of PHP and MODX. A good IDE like PHPStorm will help you auto complete available methods for 
custom migrations.**

## Goals

1. IDE driven, build out a blank migration file and then be able to use an IDE like PHPStorm to help you out. And 
provide convenience methods to speed up the process and make it consistent. 
2. To make VCS(git) workflows easily between local, remote, dev, staging and production
3. Abstracting IDs from xPDO relationships for storage. Allow passing of MODX resources(site content) and TVs 
independent of IDs but becoming dependant on alias, the alias must then be unique across environments.
4. Build a MODX dev box/branch that will load in all essential data and create as many users and user groups as 
needed for testing.
5. Allowing smooth rollbacks, if dev to prod failed, roll it back to the latest know working version

## Use case

Example 1: You have a local or dev set up and you make some new resources, snippets, chunks, ect.
But there is a constant creation of resources on the production site from the content creation team. You cannot ask the 
content production team to stop for days or weeks while you coe up the next new feature. Now with Blend it is easy to 
add in your new feature independent of the resource IDs. 

Example 2: You are going to do a complete new version of you site and you want to keep a few things from the old site. 
So you export what is needed and then torch the rest. Now you can modify the exported seeds and then import into a 
new MODX install.

## Introduction

- What are Migrations?  
  Think of migrations as creating instructions for a data to be imported or modified.
  Migrations can be used like version control for the MODX database, allowing your team to easily modify and share the changes to 
  elements (chunks, plugins, snippets, templates and template variables), resource(pages) and system settings as well as your 
  custom tables. If you manually migrate a MODX element from dev to production, then migrations will help you track and insure 
  consistent results.
- What are Seeds?  
  Currently seeds are generated files that contain the selected elements (chunks, plugins, snippets, templates and 
  template variables), resource(pages) and/or system settings as data exports of your system that can be used in another.
  For example moving from development server to production. 


## Install

### Step 1

Via composer, just run: ```composer require lci/blend```

**OR**

Via git
1. CD into the directory that you want the project to live in. Can be anywhere PHP runs, one place could be outside of 
your public or www directory. Or if you prefer the traditional MODX extras path then in MODX/core/components/blend/
2. ```git clone git@github.com:LippertComponents/Blend.git .```



### Step 2
Go the bin directory, you may need to copy the sample.config.php file to just config.php and then set the correct path to MODX.

### Step 3
CD to the bin directory and try 

If you used git: ```php BlenderCli.php``` or ```php BlenderCli.php --help```

If you used composer ```php blend``` or ```php blend --help```

You should see a help menu, then do ```php blend --install``` to install. 

Migration help & usage info ```php blend --help --migrate``` or using the short command
```php blend -m -h``` and for seeds ```php blend --help --seed``` or with the short command ```php blend -s -h```


## Examples

See the [tests/database/migrations](tests/database/migrations) directory for basic examples.

### Seed complete site 

Create a Migration and seeds for all elements, resources and system settings:  
```php blend --seed --object site --name InitSite```

Same as above, but in short command  
```php blend -s -o a -n InitSite```

### Create a custom migration file

1. Generate an empty migration file:  
 ```php blend --migrate --generate --name MyChunks```  
 Same as above but in short format
 ```php blend -m -g -n MyChunks```
2. There will now be a file: ```MODX~core/components/blend/database/migrations/MyChunks.php``` open in your IDE
3. You will want to something like below for the up method. The up method creates or updates:  
```php
<?php
// Manual set up of a chunk:
/** @var \LCI\Chunk $myChunk */
$myChunk = $this->blender->blendOneRawChunk('myChunk');
$myChunk
    ->setSeedTimeDir($this->getTimestamp())// This is needed to set the down() data
    ->setDescription('This is my test chunk, note this is limited to 255 or something')
    ->setCategoryFromNames('My Site=>Chunks')
    ->setCode('[[+testPlaceholder]]')// could do file_get_contents()
    //need the relative to the MODX root path here, or whatever lines up with media source ID: 1
    ->setAsStatic('core/components/mysite/elements/chunks/myChunk.tpl');

// The blend() method will create a back/down data before saving to allow for easy revert with the revertBlend method
if ($myChunk->blend(true)) {
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
// Allow Blend to retrieve the data exactly how it was before the up() method did a blend()
$name = 'myChunk';

$blendChunk = new \LCI\Blend\Chunk($this->modx, $this->blender);
$blendChunk
    ->setName($name)
    ->setSeedTimeDir($this->getTimestamp());// This is needed to retrieve the down data

if ( $blendChunk->revertBlend() ) {
    $this->blender->out($blendChunk->getName().' setting has been reverted to '.$this->getTimestamp());

} else {
    $this->blender->out($blendChunk->getName().' setting was not reverted', true);
}
```
5. Now save your file and then you can test is out by running all migrations:  
 ```php blend --migrate```  
 Same as above but in short format
 ```php blend -m```  
 And then roll it back:  
 ```php blend --migrate --method down``` or in short format ```php blend -m -x down```

---

### Templates & related TVs
In this example you want to export some templates and the related TVs.

1. Run ```php blend --seed --object template``` or short command ```php blend -s -o t``` you will then be prompted to
 enter in a comma separated list of template IDs or names that you wish to seed. Note you can also use the ```--name MyName```
 option if you don't want the timestamped names and directories. 
2. Something like ```MODX~core/components/blend/database/migrations/m2017_11_14_160105_Template.php``` will be created along 
with the a new directory containing the seed data:  
```MODX~core/components/blend/database/seeds/2017_11_14_160105/elements```  
This directory holds both the Template and the dependant TV seeds. You could run the migration but not much should happen
since you just exported the data. But if you moved the directories to another MODX install and ran the migration they 
should now show there.  
3. If you want to customize the content on export before the seeds are created you can write a 
[plugin](https://docs.modx.com/revolution/2.x/developing-in-modx/basic-development/plugins), these event are available:

   - OnBlendSeedElement
   - OnBlendELementBeforeSave
   - OnBlendElementAfterSave

---

### Resource seeds

Select Resources, for prompt to ask for comma separated list of IDs  
```php blend --seed --object resource```

Only do resource with ID 2  
```php blend --seed --object resource --id 2```

Only do resources that have been created or modified since 2018-01-01  
```php blend --seed --object resource --date 2018-01-01```

Name your seeds to a version or bug number:  
```php blend --seed --object resource --date 2018-01-01 --name Issue1234```

In this example export some resources

1. Run ```php blend --seed``` you will then be prompted for enter in a comma separated list of resource IDs to 
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

## Road map 

- v1.0 
  - Update/Install MODX
  - Add TV convenience methods for building custom migrations 
  - Media Sources, seed and migrate
  - Convert all System Setting to have values that are name key vs int key, templates, resources, ect.
- v1.1
  - Extras
  - List Migrations in Table, newest to oldest
- v1.2
  - Resource Groups
  - Seed ACL, user groups & permissions
  - Form customizations (via template relations?)

### Development, Running Tests

From the command line, assuming you have cloned this repo from git and you are in the project root directory. 

**All Test**  
```vendor\bin\phpunit```

**Select Test**  
 - ```vendor\bin\phpunit --bootstrap tests\bootstrap.php tests\BlendTest.php```
 - ```vendor\bin\phpunit --bootstrap tests\bootstrap.php tests\ChunkTest.php```
 - ```vendor\bin\phpunit --bootstrap tests\bootstrap.php tests\PluginTest.php```
 - ```vendor\bin\phpunit --bootstrap tests\bootstrap.php tests\ResourceTest.php```
 - ```vendor\bin\phpunit --bootstrap tests\bootstrap.php tests\SnippetTest.php```
 - ```vendor\bin\phpunit --bootstrap tests\bootstrap.php tests\SystemSettingsTest.php```
 - ```vendor\bin\phpunit --bootstrap tests\bootstrap.php tests\TemplateTest.php```


