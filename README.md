# HTTP Resource Server

The http resource server serves any static resource, like product images, web graphics, technical diagrams, transactional documents and images, etc.

The original requirement of this project was to provide an HTTP API that would serve images for a product catalog, for example, in an e-commerce solution. Then it was expanded to serve more types of resources. It abstracts the notion of a file format and incorporates the notion of a business resource type, such as transactional documents (i.e.: an invoice), vouchers, technical diagrams and others.

Implements a local cache system, along with client caching policies.

## Performance tests
Performance tests for the product images functionality can be found [here](https://docs.google.com/spreadsheets/d/e/2PACX-1vQu0HCSKShIhQXE3slWD3AopA5ks4dtdZw3C6e-2tfxEITHaFtLhi4PAXC3hkbx_OPN60SxFcXdw-5T/pubhtml).

## Installation instructions

1. Download repo

2. Run deployment script located in `web/deployment.sh`. The script accepts the following params (none of the params are used in a fresh installation, they are only useful when updating the package):

    `--no-resource` The resource directory is not created.

    `--no-composer` Composer is not downloaded nor the packages updated

    `--no-vendor` The vendor directory is not created nor copied

3. Upload files into their corresponding stores in disk