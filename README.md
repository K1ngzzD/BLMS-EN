# BLMS-EN
BLMS EN version

This is BooksLendingManagementSystem final version (BLMS) in English.


Use steps:

1. Import book.sql into PHPmyadmin


2. Modify your database link in Base/conf.php


3. The administrator account number is 10086 and the password is admin


4. Existing two user accounts,
One is a trial for team members. The account number is 10000 and the password is 123456
The other is for user experience (now upgraded to administrator account), account 10011, password 123456


* this system adopts MVC framework, using smarty templating engine, Bootstrap and JQuery

* the user login ID will be distributed by the administrator

* users can log in the current operations are: 
  book query, information modification, account loss reporting, book renewal and password modification

* the loan return operation can only be performed by the administrator

* the administrator can add, delete, modify and check the books and users in addition to 
  including all the rights of the users
