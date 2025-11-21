#!/bin/bash

# Script untuk validasi environment variables
set -e

echo "🔍 Checking environment variables..."

# Required variables
REQUIRED_VARS=(
    "DB_HOST"
    "DB_PORT"
    "DB_DATABASE"
    "DB_USERNAME"
    "DB_PASSWORD"
)

# Check if all required variables are set
ALL_SET=true
for var in "${REQUIRED_VARS[@]}"; do
    if [ -z "${!var}" ]; then
        echo "❌ Missing required environment variable: $var"
        ALL_SET=false
    else
        echo "✅ $var is set"
    fi
done

if [ "$ALL_SET" = false ]; then
    echo ""
    echo "⚠️  Some required environment variables are missing!"
    echo "Please check your .env file or docker-compose.yml"
    exit 1
fi

echo ""
echo "✅ All required environment variables are set!"
echo ""