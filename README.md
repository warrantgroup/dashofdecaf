Dash of Decaf
============

One Friday we decided to have a sprint, with the aim to create an simple interface using the Pivotal Tracker REST API to bridge the gap between software development, and the average end-user waiting for something to happen.

* Stories (per label, with filters)
* Changelog (new features and fixed bugs within a period of time)
* Roadmap (releases ordered by deadline)

Using :

* Twitter Bootstrap 3
* Silex
* Guzzle
* Pivotal Tracker API

# Installation

### Install Composer

```
$ curl -s https://getcomposer.org/installer | php
```

### Update Dependancies

```
./composer install
```

### Create src/config.yml

You must include your Pivotal Tracker API key, and the project ID. A set of valid labels must be defined to filter relevant stories. 

```
pivotaltracker:
    apiKey: [API KEY]
    projectId: [PROJECT ID]

labels:
    brewed: "Freshly Brewed"
    expresso: "Expresso Coffee"
    cappuccino: "Cappuccino Coffee"
    iced: "Iced Coffee"
```

### Cache Directory Permissons

```
chmod 777 cache
```




