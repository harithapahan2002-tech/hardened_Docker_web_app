FROM mariadb:11.2-jammy

# Copy custom MariaDB configuration
COPY conf/custom.cnf /etc/mysql/conf.d/custom.cnf

# Ensure proper permissions
RUN chmod 644 /etc/mysql/conf.d/custom.cnf

# Initialization scripts will be automatically run from /docker-entrypoint-initdb.d/
# (mounted from ./database/init/ in docker-compose.yml)

EXPOSE 3306

# Health check
HEALTHCHECK --interval=10s --timeout=5s --retries=5 \
    CMD healthcheck.sh --connect --innodb_initialized || exit 1