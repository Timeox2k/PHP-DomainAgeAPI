# Domain Age Checker

This is a simple PHP script that allows you to check the age of a domain using the Iodev Whois library and store the result in a MySQL database for later use.

## Installation

1. Clone this repository.
2. Install dependencies using `composer install`.
3. Create a new MySQL database and run the following SQL statement to create the necessary table:
4. Edit the .env file with your MySQL database credentials.
5. Upload the files to your web server.
```sql
CREATE TABLE domain_data (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    entryTime INT NOT NULL,
    age VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY (name)
) ENGINE=InnoDB;

```
## Usage
To check the age of a domain, make a GET request to the script with the domain parameter set to the domain you want to check. For example:

``` https://example.com/domain-age-checker/?domain=example.com ```

The script will return the age of the domain in Unix timestamp format. If the domain has been checked within the last 30 days, the script will return the age stored in the database.
