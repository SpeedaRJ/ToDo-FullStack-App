# FullStack TODO App
ToDo application for personal use, developed using PHP, Vue2, Bootstrap and MySQL.

The application contains a login and registration feature, where through a PHP server user data is stored in a MySQL database. Specifically in the table `users` created using the command:
```SQL
CREATE TABLE users(
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(60) NOT NULL UNIQUE,
  acronym VARCHAR(60) NOT NULL,
);
```
resulting in:
```SQL
+----------+--------------+------+-----+---------+----------------+
| Field    | Type         | Null | Key | Default | Extra          |
+----------+--------------+------+-----+---------+----------------+
| id       | int          | NO   | PRI | NULL    | auto_increment |
| username | varchar(60)  | NO   | UNI | NULL    |                |
| acronym  | varchar(60)  | NO   |     | NULL    |                |
| password | varchar(255) | NO   |     | NULL    |                |
+----------+--------------+------+-----+---------+----------------+
```
The user login data is processed in the RESTful API files `login.php`, `register.php` and `logout.php` in the `API/users/` directory.
Each user has associated tasks that are displayed on the frontend using Vue.js and BootstrapVue components and Bootstrap5 for styling.
The `tasks` table was created using the command:
```SQL
CREATE TABLE tasks(
  id INT NOT NULL PRIMARY KEY auto_increment, 
  task_name varchar(60) NOT NULL, 
  last_changed TIMESTAMP NOT NULL, 
  description varchar(255)NOT NULL, 
  completed BOOLEAN NOT NULL, 
  user_id INT NOT NULL, 
  FOREIGN KEY (user_id) REFERENCES users(id)
);
```
resulting in:
```SQL
+--------------+--------------+------+-----+---------+----------------+
| Field        | Type         | Null | Key | Default | Extra          |
+--------------+--------------+------+-----+---------+----------------+
| id           | int          | NO   | PRI | NULL    | auto_increment |
| task_name    | varchar(60)  | NO   |     | NULL    |                |
| last_changed | timestamp    | NO   |     | NULL    |                |
| description  | varchar(255) | YES  |     | NULL    |                |
| completed    | tinyint(1)   | NO   |     | NULL    |                |
| user_id      | int          | NO   | MUL | NULL    |                |
+--------------+--------------+------+-----+---------+----------------+
```
Tasks are processed using RESTful API files `login.php`,`logou.php` and `register.php` in the `/API/tasks/` directory.
Two additional files are used for the backend:
* `database_connection.php` which handles the connection to MySQL and,
* `helper.php` which contains a request parameter check function.

The `static` folder includes used `.js` and `.css`, while the main page is `index.php`.

The website was developed using an Apache2 server with PHP support.
