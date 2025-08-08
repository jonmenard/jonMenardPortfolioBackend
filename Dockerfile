# Small & simple: PHP built-in server
FROM php:8.3-cli-alpine

# Set working directory
WORKDIR /app

# Copy the php files
COPY test.php /app/test.php
COPY index.php /app/index.php

# Expose port
EXPOSE 8080

# Start PHP's built-in server
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t /app"]
