# SocialNet - Social Network Web Application

A simple social network application built with PHP, MySQL, and Nginx. Users can create accounts, manage profiles, upload avatars (new feature), and share images (new feature).

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

## Database Schema

The application uses the following tables:

- account: Stores user information (id, username, fullname, password, description, avatar)
- posts: Stores user image posts (id, user_id, image, created_at)

## Setup Instructions

### Prerequisites
- Ubuntu with Nginx installed
- PHP 7.4 with PHP-FPM installed
- MySQL/MariaDB installed
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

## Usage

### Creating a New User

1. Navigate to http://YOUR_SERVER_IP/admin/newuser.html
2. Fill in username, full name, password, and optional description
3. Click Create User button
4. User will be redirected to sign in page

### Signing In

1. Navigate to http://YOUR_SERVER_IP/socialnet/signin.html
2. Enter username and password
3. Click Sign In button
4. You will be redirected to the home page

### Home Page

- View your profile information
- See list of other users in the system
- Click on user cards to view their profiles

### Profile Page

- View user profile information
- If viewing your own profile, upload a new avatar
- Upload image posts to your gallery
- View gallery of your image posts

### Settings Page

- Edit your full name
- Edit your profile description
- Upload or change your avatar image

### Sign Out

- Click the Sign Out button in the menu bar
- Session will be destroyed and you will be redirected to sign in page

## Default User

A default admin user is included in db.sql:
- Username: admin
- Password: 123456 (hashed with bcrypt)

## Security Notes

- All passwords are hashed using bcrypt algorithm
- Prepared statements are used to prevent SQL injection
- User input is validated on both client and server side
- File uploads are validated for type and size

## Customization

### Changing Server Port

Edit the Nginx configuration file to listen on a different port:
listen 8080;

### Changing Database Credentials

Edit config.php to use different database username and password

### Modifying Student Information

Edit /socialnet/about.html to change student name and number in the student information section

## Troubleshooting

### 404 Not Found Errors
- Verify Nginx root directory is set to /var/www/socialnet-1695196
- Check file permissions: sudo chmod -R 755 /var/www/socialnet-1695196

### Database Connection Errors
- Check MySQL is running: sudo systemctl status mysql
- Verify database exists: sudo mysql -u root -p -e "SHOW DATABASES;"
- Check credentials in config.php match your MySQL setup

### Avatar/Post Upload Not Working
- Check upload folders exist: ls -la /var/www/socialnet-1695196/uploads/
- Verify folder permissions: sudo chmod 777 /var/www/socialnet-1695196/uploads/avatars
- Verify www-data ownership: sudo chown www-data:www-data /var/www/socialnet-1695196/uploads

### PHP Errors
- Check PHP-FPM is running: sudo systemctl status php7.4-fpm
- Check Nginx error log: sudo tail -20 /var/log/nginx/error.log
- Check PHP-FPM log: sudo tail -20 /var/log/php-fpm.log

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
