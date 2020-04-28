
# Transactional Email Service
     


This application is designed to send email using different external mail services.

As of now this system is integrated with two mail delivery system as follow:

- SendGrid
- MailJet


**Technology stack used in the application are :**

- Lumen Framework
- Docker
- PHP
- Mysql

Main Technology used in Transactional email:

**Lumen Framework**

-It is a micro-framework for the web application that creates micro-services.

-It handles more number of requests per second.

-Response time is less compared to Laravel.

-Lumen is all about the performance and speed therefore is the perfect solution for building Laravel based micro-services and blazing fast APIs.

**Docker**

Docker is a software platform designed to make it easier to create, deploy, and run applications by using containers. It is the best way to run application independent on system installation. We can create one service per one container and use different variation of installation for different project. 

**API Auth for header with ByPass Token**

In this application we are using an authentication with a Bypass Token. We can later modify it according to the client, in order to do that more headers can be add to the system.

Header :
     
         Content-Type: application/json
         Api-Auth-Bypass-Token: {providedKey}
         

**Dingo**
It is a very good RESTful API package of Laravel/Lumen . Added Dingo package for following Feature:

- API Versioning
- Error and Exception Handling
- Response Transformers and Formatters
- API Blueprint Documentation

**Queue**

This project is using Queue to store request sent by send-email API endpoints. Lumen/Laravle has very strong Queue functionality and provides various driver to hold queue. This project is using Queue Driver Database to hold the request in queues and later on process it making it completely asynchronous. We can replace the queue driver as per the project needs. 

**Scheduler**

In the Transactional Email Application we are adding every send-email API request to our Application Queue. In order to run it we have added a scheduler to run every one minute. We have added a corn.templates file which needs to added in the cron tab of server.

*Note for local we have to run lumen schedule run command manually to dispatched all queued data.

    php artisan schedule:run 
 
Benefit to have scheduler is :
- We can append all output of scheduler command to a log file.(Current log file name is cron-{date}.log)
- Can run multiple command with single entry in the cron tab.


**Event/Listener**

As this project is using external mail services to send the email so we have added Event Listener to add email status in our system table 'email_status'. So that we can expose list of email data going through our system.
We can get the list of emails by this API.
   
    GET: http://localhost:8080/api/get-emails/{status?} (See request and Repsonse later in this doc)
    

**Fallback Support**

This systems has custom built fallback mechanism. It does not depends on single external mail service to send the email and can have more than one. If one mail-service is down emails will be sent through another mail-service using fallback.
Currently it has two mail services MailJet, SendGrid. Project has 'priority' key inside of config array of service mail provider (you can manage it from config/mail.php).

We have Lumen functionality to try attempts on queue and we are creating an array of mail provider and trying to send email by ordering . Whenever an error occurs from the first mail service (Priority 1), next priority mail service class object will be picked to send email. 

We can check the functionality 'app/Services/EmailServices.php:sendEmail()'
 
**-Note:** We can add new external service just by adding config in config/mail.php and add a service class inside App/Services namespace which implements IMailer interface.

**Fail Scenario**

As we manage mail provider from the config of the application and creating an array in the service provider. In this application we have set $tries=1 to send email. After all the tries from all mail services, if error persist while sending emails then we mark this queue to fail table.

This fail table can have this kind of exception:

1. SendMailException

We can process the queue if its a exception other wise we can notify to admin to look into the failed table and do the appropriate action. If its a executable issue we can fix that and add that failed record again to the queue and execute.
 

**Unit Test**
This system using PHPUnit to perform testing.

  You can run Unit Test cases by this command inside of php_app container
        
        ./vendor/bin/phpunit

**<h4>Getting Started</h4>**

**API endpoints & Console command:**

This Lumen service has 3 endpoints and a console command.

- Welcome (Endpoint to see current version of the application.)
- Send Email (Endpoint to send email which expect the json as request body and then add request to a queue.)
- Get Emails (One week record of all email sent by the system).

To make the Mail service asynchronous we are using Queue. When we get the request to serve mail API , firstly it gets added in a queue and later on processed with a cron job(on local we run command manually).

The application also maintain a priority system and a fallback mechanism. In the configuration file we are maintaining the 'priority' key in the config/mail.php file of lumen. We can also change this configuration from .env file. When an API gives failure response then based on the priority set in the configuration another service will be picked to send email.

**Console Command to Send Email:**

We have a console command as well to push the data into the queue. So we have push the data either from api endpoint or from the console command. Please have a look at the usage of console command.


**<h4>Flow of Application:</h4>**

Please check **FlowDiagram.png** file from the root, <a href="https://github.com/sonam90/transactional-emails/blob/master/FlowDiagram.png">click here</a> to see.


**<h4>Class Diagram of Application:</h4>**

Please check <a href="https://github.com/sonam90/transactional-emails/blob/master/ClassDiagram.png">**ClassDiagram.png**</a> file from the root along with ClassDiagram.UML file.

**<h4>Prerequisites</h4>**

Make sure you have docker running in your system.

<h4>**How to Setup Application:**</h4>

A step by step series of examples that tell you how to get a development env running

**Step 1: Take git clone of the 'transactional-emails' by following command:**
    
    git clone https://github.com/sonam90/transactional-emails.git transaction-email 
    
**Step 2: Go to transaction-email directory:**
    
    cd transaction-email/
    
**Step 3: Run docker composer up command:**
     
     docker-compose up -d
     
**Step 4: Now go to app container to run further command:**

     docker exec -i -t php_app bash
     
**Step 5: Replace API and Secret key provided in docx file (attached in email):**
     
   update .env.example before copying 
     
**Step 6: Copy .env file:**
     
     cp .env.example .env
     
**Step 7: Run Composer inside php container:**

     composer update
     
**Step 8: Run migration inside php container:**
     
     php artisan migrate
          
     
**Step 9: Add postman.json for endpoints or you may manually try :**
 
    http://localhost:8080/api/
   
   *you will see something like this :
   
    {"API_Version":"v1","Name":"Transactional email API","Environment":"local"}


**To run Queued Jobs:**
    
    php artisan schedule:run
    
We can add **cronjobs.template** to Server crontab.

**<h4>Postman Json File</h4>**

You can download from here :<a href="https://github.com/sonam90/transactional-emails/blob/master/Transactional-emails-Apis.json">**PostMan.json**</a>
  
  
  
  **<h4>Usage of API:</h4>**
  
**1. Entry Uri :**

Request Uri:
  
     GET 'http://localhost:8080/api/'
     
Header :

    No Headers
    
Response:

    {
        "API_Version": "v1",
        "Name": "Transactional email API",
        "Environment": "local"
    }
     
**2.Send Email Uri:**

  
  Request :
  
     POST 'http://localhost:8080/api/send-email'
     
     Json Body:
        {
                         "to": [
                             {
                                 "email": "sonam.bhatnagar5@gmail.com",
                                 "name": "Customer 1"
                             },
                              {
                                 "email": "meera.bhatnagar19@gmail.com",
                                 "name": "Customer 2"
                             }
                         ],
                         "subject": "Your Order is ready!!!!",
                         "content": "Dear customer, Thank you for placing the order. Your order is confirmed and will be delivered very soon. For any questions please <a href='https://www.xyz.com/be-en/customerservice-consumer-topic-ordering'>click here</a>.  Happy Eating !!",
                         "type": "text/html"
        }
        
*Note : we can add 'bcc' and 'cc' in the same way but only 'to' is compulsory.


Header :

    'Content-Type: application/json'
    'Api-Auth-Bypass-Token: {providedKey}'
    
Response:

     {
          "status": "success",
          "pushToQueue": "true",
          "message": "Email Pushed to Queue."
      }
     
   
**3.Get Emails :**
    
Request Uri:
  
     GET 'http://localhost:8080/api/get-emails/{status?}'
     
     Status is an optional param and accepted status are:  ['sent', 'bounced', 'error', 'failed']
      
Header :
     
         'Content-Type: application/json'
             'Api-Auth-Bypass-Token: {providedKey}'
         
Response:

        {
            "status": "success",
            "data": [
                {
                    "id": 1,
                    "sent_date": "2019-06-08 16:23:53",
                    "email_data": {
                        "to": [
                            {
                                "email": "sonam.bhagar5@gmail.com",
                                "name": "Customer 1"
                            },
                            {
                                "email": "meerabhatnaga@gmail.com",
                                "name": "Customer 2"
                            }
                        ],
                        "subject": "Your Order is ready",
						"content": "Dear customer, Thank you for placing the order. Your order is confirmed and will be delivered very soon. For any questions please <a href='https://www.xyz.com/be-en/customerservice-consumer-topic-ordering'>click here</a>.  Happy Eating !!",
						"type": "text/plain"
                    },
                    "email_status": "Sent"
                },
                 {
                   "id": 2,
                   "sent_date": "2019-06-08 16:23:53",
                   "email_data": {
                        "to": [
                                    {
                                                "email": "sonam.bhagar5@gmail.com",
                                                "name": "Customer 1"
                                    },
                                    {
                                                "email": "meerabhatnaga@gmail.com",
                                                "name": "Customer 2"
                                    }
                              ],
                        "subject": "Your xyz Order is ready",
                		"content": "Dear customer, Thank you for placing the order. Your order is confirmed and will be delivered very soon. For any questions please <a href='https://www.xyz.com/be-en/customerservice-consumer-topic-ordering'>click here</a>.  Happy Eating !!",
                		"type": "text/plain"
                   },
                   "email_status": "bounced"
               }
            ]
        }

No Record found Response:
    
    {
        "status": "success",
        "data": {
            "message": "No Emails available in System."
        }
    }
    

**Usage of Console Command to Send Email:**

**Description:** Send email console command needs param like "toName: (as a string), "toEmail: (as a valid email), subject: (as a string), content: (content as plain or a html content for email body) and type: (a valid email type either text/plain or text/html)."  

**Example:**
    
    php artisan send:email 'sonam.bhatnagar5@gmail.com' 'Sonam' 'Your email Subject! ' 'Dear Xyz, welcome to Send Email Services!!' 'text/plain' 
    
    or
    
    php artisan send:email 'sonam.bhatnagar5@gmail.com' 'Sonam' 'Your email Subject! ' 'Dear <b>Xyz</b>, welcome to Send Email Services.!!' 'text/html' 
        
 
