# Changelog

All notable changes to `Dezero Framework` will be documented in this file

## 0.4.1. - 2024-05-07

- Backup database - New actions to backup MySQL database (ported from DZ Framework)

## 0.4.0. - 2024-04-23

- BackendManager - Add environment custom class in body attribute class

## 0.3.9. - 2024-03-27

- Frontend module - Adding new FrontendManager component and custom frontend View class. They are designed to be extended for a custom frontend module
- Theme - Created new method to allow load a new theme (for example "frontend") from a module
- View class - Created new POS_END_TOP position: at the top of the end
- StringHelper - Created new method slug() to generate an input strings concatenated by dashes

## 0.3.8. - 2024-03-20

- Krajee FileInput widget - Click on dropzone fires browse button
- Xml new helper to read XML structure (ported from DZ Framework)
- Xml helper - Created special methods to read XML files and parser to an array
- Help documentation - Local install guide updated

## 0.3.7. - 2024-03-19

- Status Trait - New method getLastStatusHistory()
- ActiveDataProvider - Override Yii core class to check if data provider is empty

## 0.3.6. - 2024-03-12

- TimeHelper - New class to manage time (hours) operations
- Category - Fixed error showing tree sidebar when max levels is 1

## 0.3.5. - 2024-03-06

- UploadFileService - Allow to upload files from \dezero\db\Activerecord objects
- Jasny FileInput plugin adapted to Yii2
- Javascript - New class $.dezeroLoader with several methods to manage loading modals

## 0.3.4 - 2024-03-02

- GrdiView - Export to Excel behavior.
- ExcelWriter - Lock/unlock methods improved
- ExcelWriter - Added new methods to build cells with dropdowns
- Export Excel - Added new class $.dezeroExport for exporting data to Excel

## 0.3.3 - 2024-02-24

- Logs - View all the log files directly from backend administration pages

## 0.3.2 - 2024-02-23

- Excel writer - Created new custom class to generate excel files with styles, formats and multiple options

## 0.3.1 - 2024-02-21

- Excel reader - Created new custom class to read excel files
- AuthChecker - New helper class to collect all the authorizaton checker methods used from controllers

## 0.3.0 - 2024-02-19

- Test module - Created new core module for testing purposes.
- Docs directory improved with new guides for data objects, files, images, logs, session and roles&permissions

## 0.2.9 - 2024-02-16

- Queue finished - New queue system based on Yii2 queue package
- Created new contract class "EntityInterface" used by ApiLog and Batch models
- Remove "use Dz;" statement in all .tpl.php files

## 0.2.8 - 2024-02-11

- Batch - Change "summary_json" column from VARCHAR(512) to TEXT()
- Batch model - Added new method to get endpoint information
- Url helper - Created new method to extract paramters and fragment from an URL.
- Dz::t() - Make Yii::t() method compatible with Yii 1 pluralize style
- Created new contract class "TypeInterface"

## 0.2.7 - 2024-01-31

- DataObject - Created a new implementation of concept "Data Object"
- ArrayDataObject - New data object for arrays
- StringDataObject - New data object for strings
- GridView - Detect if Gridivew is loaded inside a SlidePanel
- GridvView - Add "raw" as column default format
- Controller - New method requireSuperadmin() for \dezero\web\Controller

## 0.2.6 - 2024-01-13

- Auth module - Create default permissions for core modules
- GridView - Bulk Actions first implementation
- Layout - Header & sidebar permissions
- StatusTrait - Refactoring code

## 0.2.5 - 2023-10-14

- GridView - Custom options for SELECT2
- Dz - Create new method Dz::makeCleanObject to create ActiveRecord instances without executing loadDefaultValues()
- CategoryManager - Created new methods getCategoryList() and getAllChildren()
- Category model - Created new method fullTitle() to return the title with all the parents.
- StringHelper - New methods cleanUTF8() and max()

## 0.2.4 - 2023-10-09

- SYNC module - Created new module to manage sync operations: import and exports
- Batch model - Created new model to I/O operations (batch jobs)

## 0.2.3 - 2023-10-06

- API module - Created new ApiLog model to save requests and responses into database
- REST - Resource component (server) - Implemented option to save log into database via ApiLog model
- REST - Client component (server) - Implemented option to save log into database via ApiLog model

## 0.2.2 - 2023-10-03

- CSS & JS redefined to be extended from the app without changing the core
- Gii module - Improved search class generation process
- Settings module - Created Language model

## 0.2.1 - 2023-09-21

- REST - Custom & extended HTTP client created
- REST - HTTP client with Oauth integration finished

## 0.2.0 - 2023-09-15

- Category module - First finished version
- API module - First working version with REST API

## 0.1.16 - 2023-08-31

- Generate presets images

## 0.1.15 - 2023-08-30

- Upload files via UploadFileService

## 0.1.14 - 2023-07-28

- Category module - Created new module with first version of Category model
- Template module builder - Created a new template module to work as scaffolding of future modules

## 0.1.13 - 2023-07-10

- Gridview - Override yii.confirm() Javascript method and use Bootbox library for it
- ErrorTrait - New method showErrors() for Services classes (use cases)
- Asset module - Created new module and firt version of AssetFile model

## 0.1.12 - 2023-07-09

- Entity module - Added new module to save Entity and StatusHistory records
- TimestampBehavior - Custom behavior to register date and user before inserting or updating a model and checking if the attributes exist

## 0.1.11 - 2023-07-07

- Frontend module - Added new module with HomeController to receive afterLogin requests
- Create new Alert widget to show flash messages
- User module - Create & update pages using services classes
- Status buttons: Disable, enable and delete

## 0.1.10 - 2023-05-23

- Gridivew - Custom widget adapted to Bootstrap theme
- User module - Admin index page

## 0.1.9 - 2023-05-20

- Auth module - Add new module with RBAC system
- User module - Login & logout process with custom events

## 0.1.8 - 2023-04-28

- Added voku/Stringy PHP library for string manipulation
- Gii model generator: custom special classes for query and search

## 0.1.7 - 2023-04-27

- Upgraded Dotenv from version 2 to version 5

## 0.1.6 - 2023-03-31

- Use custom DbSession class to store session data in the database table "user_session"

## 0.1.5 - 2023-03-30

- Created special class File to work with files and directories similar to old CFile component
- Integration with Spatie image library to work with image manipulations and optimizations

## 0.1.4 - 2023-03-24

- Gii special configuration for MODEL template

## 0.1.3 - 2023-03-22

- Bootstrap files for web & cnosole 
- Base config

## 0.1.2 - 2022-05-13

- Log and DzLog helpers

## 0.1.1 - 2022-05-11

- Kint, Dotenv, Dz class, modules and views alias from DZ 1

## 0.1.0 - 2022-03-22

- Initial release
