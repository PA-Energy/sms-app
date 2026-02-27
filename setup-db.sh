#!/bin/bash
echo "Setting up database..."
cd app/api
php setup-db.php
cd ../..
