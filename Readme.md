FileAccess Orion Extension - PDF Service
==============================================

This service provides a simple yet powerful interface to generate PDF files from HTML utilizing the wkhtml2pdf cli.


Start the service
-------------------
```bash
# make sure this folder is existing and writable on your host
mkdir -p /var/log/docker-images

# define an environment variable for basic auth password, the basic auth user is 'service'
export FAA_PDF_SERVICE_PASS='**********************'

# custom passwords per domain can be defined in a data directory
mkdir -p "$data_dir/$host_name/.pass"

# Find the last releases here: https://github.com/tteichner/andromeda.pdf-service/releases
./run.sh '1.5.6'
```

When using the datadir option with services support the requests follow this url schema: `POST /api/v1/%hostname%/wkhtml2pdf/`. The host specific passwords are checked with `password_verify($password, $hashed_password)` php function.