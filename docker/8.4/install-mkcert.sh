#!/bin/bash

# Install mkcert for locally-trusted development certificates
echo "Installing mkcert..."

# Install mkcert
curl -Lo mkcert https://github.com/FiloSottile/mkcert/releases/download/v1.4.4/mkcert-v1.4.4-linux-amd64
chmod +x mkcert
sudo mv mkcert /usr/local/bin/

# Install CA certificates
mkcert -install

# Generate certificates for our domains
cd docker/8.4/ssl
mkcert -cert-file api.localtest.me.pem -key-file api.localtest.me-key.pem api.localtest.me
mkcert -cert-file creators.localtest.me.pem -key-file creators.localtest.me-key.pem creators.localtest.me
mkcert -cert-file default.pem -key-file default-key.pem localhost 127.0.0.1 ::1

echo "Certificates generated with mkcert!"
echo "These certificates are now trusted by your system."