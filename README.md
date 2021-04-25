# Zuyd Hogeschool - Design Research Casus


## Introduction

This repository contains the code of a 10-week Design Science Research project, developed by students of the ICT faculty of Zuyd University of Applied Sciences.

The prime goal of this project was developing a 'tool' which would allow users to configure decision tree's (from now on called 'graphs')<sup>1</sup>.
These graphs could then be used by other users to interactively support their decision-making based on the configured structure.
This and many other features have been implemented.

<sup>1</sup>Formally the decision supporting charts are graphs, since multiple edges are allowed on the output as well as on the input of a node. This as opposed to trees, where each node can only have one input.

## Screenshots

Editor-side             |  User-side
:-------------------------:|:-------------------------:
![image](https://user-images.githubusercontent.com/50321538/114946172-f6c7f480-9e4a-11eb-8971-10f172c94cf5.png) | ![image](https://user-images.githubusercontent.com/50321538/114946473-84a3df80-9e4b-11eb-95cd-9c6f5c2378e8.png)
<!--![image](https://user-images.githubusercontent.com/50321538/114946283-2c6cdd80-9e4b-11eb-82a5-1fa4f9344c78.png)-->

## Deployment
For deployment and troubleshooting it is recommended to follow Laravel 8's [documentation](https://laravel.com/docs/8.x/deployment).

However, the most significant and moreover specific configuration tasks have been documented below.

### Requirements

Next to Laravel 8's [server requirements](https://laravel.com/docs/8.x/deployment#server-requirements) it is required to have one supported database which can be used by the application.

### Installation

1. Clone this repository
2. Install composer's dependencies with `composer install`
3. Install npm dependencies with `npm install`
4. Configure the .env file in the repository.
    - If the `.env` file does not exist; copy `.env.example` file to `.env`
5. Generate application key with `php artisan key:generate`

### Initialize database structure

Run migrations with `php artisan migrate:fresh --seed`
- Note: this command also executes the default database seed which includes the default node-types which can be used to construct graphs. These default types can be modified and extended in the file `database/seeders/DefaultNodeTypeSeeder.php`
- Note: for demonstration purposes it is also possible to include one example graph and one test user in the seeding process. To enable this, uncomment `TestDataSeeder` in the `database/seeders/DatabaseSeeder.php` file

### Run

For development, the server can quickly be launched with Laravel's internal server using: `php artisan serve`.

Alternatively it is possible to serve the application by using another web server like Nginx or Apache. Please refer to Laravel 8's documentation for further reference.
