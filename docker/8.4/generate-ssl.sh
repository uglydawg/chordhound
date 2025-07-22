#!/bin/bash

# Create SSL directory
mkdir -p docker/8.4/ssl

# Generate SSL certificates for each domain
domains=("api.localtest.me" "creators.localtest.me" "default")

for domain in "${domains[@]}"; do
    echo "Generating SSL certificate for $domain..."
    
    # Generate private key
    openssl genrsa -out "docker/8.4/ssl/${domain}-key.pem" 2048
    
    # Generate certificate
    openssl req -new -x509 -key "docker/8.4/ssl/${domain}-key.pem" \
        -out "docker/8.4/ssl/${domain}.pem" \
        -days 365 \
        -subj "/C=US/ST=State/L=City/O=Organization/CN=$domain"
done

echo "SSL certificates generated successfully!"
echo "Certificates are located in docker/8.4/ssl/"