#!/bin/bash

# Trust self-signed certificates on Ubuntu/Debian
echo "Installing self-signed certificates to system trust store..."

# Create directory for certificates if it doesn't exist
sudo mkdir -p /usr/local/share/ca-certificates/closed-circuit

# Copy certificates
sudo cp docker/8.4/ssl/api.localtest.me.pem /usr/local/share/ca-certificates/closed-circuit/api.localtest.me.crt
sudo cp docker/8.4/ssl/creators.localtest.me.pem /usr/local/share/ca-certificates/closed-circuit/creators.localtest.me.crt

# Update certificate store
sudo update-ca-certificates

echo "Certificates added to system trust store!"
echo "You may need to restart your terminal or browser for changes to take effect."