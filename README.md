# ![D logo](./admin/img/favicon-32x32.png) Demiran Project Management Application
[![License](https://img.shields.io/badge/License-BSD%203--Clause-blue.svg)](LICENSE)

This is a lightweight project management application written in PHP using MySQL/MariaDB darabase.

# Features
 - Creating Tasks in KanBan Tables and Table structure
 - Assign tasks to people
 - Connect tasks to projects
 - Manage work hours
 - Chat system
 - Plugin support: Invoice manager

# Getting started 

## System Requirements
 - PHP 7.3 or newer
 - PHP Extensions enabled: main(), ZippArchive, ImageMagick
 - MySQL 8.0 or MariaDB 10.3 and up
 - 500MB Storage
 - minimum 1280x720p screen resolution

## Getting Started

After we copied the content to a PH Webserver, open the index page where we see the install screen:

![Install](./admin/img/install.png) 

In case if you are using **XAMPP**, the username is root, and the password is empty!

After successful install you can see the home page, where you can navigate to Login.

![Home](./admin/img/home.png) ![Login](./admin/img/login.png) 

### Default Credentials

```
username: admin
password: 1234
```
### Pages
#### Tasks
In this page you can manage the tasks, and assign then to people

![Tasks](./admin/img/screenshot1.png) 
![Kanban](./admin/img/screenshot2.png) 

#### Users
You can create Users, and clients in here.

![Create User](./admin/img/user_create.png) ![Users](./admin/img/users.png)

#### Projects
General Project Management page

![Project Management](./admin/img/screenshot3.png) 
![Calendar](./admin/img/calendar.png) 

#### Hours
We can manage workhours per individuals

![Hours](./admin/img/hours.png) 

##### Chat
Chat/Message system is a simple PHP chat, that doesn't use any polling/refresh feature,
just basic messaging functionality

![Hours](./admin/img/chat.png)

## License

This project is licensed under the BSD 3-Clause License - see the [LICENSE](LICENSE) file for details.