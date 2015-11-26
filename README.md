# php-mongodb
Sample code demonstrating basic CRUD operations on a MongoDB database using php

database.php
- interface for database operations
- wraps MongoDB php class
- converts between MongoDB classes and php native class, such as MongoDate<->DateTime

events.php
- used as sample data generator

test.php
- uses sample data for series of automated CRUD tests to database

rest.php
- outline of REST API to interact with database
- not fully tested
