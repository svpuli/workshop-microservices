# Microservices - is that easy?

Welcome to the **Microservices - is that easy?** workshop.

The goal of this workshop is to provide a general overview about the development challenges and bennefits in a Microservices architecture.

**The workshop is devided in 2 main parts:**
 * Brief overview about Microservices and Docker - [slides](https://www.pipedrive.com)
 * Workshop - hands on:
    * [Decomposition](#part1)
    * [API gateway - limit exposure](#part2)
    * [Fault tolerance and Service configuration](#part3)
    * [Service discovery and Health check](#part4)

## System Requirements

In order to successfully follow the workshop, the following tools/services must be previously installed:
 * [Git](https://git-scm.com/downloads) v2.14.1 or greater
 * [Docker](https://docs.docker.com/install/) v18.09.1 or greater
 * [Node](https://nodejs.org/en/download/) v10.15.0 or greater
    * Node can also be installed via nvm - [Linux and MacOs instructions](https://gist.github.com/d2s/372b5943bce17b964a79) & [Windows instructions](https://github.com/coreybutler/nvm-windows)

All these should be available in your **PATH**. To verify things are set up properly, you can run this:

```
git --version
docker --version
node --version
```

## Setup

You will need to start by cloning this repo, then you'll just have to spin up all the docker services. From your terminal, type:

```
git clone git@github.com:pipedrive/workshop-sinfo.git
cd workshop-sinfo
docker-compose up
```

## 🤘 Time to start

### 1.Decomposition <a id="part1"/>

As we have seen in the _**Brief overview about Microservices and Docker**_, microservices should be built accordingly with its own business logical capability.

Currently `search-api` service is requesting both **news** and **gifs** directly to external providers (instead of making use of the `news-api` and `giphy-api` service).

This does not seem to be right as **news** and **gifs** are completely different things and as so might have completely different requirements.

**Tasks to do:**
* Change the `search-api` service to start requesting both the `news-api` and the `giphy-api` services.
* Scale the `news-api` service to 2 instances.

### 2.API Gateway <a id="part2"/>

In a microservices architecture, ideally there should a be a single point of entry in order to access the back end services.

As so, we are going to change our current services so that the `search-api` service becomes our single point of entry -> ***news*** and ***gifs*** will be searched via `search-api`.

**Tasks to do:**
* Access directly the `news-api` and `giphy-api` services and check that each one returns a valid response.
    * `http:localhost:3000/news?search=<YOUR_SEARCH>`
    * `http:localhost:4000/gifs?search=<YOUR_SEARCH>`

* Edit `docker-compose.yml` and remove the port mapping for `news-api` and `giphy-api`.
* Confirm that the previous endpoints are no longer accessible but that the you can still request the results through `search-api`
    * `http://localhost:5000/search?q=<YOUR_SEARCH>`

### 3.Fault tolerance <a id="part3"/>

When a microservice is failing, the overall application whould be able to keep working (even if at a reduced level). Also redeploying a new version of a specific service should have zero impact on the overall application.

**Tasks to do:**
* Stop one of the microservices (for example the `giphy-api`service) and confirm that the overall service keeps working even if no gifs are returned.
* Change `news-api` GET /news endpoint to only return news from
_The New York Times_.

### 4.Service discover and health check <a id="part4"/>

In a microservices architecture, the number of services is intended to grow up to a level where it impossible to rely on manual configuration for services to communicate with each other.
Moreover services are constantly being scaled up and scaled down. This means that they will get available network IPs and assinged on the fly.

The solution is **service discovery**.

**Tasks to do:**
* Open consul ui and see that all the services are registered there.
* Add health check endpoint to all BE services (`news-api`, `giphy-api`, `search-api`).
* Add health check configurations to each service **Dockerfile**
* Open consul and check that all services are passing both on container health and service health check.
* Change the `giphy-api` health check reply with http code **503**. Wait a couple of seconds and check that the service is now failing on consul.

---

💚 [Pipedrive](https://www.pipedrive.com) workshop

_Built by Pipedrive_
