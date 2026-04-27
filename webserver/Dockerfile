# Build stage - for any build dependencies if needed
FROM php:8.2-fpm-alpine AS builder

# Install build dependencies
RUN apk add --no-cache \
    mysql-client \
    && docker-php-ext-install mysqli pdo pdo_mysql

# Runtime stage - minimal final image
FROM php:8.2-fpm-alpine AS runtime

# Install only runtime dependencies
RUN apk add --no-cache \
    mysql-client

# Copy PHP extensions from builder
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Create non-root user
RUN addgroup -g 1000 appuser && \
    adduser -D -u 1000 -G appuser appuser

# Set working directory
WORKDIR /var/www/html

# Create necessary directories for read-only filesystem
RUN mkdir -p /tmp /var/run && \
    chown -R appuser:appuser /tmp /var/run

# Copy application files
COPY --chown=appuser:appuser public/ /var/www/html/
COPY --chown=appuser:appuser config/ /var/www/config/

# Configure PHP-FPM to run as non-root
RUN sed -i 's/user = www-data/user = appuser/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/group = www-data/group = appuser/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/listen.owner = www-data/listen.owner = appuser/g' /usr/local/etc/php-fpm.d/www.conf && \
    sed -i 's/listen.group = www-data/listen.group = appuser/g' /usr/local/etc/php-fpm.d/www.conf

# Set proper permissions
RUN chown -R appuser:appuser /var/www

# PHP security configuration
RUN echo "session.cookie_httponly = 1" >> /usr/local/etc/php/conf.d/security.ini && \
    echo "session.cookie_secure = 1" >> /usr/local/etc/php/conf.d/security.ini && \
    echo "session.use_strict_mode = 1" >> /usr/local/etc/php/conf.d/security.ini && \
    echo "expose_php = Off" >> /usr/local/etc/php/conf.d/security.ini && \
    echo "display_errors = Off" >> /usr/local/etc/php/conf.d/security.ini && \
    echo "log_errors = On" >> /usr/local/etc/php/conf.d/security.ini

USER appuser

EXPOSE 9000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --retries=3 --start-period=40s \
    CMD pidof php-fpm || exit 1

CMD ["php-fpm"]