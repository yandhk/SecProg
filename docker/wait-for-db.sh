#!/bin/bash
set -e

host="${DB_HOST:-db}"
port="${DB_PORT:-3306}"
timeout=60
elapsed=0

echo "⏳ Waiting for database at $host:$port..."

until nc -z "$host" "$port" 2>/dev/null; do
    if [ $elapsed -ge $timeout ]; then
        echo "❌ Database connection timeout after ${timeout}s"
        exit 1
    fi
    
    echo "   Database is unavailable - waiting... (${elapsed}s/${timeout}s)"
    sleep 2
    elapsed=$((elapsed + 2))
done

echo "✅ Database connection successful!"

# Additional wait for MySQL to be fully ready
sleep 3
echo "✅ Database is ready for connections"