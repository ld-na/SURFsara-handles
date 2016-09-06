# SURFsara Handles

## Introduction ##
PHP library to create, edit and delete handles through the SURFsara 
REST API.

## Installation ##
Install the SURFsara Handles library using Composer.
```
$ composer require GravityDesignNL/SURFsara-handles
```

## Usage ##
In order to use this class to connect to SURFsara you need to have a 
private-key file and a certificate file. Check with SURFsara to get the 
document to guide you through that process.

Once you generated the security files you can start using the SURFsara 
Handles class to create or delete handles.
```
$client = new SURFsaraHandles();
``` 

### Options ###
The SURFsara Handles class needs some settings (required and optional) 
to make it work properly.

#### Key (required) (string) ####
The absolute path to the private-key file.
```
$client->setKey('<path>'));
```

#### Certificate (required) (string) ####
The absolute path to the certificate file.
```
$client->setCert('<path>'));
```

#### Organisation code (required) (string) ####
The code SURFsara has given to your organisation.
```
$client->setSurfsaraPrefixOrg('<code>'));
```

#### Organisation code (required) (string) ####
The code for the SURFsara environment you will communicate with.
```
$client->setSurfsaraPrefixEnv('<env>'));
```

#### API Url (required) (string) ####
The url for the SURFsara API you will communicate with.
```
$client->setSurfsaraPrefixEnv('<api>'));
```

#### Permissions (required (string)) ####
The permissions needed to create or delete a handle.
```
$client->setPermissions('<permissions>'));
```

#### Overwrite (optional (string)) ####
Set if you want overwrite an existing handle. Can be either 'true' or 'false'.
Defaults to 'true'. 
```
$client->setOverwrite('true'));
```

#### Verify (optional (boolean)) ####
Set if ... Can be either TRUE or FALSE.
Defaults to FALSE. 
```
$client->setVerify(FALSE));
```

#### Headers (optional (array)) ####
Set headers if you need them. Headers are added in an array.
```
$this->client->setHeaders(['Authorization' => 'Handle clientCert="true"']);
```

### Handles ###
When you added all the settings then ou can create or update a (new) handle:
``` 
$client->setHandle();
```

Or you can delete it:
``` 
$client->deleteHandle();
```

Happy handling!!!