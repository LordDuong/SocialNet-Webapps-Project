# SocialNet - Social Network Web Application

A simple social network application built with PHP, MySQL, and Nginx. Users can create accounts, manage profiles, upload avatars (new feature), and share images (new feature).
* Important note: please fill in '...' in config.php to test the project

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
- Admin page for creating new users 
- Sign in page for authentication 
- Home page showing current user info and list of other users 
- Profile page for viewing user profiles 
- Settings page for editing profile information
- About page with project details 
- Sign out page for logout 

## Technology Stack

- Backend: PHP 7.4
- Database: MySQL
- Web Server: Nginx
- Frontend: HTML5, CSS3, JavaScript
- Security: Password hashing with bcrypt, prepared statements

## Project Structure

/var/www/socialnet-1695196/
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
- MySQL installed
- Git installed

### Step 1: Clone Repository

git clone https://github.com/LordDuong/SocialNet-WebApps-Project.git socialnet-1695196
cd socialnet-1695196

### Step 2: Create Database

sudo mysql -u root -p -e "CREATE DATABASE socialnet;"
sudo mysql -u root -p socialnet < db.sql

### Step 3: Copy Project to Web Root

sudo cp -r socialnet-1695196 /var/www/socialnet-1695196
sudo chown -R www-data:www-data /var/www/socialnet-1695196
sudo chmod -R 755 /var/www/socialnet-1695196

### Step 4: Create Upload Folders

sudo mkdir -p /var/www/socialnet-1695196/uploads/avatars
sudo mkdir -p /var/www/socialnet-1695196/uploads/posts
sudo chmod 777 /var/www/socialnet-1695196/uploads/avatars
sudo chmod 777 /var/www/socialnet-1695196/uploads/posts
sudo chown -R www-data:www-data /var/www/socialnet-1695196/uploads

### Step 5: Configure Nginx

Create file /etc/nginx/sites-available/socialnet-1695196:
server {
    listen 80;
    server_name YOUR_SERVER_IP;

    root /var/www/socialnet-1695196;
    index index.php index.html;

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}

Then run:

sudo ln -s /etc/nginx/sites-available/socialnet-1695196 /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx

### Step 6: Start PHP-FPM

sudo systemctl start php7.4-fpm
sudo systemctl enable php7.4-fpm


## Customization

### Changing Server Port

Edit the Nginx configuration file to listen on a different port:
listen 8080;

### Changing Database Credentials

Edit config.php to use different database username and password

### Modifying Student Information

Edit /socialnet/about.html to change student name and number in the student information section


## Features Added

### Avatar Upload
- Users can upload profile pictures on the settings page
- Avatars are displayed on home page and profile pages
- Supports JPG, PNG, GIF formats (max 5MB)

### Image Posts
- Users can upload images to their profile
- Images are displayed in a gallery on the profile page
- Each post shows the upload date
- Gallery displays images in responsive grid layout

## License

This project is developed for educational purposes.
