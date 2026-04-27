#!/bin/bash

echo "=========================================="
echo "Vulnerability Scanning with Trivy"
echo "=========================================="
echo ""

# Scan all images
echo "🔍 Scanning nginx image..."
trivy image suggestion-app-nginx --severity HIGH,CRITICAL

echo ""
echo "🔍 Scanning webserver image..."
trivy image suggestion-app-webserver --severity HIGH,CRITICAL

echo ""
echo "🔍 Scanning database image..."
trivy image suggestion-app-database --severity HIGH,CRITICAL

echo ""
echo "=========================================="
echo "✅ Scan Complete!"
echo "=========================================="
