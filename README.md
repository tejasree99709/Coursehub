COURSEHUB
           -A centralised platform for student-faculty course management system.
In today’s world, certification courses are very important for learning new skills and growing in careers. However, managing these courses and keeping track of certifications can be challenging. Students often face difficulties in organizing their progress, and administrators struggle with handling multiple tasks.
Our project solves this problem by building a simple and user-friendly web platform. This platform helps administrators easily create and manage courses, while students can track their progress, access course materials, and get their certifications all in one place.
This system saves time, reduces confusion, and provides a smooth experience for everyone. It makes the process easier, faster, and more efficient, helping both students and administrators.

📝 **Setup Instructions**
Follow these steps to set up the project locally:

1. Clone the Repository
git clone https://github.com/tejasree99709/Coursehub.git
cd Miniproject

2. Set Up the Database
Start your local server using XAMPP, WAMP, or MAMP.
Open phpMyAdmin and create a new database named coursehub.
**Import the SQL file to set up the tables:**

CREATE DATABASE coursehub;
USE coursehub;
CREATE TABLE users (
   id int(11) NOT NULL,
   username varchar(50) NOT NULL,
   email varchar(100) NOT NULL,
   password varchar(255) NOT NULL
);

CREATE TABLE courses (
   id int(11) NOT NULL,
   course_name varchar(255) NOT NULL,
   course_link varchar(255) NOT NULL
);

CREATE TABLE student_registration(
   id int(11) NOT NULL,
   user_id int(11) NOT NULL,
   course_id int(11) NOT NULL,
   is_registered tinyint(1) NOT NULL DEFAULT 0,
   certificate_link varchar(255) DEFAULT NULL
);

3. Run the Application
Open a web browser and navigate to http://localhost:8080/Miniproject


![Login](https://github.com/user-attachments/assets/a2bbf5e0-b93b-4ff3-bb34-e215d7989b4f)
![Admin](https://github.com/user-attachments/assets/c491038f-b0af-4b4c-9cdf-efcaa0e0a10e)
![Student](https://github.com/user-attachments/assets/0acd6f29-7d86-4236-be8f-7e2b0bc4dcd7)
![student course registration](https://github.com/user-attachments/assets/f100c156-6860-45b3-9322-55c82f9b3a6a)
![Admin Courses](https://github.com/user-attachments/assets/057dd791-ac10-4d60-b82e-d5db7582ebb7)
![post courses](https://github.com/user-attachments/assets/be297c34-7491-46f7-8852-85194e5b3e0d)
![status](https://github.com/user-attachments/assets/a91e63ad-3482-433b-a7f3-6514fb56844e)
