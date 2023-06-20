# Build docker images (MailCatcher & PostgreSQL database)
    - docker-compose up [-d] [--build] [--remove-orphans]

# Launch Symfony local web server
    - symfony server:start

# Consume async Messages (execute worker):
    - symfony run php bin/console messenger:consume async [-vv]

# Open local Mailcatcher (to see sended emails)
    - symfony open:local:webmail

# Launch PHPUnit test suite
    - symfony run bin/phpunit