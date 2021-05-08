# PHP Diff Dashboard

Simple project created for learning to make Vue.js apps integrated with RESTful APIs created in Lumen. The project has a dashboard which shows the diff in the files after comparing two different folders. The folders to compare can be copied in "storage/versions" directory under the root of the project. From the dashboard you can scan the directories by making one a "base directory" and the other as "comparing directory". Once the scan is complete you can see the scan result from the scan list. By default, the dashboard shows the results of the last scan. The diff result contains

- Files added in the base directory
- Files removed in the base directory
- Files modified in the base directory
- Diff of the modified files. Changes show the updates in content based on base directory files.

## Setup
- place the project on your server's root directory
- create *public* directory available in project root as the document root for the server host
- run `composer install` to install Lumen and vendor packages
- create `.env` file under the project root directory
- update your database details in `.env`
- Put a random string of 32 characters as *APP_KEY* value in `.env`
- run `php project_root/artisan migrate` command in CLI to migrate required tables in the database

## Understand code structure
- The project route/APIs are placed in `routes/web.php`
- The controller class are available `app/Http/Controllers`
- Route binding is enabled for lumen
- Vue components are placed under `resources/js`


## Limitations
- Scanning large directories may result in a time out

