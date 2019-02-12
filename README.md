# asgardcms-icommerceauthorize

## Seeder

    run php artisan module:seed Icommerceauthorize

## Vendors

     add composer.json 

        "require": {
            "authorizenet/authorizenet":"~1.9.7"
        },

## Configurations

    You must generate: 
        - Api Login
        - Transaction Key
        - Public Key Client
    
    Links Accounts:
        - Mode Sandbox: https://sandbox.authorize.net/
        - Mode Production: https://account.authorize.net/

## API

### Init (Parameters = orderID)
    
    https://mydomain/api/icommerceauthorize/

	