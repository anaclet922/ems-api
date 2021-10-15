# Employee Management System

## Installation

### Server Requirements: 

- PHP version 7.3 or higher is required, with the following extensions installed:

	- [intl](http://php.net/manual/en/intl.requirements.php)
	- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library

	Additionally, make sure that the following extensions are enabled in your PHP:

	- json (enabled by default - don't turn it off)
	- [mbstring](http://php.net/manual/en/mbstring.installation.php)
	- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php)
	- xml (enabled by default - don't turn it off)

- Apache web server
- Composer installed ([composer](https://getcomposer.org/))

### Install packages

- Open terminal to the installation folder and run the following command:
	- `composer update`

### Setup

Open `.env` and edit:
 - baseURL and change database settings (e.g.: example.com); this url should point to /public folder where index.php located
 - change secret key for JWT to strong one, and JWT_TIME_TO_LIVE.

### Database
- Create database and edit `.env` file with database name and its user (You could have done on above step).
- Run `php spark migrate` to migrate tables of our database.
- Run `php spark db:seed EmployeeSeeder` to insert fake employee to employees table.

### Excel Template

- Named employee_template.xlsx
- Use the same template to upload bulk of employees to the system.

