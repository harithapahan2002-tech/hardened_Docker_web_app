
🛡️ Hardened Web Stack with Ansible & Docker
A production-ready, security-hardened deployment of a PHP-FPM, Nginx, and MariaDB stack. 

This project demonstrates DevSecOps principles, including automated infrastructure provisioning, kernel-level capability stripping, and immutable container design.

🏗️ Architecture OverviewWeb Server: 
  Nginx (Reverse Proxy with SSL & hardened config).
  Application: PHP 8.x (FPM) running as a non-root user.
  Database: MariaDB (Isolated within a private Docker network).
  Automation: Ansible playbooks for server hardening and deployment.
  Security Scanning: Trivy integration for vulnerability assessment.

🔒 Security Hardening FeaturesPrinciple of Least Privilege: 
  All containers utilize security_opt: no-new-privileges and cap_drop: ALL.
  Immutable Infrastructure: Service containers run with read_only root filesystems; write operations are restricted to tmpfs mounts.
  Network Isolation: Custom Docker bridge network with defined IPAM to prevent cross-container chatter.
  Host Protection: Automated UFW (Firewall) and Fail2Ban configuration via Ansible.
  Resource Limits: CPU and Memory constraints enforced to prevent DoS via resource exhaustion.

🚀 Quick Start Guide1. 
Prerequisites:
Docker24.0+ Ansible2.15+ SSHOpenSSH 8.0+2. 
Environment Setup: (check Deploy_help and fixes.txt)
Install the required toolchain (Docker, Ansible, Trivy):
Bash# Update and install Docker official GPG keys
sudo apt-get update
sudo apt-get install -y ca-certificates curl gnupg lsb-release

# Install Docker, Ansible, and Trivy
# (See 'deploy-help and fixes' for full details)
Estimated deployment time: 25-35 minutes.🛠️ Operational CommandsContainer ManagementBash# Build with no-cache to save memory
docker compose build --no-cache

# Start stack in background
docker compose up -d

# Verify health status
docker compose ps
Security AuditingBash# Check non-root execution
docker compose exec webserver whoami # Expected output: appuser

# Check resource limits in real-time
docker stats --no-stream

# Scan images for vulnerabilities
trivy image suggestion_nginx:latest
Backup & RecoveryBash# Create Database Backup
docker compose exec database mysqldump -u root -p${MYSQL_ROOT_PASSWORD} suggestion_db > backup_$(date +%Y%m%d).sql

# Full System Backup
tar -czf suggestion-app-backup-$(date +%Y%m%d).tar.gz /root/suggestion-app
🔍 Troubleshooting & LogsNginx Configuration Test: docker compose exec nginx nginx -tCheck Database Connectivity: docker compose logs database | grep ERRORVerify Firewall Ports: sudo ufw status | grep 80Check Session Persistence: If login fails, ensure session.cookie_secure in auth.php matches your protocol (HTTP vs HTTPS).📄 LicenseDistributed under the MIT License. See LICENSE for more information.
