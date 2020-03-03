
This is a microservice for sending SMS messages through API calls.

This uses the aws/aws-sdk-php-laravel library to send SMS messages. No specific configuration is required on AWS for sending SMS messages through their system.
However, the AWS Key and AWS Secret need to be set in the .env file for the given AWS account that is hosting this service. In production,
this key pair should probably be set during deployment.

Calls made to send SMS are made as follows:

http://<hostname>/sendSMS/<json string>

Calls can also be made using the POST method using the same json string to the following URL:

http://<hostname>/post

The json string will indicate the type of alert, followed by parameters. For example, a 911 call alert would have a JSON string as follows:

{"NINEONEONE": {"LOCATION": "Hilton Dallas", "TIME": "23.11", "EXTENSION": "84563","ROOM": "4563" , "TO"."+12125551212"}}

Note that target phone numbers need to begin with +1 (assuming a North American number).  The code in the microservice does 
verify the format, and will add a "+" or a "+1" if the end format is still valid. If not, an error is returned.

In order to make the service as flexible as possible, alerts can be sent using a key of "custom".  These types of requests will need
to include a "BODY" property to show the text.  Also, any request that doesn't match a template will fall into this category. This will 
help prevent bad requests from being made. In such cases, if other properties are listed in the request, they will simply be listed 
out with their values.  For example, if the following is sent to the service:

{"INBOUNDREDIRECTON": {"CONTEXT: "000-111", "NUMBER": 12123458765", "REDIRECT": "2127881234",  "TO"."+12125551212"}}

If there is no template for INBOUNDREDIRECTON, then the message will simply say: 

Alert: INBOUNDIRECT
Context: 000-111
Number: 12123458765
Redirect: 2127881234
 
 An optional parameter is "FROM".  The type of alert (e.g. NINEONEONE in this case) 
 specifies the template used for the body of the message. 
 
 It is also possible to send custom messages. In this case the type would by "CUSTOM" and would require the parameter "MESSAGE" to fill out the body of the message.
 
 TODO
 
 Even though access to this server will likely be restricted to specific servers, there should still be some form of authentication.
 
 Important Files
 
 This is set up using the Laravel structure. Laravel has a library for sending SMS messages via AWS, 
 so it was chosen for this microservice.
 
 /app/Http/Controllers/SMSController    The main controller with methods for formatting and sending messages
 /resources/views    Contains templates for sending customized messages
 /routes/web.php  routes for GET requests
 /routes/api.php  routes for POST requests
 
 
 

 
 
 

