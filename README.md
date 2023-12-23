# SymfonyMicroblog

## Project Description
SymfonyMicroblog is a web application built on the Symfony framework that allows users to create microblogs, share posts, and engage with other users. This project focuses on providing functionality related to user authentication, registration, post creation, and user interaction.

**Demo Link:** [SymfonyMicroblog Demo](https://kardas-symfonymicroblog.ct8.pl/)

## Main Features
The "SymfonyMicroblog" project offers the following features:

- **Login and Registration:** Users can log in to their accounts or register if they are new users.
- **Post Creation:** Registered users can create and publish their posts on their profiles.
- **Liking/Disliking Posts:** Users can express their opinions about other users' posts by giving them "likes" or "dislikes."
- **Following People:** Users can follow other users and see the list of followers in profile dashboard.
- **Managing Follows/Likes in Profile:** Users can manage the list of users they follow and view the posts they have liked.
- **Post Sorting:** Posts are sorted by the number of likes for easy discovery of popular content.
- **Post Search:** Users can search for posts based on keywords.
- **Push Notifications:** Through integration with Pusher, users receive push notifications about new posts and interactions with their content.
- **Email Notifications:** After adding a new post, all registered users receive an email with information about the new content.
- **Pagination:** The application offers pagination for posts to facilitate browsing and navigation through long lists of posts.

To see profile dasboard just log in and click your name in the right corner. 
If you click 'profile' in your profile dasboard you can manage your profile inforamtion such as name/email/avatar/password/account deletion.

For developers, the project also offers the following API features:

- **User Registration API:** Users can create an account by sending a POST request to the registration API endpoint with their user details.
- **User Authentication API:** Registered users can obtain an authentication token by sending a POST request to the authentication API endpoint with their credentials.
- **Post Creation API:** Users can create and publish posts by sending a POST request to the post creation API endpoint with the post content.


## Technologies
The project utilizes the following technologies and tools:

- Symfony Framework
- Alpine.js (for interactive front-end components)
- Tailwind CSS (for responsive and customizable styling)
- Pusher (for handling push notifications)
- Email delivery system (for email notifications)
- Database (e.g., MySQL) for storing user and post data

## Project Goal
The primary goal of the "SymfonyMicroblog" project is my personal journey of learning and mastering the Symfony framework. By working on this project, I aim to gain hands-on experience with Symfony's features and best practices in building web applications while achieving my own educational objectives.


## Getting Started

To get started with the "SymfonyMicroblog" project, follow these steps:

1. **Clone the Repository:**

- Clone this repository to your local development environment using Git:
   
2. **Install Dependencies:**

- Navigate to the project directory and install the required dependencies using Composer:

3. **Configure Pusher:**

- Open your `.env` file and provide your Pusher credentials. You can sign up for a Pusher account if you don't have one.

   - PUSHER_APP_ID=your-app-id

   - PUSHER_APP_KEY=your-app-key

   - PUSHER_APP_SECRET=your-app-secret

   - PUSHER_APP_CLUSTER=your-app-cluster


4. **Configure LexikJWTAuthenticationBundle:**

- Generate the SSL keys:
   `php bin/console lexik:jwt:generate-keypair`
   
- Your keys will land in config/jwt/private.pem and config/jwt/public.pem (unless you configured a different path).
   
   - Configure the SSL keys path and passphrase in your `.env`:
   
   - JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
   
   - JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
   
   - JWT_PASSPHRASE=

5. **Configure Database:**
   
- In your `.env` file, configure the database connection with your credentials. You can use a database like MySQL.

7. **Run Migrations:**

- Run the database migrations to create the necessary database schema:

7. **Start the Application:**

- Start the Symfony development server

The application will be available at `http://localhost:8000`.

8. **Explore and Learn:**

- You are now ready to explore the project, learn about Symfony, and experiment with the features of the "SymfonyMicroblog."

