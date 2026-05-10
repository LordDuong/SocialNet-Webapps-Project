# SocialNet - Social Network Web Application
A simple social network application built with PHP, MySQL, and Nginx. Users can create accounts, manage profiles, upload avatars(new feature), and share images(new feature).



## Project Information
Student Name: Nguyen Thai Duong
Student Number: 1695196



## Features

### Core Features
- User authentication (sign up, sign in, sign out)
- User profile management
- Avatar upload for users
- Image posts gallery on profile
- User profile viewing
- About page with project information

### User Pages
- Admin page for creating new users (URL: /admin/newuser.html and /admin/newuser.php)
- Sign in page for authentication (URL: /socialnet/signin.html and /socialnet/signin.php)
- Home page showing current user info and list of other users (URL: /socialnet/index.php)
- Profile page for viewing user profiles (URL: /socialnet/profile.php)
- Settings page for editing profile information (URL: /socialnet/setting.php)
- About page with project details (URL: /socialnet/about.html)
- Sign out page for logout (URL: /socialnet/signout.php)



## Technology Stack
- Backend: PHP 7.4
- Database: MySQL
- Web Server: Nginx
- Frontend: HTML
- Security: Password hashing with bcrypt, prepared statements



## Project Structure
/var/www/socialnet/
├── config.php                 (Database configuration)
├── db.sql                     (Database schema)
├── README.md                  (This file)
├── admin/
│   ├── newuser.html          (User creation form)
│   └── newuser.php           (User creation backend)
├── socialnet/
│   ├── signin.html           (Login form)
│   ├── signin.php            (Login backend)
│   ├── signout.php           (Logout)
│   ├── index.php             (Home page)
│   ├── profile.php           (User profile with image posts)
│   ├── setting.php           (Profile settings)
│   └── about.html            (About page)
├── uploads/
│   ├── avatars/              (User avatar images)
│   └── posts/                (User post images)
└── menubar.php               (Shared navigation menu)




## Setup Instructions

### Prerequisites
- Ubuntu with Nginx installed
- PHP 7.4 with PHP-FPM installed
- MySQL/MariaDB installed
- Git installed

### Step 1: Clone Repository

### Step 2: Create Database
sudo mysql -u root -p -e "CREATE DATABASE socialnet;"
sudo mysql -u root -p socialnet < db.sql

### Step 3: Copy Project to Web Root
sudo cp -r socialnet /var/www/socialnet
sudo chown -R www-data:www-data /var/www/socialnet
sudo chmod -R 755 /var/www/socialnet

### Step 4: Create Upload Folders
sudo mkdir -p /var/www/socialnet/uploads/avatars
sudo mkdir -p /var/www/socialnet/uploads/posts
sudo chmod 777 /var/www/socialnet/uploads/avatars
sudo chmod 777 /var/www/socialnet/uploads/posts
sudo chown -R www-data:www-data /var/www/socialnet/uploads

### Step 5: Configure Nginx
Create file /etc/nginx/sites-available/socialnet
Then run:
sudo ln -s /etc/nginx/sites-available/socialnet /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx

### Step 6: Start PHP-FPM
sudo systemctl start php7.4-fpm
sudo systemctl enable php7.4-fpm



## Customization

### Changing Server Port
Edit the Nginx configuration file to listen on a different port:
listen 8080;  (instead of listen 80)

### Changing Database Credentials
Edit config.php to use different database username and password

### Modifying Student Information
Edit /socialnet/about.html to change student name and number in the student information section




## License
This project is developed for educational purposes.
