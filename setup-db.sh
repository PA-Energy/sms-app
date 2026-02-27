#!/bin/bash
echo "Setting up database..."
cd "$(dirname "$0")/app/api" || exit 1
php setup-db.php
