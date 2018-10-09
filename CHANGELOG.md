## [1.0.0 beta16] - 2018=10-09

- Fix Blendable/Template->attachTemplateVariable() + Blendable/TemplateVariable->attachToTemplate() to first check if already attached
- Minor code clean up on Blendable/Template added detachTemplateVariable() to replace detachTV() + clean up Blendable/TemplateVariable

## [1.0.0 beta15] - 2018=10-09

- Fix Resource->setTVValue to properly function when a string is passed
- Add test and asserts to better check Resource setTVValue method and setFieldParentFromAlias
- Move Blend Install/Update to src/database/migrations to match Orchestrator guidelines

## [1.0.0 beta14] - 2018=10-05

- Fix #8 update to beta13 from previous versions, delay logging until after DB table has been updated to the correct version
- Added Blender->getResourceIDFromLocalAlias() 
- Added Blendable/Resource->setFieldParentFromAlias()
- Added Blendable/Resource->setTVValue()
- Added Blendable/Resource->setTVValueResourceIDFromAlias()
- Added Blendable/Resource->setTVValueMediaSourceIDFromName()
- Added Blendable/Resource->setTVValueTemplateIDFromName()

## [1.0.0 beta13] - 2018=10-03

- Command option -n has changed to -N for -name
- Fix #7 add -v for verbose, can now use $this->blender->out('Message', \LCI\Blend\Blender::VERBOSITY_DEBUG); in your migrations.
- Add MigrationException class
- Refactored BlendableLoader to reduce complexity
- Update tests to reflect Migrator changes plus a few test fixes
- Refactor Blend, extract runMigration code to new class Migrator
- Add columns project and ran_sequence to the blend_migrations table + Remove old blend update migrations 
- Add register Console commands migration file
- Created Helper/BlendableLoader, from extracted related methods that where in Blender.  
Example: $blender->getBlendableChunk() should now be $blender->getBlendableLoader()->getBlendableChunk()
- Deprecated many methods in Blender

 Deprecated | Updated use
 --- | ----
 Blender->blendManyChunks() | Blender->getBlendableLoader()->blendManyChunks() 
 Blender->blendManyContexts() | Blender->getBlendableLoader()->blendManyContexts() 
 Blender->blendManyMediaSources() | Blender->getBlendableLoader()->blendManyMediaSources() 
 Blender->blendManyPlugins() | Blender->getBlendableLoader()->blendManyPlugins() 
 Blender->blendManyResources() | Blender->getBlendableLoader()->blendManyResources() 
 Blender->blendManySnippets() | Blender->getBlendableLoader()->blendManySnippets() 
 Blender->blendManySystemSettings() | Blender->getBlendableLoader()->blendManySystemSettings() 
 Blender->blendManyTemplates() | Blender->getBlendableLoader()->blendManyTemplates() 
 Blender->getBlendableChunk() | Blender->getBlendableLoader()->getBlendableChunk() 
 Blender->getBlendableContext() | Blender->getBlendableLoader()->getBlendableContext() 
 Blender->getBlendableMediaSource() | Blender->getBlendableLoader()->getBlendableMediaSource() 
 Blender->getBlendablePlugin() | Blender->getBlendableLoader()->getBlendablePlugin() 
 Blender->getBlendableResource() | Blender->getBlendableLoader()->getBlendableResource() 
 Blender->getBlendableSnippet() | Blender->getBlendableLoader()->getBlendableSnippet() 
 Blender->getBlendableSystemSetting() | Blender->getBlendableLoader()->getBlendableSystemSetting() 
 Blender->getBlendableTemplate() | Blender->getBlendableLoader()->getBlendableTemplate() 
 Blender->getBlendableTemplateVariable() | Blender->getBlendableLoader()->getBlendableTemplateVariable() 
 Blender->revertBlendManyChunks() | Blender->getBlendableLoader()->revertBlendManyChunks() 
 Blender->revertBlendManyContexts() | Blender->getBlendableLoader()->revertBlendManyContexts() 
 Blender->revertBlendManyMediaSources() | Blender->getBlendableLoader()->revertBlendManyMediaSources() 
 Blender->revertBlendManyPlugins() | Blender->getBlendableLoader()->revertBlendManyPlugins() 
 Blender->revertBlendManyResources() | Blender->getBlendableLoader()->revertBlendManyResources() 
 Blender->revertBlendManySnippets() | Blender->getBlendableLoader()->revertBlendManySnippets() 
 Blender->revertBlendManySystemSettings() | Blender->getBlendableLoader()->revertBlendManySystemSettings() 
 Blender->revertBlendManyTemplates() | Blender->getBlendableLoader()->revertBlendManyTemplates() 

## [1.0.0 beta12] - 2018=09-29

- Add BLEND_COPY_TEST_MIGRATION_FILES in tests/config.php, fix to run tests on scrutinizer 
- Fix reverting TVs to previous state on a new seeded Template Object that gets reverted
- Fix #5 seed raw values of TVs  

## [1.0.0 beta11] - 2018=09-25

- Fix Blender to use promptConfirm method 

## [1.0.0 beta10] - 2018=09-25

- Fix for migration paths to have proper directory separator 
- Use MODX_CORE_PATH as base path for GenerateMigrations to allow Blend to still work as standalone out of the box
- Fix Blend ascii logo path

## [1.0.0 beta9] - 2018=09-25

- Remove unneeded dependencies from composer.json

## [1.0.0 beta8] - 2018=09-15

- Fix seedToArray remove call to seedRelated on non existing xPDO Object

## [1.0.0 beta7] - 2018=09-13

- Refactor Blender, pulled out makeSeeds Methods into SeedMaker class
- Remove MODX install related code

## [1.0.0 beta6] - 2018=09-12

- Replace config.php files with .env
- Refactored Blender, pulled out MigrationsCreator and Format 
- Added command GenerateMigration to run independent of MODX
- Removed the generate option fo the Migrate command
- Remove MODX install and update and related migrations. Blend will not do this for 2.x & 3.x

## [1.0.0 beta] - 2018-03-20

 - 497c55b - 2018-03-20 : Add in v1.0.0 beta update and version
 - 8983410 - 2018-03-20 : Remove deprecated code: getTimestamp & setSeedTimeDir
 - 55415f7 - 2018-03-20 : Add new event: OnBlendLoadRelatedData
 - 231c715 - 2018-03-20 : Make SystemSettings Blendable
 - fe7af55 - 2018-03-20 : Refactor blender->getSeedsDirectory + getMigrationDirectory to be getSeedsPath + getMigrationDirectory
 - 3475c1f - 2018-03-19 : Minor update to README, updated migration example code
 - e2a0de4 - 2018-03-19 : Add MediaSourceTest to the phpunit.xml
 - 3abafb5 - 2018-03-19 : Update Blendable\Resource to pass tests
 - 5607569 - 2018-03-19 : Update PHP doc on Properties
 - 76cc505 - 2018-03-19 : Minor fix for Plugin to match updated Blendable
 - c8c1b4b - 2018-03-19 : Make Template and TemplateVariables Blendable
 - afd2538 - 2018-03-19 : Move TemplateVariable into Blendable
 - d4957c5 - 2018-03-17 : Move Template into Blendable
 - 1d5a453 - 2018-03-17 : Make Plugin Blendable
 - 80c4093 - 2018-03-17 : Move Plugin to Blendable
 - 694050d - 2018-03-17 : Make Chunk Blendable
 - 2343ee7 - 2018-03-17 : move Chunk to Blendable
 - 967341b - 2018-03-17 : Update .gitignore
 - b7322c2 - 2018-03-17 : Finish commit for making Resource Blendable
 - 6ddc7c9 - 2018-03-17 : Make Elements and Snippets Blendable
 - 88f431b - 2018-03-16 : Make Resources Blendable
 - c8fc563 - 2018-03-14 : Refactor MediaSource and tests to match refactored Blendable
 - ca54610 - 2018-03-14 : Refactored Blendable and BlendableInterface, simplify for easier extendability
 - d725ee4 - 2018-03-13 : Add new Trait Helpers/Files, refactor DownloadModxVersion and Simple cache to use. BaseBlend uses SimpleCache to clean up down migration files
 - 87ac305 - 2018-03-13 : Add MediaSource tests
 - 9d823b2 - 2018-03-13 : Add Blendable/MediaSource
 - 74fdd9b - 2018-03-13 : Add BlendableInterface, abstract Blendable and traits: BlendableProperties and DescriptionGetterAndSetter
 - 280c507 - 2018-03-02 : Update README to 1.0.0-dev
 - 0c30a83 - 2018-03-02 : Remove unneeded files
 - 45ccb0a - 2018-03-02 : Merge branch 'master' into dev
 - 9a69888 - 2018-03-02 : Merge master
 - 4401d0f - 2018-03-02 : Add MODX 3.x db transport files, temp/legacy
 - c70bc06 - 2018-03-02 : Hide MODXUpdate and Install Package, not complete
 - 5205e29 - 2018-03-02 : Downgrade src minimum php version from 7 to 5.6
 - 8256db7 - 2018-03-02 : Downgrade tests minimum version 7 to 5.6
 - 7636504 - 2018-03-02 : Update test/sample.config.php
 - 4ecfe70 - 2018-03-02 : Correct xPDO2 Blend package name for MODX 2.x
 - 7784a18 - 2018-03-02 : Move migraiton templates to Migrations/templates
 - e61db9f - 2018-03-02 : Set blend object correctly for uninstall blend
 - b940693 - 2018-03-02 : Move blend setup/install/update migrations
 - 55c9e93 - 2018-03-02 : Add MODX install for 3.x direct from Git, no build
 - 6766ef8 - 2018-02-28 : Merge branch 'dev' into mx
 - d705422 - 2018-02-28 : Blend to choose correct xPDO model version
 - 1e8f53b - 2018-02-28 : Add xPDO v3 blend map files and move v2 to xpdo2
 - 5166b86 - 2018-02-27 : MODX Install code, not complete
 - ceb6511 - 2018-02-16 : Remove old BlenderCli class
 - 23238e9 - 2018-02-16 : Change to the Sypfony Console project for all CLI actions, drop CLImate
 - 24dbf69 - 2018-02-16 : Fixed grammatical issue. (#2)
 - 7f56e39 - 2018-02-15 : Updated ReadMe (#1)
 - 84f0a3b - 2018-02-15 : Add art work
 
## [0.9.11] - 2018-02-12
 
 - 29758b4 - 2018-02-14 : Update to v0.9.11
 - b85843d - 2018-02-14 : Add getCurrentValue method for SystemSettings

## [0.9.10] - 2018-02-12

- Add getCurrentValue method for SystemSettings, this method returns the current value of the system setting before blend/save

## [0.9.10] - 2018-02-12

- Resources sorted by context directories
- Create resource groups if they do not exist add attach resource, no ACLs are created
- Improve ResourceTest

## [0.9.9] - 2018-02-10

- Add TemplateTVTest
- Fix TVs to seed and blend with elements data 
- Add related data to revert process, Template=>TVs now revert

## [0.9.8] - 2018-02-08

- Fix site migration template with proper method name

## [0.9.7] - 2018-02-08

 - Fix for not setting code/content on elements if they are set as static, overwrite is now an option
 - Finish matching the migration file name to the seeds directory name
 - Add Resource Groups to resource seeds
 - Refactor to so that seeds directory matches the name of the migration file
 - Refactor timestamp to seeds_dir
 - Added version info, author to Migrations and a refresh cache option
